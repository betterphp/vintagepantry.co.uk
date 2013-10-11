<?php

/**
 * Represents a category in the shop.
 */
class category {
	
	/**
	 * @var int The internal category ID.
	 */
	private $id;
	
	/**
	 * @var string The name of the category.
	 */
	private $name;
	
	/**
	 * @var boolean The removed state of the category.
	 */
	private $removed;
	
	public function __construct($id, $name, $removed){
		$this->id = $id;
		$this->name = $name;
		$this->removed = $removed;
	}
	
	/**
	 * Gets the ID of this category.
	 * 
	 * @return int The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the name of this category.
	 * 
	 * @return string The name.
	 */
	public function get_name(){
		return $this->name;
	}
	
	/**
	 * Sets the name of the category.
	 * 
	 * @param string $name The name.
	 */
	public function set_name($name){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('UPDATE `categories` SET `category_name` = ? WHERE `category_id` = ?');
		$stmt->bind_param('si', $name, $this->id);
		$stmt->execute();
		$stmt->close();
		
		$this->name = $name;
	}
	
	/**
	 * Gets the removed state of the category.
	 * 
	 * @return boolean The removed state.
	 */
	public function is_removed(){
		return $this->removed;
	}
	
	/**
	 * Sets the category to be removed or not.
	 * 
	 * @param boolean $removed The removed state.
	 */
	public function set_removed($removed){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('UPDATE `categories` SET `category_removed` = ? WHERE `category_id` = ?');
		$stmt->bind_param('ii', $removed, $this->id);
		$stmt->execute();
		$stmt->close();
		
		$this->removed = (bool) $removed;
	}
	
	/**
	 * Fetches all categories.
	 * 
	 * @return array An array of category objects.
	 */
	public static function fetch_all(){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT `category_id`, `category_name`, `category_removed` FROM `categories`');
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		$categories = array();
		
		while (($row = $result->fetch_assoc()) !== null){
			$categories[] = new self(intval($row['category_id']), $row['category_name'], (bool) $row['category_removed']);
		}
		
		return $categories;
	}
	
	/**
	 * Fetches a category by its ID.
	 * 
	 * @param int $id The ID.
	 * @return category The category or false on failure.
	 */
	public static function fetch_by_id($id){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT `category_id`, `category_name`, `category_removed` FROM `categories` WHERE `category_id` = ?');
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		if ($result->num_rows != 1){
			return false;
		}
		
		$row = $result->fetch_assoc();
		
		return new self(intval($row['category_id']), $row['category_name'], (bool) $row['category_removed']);
	}
	
	/**
	 * Adds a new category.
	 * 
	 * @param string $name The name of the new category.
	 * @return category The created category.
	 */
	public static function add_new($name){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('INSERT INTO `categories` (`category_name`, `category_removed`) VALUES (?, 0)');
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt->close();
		
		return new self($mysql->insert_id, $name, false);
	}
	
}

?>