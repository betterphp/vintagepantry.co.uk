<?php

/**
 * Represents a shipping price band.
 */
class shipping_band {
	
	/**
	 * @var int The ID of the band.
	 */
	private $id;
	
	/**
	 * @var shipping_destination The destination.
	 */
	private $destination;
	
	/**
	 * @var float The amount this band costs.
	 */
	private $price;
	
	/**
	 * @var int The minimum weight of this band.
	 */
	private $min_weight;
	
	/**
	 * @var int The maximum weight of the band.
	 */
	private $max_weight;
	
	public function __construct($id, $destination, $price, $min_weight, $max_weight){
		$this->id = $id;
		$this->destination = $destination;
		$this->price = $price;
		$this->min_weight = $min_weight;
		$this->max_weight = $max_weight;
	}
	
	/**
	 * Gets the ID of this band.
	 * 
	 * @return int The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the destination this band is for.
	 * 
	 * @return shipping_destination The destination.
	 */
	public function get_destination(){
		return $this->destination;
	}
	
	/**
	 * Gets the price of this band.
	 * 
	 * @return float The price.
	 */
	public function get_price(){
		return $this->price;
	}
	
	/**
	 * Gets the lower weight limit of this band.
	 * 
	 * @return int The weight in grams.
	 */
	public function get_min_weight(){
		return $this->min_weight;
	}
	
	/**
	 * Gets the upper weight limit of this band.
	 * 
	 * @return int The weight in grams.
	 */
	public function get_max_weight(){
		return $this->max_weight;
	}
	
	/**
	 * Removes the shipping band.
	 */
	public function remove(){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('DELETE FROM `shipping_bands` WHERE `shipping_band_id` = ?');
		$stmt->bind_param('i', $this->id);
		$stmt->execute();
		$stmt->close();
	}
	
	/**
	 * Fetches a shipping band by it's ID.
	 * 
	 * @param int $id The ID
	 * @return shipping_band The shipping band or false on failure.
	 */
	public static function fetch_by_id($id){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'SELECT
					`shipping_bands`.`shipping_band_id`,
					`shipping_bands`.`shipping_band_price`,
					`shipping_bands`.`shipping_band_min_weight`,
					`shipping_bands`.`shipping_band_max_weight`,
					`shipping_destinations`.`shipping_destination_id`,
					`shipping_destinations`.`shipping_destination_name`
				FROM `shipping_bands`
				INNER JOIN `shipping_destinations` ON `shipping_bands`.`shipping_destination_id` = `shipping_destinations`.`shipping_destination_id`
				WHERE `shipping_bands`.`shipping_band_id` = ?';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		if ($result->num_rows != 1){
			return false;
		}
		
		$row = $result->fetch_assoc();
		
		return new shipping_band(intval($row['shipping_band_id']), new shipping_destination(intval($row['shipping_destination_id']), $row['shipping_destination_name']), floatval($row['shipping_band_price']), intval($row['shipping_band_min_weight']), intval($row['shipping_band_max_weight']));
	}
	
}

?>