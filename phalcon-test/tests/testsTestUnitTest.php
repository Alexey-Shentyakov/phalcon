<?php

namespace Test;

/**
 * Class UnitTest
 */
class UnitTest extends \UnitTestCase
{
    public function testTestCase()
    {
        $my_service = $this->di->get('myserv');
        
        // -----------------------
        
        $ab = $my_service->getCustomerAverageBill(3);
        
        $this->assertEquals(
			$ab,
			"72.30",
			"average bill of customer 3"
        );
        
        // -----------------------
        
        $ab = $my_service->getCustomerAverageBill(1);
        
        $this->assertEquals(
			$ab,
			"31.19",
			"average bill of customer 1"
        );
    }
}
