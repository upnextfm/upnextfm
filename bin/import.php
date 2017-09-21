<?php
use Symfony\Component\Yaml\Yaml;
use Madcoda\Youtube\Youtube;

include(__DIR__ . "/../vendor/autoload.php");
$params = Yaml::parse(file_get_contents(__DIR__ . "/../app/config/parameters.yml"));
$params = $params["parameters"];

$pdoUpnext = new \PDO(
  "mysql:dbname=${params['database_name']};host=${params['database_host']};charset=utf8",
  $params["database_user"],
  $params["database_password"]
);
$pdoCytube = new \PDO(
  "mysql:dbname=${params['database_cytube_name']};host=${params['database_cytube_host']};charset=utf8",
  $params["database_cytube_user"],
  $params["database_cytube_password"]
);

// importUserInfo();
// importRooms();
// importRoomSettings();
// importVideos();
// importVideoLogs();
// importChatLogs();
// importUserEvents();
importFavorites();

/**
 *
 */
function importUserEvents()
{
  global $pdoUpnext, $pdoCytube;

  $videoLogs = [];
  foreach($pdoUpnext->query("SELECT *, UNIX_TIMESTAMP(date_created) * 1000 AS time FROM `video_log` ORDER BY `id` ASC") as $row) {
    $row["type"] = "played_video";
    $videoLogs[] = $row;
  }

  $favorites = [];
  foreach($pdoCytube->query("SELECT * FROM `favorites` ORDER BY `id` ASC") as $row) {
    $cytubeUser = fetchCytubeUserByID($row["user_id"]);
    if ($cytubeUser) {
      $upnextUser = fetchUserByUsername($cytubeUser["name"]);
      if ($upnextUser) {
        $cytubeMedia = fetchCytubeMediaByID($row["media_id"]);
        if ($cytubeMedia) {
          $upnextVideo = fetchVideoByCodename($cytubeMedia["uid"]);
          if ($upnextVideo) {
            $row["user_id"]  = $upnextUser["id"];
            $row["video_id"] = $upnextVideo["id"];
            $row["room_id"]  = 1;
            $row["type"]     = "favorited";
            $favorites[]     = $row;
          }
        }
      }
    }
  }

  $votes = [];
  foreach($pdoCytube->query("SELECT * FROM `votes` ORDER BY `id` ASC") as $row) {
    if ($row["value"] != 1) {
      continue;
    }

    $cytubeUser = fetchCytubeUserByID($row["user_id"]);
    if ($cytubeUser) {
      $upnextUser = fetchUserByUsername($cytubeUser["name"]);
      if ($upnextUser) {
        $cytubeMedia = fetchCytubeMediaByID($row["media_id"]);
        if ($cytubeMedia) {
          $upnextVideo = fetchVideoByCodename($cytubeMedia["uid"]);
          if ($upnextVideo) {
            $row["user_id"]  = $upnextUser["id"];
            $row["video_id"] = $upnextVideo["id"];
            $row["room_id"]  = 1;
            $row["type"]     = "upvoted";
            $votes[]         = $row;
          }
        }
      }
    }
  }

  $channels = [];
  foreach($pdoCytube->query("SELECT * FROM `channels` ORDER BY `id` ASC") as $row) {
    $upnextUser = fetchUserByUsername($row["owner"]);
    if ($upnextUser) {
      $upnextRoom = fetchRoomByName($row["name"]);
      if ($upnextRoom) {
        $row["user_id"]  = $upnextUser["id"];
        $row["video_id"] = null;
        $row["room_id"]  = $upnextRoom["id"];
        $row["type"]     = "created_room";
        $channels[]      = $row;
      }
    }
  }

  $events = array_merge($videoLogs, $favorites, $votes, $channels);
  usort($events, function($a, $b) {
    if ($a["time"] == $b["time"]) {
      return 0;
    }
    return ($a["time"] < $b["time"]) ? -1 : 1;
  });

  foreach($events as $event) {
    $dateCreated = timeToDate($event["time"]);
    println($dateCreated . " " . $event["type"]);

    $sql = "
      INSERT INTO `user_event`
      (`type`, `target_video_id`, `target_room_id`, `user_id`, `date_created`)
      VALUES
      (:type, :target_video_id, :target_room_id, :user_id, :date_created)
    ";
    $stmt = $pdoUpnext->prepare($sql);
    $stmt->execute([
      ":type"            => $event["type"],
      ":target_video_id" => $event["video_id"],
      ":target_room_id"  => $event["room_id"],
      ":user_id"         => $event["user_id"],
      ":date_created"    => $dateCreated
    ]);
  }
}

