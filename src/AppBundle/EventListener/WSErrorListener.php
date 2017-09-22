<?php
namespace AppBundle\EventListener;

use Gos\Bundle\WebSocketBundle\Event\ClientErrorEvent;

class WSErrorListener
{
  /**
   * Called whenever a client errors
   *
   * @param ClientErrorEvent $event
   */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e = $event->getException();

        echo "connection error occurred: " . $e->getMessage() . PHP_EOL;
    }
}
