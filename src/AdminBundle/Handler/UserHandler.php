<?php
namespace AdminBundle\Handler;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\AbstractRepository;
use AppBundle\Entity\UserRepository;
use AppBundle\Api\Response;
use AppBundle\Entity\User;

class UserHandler extends AbstractHandler
{
  /**
   * @return AbstractRepository
   */
    public function getRepository()
    {
        return $this->doctrine->getRepository("AppBundle:User");
    }

  /**
   * @param int $id
   * @return null|object
   */
    public function getEntityByID($id)
    {
        return $this->getRepository()->findByID($id);
    }

  /**
   * @return string
   */
    public function getFilterColumn()
    {
        return "username";
    }

  /**
   * @return array
   */
    public function getTableColumns()
    {
        return [
        "id"        => "ID",
        "username"  => "Username",
        "email"     => "Email",
        "lastLogin" => "Last Login"
        ];
    }

  /**
   * @return array
   */
    public function getHydrateColumns()
    {
        return ["username", "email", "enabled", "newPassword", "info"];
    }

  /**
   * @param User $entity
   * @param array $values
   * @return array
   */
    public function validate($entity, array $values)
    {
      /** @var UserRepository $repo */
        $repo = $this->getRepository();
        if ($values["username"] !== $entity->getUsername()) {
            if ($repo->findByUsername($values["username"])) {
                return ["username" => "Username taken."];
            }
        }
        if ($values["email"] !== $entity->getEmail()) {
            if ($repo->findByEmail($values["email"])) {
                return ["email" => "Email taken."];
            }
        }

        return [];
    }

  /**
   * @param User $entity
   * @param array $values
   */
    public function preHydrate($entity, array $values)
    {
        if (!empty($values["newPassword"])) {
            $entity->setPlainPassword($values["newPassword"]);
        }
        $entity->setUsernameCanonical($values["username"]);
        $entity->setEmailCanonical($values["email"]);
    }

  /**
   * @param Request $request
   * @param int $id
   * @return Response|null
   */
    public function handlePOST(Request $request, $id)
    {
      /** @var User $user */
        $repo = $this->getRepository();
        $user = $repo->findByID($id);
        if ($avatar = $request->files->get("file")) {
            $urls = $this->uploadAvatar($user, $avatar);
            return new Response($urls);
        }

        return null;
    }

  /**
   * @param User $user
   * @param UploadedFile $avatar
   * @return array
   */
    private function uploadAvatar(User $user, UploadedFile $avatar)
    {
        $thumbService  = $this->get("app.service.thumbs");
        $uploadService = $this->get("app.service.upload");
        $tempFiles     = $thumbService->create($avatar->getPathname());
        $avatarURLs    = [];

        foreach ($tempFiles as $size => $tempFile) {
            $avatarName = sprintf("avatar%s", ucwords($size));
            $uploadName = sprintf("%s/%s/%s", $user->getUsername(), date("Y-m-d"), sprintf("%s.png", $avatarName));
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
