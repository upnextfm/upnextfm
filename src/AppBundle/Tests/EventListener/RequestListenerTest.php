<?php
namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\RequestListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @coversDefaultClass AppBundle\EventListener\RequestListener
 */
class RequestListenerTest extends KernelTestCase
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
    $listener = new RequestListener($container->get("serializer_json"));
    $this->dispatcher->addListener(KernelEvents::REQUEST, array($listener, 'onKernelRequest'));
  }

  /**
   * Called after each test
   */
  protected function tearDown()
  {
    $this->dispatcher = null;
  }

  /**
   * @covers ::onKernelRequest
   */
  public function testNotApplicationJson()
  {
    $request = new Request();
    $event   = $this->getGetResponseEventMock($request);
    $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);
    $this->assertEmpty($request->request->all());
  }

  /**
   * @covers ::onKernelRequest
   */
  public function testNotDecodeableMethod()
  {
    $request = new Request([], [], [], [], [], ["REQUEST_METHOD" => "GET"], '{"foo":"bar"}');
    $event   = $this->getGetResponseEventMock($request);
    $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);
    $this->assertEmpty($request->request->all());
  }

  /**
   * @covers ::onKernelRequest
   */
  public function testDecodeable()
  {
    $request = new Request([], [], [], [], [], ["REQUEST_METHOD" => "POST"], '{"foo":"bar"}');
    $request->headers->set("Content-Type", "application/json");
    $event   = $this->getGetResponseEventMock($request);
    $this->dispatcher->dispatch(KernelEvents::REQUEST, $event);
    $this->assertEquals("bar", $request->request->get("foo"));
  }

  /**
   * @param Request $request
   * @return \PHPUnit_Framework_MockObject_MockObject
   */
  protected function getGetResponseEventMock(Request $request)
  {
    $event = $this
      ->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
      ->disableOriginalConstructor()
      ->getMock();
    $event->expects($this->any())
      ->method('getRequest')
      ->will($this->returnValue($request));

    return $event;
  }
}
