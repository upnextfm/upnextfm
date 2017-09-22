<?php
namespace AdminBundle\Handler;

use AppBundle\Api\Response;
use AppBundle\Entity\AbstractRepository;
use AppBundle\Entity\Room;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class RoomHandler extends AbstractHandler
{

  /**
   * @return AbstractRepository
   */
    public function getRepository()
    {
        return $this->doctrine->getRepository("AppBundle:Room");
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
        return "name";
    }

  /**
   * @return array
   */
    public function getTableColumns()
    {
        return [
        "id"            => "ID",
        "name"          => "Name",
        "displayName"   => "Display Name",
        // "createdByUser.username" => "Created By",
        "isPrivate"     => "Private",
        "isDeleted"     => "Deleted",
        "dateCreated"   => "Date Created"
        ];
    }

  /**
   * @return array
   */
    public function getHydrateColumns()
    {
        return ["name", "displayName", "isPrivate", "isDeleted", "description", "settings"];
    }

  /**
   * @param Request $request
   * @param int $id
   * @return Response|null
   */
    public function handlePOST(Request $request, $id)
    {
      /** @var Room $room */
        $repo = $this->getRepository();
        $room = $repo->findByID($id);
        if ($thumb = $request->files->get("file")) {
            $urls = $this->uploadThumb($room, $thumb);
            return new Response($urls);
        }

        return null;
    }

  /**
   * @param Room $room
   * @param UploadedFile $thumb
   * @return array
   */
    private function uploadThumb(Room $room, UploadedFile $thumb)
    {
        $thumbService  = $this->get("app.service.thumbs");
        $uploadService = $this->get("app.service.upload");
        $tempFiles     = $thumbService->create($thumb->getPathname());
        $thumbURLs     = [];

        foreach ($tempFiles as $size => $tempFile) {
            $thumbName  = sprintf("thumb%s", ucwords($size));
            $uploadName = sprintf("rooms/%s/%s-%s", $room->getName(), date("Y-m-d"), sprintf("%s.png", $thumbName));
            $thumbURLs[$thumbName] = $uploadService->upload(
                $tempFile,
                $uploadName,
                $room->getCreatedByUser(),
                "image/png"
            );
        }

        return $thumbURLs;
    }
}
