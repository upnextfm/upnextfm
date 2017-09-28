<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends Controller
{
  /**
   * @Route("/u/{username}", name="profile")
   *
   * @param string $username
   * @return Response
   */
  public function indexAction($username)
  {
    $em = $this->getDoctrine()->getManager();
    $user = $this->findUserOrThrow($username);
    $events = $em->getRepository("AppBundle:UserEvent")
      ->findByUser($user, 25);

    return $this->render("AppBundle:user:index.html.twig", [
      "user"   => $user,
      "events" => $events
    ]);
  }

  /**
   * @Route("/u/{username}/favorites/{page}", name="favorites", defaults={"page" = 1})
   *
   * @param string $username
   * @param int $page
   * @return Response
   */
  public function favoritesAction($username, $page = 1)
  {
    $user = $this->findUserOrThrow($username);

    $limit = 30;
    $offset = ($page - 1) * 30;

    $em = $this->getDoctrine()->getManager();
    $repo = $em->getRepository("AppBundle:Favorite");
    $favorites = $repo->findByUser($user, $limit, $offset);
    $favoritesCount = $repo->countByUser($user);

    $pages = ceil($favoritesCount / $limit);
    $minPage = $page - 4;
    $maxPage = $page + 4;
    if ($minPage < 1) {
      $minPage = 1;
    }
    if ($maxPage > $pages) {
      $maxPage = $pages;
    }

    return $this->render("AppBundle:user:favorites.html.twig", [
      "user"           => $user,
      "favorites"      => $favorites,
      "favoritesCount" => $favoritesCount,
      "currentPage"    => $page,
      "minPage"        => $minPage,
      "maxPage"        => $maxPage
    ]);
  }

  /**
   * @Route("/account", name="account")
   *
   * @param Request $request
   * @return Response
   */
  public function accountAction(Request $request)
  {
    /** @var User $user */
    $user = $this->getUser();
    $info = $user->getInfo();
    if (!($user instanceof UserInterface)) {
      throw $this->createNotFoundException();
    }

    // Updates to user account information - in order of appearance  
    if ($request->getMethod() === "POST") {
      $values = $request->request->all();

      // Password
      if (!empty($values["password"])) {
        $user->setPlainPassword($values["password"]);
        $this->get("fos_user.user_manager")->updatePassword($user);
      }

      // Email 
      $user->setEmail($values["email"]);
      $user->setEmailCanonical($values["email"]);

      // Location 
      $info->setLocation($values["location"]);

      // Website 
      $info->setWebsite($values["website"]);

      // Bio Profile 
      $info->setBio($values["bio"]);
      
      // Avatar 
      if ($avatar = $request->files->get("avatar")) {
        $urls = $this->processAvatar($avatar, $user);
        $info->setAvatarSm($urls["avatarSm"]);
        $info->setAvatarMd($urls["avatarMd"]);
        $info->setAvatarLg($urls["avatarLg"]);
      }

      $this->getDoctrine()->getEntityManager()->flush();
      $this->addFlash("success", "Account updated.");
    } /* End Request Post */

    return $this->render("AppBundle:user:account.html.twig", ['user' => $user ]);
  }

  /**
   * @param string $username
   * @return \AppBundle\Entity\User
   */
  protected function findUserOrThrow($username)
  {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository("AppBundle:User")->findByUsername($username);
    if (!$user) {
      throw $this->createNotFoundException();
    }

    return $user;
  }

  /**
   * @param UploadedFile $avatar
   * @param User $user
   * @return array
   */
  protected function processAvatar(UploadedFile $avatar, User $user)
  {
    $thumbService  = $this->get("app.service.thumbs");
    $uploadService = $this->get("app.service.upload");
    $tempFiles     = $thumbService->create($avatar->getPathname());
    $avatarURLs    = [];

    foreach ($tempFiles as $size => $tempFile) {
      $avatarName = sprintf("avatar%s", ucwords($size));
      $uploadName = sprintf("%s/%s/%s", $user->getUsername(), date("Y-m-d"), sprintf("%s-%d.png", $avatarName, rand(0, 1000)));
      $avatarURLs[$avatarName] = $uploadService->upload(
        $tempFile,
        $uploadName,
        $user,
        "image/png"
      );
    }

    return $avatarURLs;
  }
}
