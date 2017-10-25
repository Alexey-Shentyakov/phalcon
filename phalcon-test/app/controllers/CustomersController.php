<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class CustomersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for customers
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Customers', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "customer_id";

        $customers = Customers::find($parameters);
        if (count($customers) == 0) {
            $this->flash->notice("The search did not find any customers");

            $this->dispatcher->forward([
                "controller" => "customers",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $customers,
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
     * Edits a customer
     *
     * @param string $customer_id
     */
    public function editAction($customer_id)
    {
        if (!$this->request->isPost()) {

            $customer = Customers::findFirstBycustomer_id($customer_id);
            if (!$customer) {
                $this->flash->error("customer was not found");

                $this->dispatcher->forward([
                    'controller' => "customers",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->customer_id = $customer->customer_id;

            $this->tag->setDefault("customer_id", $customer->customer_id);
            $this->tag->setDefault("name", $customer->name);
            $this->tag->setDefault("phone_number", $customer->phone_number);
            
            $current_balance = preg_replace('/\$/', '', $customer->balance);
            $this->tag->setDefault("balance", $current_balance);
            
        }
    }

    /**
     * Creates a new customer
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'index'
            ]);

            return;
        }

        $customer = new Customers();
        $customer->name = $this->request->getPost("name");
        $customer->phone_number = $this->request->getPost("phone_number");
        $customer->balance = $this->request->getPost("balance");
        

        if (!$customer->save()) {
            foreach ($customer->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("customer was created successfully");

        $this->dispatcher->forward([
            'controller' => "customers",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a customer edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'index'
            ]);

            return;
        }

        $customer_id = $this->request->getPost("customer_id");
        $customer = Customers::findFirstBycustomer_id($customer_id);

        if (!$customer) {
            $this->flash->error("customer does not exist " . $customer_id);

            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'index'
            ]);

            return;
        }

        $customer->name = $this->request->getPost("name");
        $customer->phone_number = $this->request->getPost("phone_number");
        $customer->balance = $this->request->getPost("balance");
        

        if (!$customer->save()) {

            foreach ($customer->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'edit',
                'params' => [$customer->customer_id]
            ]);

            return;
        }

        $this->flash->success("customer was updated successfully");

        $this->dispatcher->forward([
            'controller' => "customers",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a customer
     *
     * @param string $customer_id
     */
    public function deleteAction($customer_id)
    {
        $customer = Customers::findFirstBycustomer_id($customer_id);
        if (!$customer) {
            $this->flash->error("customer was not found");

            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'index'
            ]);

            return;
        }

        if (!$customer->delete()) {

            foreach ($customer->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "customers",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("customer was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "customers",
            'action' => "index"
        ]);
    }
}
