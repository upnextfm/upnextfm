<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
  /**
   * @Route("/r/{name}", name="room", methods={"GET"})
   *
   * @param string $name Name of the room
   * @return Response
   */
  public function indexAction($name)
  {
    return $this->render(":room:index.html.twig", [
      "hide_navbar" => true,
      "materialize" => false,
      "name" => $name
    ]);
  }

  /**
   * @Route("/api/r/{name}/playlist/{playID}", name="api_room_playlist_add", methods={"PUT"})
   *
   * @param string $name Name of the room
   * @param string $playID VideoLog ID
   * @return Response
   */
  public function apiPlaylistAddAction($name, $playID)
  {
    $em = $this->getDoctrine();
    $roomRepo  = $em->getRepository("AppBundle:Room");
    $videoRepo = $em->getRepository("AppBundle:VideoLog");

    $room = $roomRepo->findByName($name);
    if (!$room) {
      throw $this->createNotFoundException();
    }
    $videoLog = $videoRepo->findByID($playID);
    if (!$videoLog) {
      throw $this->createNotFoundException();
    }

    $redis = $this->get("snc_redis.video");
    $redis->set("playlist:play", json_encode([
      "roomName" => $room->getName(),
      "videoID"  => $videoLog->getVideo()->getId()
    ]));

    return new Response(json_encode([
      "name"   => $room->getName(),
      "playID" => $videoLog->getId()
    ]), 200, ["Content-Type" => "application/json"]);
  }
}
