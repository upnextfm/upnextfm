<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PlaylistController extends Controller
{
  /**
   * @Route("/recent", name="playlist_recent")
   */
  public function homeAction()
  {
    $em = $this->getDoctrine()->getManager();
    $playedRecently = $em->getRepository("AppBundle:VideoLog")->findRecent(10);

    return $this->render(":playlist:recent.html.twig", [
      "playedRecently" => $playedRecently
    ]);
  }
}
