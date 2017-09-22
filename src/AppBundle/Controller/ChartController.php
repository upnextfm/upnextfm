<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Vote;
use AppBundle\Entity\ValueDecorator;
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

    	$videosRaw = $em->getRepository('AppBundle:Video')
    						->findVideosWithVotes(500);

    	foreach ($videosRaw as $video) {
    		$videos[] = new ValueDecorator($video, [
    			"voteCount" => count($video->getVotes())
    		]);
    	}

    	usort($videos, function ($a, $b) {
    		if ($a->voteCount > $b->voteCount) {
    			return -1;
    		} else {
    			return 1;
    		}
    	});

    	// die(var_dump($videos));

	    return $this->render("AppBundle:chart:upvoted.html.twig", [
	      "videos" => $videos
	    ]);
    }
}