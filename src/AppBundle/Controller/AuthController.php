<?php
namespace AppBundle\Controller;

use AppBundle\Form\LoginModel;
use AppBundle\Form\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthController extends Controller
{
  /**
   * @Route("/login", name="login")
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction(Request $request)
  {
    $model = new LoginModel();
    $form  = $this->createForm(LoginType::class, $model);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em   = $this->getDoctrine()->getManager();
      $user = $em->getRepository("AppBundle:User")->findByUsername($model->getUsername());
      if(!$user){
        return new Response(
          "Username doesn't exists",
          Response::HTTP_UNAUTHORIZED,
          ['Content-type' => 'application/json']
        );
      }

      $factory = $this->get('security.encoder_factory');
      $encoder = $factory->getEncoder($user);
      $salt    = $user->getSalt();
      if(!$encoder->isPasswordValid($user->getPassword(), $model->getPassword(), $salt)) {
        return new Response(
          "Username or Password not valid.",
          Response::HTTP_UNAUTHORIZED,
          ["Content-type" => "application/json"]
        );
      }

      $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
      $this->get("security.token_storage")->setToken($token);
      $this->get("session")->set("_security_main", serialize($token));
      $event = new InteractiveLoginEvent($request, $token);
      $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

      return $this->redirect($this->generateUrl("homepage"));
    }

    return $this->render(":auth:login.html.twig", [
      "form" => $form->createView()
    ]);
  }
}
