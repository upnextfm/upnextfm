<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
  /**
   * @Route("/u/{username}", name="profile")
   *
   * @param string $username
   * @return Response
   */
  public function indexAction($username)
  {
    $em   = $this->getDoctrine()->getManager();
    $user = $this->findUserOrThrow($username);
    $playedRecently = $em->getRepository("AppBundle:VideoLog")
      ->findRecentByUser($user, 30);

    return $this->render("AppBundle:user:index.html.twig", [
      "user"           => $user,
      "playedRecently" => $playedRecently
    ]);
  }

  /**
   * @Route("/u/{username}/favorites", name="favorites")
   *
   * @param string $username
   * @return Response
   */
  public function favoritesAction($username)
  {
    $user = $this->findUserOrThrow($username);
    return $this->render("AppBundle:user:favorites.html.twig", [
      "user" => $user
    ]);
  }

  /**
   * @param string $username
   * @return \AppBundle\Entity\User
   */
  protected function findUserOrThrow($username)
  {
    $em   = $this->getDoctrine()->getManager();
    $user = $em->getRepository("AppBundle:User")->findByUsername($username);
    if (!$user) {
      throw $this->createNotFoundException();
    }

    return $user;
  }
}
