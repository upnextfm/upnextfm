<?php
namespace AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use AdminBundle\UI\TableResponse;
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
}
