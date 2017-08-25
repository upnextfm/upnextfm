<?php
namespace AppBundle\Controller;

class Controller
    extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
  /**
   * @param string $roomName
   * @return \AppBundle\Entity\Room
   */
  public function findRoom($roomName)
  {
    $room = $this->getDoctrine()->getRepository("AppBundle:Room")->findByName($roomName);
    if (!$room) {
      throw $this->createNotFoundException();
    }

    return $room;
  }
}