/**
 *
 */
function importUserInfo()
{
  global $pdoUpnext, $pdoCytube;

  foreach($pdoCytube->query("SELECT * FROM `users`") as $row) {
    println($row["name"]);

    $user = fetchUserByUsername($row["name"]);
    if (!$user) {
      continue;
    }

    $profile = json_decode($row["profile"], true);
    if (empty($profile["image"])) {
      $profile["image"] = sprintf('https://robohash.org/%s?set=set3', $row["name"]);
    }

    $exec = [
      ":user_id"   => $row["id"],
      ":avatar_sm" => $profile["image"],
      ":avatar_md" => $profile["image"],
      ":avatar_lg" => $profile["image"],
      ":location"  => isset($profile["location"]) ? $profile["location"] : "",
      ":website"   => isset($profile["website"]) ? $profile["website"] : "",
      ":bio"       => isset($profile["bio"]) ? $profile["bio"] : ""
    ];

    $sql = "
      INSERT INTO `user_info`
      (`user_id`, `avatar_sm`, `avatar_md`, `avatar_lg`, `location`, `website`, `bio`)
      VALUES
      (:user_id, :avatar_sm, :avatar_md, :avatar_lg, :location, :website, :bio)
    ";
    $stmt = $pdoUpnext->prepare($sql);
    $stmt->execute($exec);
  }

  foreach($pdoUpnext->query("SELECT * FROM `user`") as $row) {
    println($row["username"]);

    $stmt = $pdoUpnext->prepare("SELECT * FROM `user_info` WHERE `user_id` = :user_id LIMIT 1");
    $stmt->execute([":user_id" => $row["id"]]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$info) {
      $avatar = sprintf('https://robohash.org/%s?set=set3', $row["username"]);
      $exec = [
        ":user_id"   => $row["id"],
        ":avatar_sm" => $avatar,
        ":avatar_md" => $avatar,
        ":avatar_lg" => $avatar,
        ":location"  => "",
        ":website"   => "",
        ":bio"       => ""
      ];

      $sql = "
      INSERT INTO `user_info`
      (`user_id`, `avatar_sm`, `avatar_md`, `avatar_lg`, `location`, `website`, `bio`)
      VALUES
      (:user_id, :avatar_sm, :avatar_md, :avatar_lg, :location, :website, :bio)
    ";
      $stmt = $pdoUpnext->prepare($sql);
      $stmt->execute($exec);
    }
  }
}

/**
 *
 */
function importChatLogs()
{
  global $pdoUpnext, $pdoCytube;

  foreach($pdoCytube->query("SELECT * FROM `chat_logs` WHERE `type` = 'message'") as $row) {
    println($row["user"]);

    $channel = fetchCytubeChannelByID($row["channel_id"]);
    if (!$channel) {
      continue;
    }
    $room = fetchRoomByName($channel["name"]);
    if (!$room) {
      continue;
    }
    $user = fetchUserByUsername($row["user"]);
    if (!$user) {
      continue;
    }
    $meta  = json_decode($row["meta"], true);
    if (!$meta["color"]) {
      continue;
    }

    $message = trim(strip_tags($row["msg"]));
    if (!$message) {
      continue;
    }
    $message = decodeEntities($message);
    $message = sprintf("[%s]%s[/#]", $meta["color"], $message);

    $exec = [
      ":room_id"      => $room["id"],
      ":user_id"      => $user["id"],
      ":message"      => $message,
      ":date_created" => timeToDate($row["time"])
    ];

    $sql = "
      INSERT INTO `chat_log`
      (`room_id`, `user_id`, `message`, `date_created`)
      VALUES
      (:room_id, :user_id, :message, :date_created)
    ";
    $stmt = $pdoUpnext->prepare($sql);
    $stmt->execute($exec);
  }
}

