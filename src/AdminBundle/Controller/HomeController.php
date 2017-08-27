<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
  /**
   * @Route("/", name="admin_homepage")
   */
    public function indexAction()
    {
        return $this->render('AdminBundle:home:index.html.twig');
    }
}
