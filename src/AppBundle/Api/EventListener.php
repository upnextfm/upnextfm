<?php
namespace AppBundle\Api;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
   * @var ObjectNormalizer
   */
  protected $normalizer;

  /**
   * @param Serializer $serializer
   * @param ObjectNormalizer $normalizer
   */
  public function __construct(Serializer $serializer, ObjectNormalizer $normalizer)
  {
    $this->serializer = $serializer;
    $this->normalizer = $normalizer;
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
    array_walk_recursive($data, function(&$data) {
      if (is_object($data)) {
        $data = $this->normalizer->normalize($data);
        array_walk($data, function(&$d) {
          if (is_array($d) && isset($d["timezone"]) && isset($d["timestamp"])) {
            $d = date(\DateTime::RFC3339, $d["timestamp"]);
          }
        });
      }
    });

    $headers["Content-Type"][] = "application/json";
    $json = $this->serializer->serialize($data, "json");

    return new HttpResponse($json, $status, $headers);
  }
}
