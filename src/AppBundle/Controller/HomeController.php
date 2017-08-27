<?php
namespace AppBundle\Controller;

use AppBundle\Entity\ChatLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ValueDecorator;
use AppBundle\Form\ContactModel;
use AppBundle\Form\ContactType;

class HomeController extends Controller
{
  /**
   * @Route("/", name="homepage")
   */
  public function indexAction()
  {
    if ($this->isDev()) {
      return $this->homepageAction();
    } else {
      return $this->render("AppBundle:home:index.html.twig", [
        "hide_navbar" => true
      ]);
    }
  }

  /**
   * @Route("/homepage", name="homepage_dev")
   */
  public function homepageAction()
  {
    $rooms = [];
    $roomStorage = $this->get("app.storage.room");
    $repo  = $this->getDoctrine()->getRepository("AppBundle:Room");
    foreach($repo->findPublic(50) as $room) {
      $rooms[] = new ValueDecorator($room, [
        "numUsers" => $roomStorage->getRoomUserCount($room)
      ]);
    }

    return $this->render("AppBundle:home:homepage.html.twig", [
      "rooms" => $rooms
    ]);
  }

  /**
   * @Route("/contact", name="contact")
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function contactAction(Request $request)
  {
    $model = new ContactModel();
    $form  = $this->createForm(
      ContactType::class,
      $model
    );

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid() && $model->getNonce() === "bot-check") {
      $text = $model->getMessage();
      $ua   = $request->headers->get("user-agent");
      $ip   = $request->getClientIp();
      $text = "{$text}\r\n\r\nUser-Agent: {$ua}\r\nIP: {$ip}";

      $message = \Swift_Message::newInstance()
        ->setSubject("[UpNext] Contact Form Submitted")
        ->setReplyTo($model->getEmail())
        ->setFrom($this->getParameter("mailer_user"))
        ->setTo("sean@upnext.fm")
        ->setBody($text);
      $this->get("mailer")->send($message);

      return $this->render("AppBundle:home:contact_success.html.twig");
    }

    return $this->render("AppBundle:home:contact.html.twig", [
      "form" => $form->createView()
    ]);
  }

  /**
   * @Route("/about", name="about")
   */
  public function aboutAction()
  {
    $userRepo = $this->getDoctrine()->getRepository("AppBundle:User");
    $logRepo  = $this->getDoctrine()->getRepository("AppBundle:ChatLog");
    $headzoo  = $userRepo->findByUsername("headzoo");
    $az4521   = $userRepo->findByUsername("az4521");

    $founding = [];
    $chatLogs = [];
    $ignored  = ["TriviaBot", "PieNudesBot"];
    foreach($userRepo->findFoundingMembers() as $user) {
      $username = $user->getUsername();
      $logs     = $logRepo->findRecentByUser($user, 100);
      if ($logs && !in_array($username, $ignored)) {
        $founding[] = $user;
        $rand = rand(0, count($logs) - 1);
        $chatLogs[$username] = $this->parseLog($logs[$rand]);
      }
    }

    return $this->render("AppBundle:home:about.html.twig", [
      "headzoo"  => $headzoo,
      "az4521"   => $az4521,
      "founding" => $founding,
      "chatLogs" => $chatLogs
    ]);
  }

  /**
   * @Route("/help", name="help")
   */
  public function helpAction()
  {
    return $this->render("AppBundle:home:help.html.twig");
  }

  /**
   * @Route("/ayy", name="ayy")
   */
  public function ayyAction()
  {
    return $this->render("AppBundle:home:ayy.html.twig");
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
