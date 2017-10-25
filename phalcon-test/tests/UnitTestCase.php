<?php

use Phalcon\Di;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Test\UnitTestCase as PhalconTestCase;

abstract class UnitTestCase extends PhalconTestCase
{
    /**
     * @var bool
     */
    private $_loaded = false;



    public function setUp()
    {
        parent::setUp();

        // Load any additional services that might be required during testing
        $di = Di::getDefault();
        
        define('APP_PATH', ROOT_PATH . '/../app');
		include APP_PATH . '/config/services.php';

		$di->set(
	    "modelsManager",
	    function() {
	        return new ModelsManager();
	    }
		);

        $this->setDi($di);

        $this->_loaded = true;
    }

    /**
     * Check if the test case is setup properly
     *
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct()
    {
        if (!$this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }
}
