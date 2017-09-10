<?php
namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AdminBundle\UI\TableResponse;
use AppBundle\Api\Response;
use AppBundle\Entity\User;

class UsersController extends Controller
{
  /**
   * @Route("/users/{page}", name="admin_users", defaults={"page"=1})
   * @param int $page
   * @return TableResponse
   */
  public function indexAction($page = 1)
  {
    $limit = 25;
    $paginator = $this->getDoctrine()->getRepository("AppBundle:User")
      ->findAllByPage($page, $limit);

    $columns = ["id" => "ID", "username" => "Username", "email" => "Email", "lastLogin" => "Last Login"];
    $table   = new TableResponse($columns, $paginator->getIterator());
    $table->setCurrentPage($page);
    $table->setNumPages(ceil($paginator->count() / $limit));

    return $table;
  }

  /**
   * @Route("/entity/user/{id}", name="admin_entity_user_get", methods={"GET"})
   * @param User $user
   * @return Response
   */
  public function getAction(User $user)
  {
    return new Response($user);
  }

  /**
   * @Route("/entity/user/{id}", name="admin_entity_user_post", methods={"POST"})
   * @param User $user
   * @param Request $request
   * @return Response
   */
  public function postAction(User $user, Request $request)
  {
    $em   = $this->getDoctrine();
    $repo = $em->getRepository('AppBundle:User');

    $keep   = array_flip(["username", "email", "enabled", "newPassword", "info"]);
    $values = array_intersect_key($request->request->all(), $keep);
    if ($values["username"] !== $user->getUsername()) {
      if ($repo->findByUsername($values["username"])) {
        return new Response(["validationErrors" => ["username" => "Username taken."]], 400);
      }
    }
    if ($values["email"] !== $user->getEmail()) {
      if ($repo->findByEmail($values["email"])) {
        return new Response(["validationErrors" => ["email" => "Email taken."]], 400);
      }
    }
    if (!empty($values["newPassword"])) {
      $user->setPlainPassword($values["newPassword"]);
    }

    $repo->hydrateFromArray($user, $values);
    $user->setUsernameCanonical($user->getUsername());
    $user->setEmailCanonical($user->getEmail());
    $em->getManager()->flush();

    return new Response($user);
  }
}
