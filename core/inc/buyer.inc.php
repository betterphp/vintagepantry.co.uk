<?php

/**
 * Represents a user that bought an item.
 */
class buyer {
	
	/**
	 * @var string The PayPal account ID
	 */
	private $id;
	
	/**
	 * @var string The name of the buyer.
	 */
	private $name;
	
	/**
	 * @var string The email address.
	 */
	private $email;
	
	public function __construct($id, $name, $email){
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
	}
	
	/**
	 * Gets the PayPal account ID of the buyer.
	 * 
	 * @return string The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the name of the buyer.
	 * 
	 * @return string The name.
	 */
	public function get_name(){
		return $this->name;
	}
	
	/**
	 * Gets the email address of the buyer.
	 * 
	 * @return string The address.
	 */
	public function get_email(){
		return $this->email;
	}
	
}

?>