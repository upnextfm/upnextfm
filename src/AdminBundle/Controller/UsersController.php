<?php
namespace AdminBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

class UsersController extends Controller
{
  /**
   * @Route("/users", name="admin_users")
   */
  public function indexAction()
  {
    return $this->render('AdminBundle:users:index.html.twig');
  }
}
