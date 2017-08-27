<?php
namespace AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

class PlaylistsController extends Controller
{
  /**
   * @Route("/playlists", name="admin_playlists")
   */
  public function indexAction()
  {
    return $this->render('AdminBundle:playlists:index.html.twig');
  }
}
