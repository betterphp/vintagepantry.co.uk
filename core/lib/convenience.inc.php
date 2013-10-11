<?php

/**
 * Generates a string made up of random characters.
 *
 * @param int $length The length of the string to create.
 * @return string The string.
 */
function str_rand($length){
	$chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 0));
	$source_str = str_shuffle(implode('', $chars));
	$max_len = strlen($source_str);
	
	if ($length > $max_len){
		$length = $max_len;
	}
	
	return substr($source_str, 0, $length);
}

/**
 * Converts a string to title case, capitalising all major words
 *
 * @param string $string The string to process
 * @return string The resulting string
 */
function title_case($string){
	$exceptions = array('the', 'and', 'nor', 'but', 'then', 'else', 'when', 'from', 'off', 'for', 'out', 'over', 'into', 'with');
	
	$words = explode(' ', strtolower($string));
	
	foreach ($words as &$word){
		if (!in_array($word, $exceptions) && strlen($word) > 2){
			$word = ucfirst($word);
		}
	}
	
	return implode(' ', $words);
}

/**
 * Clears any output and sends the HTTP headers for a redirect.
 * 
 * <p>
 * 	Note that no validation is done on the location.
 * </p>
 * 
 * @param string $url The location to redirect.
 * @param string $status The HTTP status to use, defaults to <i>302 Found</i>
 * @return void
 */
function redirect($url, $status = '302 Found'){
	if (ob_get_length() !== false){
		ob_end_clean();
	}
	
	header("{$_SERVER['SERVER_PROTOCOL']} {$status}");
	header("Location: {$url}");
	die();
}

/**
 * Formats a number of seconds into a readable format.
 *
 * @param int $second The number of seconds to process.
 * @param int $precision = null The number of components to innclude, defaults to all available.
 * @return string The formatted time.
 */
function format_time_diff($second, $precision = null){
	$units = array(
		'year'		=> 31556926,
		'month'		=> 2629740,
		'week'		=> 604800,
		'day'		=> 86400,
		'hour'		=> 3600,
		'minute'	=> 60,
	);
	
	foreach ($units as $name => $amount){
		$$name = (int) ($second / $amount);
		
		$second -= $$name * $amount;
		
		if ($$name > 0){
			$strings[] = "{$$name} " . (($$name != 1) ? "{$name}s" : $name);
		}
	}
	
	$strings[] = "{$second} " . (($second != 1) ? "seconds" : 'second');
	
	$total_strings = count($strings);
	
	if ($precision == null){
		$precision = count($strings);
	}
	
	if ($total_strings == 1 || $precision == 1){
		return $strings[0];
	}
	
	$strings = array_slice($strings, 0, $precision);
	$last_string = array_pop($strings);
	
	return implode(', ', $strings) . ' and ' . $last_string;
}

/**
 * Formats a plain text string as an item description.
 * 
 * @param string $input
 * @return string The result.
 */
function format_description($input){
	$input = preg_replace('#(?:\r\n|\r|\n){2,}#i', "\n", $input);
	$input = preg_replace('#^(.+)$#im', '<p>$1</p>', $input);
	
	return $input;
}

?>