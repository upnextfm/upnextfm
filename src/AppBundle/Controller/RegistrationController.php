<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{
  /**
   * @Route("/api/register", name="register")
   *
   * @return Response
   */
  public function registerAction()
  {
    $data = $this->getJsonRequest();
    if (empty($data["username"]) || empty($data["email"]) || empty($data["password"])) {
      return $this->createJsonResponse(["error" => "Missing field."], 400);
    }

    $em   = $this->getDoctrine()->getManager();
    $repo = $em->getRepository("AppBundle:User");
    $user = $repo->findByUsername($data["username"]);
    if ($user) {
      return $this->createJsonResponse(["error" => "Username taken."], 401);
    }
    $user = $repo->findByEmail($data["email"]);
    if ($user) {
      return $this->createJsonResponse(["error" => "Email already in use."], 401);
    }

    $user     = new User();
    $factory  = $this->get("security.encoder_factory");
    $encoder  = $factory->getEncoder($user);
    $password = $encoder->encodePassword($data["password"], null);

    $user->setUsername($data["username"]);
    $user->setUsernameCanonical($data["username"]);
    $user->setEmail($data["email"]);
    $user->setEmailCanonical($data["email"]);
    $user->setEnabled(true);
    $user->setLastLogin(new \DateTime());
    $user->setPassword($password);

    $em->persist($user);
    $em->flush();

    $authenticationSuccessHandler = $this->get('lexik_jwt_authentication.handler.authentication_success');
    return $authenticationSuccessHandler->handleAuthenticationSuccess($user);
  }
}
