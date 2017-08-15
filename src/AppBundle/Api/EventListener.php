<?php
namespace AppBundle\Api;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Serializer\Serializer;

/**
 * Parent for API related event listeners.
 */
abstract class EventListener
{
  /**
   * @var Serializer
   */
  protected $serializer;

  /**
   * @param Serializer $serializer
   */
  public function __construct(Serializer $serializer)
  {
    $this->serializer = $serializer;
  }

  /**
   * Creates and returns a new Response which contains a json body
   *
   * The Content-Type header is automatically set to "application/json".
   *
   * @param mixed $data    The data to json encode
   * @param int   $status  The http status code
   * @param array $headers Headers to send with the response
   *
   * @return HttpResponse
   */
  public function createJsonResponse($data, $status = 200, array $headers = [])
  {
    $headers["Content-Type"][] = "application/json";
    $json = $this->serializer->serialize($data, "json");

    return new HttpResponse($json, $status, $headers);
  }
}
