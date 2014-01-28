<?php

$core_path = dirname(__FILE__);

include("{$core_path}/config.inc.php");
include("{$core_path}/lib/convenience.inc.php");

spl_autoload_register(function($class_name){
	$locations = array(
		"{$GLOBALS['core_path']}/inc",
		"{$GLOBALS['core_path']}/lib",
	);
	
	foreach ($locations as $location){
		if (file_exists("{$location}/{$class_name}.inc.php")){
			include("{$location}/{$class_name}.inc.php");
			return;
		}
	}
	
	die("Fatal Error: Could not find {$class_name}.inc.php");
});

set_error_handler(function($errno, $errstr, $errfile, $errline){
	if (error_reporting() == 0){
		return;
	}

	if (ob_get_length() !== false){
		ob_end_clean();
	}

	$error_levels = array(
		E_ALL				=> 'E_ALL',
		E_ERROR				=> 'E_ERROR',
		E_RECOVERABLE_ERROR	=> 'E_RECOVERABLE_ERROR',
		E_WARNING			=> 'E_WARNING',
		E_PARSE				=> 'E_PARSE',
		E_NOTICE			=> 'E_NOTICE',
		E_STRICT			=> 'E_STRICT',
		E_CORE_ERROR		=> 'E_CORE_ERROR',
		E_CORE_WARNING		=> 'E_CORE_WARNING',
		E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
		E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
		E_USER_ERROR		=> 'E_USER_ERROR',
		E_USER_WARNING		=> 'E_USER_WARNING',
		E_USER_NOTICE		=> 'E_USER_NOTICE',
		E_DEPRECATED		=> 'E_DEPRECATED',
	);

	$level = (isset($error_levels[$errno])) ? $error_levels[$errno] : "E_UNKNOWN ({$errno})";

	$write = date('[d/m/Y H:i:s]') . "[{$_SERVER['REMOTE_ADDR']}] {$level}: {$errstr} in {$errfile} on line {$errline}\n";

	file_put_contents("{$GLOBALS['core_path']}/error.log", $write, FILE_APPEND);

	if ($_SERVER['SERVER_NAME'] == '192.168.1.10'){
		die($write . '<pre>' . print_r(debug_backtrace(), true) . '</pre>');
	}else{
		die(':/ Something went wrong, but don\'t worry it\'s been logged and will be fixed soon \o/');
	}
}, E_ALL & ~E_STRICT);

error_reporting(E_ALL & ~E_STRICT);

ob_start();
session_start();

if (end(explode('/', current(get_included_files()))) == 'index.php'){
	if (empty($_GET['page'])){
		redirect('shop.html');
	}
	
	if (!in_array("{$_GET['page']}.page.inc.php", scandir("{$core_path}/pages"))){
		header('HTTP/1.1 404 Not Found');
		$_GET['page'] = '404';
	}
	
	$public_pages = array(
		'404',
		'403',
		'shop',
		'view_item',
		'payment_complete',
		'about',
		'contact',
		'login',
		'terms',
	);
	
	if (!isset($_SESSION['user']) && !in_array($_GET['page'], $public_pages)){
		redirect('login.html', '403 Forbidden');
	}
	
	$include_file = "{$core_path}/pages/{$_GET['page']}.page.inc.php";
}

?>
