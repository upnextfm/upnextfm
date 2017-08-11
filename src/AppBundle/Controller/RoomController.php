<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends Controller
{
    /**
     * @Route("/r/{name}", name="room")
     *
     * @param string $name Name of the room
     * @return Response
     */
    public function indexAction($name)
    {
        return $this->render(':room:index.html.twig', [
            'name' => $name
        ]);
    }
}
