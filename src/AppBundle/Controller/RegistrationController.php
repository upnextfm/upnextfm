<?php
namespace AppBundle\Controller;

use AppBundle\Entity\UserInfo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Api\Response;
use AppBundle\Entity\User;

class RegistrationController extends Controller
{
  /**
   * @Route("/api/register", name="register", methods={"POST"})
   *
   * @param Request $request
   * @return Response
   */
  public function registerAction(Request $request)
  {
    $username = $request->request->get("username");
    $password = $request->request->get("password");
    $email    = $request->request->get("email");
    if (empty($username) || empty($email) || empty($password)) {
      return new Response(["error" => "Missing field."], 400);
    }

    $em   = $this->getDoctrine()->getManager();
    $repo = $em->getRepository("AppBundle:User");
    if ($repo->findByUsername($username)) {
      return new Response(["error" => "Username taken."], 401);
    }
    if ($repo->findByEmail($email)) {
      return new Response(["error" => "Email already in use."], 401);
    }

    $user    = new User();
    $factory = $this->get("security.encoder_factory");
    $encoder = $factory->getEncoder($user);
    $user->setUsername($username);
    $user->setUsernameCanonical($username);
    $user->setEmail($email);
    $user->setEmailCanonical($email);
    $user->setEnabled(true);
    $user->setLastLogin(new \DateTime());
    $user->setPassword($encoder->encodePassword($password, null));

    $info = new UserInfo();
    $info->setUser($user);
    $info->setAvatarSm(sprintf('https://robohash.org/%s?set=set3&size=40x40', $username));
    $info->setAvatarMd(sprintf('https://robohash.org/%s?set=set3&size=100x100', $username));
    $info->setAvatarLg(sprintf('https://robohash.org/%s?set=set3&size=250x250', $username));
    $user->setInfo($info);

    $em->persist($user);
    $em->flush();

    $handler = $this->get('lexik_jwt_authentication.handler.authentication_success');
    return $handler->handleAuthenticationSuccess($user);
  }
}
