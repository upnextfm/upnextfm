<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use AppBundle\Api\EventListener;

/**
 * Converts exceptions into JSON responses.
 */
class ExceptionListener extends EventListener
{
  /**
   * @param GetResponseForExceptionEvent $event
   */
  public function onKernelException(GetResponseForExceptionEvent $event)
  {
    $request = $event->getRequest();
    if ($request->headers->get("Accept") === "application/json") {
      $exception = $event->getException();
      $response = $this->createJsonResponse([
        "error" => $exception->getMessage(),
        "code"  => $exception->getCode(),
        "file"  => $exception->getFile(),
        "line"  => $exception->getLine(),
        "trace" => $exception->getTraceAsString()
      ], 500);
      $event->setResponse($response);
    }
  }
}
