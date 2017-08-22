<?php
namespace AppBundle\Controller;

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
    $repo  = $this->getDoctrine()->getRepository("AppBundle:Room");
    $rooms = $repo->findAll();

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

  }

  /**
   * @Route("/help", name="help")
   */
  public function helpAction()
  {

  }
}
