<?php
namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AdminBundle\Handler\HandlerInterface;
use AdminBundle\UI\TableResponse;
use AppBundle\Api\Response;

class EntityController extends Controller
{
  /**
   * @var int
   */
    static $limit = 25;

  /**
   * @Route("/entity/{entityName}/collection/{page}", name="admin_entity_collection", defaults={"page"=1})
   *
   * @param Request $request
   * @param string $entityName
   * @param int $page
   * @return TableResponse
   */
    public function getCollectionAction(Request $request, $entityName, $page = 1)
    {
        $rest = $this->handler($entityName);

        $filters = [];
        $filter  = $request->query->get("filter");
        if ($filter) {
            $filters[$rest->getFilterColumn()] = $filter;
        }

        $paginator = $rest->getRepository()
        ->findAllByPage($page, self::$limit, $filters);

        $table = new TableResponse($rest->getTableColumns(), $paginator->getIterator());
        $table->setCurrentPage($page);
        $table->setNumPages(ceil($paginator->count() / self::$limit));
        $table->setFilter($filter);

        return $table;
    }

  /**
   * @Route("/entity/{entityName}/{id}", name="admin_entity_get", methods={"GET"})
   *
   * @param Request $request
   * @param string $entityName
   * @param int $id
   * @return Response
   */
    public function getAction(Request $request, $entityName, $id)
    {
        $rest     = $this->handler($entityName);
        $response = $rest->handleGET($request, $id);
        if ($response instanceof Response) {
            return $response;
        }

        $entity = $rest->getRepository()
        ->findByID($id);

        return new Response($entity);
    }

  /**
   * @Route("/entity/{entityName}/{id}", name="admin_entity_post", methods={"POST"})
   *
   * @param Request $request
   * @param string $entityName
   * @param int $id
   * @return Response
   */
    public function postAction(Request $request, $entityName, $id)
    {
        $rest   = $this->handler($entityName);
        $response = $rest->handlePOST($request, $id);
        if ($response instanceof Response) {
            return $response;
        }

        $repo   = $rest->getRepository();
        $keep   = array_flip($rest->getHydrateColumns());
        $values = array_intersect_key($request->request->all(), $keep);
        $entity = $rest->getEntityByID($id);

        $validationErrors = $rest->validate($entity, $values);
        if (!empty($validationErrors)) {
            return new Response(["validationErrors" => $validationErrors], 400);
        }

        $rest->preHydrate($entity, $values);
        $repo->hydrateFromArray($entity, $values);
        $this->getDoctrine()->getManager()->flush();

        return new Response($entity);
    }

  /**
   * @param string $entityName
   * @return HandlerInterface
   */
    protected function handler($entityName)
    {
        return $this->container->get("admin.handler.${entityName}");
    }
}
