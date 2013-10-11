<?php

/**
 * Represents a destination that can be posted to.
 */
class shipping_destination {
	
	/**
	 * @var int The internal ID of the destination.
	 */
	private $id;
	
	/**
	 * @var string the name of the destination.
	 */
	private $name;
	
	public function __construct($id, $name){
		$this->id = $id;
		$this->name = $name;
	}
	
	/**
	 * Gets the ID of this destination.
	 * 
	 * @return int The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the name of this destination.
	 * 
	 * @return string The name.
	 */
	public function get_name(){
		return $this->name;
	}
	
	/**
	 * Sets the name of the destination.
	 * 
	 * @param string $name The name.
	 */
	public function set_name($name){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('UPDATE `shipping_destinations` SET `shipping_destination_name` = ? WHERE `shipping_destination_id` = ?');
		$stmt->bind_param('si', $name, $this->id);
		$stmt->execute();
		$stmt->close();
		
		$this->name = $name;
	}
	
	/**
	 * Removes the destination and any configured bands.
	 */
	public function remove(){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('DELETE FROM `shipping_destinations` WHERE `shipping_destination_id` = ?');
		$stmt->bind_param('i', $this->id);
		$stmt->execute();
		$stmt->close();
		
		$stmt = $mysql->prepare('DELETE FROM `shipping_bands` WHERE `shipping_destination_id` = ?');
		$stmt->bind_param('i', $this->id);
		$stmt->execute();
		$stmt->close();
	}
	
	/**
	 * Fetches all of the payment bands for this destination.
	 * 
	 * @return array An array of shipping_band objects.
	 */
	public function fetch_bands(){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT `shipping_band_id`, `shipping_band_price`, `shipping_band_min_weight`, `shipping_band_max_weight` FROM `shipping_bands` WHERE `shipping_destination_id` = ?');
		$stmt->bind_param('i', $this->id);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		$bands = array();
		
		while (($row = $result->fetch_assoc()) != null){
			$bands[] = new shipping_band(intval($row['shipping_band_id']), $this, floatval($row['shipping_band_price']), intval($row['shipping_band_min_weight']), intval($row['shipping_band_max_weight']));
		}
		
		return $bands;
	}
	
	/**
	 * Adds a new shipping band to this destination.
	 * 
	 * @param int $min_weight The minimum weight in grams.
	 * @param int $max_weight The maximum weight in grams.
	 * @param float $price The price.
	 * @return shipping_band The created band.
	 */
	public function add_band($min_weight, $max_weight, $price){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('INSERT INTO `shipping_bands` (`shipping_destination_id`, `shipping_band_price`, `shipping_band_min_weight`, `shipping_band_max_weight`) VALUES (?, ?, ?, ?)');
		$stmt->bind_param('idii', $this->id, $price, $min_weight, $max_weight);
		$stmt->execute();
		$stmt->close();
		
		return new shipping_band($mysql->insert_id, $this, $price, $min_weight, $max_weight);
	}
	
	/**
	 * Fetches all shipping destinations from the database.
	 * 
	 * @return array An array of destinations.
	 */
	public static function fetch_all(){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT `shipping_destination_id`, `shipping_destination_name` FROM `shipping_destinations`');
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		$destinations = array();
		
		while (($row = $result->fetch_assoc()) != null){
			$destinations[] = new self(intval($row['shipping_destination_id']), $row['shipping_destination_name']);
		}
		
		return $destinations;
	}
	
	/**
	 * Fetches a shipping destination by its ID.
	 * 
	 * @param int $id The ID.
	 * @return shipping_destination The destination or false on failure.
	 */
	public static function fetch_by_id($id){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT `shipping_destination_name` FROM `shipping_destinations` WHERE `shipping_destination_id` = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		if ($result->num_rows != 1){
			return false;
		}
		
		$row = $result->fetch_assoc();
		
		return new self($id, $row['shipping_destination_name']);
	}
	
	/**
	 * Adds a new destination to the database.
	 * 
	 * @param string $name The name of the destination.
	 * @return shipping_destination The created destination.
	 */
	public static function add_new($name){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('INSERT INTO `shipping_destinations` (`shipping_destination_name`) VALUES (?)');
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt->close();
		
		return new self($mysql->insert_id, $name);
	}
	
}

?>