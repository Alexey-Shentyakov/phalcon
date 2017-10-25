<?php

class Myservice
{
	/**
	 * Calculate average order bill for the customer
	 * 
	 * @param $customer_id
	 * @return string average bill
	 * 
	 */
	public function getCustomerAverageBill($customer_id)
	{
		$parameters = ["customer_id = $customer_id"];
		$customer_orders = Orders::find($parameters);
		$n = count($customer_orders);
		
		$average = '0.00';
		
		if ($n > 0) {
			$sum = 0.0;
			
			foreach ($customer_orders as $order) {
				$cost = preg_replace('/\$/', '', $order->cost);
				$sum += $cost;
			}
			
			$average = $sum / $n;
			$average = sprintf('%.2F', $average);
		}
		
		return $average;
	}
}
