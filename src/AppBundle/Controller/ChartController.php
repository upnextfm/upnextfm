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

    	$videosWithVoteCount = $em->getRepository('AppBundle:Video')
    						->findVideosWithVotes();

        $videos = [];

    	foreach ($videosWithVoteCount as $videoWithVoteCount) {
    		$netUpVotes = $videoWithVoteCount["voteCount"];

    		$videos[] = new ValueDecorator($videoWithVoteCount[0], [
    			"voteCount" => $netUpVotes
    		]);
    	}

    	usort($videos, function ($a, $b) {
    		if ($a->voteCount > $b->voteCount) {
    			return -1;
    		} else {
    			return 1;
    		}
    	});

        $videos = array_slice($videos, 0, 400);

	    return $this->render("AppBundle:chart:upvoted.html.twig", [
	      "videos" => $videos
	    ]);
    }
}