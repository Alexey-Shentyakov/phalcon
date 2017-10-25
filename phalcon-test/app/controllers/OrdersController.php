<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class OrdersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for orders
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Orders', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "order_id";

        $orders = Orders::find($parameters);
        if (count($orders) == 0) {
            $this->flash->notice("The search did not find any orders");

            $this->dispatcher->forward([
                "controller" => "orders",
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

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a order
     *
     * @param string $order_id
     */
    public function editAction($order_id)
    {
        if (!$this->request->isPost()) {

            $order = Orders::findFirstByorder_id($order_id);
            if (!$order) {
                $this->flash->error("order was not found");

                $this->dispatcher->forward([
                    'controller' => "orders",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->order_id = $order->order_id;

            $this->tag->setDefault("order_id", $order->order_id);
            $this->tag->setDefault("customer_id", $order->customer_id);
            $this->tag->setDefault("due_date", $order->due_date);
            $this->tag->setDefault("details", $order->details);
            $this->tag->setDefault("status", $order->status);
            
            $cost = preg_replace('/\$/', '', $order->cost);
            $this->tag->setDefault("cost", $cost);
        }
    }

    /**
     * Creates a new order
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        $order = new Orders();
        $order->customer_id = $this->request->getPost("customer_id");
        $order->due_date = $this->request->getPost("due_date");
        $order->details = $this->request->getPost("details");
        $order->status = $this->request->getPost("status");
        $order->cost = $this->request->getPost("cost");

        if (!$order->save()) {
            foreach ($order->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("order was created successfully");

        $this->dispatcher->forward([
            'controller' => "orders",
            'action' => 'index'
        ]);
    }

    /**
     * Saves an order edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        $order_id = $this->request->getPost("order_id");
        $order = Orders::findFirstByorder_id($order_id);

        if (!$order) {
            $this->flash->error("order does not exist " . $order_id);

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        $order->customer_id = $this->request->getPost("customer_id");
        $order->due_date = $this->request->getPost("due_date");
        $order->details = $this->request->getPost("details");
        $order->status = $this->request->getPost("status");
        $order->cost = $this->request->getPost("cost");

        if (!$order->save()) {

            foreach ($order->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'edit',
                'params' => [$order->order_id]
            ]);

            return;
        }

        $this->flash->success("order was updated successfully");

        $this->dispatcher->forward([
            'controller' => "orders",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes an order
     *
     * @param string $order_id
     */
    public function deleteAction($order_id)
    {
        $order = Orders::findFirstByorder_id($order_id);
        if (!$order) {
            $this->flash->error("order was not found");

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        if (!$order->delete()) {

            foreach ($order->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("order was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "orders",
            'action' => "index"
        ]);
    }

}
