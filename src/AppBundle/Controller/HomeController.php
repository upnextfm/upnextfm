<?php
namespace AppBundle\Controller;

use AppBundle\Entity\ValueDecorator;
use AppBundle\Form\ContactModel;
use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
  /**
   * @Route("/", name="homepage")
   */
  public function indexAction()
  {
    return $this->render(":home:index.html.twig", [
      "hide_navbar" => true
    ]);
  }

  /**
   * @Route("/homepage", name="homepage_dev")
   */
  public function homepageAction()
  {
    $rooms = [];
    $roomStorage = $this->get("app.storage.room");
    $repo  = $this->getDoctrine()->getRepository("AppBundle:Room");
    foreach($repo->findAll() as $room) {
      $rooms[] = new ValueDecorator($room, [
        "numUsers" => $roomStorage->getRoomUserCount($room)
      ]);
    }

    return $this->render(":home:homepage.html.twig", [
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

      return $this->render(":home:contact_success.html.twig");
    }

    return $this->render(":home:contact.html.twig", [
      "form" => $form->createView()
    ]);
  }

  /**
   * @Route("/about", name="about")
   */
  public function aboutAction()
  {
    $userRepo = $this->getDoctrine()->getRepository("AppBundle:User");
    $headzoo  = $userRepo->findByUsername("headzoo");
    $az4521   = $userRepo->findByUsername("az4521");
    $founding = $userRepo->findFoundingMembers();

    return $this->render(":home:about.html.twig", [
      "headzoo"  => $headzoo,
      "az4521"   => $az4521,
      "founding" => $founding
    ]);
  }

  /**
   * @Route("/help", name="help")
   */
  public function helpAction()
  {
    return $this->render(":home:help.html.twig");
  }

  /**
   * @Route("/ayy", name="ayy")
   */
  public function ayyAction()
  {
    return $this->render(":home:ayy.html.twig");
  }
}
