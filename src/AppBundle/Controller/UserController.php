<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

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
    $em     = $this->getDoctrine()->getManager();
    $user   = $this->findUserOrThrow($username);
    $events = $em->getRepository("AppBundle:UserEvent")
      ->findByUser($user, 25);

    return $this->render("AppBundle:user:index.html.twig", [
      "user"   => $user,
      "events" => $events
    ]);
  }

  /**
   * @Route("/u/{username}/favorites/{page}", name="favorites", defaults={"page" = 1})
   *
   * @param string $username
   * @param int $page
   * @return Response
   */
  public function favoritesAction($username, $page = 1)
  {
    $user = $this->findUserOrThrow($username);

    $limit  = 30;
    $offset = ($page - 1) * 30;

    $em             = $this->getDoctrine()->getManager();
    $repo           = $em->getRepository("AppBundle:Favorite");
    $favorites      = $repo->findByUser($user, $limit, $offset);
    $favoritesCount = $repo->countByUser($user);

    $pages   = ceil($favoritesCount / $limit);
    $minPage = $page - 4;
    $maxPage = $page + 4;
    if ($minPage < 1) {
      $minPage = 1;
    }
    if ($maxPage > $pages) {
      $maxPage = $pages;
    }

    return $this->render("AppBundle:user:favorites.html.twig", [
      "user"           => $user,
      "favorites"      => $favorites,
      "favoritesCount" => $favoritesCount,
      "currentPage"    => $page,
      "minPage"        => $minPage,
      "maxPage"        => $maxPage
    ]);
  }

  /**
   * @Route("/account", name="account")
   *
   * @param Request $request
   * @return Response
   */
  public function accountAction(Request $request)
  {
    $user = $this->getUser();
    if (!($user instanceof UserInterface)) {
      throw $this->createNotFoundException();
    }

    if ($request->getMethod() === "POST") {
      dump($request->request->all());
      die();
    }

    return $this->render("AppBundle:user:account.html.twig", [
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
