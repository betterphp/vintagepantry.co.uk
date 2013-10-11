<?php

/**
 * Represents a user of the site.
 */
class user {
	
	/**
	 * @var int The internal ID of the user.
	 */
	private $id;
	
	/**
	 * @var string The email address of the user.
	 */
	private $email;
	
	/**
	 * @var string A hash of the users password.
	 */
	private $password_hash;
	
	/**
	 * @var int The time the user was created.
	 */
	private $time_created;
	
	public function __construct($id, $email, $password_hash, $time_created){
		$this->id = $id;
		$this->email = $email;
		$this->password_hash = $password_hash;
		$this->time_created = $time_created;
	}
	
	/**
	 * Gets the ID of this user.
	 * 
	 * @return int The ID.
	 */
	public function get_id(){
		return $this->id;
	}
	
	/**
	 * Gets the email address of this user.
	 * 
	 * @return string The email address.
	 */
	public function get_email(){
		return $this->email;
	}
	
	/**
	 * Gets the time this user was created as a unix timestamp.
	 * 
	 * @return int The time.
	 */
	public function get_time_created(){
		return $this->time_created;
	}
	
	/**
	 * Checks a password against the database.
	 * 
	 * @param string $password The password to check.
	 * @return boolean True if the password matches false if not.
	 */
	public function is_correct_password($password){
		// If the database failed to respond with a password we don't want to allow logins.
		if (empty($this->password_hash)){
			return false;
		}
		
		if (!is_string($password)){
			return false;
		}
		
		return (crypt($password, $this->password_hash) === $this->password_hash);
	}
	
	/**
	 * Sets the password for this user
	 * 
	 * @param string $password The new password.
	 */
	public function set_password($password){
		$mysql = mysql_connection::get_instance();
		
		$password_hash = crypt($password, '$2y$12$' . str_rand(22) . '$');
		
		$stmt = $mysql->prepare('UPDATE `users` SET `user_password_hash` = ? WHERE `user_id` = ?');
		$stmt->bind_param('si', $password_hash, $this->id);
		$stmt->execute();
		$stmt->close();
		
		$this->password_hash = $password_hash;
	}
	
	/**
	 * Fetches a users by their email address.
	 * 
	 * @param string $email The email address.
	 * @return user The user or false on failure.
	 */
	public static function fetch_by_email($email){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT `user_id`, `user_email`, `user_password_hash`, `user_time_created` FROM `users` WHERE `user_email` = ?');
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		
		if ($result->num_rows != 1){
			return false;
		}
		
		$row = $result->fetch_assoc();
		
		return new self(intval($row['user_id']), $row['user_email'], $row['user_password_hash'], intval($row['user_time_created']));
	}
	
	/**
	 * Adds a new user to the database.
	 * 
	 * @param string $email The email address.
	 * @param string $password The password
	 * @return user The user that was created.
	 */
	public static function add_new($email, $password){
		$mysql = mysql_connection::get_instance();
		
		$password_hash = crypt($password, '$2y$12$' . str_rand(22) . '$');
		
		$stmt = $mysql->prepare('INSERT INTO `users` (`user_email`, `user_password_hash`, `user_time_created`) VALUES (?, ?, UNIX_TIMESTAMP())');
		$stmt->bind_param('ss', $email, $password_hash);
		$stmt->execute();
		$stmt->close();
		
		return new self($mysql->insert_id, $email, $password_hash, time());
	}
	
}

?>