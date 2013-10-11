<?php

/**
 * Represents the same of an item.
 */
class sale {
	
	/**
	 * @var int The sale ID.
	 */
	private $id;
	
	/**
	 * @var item The item that was sold.
	 */
	private $item;
	
	/**
	 * @var payment The payment that was made.
	 */
	private $payment;
	
	/**
	 * @var buyer The user that sent the payment.
	 */
	private $buyer;
	
	/**
	 * @var array The address the item should be sent to.
	 */
	private $shipping_address;
	
	/**
	 * @var string A buyer note related to the sale.
	 */
	private $note;
	
	public function __construct($id, $item, $payment, $buyer, $shipping_address, $note){
		$this->id = $id;
		$this->item = $item;
		$this->payment = $payment;
		$this->buyer = $buyer;
		$this->shipping_address = $shipping_address;
		$this->note = $note;
	}
	
	/**
	 * Gets the ID of this sale.
	 * 
	 * @return int The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the item that was sold.
	 * 
	 * @return item The item.
	 */
	public function get_item(){
		return $this->item;
	}
	
	/**
	 * Gets the payment details for the sale.
	 * 
	 * @return payment The details.
	 */
	public function get_payment(){
		return $this->payment;
	}
	
	/**
	 * Gets the user that bought the item.
	 * 
	 * @return buyer The buyer.
	 */
	public function get_buyer(){
		return $this->buyer;
	}
	
	/**
	 * Gets the address that the item should be sent to.
	 * 
	 * @return array The lines of the address.
	 */
	public function get_shipping_address(){
		return $this->shipping_address;
	}
	
	/**
	 * Gets the note from the buyer.
	 * 
	 * @return string The note.
	 */
	public function get_note(){
		return $this->note;
	}
	
	/**
	 * Fetches all sales in the last $time days.
	 * 
	 * @param int $time The number of days to go back.
	 */
	public static function fetch_all($time = 7){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'SELECT
					`sales`.`sale_id`,
					`sales`.`payment_amount`,
					`sales`.`payment_shipping_amount`,
					`sales`.`payment_fee`,
					`sales`.`payment_txn_id`,
					`sales`.`payment_buyer_id`,
					`sales`.`payment_buyer_name`,
					`sales`.`payment_buyer_email`,
					`sales`.`payment_address_name`,
					`sales`.`payment_address_street`,
					`sales`.`payment_address_city`,
					`sales`.`payment_address_state`,
					`sales`.`payment_address_zip`,
					`sales`.`payment_address_country`,
					`sales`.`payment_time`,
					`sales`.`payment_note`,
					`items`.`item_id`,
					`items`.`item_title`,
					`items`.`item_description`,
					`items`.`item_price`,
					`items`.`item_weight`,
					`items`.`item_quantity`,
					`items`.`item_time_created`,
					`categories`.`category_id`,
					`categories`.`category_name`,
					`categories`.`category_removed`
				FROM `sales`
				INNER JOIN `items` ON `sales`.`item_id` = `items`.`item_id`
				INNER JOIN `categories` ON `items`.`category_id` = `categories`.`category_id`
				WHERE DATEDIFF(CURDATE(), FROM_UNIXTIME(`payment_time`)) <= ?';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('i', $time);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		$sales = array();
		
		while (($row = $result->fetch_assoc()) != null){
			$category = new category(intval($row['category_id']), $row['category_name'], (bool) $row['category_removed']);
			$item = new item(intval($row['item_id']), $category, $row['item_title'], $row['item_description'], floatval($row['item_price']), intval($row['item_weight']), intval($row['item_quantity']), intval($row['item_time_created']));
			$payment = new payment($row['payment_txn_id'], floatval($row['payment_amount']), floatval($row['payment_shipping_amount']), floatval($row['payment_fee']), intval($row['payment_time']));
			$buyer = new buyer($row['payment_buyer_id'], $row['payment_buyer_name'], $row['payment_buyer_email']);
			
			$shipping_address[] = $row['payment_address_name'];
			
			foreach (explode("\n", $row['payment_address_street']) as $line){
				$shipping_address[] = $line;
			}
			
			$shipping_address[] = $row['payment_address_city'];
			$shipping_address[] = $row['payment_address_state'];
			$shipping_address[] = $row['payment_address_zip'];
			$shipping_address[] = $row['payment_address_country'];
			
			$sales[] = new self(intval($row['sale_id']), $item, $payment, $buyer, $shipping_address, $row['payment_note']);
		}
		
		return $sales;
	}
	
	/**
	 * Adds a new sale to the database.
	 * 
	 * @param item $item The item that was sold.
	 * @param payment $payment The payment details.
	 * @param buyer $buyer The buyer details.
	 * @param string $address_name The delivery address name
	 * @param string $address_street The delivery address street
	 * @param string $address_city The delivery address city
	 * @param string $address_state The delivery address state
	 * @param string $address_zip The delivery address zip code
	 * @param string $address_country The delivery address country
	 * @param string $note A note attached to the payment.
	 */
	public static function add_new($item, $payment, $buyer, $address_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $note){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'INSERT INTO `sales` (
					`item_id`,
					`payment_amount`,
					`payment_shipping_amount`,
					`payment_fee`,
					`payment_txn_id`,
					`payment_buyer_id`,
					`payment_buyer_name`,
					`payment_buyer_email`,
					`payment_address_name`,
					`payment_address_street`,
					`payment_address_city`,
					`payment_address_state`,
					`payment_address_zip`,
					`payment_address_country`,
					`payment_time`,
					`payment_note`
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), ?)';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('idddsssssssssss', $item->get_id(), $payment->get_amount(), $payment->get_shipping_amount(), $payment->get_fee(), $payment->get_txn_id(), $buyer->get_id(), $buyer->get_name(), $buyer->get_email(), $address_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $note);
		$stmt->execute();
		$stmt->close();
		
		$shipping_address[] = $address_name;
		
		foreach (explode("\n", $address_street) as $line){
			$shipping_address[] = $line;
		}
		
		$shipping_address[] = $address_city;
		$shipping_address[] = $address_state;
		$shipping_address[] = $address_zip;
		$shipping_address[] = $address_country;
		
		return new self($mysql->insert_id, $item, $payment, $buyer, $shipping_address, $note);
	}
	
}

?>