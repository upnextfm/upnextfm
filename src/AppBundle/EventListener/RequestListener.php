<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Api\EventListener;

/**
 * Fills the request parameter bag for JSON requests.
 */
class RequestListener extends EventListener
{
  /**
   * @param GetResponseEvent $event
   */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->headers->get("Content-Type") === "application/json" && $this->isDecodeable($request)) {
            $content = $request->getContent();
            $data    = $this->serializer->decode($content, "json");
            $request->request = new ParameterBag($data);
        }
    }

  /**
   * Check if we should try to decode the body.
   *
   * @param Request $request
   *
   * @return bool
   */
    protected function isDecodeable(Request $request)
    {
        if (!in_array($request->getMethod(), ["POST", "PUT", "PATCH", "DELETE"])) {
            return false;
        }
        return true;
    }
}
