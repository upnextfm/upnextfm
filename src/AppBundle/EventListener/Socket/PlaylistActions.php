<?php
namespace AppBundle\EventListener\Socket;

class PlaylistActions
{
  const VIDEOS = "playlist:playlistVideos";
  const START  = "playlist:playlistStart";
  const STOP   = "playlist:playlistStop";
  const TIME   = "player:playerTime";
}
