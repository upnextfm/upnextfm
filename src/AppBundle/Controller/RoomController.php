<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

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
    $username = "";
    $user = $this->getUser();
    if ($user instanceof UserInterface) {
      $username = $user->getUsername();
    }

    return $this->render(":room:index.html.twig", [
      "username"    => $username,
      "name"        => $name,
      "hide_navbar" => true,
      "materialize" => false
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
    $videoRepo = $em->getRepository("AppBundle:VideoLog");

    $room     = $this->findRoom($name);
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
