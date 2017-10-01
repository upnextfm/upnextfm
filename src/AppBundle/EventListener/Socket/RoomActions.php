<?php
namespace AppBundle\EventListener\Socket;

class RoomActions
{
  const MESSAGES = "room:roomMessages";
  const MESSAGE  = "room:roomMessage";
  const USERS    = "room:roomUsers";
  const JOINED   = "room:roomJoined";
  const PARTED   = "room:roomParted";
  const PONG     = "room:pong";
}
