<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\LoginModel;
use AppBundle\Form\LoginType;

class AuthController extends Controller
{
  /**
   * @Route("/login", name="login")
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function loginAction(Request $request)
  {
    $model = new LoginModel();
    $form  = $this->createForm(LoginType::class, $model);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em   = $this->getDoctrine()->getManager();
      $user = $em->getRepository("AppBundle:User")
        ->findByUsername($model->getUsername());

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

      $handler = $this->get('lexik_jwt_authentication.handler.authentication_success');
      $tokens  = json_decode($handler->handleAuthenticationSuccess($user)->getContent(), true);
      $response = $this->redirectToRoute("homepage");
      $response->headers->setCookie(new Cookie("token", $tokens["token"]));

      return $response;
    }

    return $this->render(":auth:login.html.twig", [
      "form" => $form->createView()
    ]);
  }

  /**
   * @Route("/logout", name="logout")
   *
   * @param Request $request
   * @return Response
   */
  public function logoutAction(Request $request)
  {
    $this->get('security.token_storage')->setToken(null);
    $session = $request->getSession();
    $session->invalidate();

    $response = $this->redirectToRoute("login");
    $response->headers->clearCookie("token");

    return $response;
  }
}
