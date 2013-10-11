<?php

/**
 * Represents an item for sale in the shop.
 */
class item {
	
	/**
	 * @var int The internal ID of the item.
	 */
	private $id;
	
	/**
	 * @var category The category the item is in.
	 */
	private $category;
	
	/**
	 * @var string The item title.
	 */
	private $title;
	
	/**
	 * @var string The item description.
	 */
	private $description;
	
	/**
	 * @var array The images of the item.
	 */
	private $images;
	
	/**
	 * @var float The item price.
	 */
	private $price;
	
	/**
	 * @var int The item weight (in grams).
	 */
	private $weight;
	
	/**
	 * @var int The number of items available.
	 */
	private $quantity;
	
	/**
	 * @var int The time the item was first added.
	 */
	private $time_created;
	
	public function __construct($id, $category, $title, $description, $price, $weight, $quantity, $time_created){
		$this->id = $id;
		$this->category = $category;
		$this->title = $title;
		$this->description = $description;
		$this->price = $price;
		$this->weight = $weight;
		$this->quantity = $quantity;
		$this->time_created = $time_created;
		
		$image_dir = "{$GLOBALS['core_path']}/user_content/item_images/{$this->id}";
		$this->images = array();
		
		foreach (glob("{$image_dir}/*_full.png") as $file){
			$file_name = basename($file);
			$image_id = intval(substr($file_name, 0, strpos($file_name, '_')));
				
			$this->images[] = new item_image($this->id, $image_id);
		}
	}
	
	/**
	 * Gets the item ID.
	 * 
	 * @return int The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the category that this item is listed in.
	 * 
	 * @return category The category.
	 */
	public function get_category(){
		return $this->category;
	}
	
	/**
	 * Gets the item title.
	 * 
	 * @return string The title.
	 */
	public function get_title(){
		return $this->title;
	}
	
	/**
	 * Gets the item description.
	 * 
	 * @return string The description.
	 */
	public function get_description(){
		return $this->description;
	}
	
	/**
	 * Gets the images of this item.
	 * 
	 * @return array An array of item_image objects.
	 */
	public function get_images(){
		return $this->images;
	}
	
	/**
	 * Gets the item price.
	 * 
	 * @return float The price.
	 */
	public function get_price(){
		return $this->price;
	}
	
	/**
	 * Gets the item weight in grams.
	 * 
	 * @return int The weight.
	 */
	public function get_weight(){
		return $this->weight;
	}
	
	/**
	 * Gets the number of items available.
	 * 
	 * @return int The number.
	 */
	public function get_quantity(){
		return $this->quantity;
	}
	
	/**
	 * Decreases the quantity available by 1.
	 */
	public function decrement_quantity(){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('UPDATE `items` SET `item_quantity` = `item_quantity` - 1 WHERE `item_id` = ?');
		$stmt->bind_param('i', $this->id);
		$stmt->execute();
		$stmt->close();
	}
	
	/**
	 * Gets the time that the item was added to the shop as a unix timestamp.
	 * 
	 * @return int The time.
	 */
	public function get_time_created(){
		return $this->time_created;
	}
	
	/**
	 * Fetches all of the shipping bands that can be applied to this item.
	 * 
	 * @return array An array of shipping_band objects.
	 */
	public function fetch_shipping_options(){
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
				WHERE ? BETWEEN `shipping_bands`.`shipping_band_min_weight` AND `shipping_bands`.`shipping_band_max_weight`';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('i', $this->weight);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		$bands = array();
		
		while (($row = $result->fetch_assoc()) != null){
			$bands[] = new shipping_band(intval($row['shipping_band_id']), new shipping_destination(intval($row['shipping_destination_id']), $row['shipping_destination_name']), floatval($row['shipping_band_price']), intval($row['shipping_band_min_weight']), intval($row['shipping_band_max_weight']));
		}
		
		return $bands;
	}
	
	/**
	 * Updates all item details.
	 * 
	 * @param string $title The new title.
	 * @param category $category The new category.
	 * @param float $price The new price
	 * @param int $weight The new weight
	 * @param int $quantity The new quantity.
	 * @param string $description The new description
	 */
	public function update_details($title, $category, $price, $weight, $quantity, $description){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'UPDATE `items` SET
					`category_id` = ?,
					`item_title` = ?,
					`item_description` = ?,
					`item_price` = ?,
					`item_weight` = ?,
					`item_quantity` = ?
				WHERE `item_id` = ?';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('issdiii', $category->get_id(), $title, $description, $price, $weight, $quantity, $this->id);
		$stmt->execute();
		$stmt->close();
		
		$this->title = $title;
		$this->category = $category;
		$this->price = $price;
		$this->weight = $weight;
		$this->quantity = $quantity;
		$this->description = $description;
	}
	
