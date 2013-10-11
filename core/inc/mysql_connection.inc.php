<?php

/**
 * Holds a connection to the MySQL server.
 */
class mysql_connection {
	
	/**
	 * @var mysqli The connection.
	 */
	private static $connection = null;
	
	/**
	 * Gets a connection to the MySQL server, creating one if necessary.
	 * 
	 * @return mysqli The conenction.
	 */
	public static function get_instance(){
		if (self::$connection === null){
			self::$connection = new mysqli(config::DATABASE_HOST, config::DATABASE_USER, config::DATABASE_PASS, config::DATABASE_NAME);
		}
		
		return self::$connection;
	}
	
}

?>
