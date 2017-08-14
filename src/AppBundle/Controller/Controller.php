<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class Controller
    extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
  /**
   * Creates and returns a new Response which contains a json body
   *
   * The Content-Type header is automatically set to "application/json".
   *
   * @param mixed $data    The data to json encode
   * @param int   $status  The http status code
   * @param array $headers Headers to send with the response
   *
   * @return Response
   */
  public function createJsonResponse($data, $status = 200, array $headers = [])
  {
    $headers["Content-Type"][] = "application/json";
    $json = $this->get("serializer_json")->serialize($data, "json");

    return new Response($json, $status, $headers);
  }

  /**
   * Decodes the request body as a JSON array
   *
   * @return array
   */
  public function getJsonRequest()
  {
    $request = $this->get("request_stack")->getCurrentRequest();
    if (!$request->isMethod("POST")) {
      return [];
    }
    if ($request->headers->get("Content-Type") !== "application/json") {
      return [];
    }

    return $this->get("serializer_json")->decode($request->getContent(), "json");
  }
}
