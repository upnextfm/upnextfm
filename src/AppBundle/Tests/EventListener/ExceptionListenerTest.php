<?php
namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\ExceptionListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @coversDefaultClass AppBundle\EventListener\ExceptionListener
 */
class ExceptionListenerTest extends KernelTestCase
{
  /**
   * @var EventDispatcher
   */
  private $dispatcher;

  /**
   * Called before each test
   */
  protected function setUp()
  {
    self::bootKernel();
    $container = self::$kernel->getContainer();
    $this->dispatcher = new EventDispatcher();
    $listener = new ExceptionListener($container->get("serializer_json"));
    $this->dispatcher->addListener(KernelEvents::EXCEPTION, array($listener, 'onKernelException'));
  }

  /**
   * Called after each test
   */
  protected function tearDown()
  {
    $this->dispatcher = null;
  }

  /**
   * @covers ::onKernelException
   */
  public function testDoesNotChangeResponse()
  {
    $event = new GetResponseForExceptionEvent(
      self::$kernel,
      new Request(),
      HttpKernelInterface::MASTER_REQUEST,
      new \Exception("Testing")
    );
    $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);
    $this->assertNull($event->getResponse());
  }

  /**
   * @covers ::onKernelException
   */
  public function testChangesResponse()
  {
    $request = new Request();
    $request->headers->set("Accept", "application/json");
    $event = new GetResponseForExceptionEvent(
      self::$kernel,
      $request,
      HttpKernelInterface::MASTER_REQUEST,
      new \Exception("Testing")
    );
    $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);
    $this->assertEquals("application/json", $event->getResponse()->headers->get("content-type"));
  }
}
