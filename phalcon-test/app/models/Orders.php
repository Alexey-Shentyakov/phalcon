<?php

class Orders extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=32, nullable=false)
     */
    public $order_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=32, nullable=false)
     */
    public $customer_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $due_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $details;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $status;
    
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $cost;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("orders");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'orders';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orders[]|Orders|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orders|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

	/**
	 * Returns all available orders with joined customer name
	 * 
	 * @return Phalcon\Mvc\Model\Resultset\Simple
	 */
	public static function getAllWithCustomerNames()
	{
		$params = [
			"models" => ["Orders"],
			"columns" => ["Orders.order_id", "Orders.details", "Orders.status", "Orders.cost", "Customers.name AS customer_name"]
		];
		
		$queryBuilder = new \Phalcon\Mvc\Model\Query\Builder($params);
		
		$queryBuilder->join("Customers", "Orders.customer_id = Customers.customer_id");
//var_dump($queryBuilder->getPhql());exit;
		$result = $queryBuilder->getQuery()->execute();
//var_dump($result);exit;
		return $result;
	}
}
