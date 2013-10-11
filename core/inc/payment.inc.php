<?php

/**
 * Represents a payment.
 */
class payment {
	
	/**
	 * @var string The PayPal TXN ID.
	 */
	private $txn_id;
	
	/**
	 * @var float The total amount received.
	 */
	private $amount;
	
	/**
	 * @var float The amount charged for shipping.
	 */
	private $shipping_amount;
	
	/**
	 * @var float The PayPal fee for processing this payment.
	 */
	private $fee;
	
	/**
	 * @var int The time the payment was made.
	 */
	private $time;
	
	public function __construct($txn_id, $amount, $shipping_amount, $fee, $time){
		$this->txn_id = $txn_id;
		$this->amount = $amount;
		$this->shipping_amount = $shipping_amount;
		$this->fee = $fee;
		$this->time = $time;
	}
	
	/**
	 * Gets the PayPal transaction ID.
	 * 
	 * @return string The TXN ID.
	 */
	public function get_txn_id(){
		return $this->txn_id;
	}
	
	/**
	 * Gets the total amount for this payment.
	 * 
	 * @return float The amount.
	 */
	public function get_amount(){
		return $this->amount;
	}
	
	/**
	 * Gets the amount charged for shipping.
	 * 
	 * @return float The amount.
	 */
	public function get_shipping_amount(){
		return $this->shipping_amount;
	}
	
	/**
	 * Gets the PayPal fee for processing this payment.
	 * 
	 * @return float The amount.
	 */
	public function get_fee(){
		return $this->fee;
	}
	
	/**
	 * Gets the time that the payment was made as a unix timestamp.
	 * 
	 * @return int The time.
	 */
	public function get_time(){
		return $this->time;
	}
	
}

?>