/**
 *
 */
function importFavorites()
{
  global $pdoUpnext, $pdoCytube;

  foreach($pdoCytube->query("SELECT * FROM `favorites`") as $row) {
    $media = fetchCytubeMediaByID($row["media_id"]);
    if ($media) {
      $media = fetchVideoByCodename($media["uid"]);
      $user  = fetchCytubeUserByID($row["user_id"]);
      if ($media && $user) {
        $user  = fetchUserByUsername($user["name"]);
        println($media["codename"]);

        $exec  = [
          ":video_id"     => $media["id"],
          ":user_id"      => $user["id"],
          ":date_created" => timeToDate($row["time"])
        ];
        $sql = "
          INSERT INTO `favorite`
          (`video_id`, `user_id`, `date_created`)
          VALUES
          (:video_id, :user_id, :date_created)
        ";
        $stmt = $pdoUpnext->prepare($sql);
        $stmt->execute($exec);
      }
    }
  }
}

/**
 *
 */
function importVideoLogs()
{
  global $pdoUpnext, $pdoCytube;

  foreach($pdoCytube->query("SELECT * FROM `playlist_history`") as $row) {
    println($row["channel"] . " " . $row["user"]);

    $stmt = $pdoCytube->prepare("SELECT * FROM `media` WHERE `id` = :id LIMIT 1");
    $stmt->execute([":id" => $row["media_id"]]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$media) {
      continue;
    }

    $stmt = $pdoUpnext->prepare("SELECT * FROM `video` WHERE `codename` = :codename LIMIT 1");
    $stmt->execute([":codename" => $media["uid"]]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$video) {
      continue;
    }

    $room = fetchRoomByName($row["channel"]);
    if (!$room) {
      continue;
    }
    $user = fetchUserByUsername($row["user"]);
    if (!$user) {
      continue;
    }

    $exec = [
      ":video_id"     => $video["id"],
      ":room_id"      => $room["id"],
      ":user_id"      => $user["id"],
      ":date_created" => timeToDate($row["time"])
    ];

    $sql = "
      INSERT INTO `video_log`
      (`video_id`, `room_id`, `user_id`, `date_created`)
      VALUES
      (:video_id, :room_id, :user_id, :date_created)
    ";
    $stmt = $pdoUpnext->prepare($sql);
    $stmt->execute($exec);
  }
}

/**
 *
 */
