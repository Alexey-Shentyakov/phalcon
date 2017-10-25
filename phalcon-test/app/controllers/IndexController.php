<?php

use Phalcon\Paginator\Adapter\Model as Paginator;

class IndexController extends ControllerBase
{

    public function indexAction()
    {

    }

	/**
	 * calculate average bill for customer's orders
	 * 
	 * @param string $customer_id
	 * 
	 */
	public function averagebillAction($customer_id)
	{
		$my_service = $this->di->get('myserv');
		echo $my_service->getCustomerAverageBill($customer_id);
	}
	
	/**
	 * all orders with pagination
	 * 
	 * page - pagination page number
	 * 
	 */
	public function allordersAction()
	{
		$numberPage = $this->request->getQuery("page", "int");
		
		$orders = Orders::getAllWithCustomerNames();
		
		if (count($orders) == 0) {
			$this->flash->notice("No orders");
			
            $this->dispatcher->forward([
                "controller" => "index",
                "action" => "index"
            ]);
			
			return;
		}
		
        $paginator = new Paginator([
            'data' => $orders,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
	}
}

