<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        return $this->render("AppBundle:room:index.html.twig", [
        "username"    => $username,
        "name"        => $name,
        "settings"    => json_encode($this->getParameter("app_ws_settings")),
        "hide_navbar" => true,
        "materialize" => false
        ]);
    }

  /**
   * @Route("/room/upload", name="room_upload", methods={"POST"})
   *
   * @param Request $request
   * @return Response
   */
    public function uploadAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user)) {
            throw $this->createAccessDeniedException();
        }

        $file = $request->files->get("file");
        if ($file->getError()) {
            return new Response('Upload failed', 500);
        }

        $us  = $this->get("app.service.upload");
        $url = $us->upload(
            $file->getPathname(),
            sprintf("%s/%s/%s", $user->getUsername(), date("Y-m-d"), $file->getClientOriginalName()),
            $user,
            $file->getClientMimeType()
        );

        return new Response($url);
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
        $user = $this->getUser();
        if (!($user instanceof UserInterface)) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine();
        $videoRepo = $em->getRepository("AppBundle:VideoLog");

        $room     = $this->findRoom($name);
        $videoLog = $videoRepo->findByID($playID);
        if (!$videoLog) {
            throw $this->createNotFoundException();
        }

        $redis = $this->get("snc_redis.video");
        $redis->rpush("playlist:append", [json_encode([
        "username" => $user->getUsername(),
        "roomName" => $room->getName(),
        "videoID"  => $videoLog->getVideo()->getId()
        ])]);

        return new Response(json_encode([
        "name"   => $room->getName(),
        "playID" => $videoLog->getId()
        ]), 200, ["Content-Type" => "application/json"]);
    }
}
