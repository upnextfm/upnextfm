<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PlaylistController extends Controller
{
  /**
   * @Route("/recent/{page}", name="playlist_recent", defaults={"page" = 1})
   *
   * @param int $page
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function recentAction($page)
  {
    $limit  = 30;
    $offset = ($page - 1) * 30;

    $em             = $this->getDoctrine()->getManager();
    $repo           = $em->getRepository("AppBundle:VideoLog");
    $playedRecently = $repo->findRecent($limit, $offset);
    $playedCount    = $repo->countAll();

    $minDate = new \DateTime();
    $maxDate = new \DateTime();
    if ($playedRecently) {
      $minDate = $playedRecently[0]->getDateCreated();
      $maxDate = $playedRecently[count($playedRecently) - 1]->getDateCreated();
    }

    $pages   = ceil($playedCount / $limit);
    $minPage = $page - 4;
    $maxPage = $page + 4;
    if ($minPage < 1) {
      $minPage = 1;
    }
    if ($maxPage > $pages) {
      $maxPage = $pages;
    }

    $rooms = [];
    $user  = $this->getUser();
    if ($user) {
      $storage = $this->get("app.storage.room");
      foreach($storage->getUserRooms($user) as $roomName) {
        $rooms[] = [
          "name" => $roomName
        ];
      }
    }

    return $this->render("AppBundle:playlist:recent.html.twig", [
      "activeTab"      => "recent",
      "playedRecently" => $playedRecently,
      "playedCount"    => $playedCount,
      "currentPage"    => $page,
      "minPage"        => $minPage,
      "maxPage"        => $maxPage,
      "minDate"        => $minDate,
      "maxDate"        => $maxDate,
      "playModalRooms" => $rooms
    ]);
  }
}