	/**
	 * Fetches all matching items.
	 * 
	 * @param int $category_id The ID of the category to search in.
	 * @param string $search_phrase The phrase to search for.
	 * @return array An array of item objects.
	 */
	public static function fetch_all($category_id = null, $search_phrase = null){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'SELECT
					`items`.`item_id`,
					`items`.`item_title`,
					`items`.`item_description`,
					`items`.`item_price`,
					`items`.`item_weight`,
					`items`.`item_quantity`,
					`items`.`item_time_created`,
					`categories`.`category_id`,
					`categories`.`category_name`
				FROM `items`
				INNER JOIN `categories` ON `items`.`category_id` = `categories`.`category_id`
				WHERE `item_quantity` > 0
				AND `categories`.`category_removed` = 0';
		
		if ($category_id != null || $search_phrase != null){
			$params = array('');
			
			if ($category_id != null){
				$sql .= ' AND `items`.`category_id` = ?';
				
				$params[0] .= 'i';
				$params[] =& $category_id;
			}
			
			if ($search_phrase != null){
				$sql .= ' AND MATCH(`items`.`item_title`) AGAINST (? IN BOOLEAN MODE)';
				
				$params[0] .= 's';
				$params[] =& $search_phrase;
			}
		}
		
		$stmt = $mysql->prepare($sql);
		
		if (!empty($params)){
			call_user_func_array(array($stmt, 'bind_param'), $params);
		}
		
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		$items = array();
		
		while (($row = $result->fetch_assoc()) !== null){
			$category = new category(intval($row['category_id']), $row['category_name'], false);
			
			$items[] = new self(intval($row['item_id']), $category, $row['item_title'], $row['item_description'], floatval($row['item_price']), intval($row['item_weight']), intval($row['item_quantity']), intval($row['item_time_created']));
		}
		
		return $items;
	}
	
	/**
	 * Fetches a single item from it's ID.
	 * 
	 * @param int $id The ID.
	 * @return item The item or false on failure.
	 */
	public static function fetch_by_id($id){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'SELECT
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
				FROM `items`
				INNER JOIN `categories` ON `items`.`category_id` = `categories`.`category_id`
				WHERE `item_id` = ?';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		if ($result->num_rows != 1){
			return false;
		}
		
		$row = $result->fetch_assoc();
		
		$category = new category(intval($row['category_id']), $row['category_name'], (bool) $row['category_removed']);
		
		return new self(intval($row['item_id']), $category, $row['item_title'], $row['item_description'], floatval($row['item_price']), intval($row['item_weight']), intval($row['item_quantity']), intval($row['item_time_created']));
	}
	
	/**
	 * Resizes an image cropping to best fit. 
	 * 
	 * @param resource $source The source image.
	 * @param int $width The new width.
	 * @param int $height The new height.
	 * @return resource A GD image resource
	 */
	private static function resize_image($source, $width, $height){
		$source_width = imagesx($source);
		$source_height = imagesy($source);
		$source_aspect = round($source_width / $source_height, 1);
		$target_aspect = round($width / $height, 1);
		
		$resized = imagecreatetruecolor($width, $height);
		
		if ($source_aspect < $target_aspect){
			// higher
			$new_size = array($width, ($width / $source_width) * $source_height);
			$source_pos = array(0, (($new_size[1] - $height) * ($source_height / $new_size[1])) / 2);
		}else if ($source_aspect > $target_aspect){
			// wider
			$new_size = array(($height / $source_height) * $source_width, $height);
			$source_pos = array((($new_size[0] - $width) * ($source_width / $new_size[0])) / 2, 0);
		}else{
			// same shape
			$new_size = array($width, $height);
			$source_pos = array(0, 0);
		}
		
		if ($new_size[0] < 1) $new_size[0] = 1;
		if ($new_size[1] < 1) $new_size[1] = 1;
		
		imagecopyresampled($resized, $source, 0, 0, $source_pos[0], $source_pos[1], $new_size[0], $new_size[1], $source_width, $source_height);
		
		return $resized;
	}
	
	/**
	 * Adds a new item to the shop.
	 * 
	 * @param category $category The ID of the category the item will be in.
	 * @param string $title The title of the item.
	 * @param string $description The item description.
	 * @param array $images The images of the item.
	 * @param float $price The item price.
	 * @param int $weight The item weight (in grams).
	 * @param int $quantity The number of items available.
	 */
	public static function add_new($category, $title, $description, $images, $price, $weight, $quantity){
		$mysql = mysql_connection::get_instance();
		
		$sql = 'INSERT INTO `items` (`category_id`, `user_id`, `item_title`, `item_description`, `item_price`, `item_weight`, `item_quantity`, `item_time_created`)
				VALUES (?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())';
		
		$stmt = $mysql->prepare($sql);
		$stmt->bind_param('iissdii', $category->get_id(), $_SESSION['user']->get_id(), $title, $description, $price, $weight, $quantity);
		$stmt->execute();
		$stmt->close();
		
		$item_id = $mysql->insert_id;
		
		$image_dir = "{$GLOBALS['core_path']}/user_content/item_images/{$item_id}";
		
		if (!file_exists($image_dir)){
			mkdir($image_dir, 0750);
		}
		
		foreach ($images as $key => $image_file){
			$image = imagecreatefromstring(file_get_contents($image_file));
			
			$preview = self::resize_image($image, item_image::PREVIEW_WIDTH, item_image::PREVIEW_HEIGHT);
			$cover = self::resize_image($image, item_image::COVER_WIDTH, item_image::COVER_HEIGHT);
			$thumb = self::resize_image($image, item_image::THUMB_WIDTH, item_image::THUMB_HEIGHT);
			
			imagepng($preview, "{$image_dir}/{$key}_preview.png");
			imagepng($cover, "{$image_dir}/{$key}_cover.png");
			imagepng($thumb, "{$image_dir}/{$key}_thumb.png");
			imagepng($image, "{$image_dir}/{$key}_full.png");
			
			imagedestroy($preview);
			imagedestroy($cover);
			imagedestroy($image);
			imagedestroy($thumb);
			
			unlink($image_file);
		}
		
		return new self($item_id, $category, $title, $description, $price, $weight, $quantity, time());
	}
	
}

?>