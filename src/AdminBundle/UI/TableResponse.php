<?php
namespace AdminBundle\UI;

use AppBundle\Api\Response;
use ArrayIterator;

class TableResponse extends Response
{
  /**
   * @var array
   */
  protected $columns = [];

  /**
   * @var ArrayIterator|array
   */
  protected $rows = [];

  /**
   * @var string
   */
  protected $filter = '';

  /**
   * @var int
   */
  protected $numPages = 1;

  /**
   * @var int
   */
  protected $currentPage = 1;

  /**
   * @var array
   */
  protected $orderables = ['id'];

  /**
   * @var array
   */
  protected $currentOrder = ['id' => 'desc'];

  /**
   * @param array $columns
   * @param ArrayIterator|array $rows
   * @param int $statusCode
   * @param array $headers
   */
  public function __construct($columns, $rows, $statusCode = 200, $headers = [])
  {
    parent::__construct('', $statusCode, $headers);
    $this->setColumns($columns);
    $this->setRows($rows);
  }

  /**
   * @return array
   */
  public function getColumns()
  {
    return $this->columns;
  }

  /**
   * @param array $columns
   * @return TableResponse
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
    return $this;
  }

  /**
   * @return array
   */
  public function getRows()
  {
    return $this->rows;
  }

  /**
   * @param array $rows
   * @return TableResponse
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
    return $this;
  }

  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }

  /**
   * @param string $filter
   * @return TableResponse
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
    return $this;
  }

  /**
   * @return int
   */
  public function getNumPages()
  {
    return $this->numPages;
  }

  /**
   * @param int $numPages
   * @return TableResponse
   */
  public function setNumPages($numPages)
  {
    $this->numPages = $numPages;
    return $this;
  }

  /**
   * @return int
   */
  public function getCurrentPage()
  {
    return $this->currentPage;
  }

  /**
   * @param int $currentPage
   * @return TableResponse
   */
  public function setCurrentPage($currentPage)
  {
    $this->currentPage = $currentPage;
    return $this;
  }

  /**
   * @return array
   */
  public function getOrderables()
  {
    return $this->orderables;
  }

  /**
   * @param array $orderables
   * @return TableResponse
   */
  public function setOrderables($orderables)
  {
    $this->orderables = $orderables;
    return $this;
  }

  /**
   * @return array
   */
  public function getCurrentOrder()
  {
    return $this->currentOrder;
  }

  /**
   * @param array $currentOrder
   * @return TableResponse
   */
  public function setCurrentOrder($currentOrder)
  {
    $this->currentOrder = $currentOrder;
    return $this;
  }

  /**
   * @param array $rows
   * @return TableResponse
   */
  public function setData($rows)
  {
    return $this->setRows($rows);
  }

  /**
   * @return array
   */
  public function getData()
  {
    return [
      'filter'        => $this->filter,
      'numPages'      => $this->numPages,
      'currentPage'   => $this->currentPage,
      'orderables'    => $this->orderables,
      'currentOrder'  => $this->currentOrder,
      'columns'       => $this->columns,
      'rows'          => (array)$this->rows
    ];
  }
}