function importVideos()
{
  global $pdoUpnext, $pdoCytube, $params;

  $youtube = new Youtube(["key" => $params["app_service_youtube_api_key"]]);

  foreach($pdoCytube->query("SELECT * FROM `media`") as $row) {
    println($row["title"]);
    if ($row["type"] == "yt") {

      $stmt = $pdoCytube->prepare("SELECT * FROM `playlist_history` WHERE `media_id` = :media_id ORDER BY `id` ASC LIMIT 1");
      $stmt->execute([":media_id" => $row["id"]]);
      $history = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$history) {
        continue;
      }
      $stmt = $pdoCytube->prepare("SELECT * FROM `playlist_history` WHERE `media_id` = :media_id ORDER BY `id` DESC LIMIT 1");
      $stmt->execute([":media_id" => $row["id"]]);
      $historyLast = $stmt->fetch(PDO::FETCH_ASSOC);

      $createdByUser = fetchUserByUsername($history["user"]);
      if (!$createdByUser) {
        continue;
      }
      $createdInRoom = fetchRoomByName($history["channel"]);
      if (!$createdInRoom) {
        continue;
      }
      $resp = $youtube->getVideoInfo($row["uid"]);
      if (!$resp) {
        continue;
      }

      $exec = [
        ":created_by_user_id" => $createdByUser["id"],
        ":created_in_room_id" => $createdInRoom["id"],
        ":permalink"          => "https://youtu.be/${row['uid']}",
        ":title"              => $resp->snippet->title,
        ":codename"           => $row["uid"],
        ":provider"           => "youtube",
        ":seconds"            => $row["seconds"],
        ":num_plays"          => fetchCytubePlaylistHistoryCount($history["media_id"]),
        ":date_created"       => timeToDate($row["time"]),
        ":date_last_played"   => timeToDate($historyLast["time"]),
        ":thumb_sm"           => (!empty($resp->snippet->thumbnails->medium->url)
          ? $resp->snippet->thumbnails->medium->url
          : $resp->snippet->thumbnails->default->url),
        ":thumb_md"           => (!empty($resp->snippet->thumbnails->standard->url)
          ? $resp->snippet->thumbnails->standard->url
          : $resp->snippet->thumbnails->default->url),
        ":thumb_lg"           => (!empty($resp->snippet->thumbnails->high->url)
          ? $resp->snippet->thumbnails->high->url
          : $resp->snippet->thumbnails->default->url)
      ];

      $sql = "
      INSERT INTO `video`
      (`created_by_user_id`, `created_in_room_id`, `permalink`, `title`, `codename`, `provider`, `seconds`, `num_plays`, `date_created`, `date_last_played`, `thumb_sm`, `thumb_md`, `thumb_lg`)
      VALUES
      (:created_by_user_id, :created_in_room_id, :permalink, :title, :codename, :provider, :seconds, :num_plays, :date_created, :date_last_played, :thumb_sm, :thumb_md, :thumb_lg)
    ";
      $stmt = $pdoUpnext->prepare($sql);
      $stmt->execute($exec);
      usleep(1000);
    }
  }
}

/**
 *
 */
function importRooms()
{
  global $pdoUpnext, $pdoCytube;

  foreach($pdoCytube->query("SELECT * FROM `channels`") as $row) {
    println($row["name"]);
    if ($row["name"] == "lobby") {
      continue;
    }

    $owner = fetchUserByUsername($row["owner"]);
    if (!$owner) {
      continue;
    }

    $stmt = $pdoCytube->prepare("SELECT * FROM `channel_data` WHERE `channel_id` = :channel_id AND `key` = 'bio' LIMIT 1");
    $stmt->execute([":channel_id" => $row["id"]]);
    $chanBio = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($chanBio) {
      $chanBio = $chanBio["value"];
    } else {
      $chanBio = "";
    }

    $sql = "
      INSERT INTO `room`
      (`name`, `description`, `is_private`, `is_deleted`, `created_by_user_id`, `date_created`)
      VALUES
      (:name, :description, :is_private, :is_deleted, :created_by_user_id, :date_created)
    ";
    $stmt = $pdoUpnext->prepare($sql);
    $stmt->execute([
      ":name"               => $row["name"],
      ":description"        => $chanBio,
      ":is_private"         => 0,
      ":is_deleted"         => 0,
      ":created_by_user_id" => $owner["id"],
      ":date_created"       => timeToDate($row["time"])
    ]);
  }
}

/**
 *
 */
function importRoomSettings()
{
  global $pdoUpnext, $pdoCytube;

  foreach($pdoUpnext->query("SELECT * FROM `room`") as $row) {
    $channel = fetchCytubeChannelByName($row["name"]);
    if (!$channel) {
      continue;
    }

    println($channel["name"]);

    $stmt = $pdoCytube->prepare("SELECT * FROM `channel_data` WHERE `channel_id` = :channel_id AND `key` = 'opts' LIMIT 1");
    $stmt->execute([":channel_id" => $channel["id"]]);
    $opts = json_decode($stmt->fetch(PDO::FETCH_ASSOC)["value"], true);
    if (!$opts["join_msg"]) {
      $opts["join_msg"] = "";
    }

    $exec = [
      ":room_id"      => $row["id"],
      ":is_public"    => 1,
      ":join_message" => $opts["join_msg"],
      ":thumb_sm"     => $opts["thumbnail"],
      ":thumb_md"     => $opts["thumbnail"],
      ":thumb_lg"     => $opts["thumbnail"],
      ":date_updated" => date("Y-m-d H:i:s")
    ];

    $sql = "
      INSERT INTO `room_settings`
      (`room_id`, `is_public`, `thumb_sm`, `thumb_md`, `thumb_lg`, `join_message`, `date_updated`)
      VALUES
      (:room_id, :is_public, :thumb_sm, :thumb_md, :thumb_lg, :join_message, :date_updated)
    ";
    $stmt = $pdoUpnext->prepare($sql);
    $stmt->execute($exec);
  }
}

