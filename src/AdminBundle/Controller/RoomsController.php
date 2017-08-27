<?php
namespace AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

class RoomsController extends Controller
{
  /**
   * @Route("/rooms", name="admin_rooms")
   */
  public function indexAction()
  {
    return $this->render('AdminBundle:rooms:index.html.twig');
  }
}
