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
    $em = $this->getDoctrine();
    $em->getRepository('AppBundle:User')
      ->hydrateFromArray($user, $request->request->all());
    $em->getManager()->flush();

    return new Response(['status' => 'ok']);
  }
}
