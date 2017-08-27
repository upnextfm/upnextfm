<?php
namespace AppBundle\Controller;

use AppBundle\Entity\ChatLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{
  /**
   * @Route("/chat", name="chat")
   */
  public function indexAction()
  {
    return $this->redirectToRoute("chat_logs");
  }

  /**
   * @Route("/chat/logs/{room}", name="chat_logs", defaults={"room" = "lobby"})
   *
   * @param Request $request
   * @param string $room
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function logsAction(Request $request, $room)
  {
    $searchTerm = $request->query->get("q");
    $repo = $this->getDoctrine()->getRepository("AppBundle:ChatLog");
    $logs = $repo->findRecent($this->findRoom($room), 200);
    foreach($logs as $log) {
      $this->parseLog($log);
    }

    return $this->render("AppBundle:chat:logs.html.twig", [
      "logs"       => array_reverse($logs),
      "searchTerm" => $searchTerm
    ]);
  }

  private function parseLog(ChatLog $log)
  {
    $message = preg_replace_callback('/\\[#([a-fA-F0-9]{6})\\]/', function($m) {
      return sprintf('<span style="color: #%s;">', $m[1]);
    }, $log->getMessage());
    $message = str_replace('[/#]', '</span>', $message);
    $log->setMessage($message);

    return $log;
  }
}
