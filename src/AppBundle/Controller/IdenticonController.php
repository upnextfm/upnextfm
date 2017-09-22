<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IdenticonController extends Controller
{
  /**
   * @Route("/identicon", name="identicon")
   *
   * @param Request $request
   * @return Response
   */
    public function indexAction(Request $request)
    {
        $string = $request->query->get("s", md5(time()));
        if (strlen($string) > 100) {
            $string = substr($string, 0, 100);
        }
        $size = $request->query->get("size", 250);
        if ($size > 500) {
            $size = 500;
        } elseif ($size < 0) {
            $size = 250;
        }
        $color = $request->query->get("color", null);
        if (!preg_match('/^[a-fA-F0-9]{6}$/', $color)) {
            $color = null;
        }

        return new Response(
            $this->get("app.identicon")->getImageData($string, $size, $color),
            200,
            ["Content-Type" => "image/png"]
        );
    }
}
