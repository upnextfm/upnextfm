<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Vote;
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
    	$em = $this->getDoctrine()->getManager();

    	$votes = $em->getRepository('AppBundle:Vote')
    						->findMostUpvotedVideos();

	    return $this->render("AppBundle:chart:upvoted.html.twig", [
	      "votes" => $votes
	    ]);
    }
}