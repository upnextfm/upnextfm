<?php
namespace AdminBundle\Handler;

use AppBundle\Api\Response;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHandler implements HandlerInterface
{
    use ContainerAwareTrait;

  /**
   * @var Registry
   */
    protected $doctrine;

  /**
   * @param Registry $doctrine
   * @param ContainerInterface $container
   */
    public function __construct(Registry $doctrine, ContainerInterface $container)
    {
        $this->doctrine = $doctrine;
        $this->setContainer($container);
    }

  /**
   * @param object $entity
   * @param array $values
   */
    public function preHydrate($entity, array $values)
    {
    }

  /**
   * @param Request $request
   * @param int $id
   * @return Response|null
   */
    public function handleGET(Request $request, $id)
    {
        return null;
    }

  /**
   * @param Request $request
   * @param int $id
   * @return Response|null
   */
    public function handlePOST(Request $request, $id)
    {
        return null;
    }

  /**
   * @param object $entity
   * @param array $values
   * @return array
   */
    public function validate($entity, array $values)
    {
        return [];
    }

  /**
   * @param $service
   * @return object
   */
    public function get($service)
    {
        return $this->container->get($service);
    }

  /**
   * @param $key
   * @return mixed
   */
    public function getParameter($key)
    {
        return $this->container->getParameter($key);
    }
}
