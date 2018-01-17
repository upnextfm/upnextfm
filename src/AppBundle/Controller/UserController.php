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
   * @Route("/u/{username}/favorites/{page}", name="favorites", defaults={"page" = 1} )
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
   * @Route("/u/{username}/{page}", name="profile", defaults={"page"= 1})
   *
   * @param Request $request
   * @param string $username
   * @param int $page
   * @return Response
   */
  public function indexAction(Request $request, $username, $page = 1)
  {
    $user = $this->findUserOrThrow($username);
    $limit = 25; // Limit of number of records retrieved
    $offset = ($page - 1) * 25; // Calculation for the range of records retrieved

    $em = $this->getDoctrine()->getManager();
    $repo = $em->getRepository("AppBundle:UserEvent");
    $events = $repo->findByUser($user, $limit, $offset); // retrieves users recently played videos
    $eventsCount = sizeOf($repo->findAllByUser($user));

    $currentPage = $page;
    $pages = ceil($eventsCount / $limit); // Number of pages to render in pagination

    // Sets minimum and maximum page
    $minPage = $page - 3;
    $maxPage = $page + 3;

    // Prevents pages going under 1
    if ($minPage < 1) {
      $minPage = 1;
    }

    if ($maxPage > $pages) {
      $maxPage = $pages;
    }
    // dump($request->isXmlHttpRequest());
    // die();
    if ($request->isXmlHttpRequest()) {
      return $this->render('AppBundle:user:index_list.html.twig', [
          "user"           => $user,
          "events"         => $events,
          "currentPage"    => $page,
          "minPage"        => $minPage,
          "maxPage"        => $maxPage
      ]);
     } // End if

     // User enters profile page explicitly
    else {
      return $this->render('AppBundle:user:index.html.twig', [
        "user"           => $user,
        "events"         => $events,
        "currentPage"    => $page,
        "minPage"        => $minPage,
        "maxPage"        => $maxPage
    ]);
    }

  } // End indexAction

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

    if (!($user instanceof UserInterface)) {
      throw $this->createNotFoundException();
    }

    // Updates to user account information - in order of appearance
    if ($request->getMethod() === "POST") {
      $values = $request->request->all();
      $info = $user->getInfo(); // Retrieve UserInfo field from User entity

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

  /**
   * @Route("/users", name="users")
   *
   * @param Request $request
   * @return Response
   */
   public function userAction(Request $request)
   {
     $em = $this->getDoctrine()->getManager();
     $users = []; // For User retrieval
     $users = $em->getRepository("AppBundle:User")->findAll();
     return $this->render("AppBundle:user:users.html.twig", [
       "users" => $users
     ]);
   }

}
