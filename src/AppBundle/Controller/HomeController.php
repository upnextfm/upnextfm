<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render(":home:index.html.twig", [
          "hide_navbar" => true
        ]);
    }

  /**
   * @Route("/about", name="about")
   */
    public function aboutAction()
    {

    }

  /**
   * @Route("/help", name="help")
   */
    public function helpAction()
    {

    }
}
