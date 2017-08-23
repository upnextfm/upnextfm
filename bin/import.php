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

//importRooms();
//importVideos();
//importVideoLogs();
importChatLogs();

function decodeEntities($message) {
  $message = html_entity_decode($message);
  return preg_replace_callback("/(&#[0-9]+;)/", function($m) {
    return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
  }, $message);
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
 * @param string $username
 * @return array
 */
function fetchUserByUsername($username)
{
  global $pdoUpnext;

  $stmt = $pdoUpnext->prepare("SELECT * FROM `user` WHERE `username` = :username LIMIT 1");
  $stmt->execute([":username" => $username]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * @param string $name
 * @return array
 */
function fetchRoomByName($name)
{
  global $pdoUpnext;

  $stmt = $pdoUpnext->prepare("SELECT * FROM `room` WHERE `name` = :name LIMIT 1");
  $stmt->execute([":name" => $name]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
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
 * @param string $str
 */
function println($str) {
  echo $str . "\n";
}
