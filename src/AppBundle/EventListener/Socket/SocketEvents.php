<?php
namespace AppBundle\EventListener\Socket;

class SocketEvents
{
  const ROOM_REQUEST      = "socket_room_request";
  const VIDEO_REQUEST     = "socket_video_request";
  const PM_REQUEST        = "socket_pm_request";
  const ROOM_RESPONSE     = "socket_room_response";
  const VIDEO_RESPONSE    = "socket_video_response";
  const PLAYLIST_RESPONSE = "socket_playlist_response";
  const PM_RESPONSE       = "socket_pm_response";
}