/**
 * @param string $username
 * @return array
 */
function fetchUserByUsername($username)
{
  global $pdoUpnext;
  static $users = [];

  if (!isset($users[$username])) {
    $stmt = $pdoUpnext->prepare("SELECT * FROM `user` WHERE `username` = :username LIMIT 1");
    $stmt->execute([":username" => $username]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $users[$username] = $row;
    }
  }

  return isset($users[$username]) ? $users[$username] : null;
}

/**
 * @param string $codename
 * @return array
 */
function fetchVideoByCodename($codename)
{
  global $pdoUpnext;
  static $videos = [];

  if (!isset($videos[$codename])) {
    $stmt = $pdoUpnext->prepare("SELECT * FROM `video` WHERE `codename` = :codename LIMIT 1");
    $stmt->execute([":codename" => $codename]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $videos[$codename] = $row;
    }
  }

  return isset($videos[$codename]) ? $videos[$codename] : null;
}

/**
 * @param string $name
 * @return array
 */
function fetchRoomByName($name)
{
  global $pdoUpnext;
  static $rooms = [];

  if (!isset($rooms[$name])) {
    $stmt = $pdoUpnext->prepare("SELECT * FROM `room` WHERE `name` = :name LIMIT 1");
    $stmt->execute([":name" => $name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      $rooms[$name] = $row;
    }
  }

  return $rooms[$name] ?: null;
}

/**
 * @param int $userID
 * @return array
 */
function fetchCytubeUserByID($userID)
{
  global $pdoCytube;
  static $users = [];

  if (!isset($users[$userID])) {
    $stmt = $pdoCytube->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
    $stmt->execute([":id" => $userID]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $users[$userID] = $row;
    }
  }

  return isset($users[$userID]) ? $users[$userID] : null;
}

/**
 * @param int $channelID
 * @return array
 */
function fetchCytubeChannelByID($channelID)
{
  global $pdoCytube;

  $stmt = $pdoCytube->prepare("SELECT * FROM `channels` WHERE `id` = :id LIMIT 1");
  $stmt->execute([":id" => $channelID]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param string $name
 * @return array
 */
function fetchCytubeChannelByName($name)
{
  global $pdoCytube;

  $stmt = $pdoCytube->prepare("SELECT * FROM `channels` WHERE `name` = :name LIMIT 1");
  $stmt->execute([":name" => $name]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param int $mediaID
 * @return array
 */
function fetchCytubeMediaByID($mediaID)
{
  global $pdoCytube;
  static $media = [];

  if (!isset($media[$mediaID])) {
    $stmt = $pdoCytube->prepare("SELECT * FROM `media` WHERE `id` = :id LIMIT 1");
    $stmt->execute([":id" => $mediaID]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $media[$mediaID] = $row;
    }
  }

  return isset($media[$mediaID]) ? $media[$mediaID] : null;
}

/**
 * @param int $media_id
 * @return array
 */
function fetchCytubePlaylistHistoryCount($media_id)
{
  global $pdoCytube;

  $stmt = $pdoCytube->prepare("SELECT COUNT(*) AS c FROM `playlist_history` WHERE `media_id` = :media_id LIMIT 1");
  $stmt->execute([":media_id" => $media_id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row["c"];
}

/**
 * @param int $time
 * @return string
 */
function timeToDate($time)
{
  return date("Y-m-d H:i:s", $time / 1000);
}

/**
 * @param string $message
 * @return string
 */
function decodeEntities($message) {
  $message = html_entity_decode($message);
  return preg_replace_callback("/(&#[0-9]+;)/", function($m) {
    return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
  }, $message);
}

/**
 * @param string $str
 */
function println($str) {
  echo $str . "\n";
}
