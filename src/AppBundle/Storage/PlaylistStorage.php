<?php
namespace AppBundle\Storage;

use AppBundle\Entity\Room;
use AppBundle\Entity\VideoLog;
use AppBundle\Entity\VideoLogRepository;
use malkusch\lock\mutex\LockMutex;

class PlaylistStorage extends AbstractStorage
{
  /**
   * @var VideoLogRepository
   */
    private $videoLogRepository;

  /**
   * @var LockMutex
   */
    private $mutex;

  /**
   * @var array
   */
    private $siteSettings = [];

  /**
   * @param VideoLogRepository $videoLogRepository
   * @return $this
   */
    public function setVideoLogRepository(VideoLogRepository $videoLogRepository)
    {
        $this->videoLogRepository = $videoLogRepository;
        return $this;
    }

  /**
   * @param LockMutex $mutex
   * @return $this
   */
    public function setMutex(LockMutex $mutex)
    {
        $this->mutex = $mutex;
        return $this;
    }

  /**
   * @param array $siteSettings
   * @return $this
   */
    public function setSiteSettings(array $siteSettings)
    {
        $this->siteSettings = $siteSettings;
        return $this;
    }

  /**
   * @param Room $room
   * @return VideoLog[]
   */
    public function getAll(Room $room)
    {
        $videoLogs        = [];
        $maxPlaylistItems = $this->siteSettings["maxPlaylistItems"];
        foreach ($this->redis->lrange($this->keyRoomPlaylist($room), 0, $maxPlaylistItems) as $videoLogID) {
            if ($videoLog = $this->videoLogRepository->findByID($videoLogID)) {
                $videoLogs[] = $videoLog;
            }
        }

        return $videoLogs;
    }

  /**
   * @param VideoLog $videoLog
   * @return int
   */
    public function append(VideoLog $videoLog)
    {
        $id = $videoLog->getId();
        if (!$id) {
            throw new \RuntimeException('Found videoLog without ID.');
        }

        return $this->redis->rpush(
            $this->keyRoomPlaylist($videoLog->getRoom()),
            [$id]
        );
    }

  /**
   * @param Room $room
   * @return array|null
   */
    public function popToCurrent(Room $room)
    {
        $videoLog = $this->pop($room);
        if ($videoLog) {
            $timeStarted = time();
            $this->setCurrent($videoLog, $timeStarted);

            return [
            "timeStarted" => $timeStarted,
            "videoLog"    => $videoLog
            ];
        }

        return null;
    }

  /**
   * @param Room $room
   * @return VideoLog|null
   */
    public function pop(Room $room)
    {
        if ($id = $this->redis->lpop($this->keyRoomPlaylist($room))) {
            return $this->videoLogRepository->findByID($id);
        }
        return null;
    }

  /**
   * @param VideoLog $videoLog
   * @param int $timeStarted
   * @return bool
   */
    public function setCurrent(VideoLog $videoLog, $timeStarted = 0)
    {
        $id = $videoLog->getId();
        if (!$id) {
            throw new \RuntimeException('Found videoLog without ID.');
        }
        $value = json_encode([
        "timeStarted"  => $timeStarted !== 0 ? $timeStarted : time(),
        "videoLogID"   => $id
        ]);
        $resp = $this->redis->set($this->keyRoomCurrent($videoLog->getRoom()), $value);

        return (bool)$resp;
    }

  /**
   * @param Room $room
   * @return array|null
   */
    public function getCurrent(Room $room)
    {
        if ($encoded = $this->redis->get($this->keyRoomCurrent($room))) {
            $decoded = json_decode($encoded, true);
            return [
            "timeStarted" => $decoded["timeStarted"],
            "videoLog"    => $this->videoLogRepository->findByID($decoded["videoLogID"])
            ];
        }

        return null;
    }

  /**
   * @param Room $room
   * @return int
   */
    public function clearCurrent(Room $room)
    {
        return $this->redis->del($this->keyRoomCurrent($room));
    }

  /**
   * @param Room $room
   * @param int $videoID
   * @return bool|array
   */
    public function removeByID(Room $room, $videoID)
    {
        if ($this->redis->lrem($this->keyRoomPlaylist($room), 0, $videoID)) {
            return true;
        }
        if ($encoded = $this->redis->get($this->keyRoomCurrent($room))) {
            $decoded = json_decode($encoded, true);
            if ($decoded["videoLogID"] == $videoID) {
                return $this->popToCurrent($room);
            }
        }

        return false;
    }

  /**
   * @param Room $room
   * @param int $videoID
   * @return int
   */
    public function playNext(Room $room, $videoID)
    {
        $this->redis->lrem($this->keyRoomPlaylist($room), 0, $videoID);
        return $this->redis->lpush(
            $this->keyRoomPlaylist($room),
            [$videoID]
        );
    }

  /**
   * @param Room $room
   * @return int
   */
    public function getLength(Room $room)
    {
        return $this->redis->llen($this->keyRoomPlaylist($room));
    }

  /**
   * @param Room $room
   * @return string
   */
    private function keyRoomPlaylist(Room $room)
    {
        return sprintf("room:%s:playlist", $room->getName());
    }

  /**
   * @param Room $room
   * @return string
   */
    private function keyRoomCurrent(Room $room)
    {
        return sprintf("room:%s:playlist:current", $room->getName());
    }
}
