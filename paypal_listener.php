<?php

include('core/init.inc.php');

if (!isset($_POST['item_number'])){
	trigger_error('E01 - Invalid request', E_USER_ERROR);
}

if ($_POST['payment_status'] != 'Completed'){
	trigger_error('E02 - Incomplete payment', E_USER_ERROR);
}

if (paypal::is_txn_processed($_POST['txn_id'])){
	trigger_error('E03 - Duplicate payment', E_USER_ERROR);
}

if (!paypal::is_valid_ipn_message($_POST)){
	trigger_error('E04 - Invalid request', E_USER_ERROR);
}

$item = item::fetch_by_id($_POST['item_number']);

if ($item === false){
	trigger_error('E05 - Invalid item', E_USER_ERROR);
}

if ($item->get_quantity() == 0){
	trigger_error('E07 - Item not available', E_USER_ERROR);
}

$payment = new payment($_POST['txn_id'], floatval($_POST['mc_gross']), floatval($_POST['shipping']), floatval($_POST['mc_fee']), strtotime($_POST['payment_date']));
$buyer = new buyer($_POST['payer_id'], "{$_POST['first_name']} {$_POST['last_name']}", $_POST['payer_email']);

sale::add_new($item, $payment, $buyer, $_POST['address_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], (isset($_POST['note'])) ? $_POST['note'] : '');

$item->decrement_quantity();

?>