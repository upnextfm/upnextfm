<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ChartController extends Controller
{
  /**
   * @Route("/charts/upvoted", name="upvoted", methods={"GET"})
   *
   * @return Response
   */
    public function mostUpvotedAction()
    {
	    return $this->render("AppBundle:chart:upvoted.html.twig", [
	      "user" => $user
	    ]);
    }
}