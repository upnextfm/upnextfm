<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use AppBundle\Api\EventListener;
use AppBundle\Api\Response;

/**
 * Converts controller responses into JSON responses.
 */
class ControllerResponseListener extends EventListener
{
  /**
   * @param GetResponseForControllerResultEvent $event
   */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        if ($request->headers->get("Accept") === "application/json") {
            $resp = $event->getControllerResult();
            if ($resp instanceof Response) {
                $event->setResponse($this->createJsonResponse($resp->getData(), $resp->getStatusCode(), $resp->getHeaders()));
            } else {
                $event->setResponse($this->createJsonResponse($resp));
            }
        }
    }
}
