<?php
namespace AdminBundle\Handler;

use AppBundle\Api\Response;
use AppBundle\Entity\AbstractRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface extends ContainerAwareInterface
{
  /**
   * @param Request $request
   * @param int $id
   * @return Response|null
   */
    public function handleGET(Request $request, $id);

  /**
   * @param Request $request
   * @param int $id
   * @return Response|null
   */
    public function handlePOST(Request $request, $id);

  /**
   * @return AbstractRepository
   */
    public function getRepository();

  /**
   * @param int $id
   * @return null|object
   */
    public function getEntityByID($id);

  /**
   * @return string
   */
    public function getFilterColumn();

  /**
   * @return array
   */
    public function getTableColumns();

  /**
   * @return array
   */
    public function getHydrateColumns();

  /**
   * @param object $entity
   * @param array $values
   * @return array|bool
   */
    public function validate($entity, array $values);

  /**
   * @param object $entity
   * @param array $values
   */
    public function preHydrate($entity, array $values);
}
