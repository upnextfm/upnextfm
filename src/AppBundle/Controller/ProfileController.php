<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
  /**
   * @Route("/u/{username}", name="profile")
   *
   * @param string $username
   * @return Response
   */
  public function indexAction($username)
  {
    $user = $this->getDoctrine()
      ->getManager()
      ->getRepository("AppBundle:User")
      ->findByUsername($username);
    if (!$user) {
      throw $this->createNotFoundException();
    }

    return $this->render(":profile:index.html.twig", [
      "user" => $user
    ]);
  }
}
