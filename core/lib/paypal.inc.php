<?php

/**
 * Provides an interface to the PayPal API
 */
class paypal {
	
	const BASE_DOMAIN		= 'sandbox.paypal.com';
	const API_DOMAIN		= 'api.paypal.com';
	
	/**
	 * Creates the encrypted string for secure PayPal buttons.
	 * 
	 * @param array $input The input variables for the button.
	 * @return string The encrypted string.
	 */
	public static function create_encryted_button($input){
		$input['cert_id'] = config::PAYPAL_CERT_ID;
		$lines = array();
		
		foreach ($input as $key => $value){
			$lines[] = "{$key}={$value}";
		}
		
		$content_file = tempnam($GLOBALS['core_path'] . '/tmp', 'PAYPAL_');
		$signed_content_file = tempnam($GLOBALS['core_path'] . '/tmp', 'PAYPAL_');
		$encrypted_content_file = tempnam($GLOBALS['core_path'] . '/tmp', 'PAYPAL_');
		
		file_put_contents($content_file, implode("\n", $lines));
		
		openssl_pkcs7_sign($content_file, $signed_content_file, "file://{$GLOBALS['core_path']}/vintagepantry-public.pem", "file://{$GLOBALS['core_path']}/vintagepantry-private.pem", array(), PKCS7_BINARY);
		
		$signed_content = file_get_contents($signed_content_file);
		$signed_content = trim(substr($signed_content, strpos($signed_content, "\n\n")));
		$signed_content = base64_decode($signed_content);
		
		file_put_contents($signed_content_file, $signed_content);
		
		openssl_pkcs7_encrypt($signed_content_file, $encrypted_content_file, file_get_contents("{$GLOBALS['core_path']}/paypal-public.pem"), array(), PKCS7_BINARY);
		
		$result = file_get_contents($encrypted_content_file);
		$result = trim(substr($result, strpos($result, "\n\n")));
		$result = str_replace("\n", '', $result);
		
		unlink($content_file);
		unlink($signed_content_file);
		unlink($encrypted_content_file);
		
		return "-----BEGIN PKCS7-----{$result}-----END PKCS7-----";
	}
	
	/**
	 * Validates a IPN message with PayPal.
	 * 
	 * @param array $data The message data, usually $_POST
	 * @return boolean True if the message is valid false if not.
	 */
	public static function is_valid_ipn_message($data){
		$response = file_get_contents('https://' . self::BASE_DOMAIN . '/cgi-bin/webscr', false, stream_context_create(array(
			'http' => array(
				'method'	=> 'POST',
				'timeout'	=> 10000,
				'header'	=> implode("\r\n", array(
					'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
				)),
				'content'	=> http_build_query(array_merge(array('cmd' => '_notify-validate'), $data)),
			),
		)));
		
		return (strtoupper($response) === 'VERIFIED');
	}
	
	/**
	 * Checks if a transaction has been processed.
	 * 
	 * @param string $txn_id The PayPal TXN ID.
	 * @return boolean True if the transaction has been processed false if not.
	 */
	public static function is_txn_processed($txn_id){
		$mysql = mysql_connection::get_instance();
		
		$stmt = $mysql->prepare('SELECT 1 FROM `sales` WHERE `payment_txn_id` = ?');
		$stmt->bind_param('s', $txn_id);
		$stmt->execute();
		$total = $stmt->get_result()->num_rows;
		$stmt->close();
		
		return ($total > 0);
	}
	
}

?>
