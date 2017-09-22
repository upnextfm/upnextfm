<?php
namespace AppBundle\Tests\EventListener;

use AppBundle\EventListener\ControllerResponseListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @coversDefaultClass AppBundle\EventListener\ResponseListener
 */
class ControllerResponseListenerTest extends KernelTestCase
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
        $listener = new ControllerResponseListener($container->get("serializer_json"));
        $this->dispatcher->addListener(KernelEvents::VIEW, array($listener, 'onKernelView'));
    }

  /**
   * Called after each test
   */
    protected function tearDown()
    {
        $this->dispatcher = null;
    }

  /**
   * @covers ::onKernelView
   */
    public function testDoesNotChangeResponse()
    {
        $request = new Request();
        $controllerResult = new Response();
        $event    = $this->getGetResponseForControllerResultEvent($request, $controllerResult);
        $this->dispatcher->dispatch(KernelEvents::VIEW, $event);
        $this->assertNull($event->getResponse());
    }

  /**
   * @covers ::onKernelView
   */
    public function testChangesResponse()
    {
        $request  = new Request();
        $request->headers->set("Accept", "application/json");
        $controllerResult = ["foo" => "bar"];
        $event = $this->getGetResponseForControllerResultEvent($request, $controllerResult);
        $this->dispatcher->dispatch(KernelEvents::VIEW, $event);
        $response = $event->getResponse();
        $this->assertEquals("application/json", $response->headers->get("content-type"));
        $this->assertEquals(json_encode($controllerResult), $response->getContent());
    }

  /**
   * @param Request $request
   * @param mixed $controllerResult
   * @return GetResponseForControllerResultEvent
   */
    protected function getGetResponseForControllerResultEvent(Request $request, $controllerResult)
    {
        $event = new GetResponseForControllerResultEvent(
            self::$kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        return $event;
    }
}
