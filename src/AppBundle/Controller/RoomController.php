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
}
