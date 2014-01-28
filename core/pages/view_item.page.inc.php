<?php

if (!isset($_GET['item_id']) || ($item = item::fetch_by_id($_GET['item_id'])) === false){
	redirect('404.html', '404 Not Found');
}

$page_title = $item->get_title();

$og_vars = array(
	'type'				=> 'product',
	'title'				=> $item->get_title(),
	'price:amount'		=> $item->get_price(),
	'price:currency'	=> 'GBP',
	'site_name'			=> 'The Vintage Pantry',
	'description'		=> str_replace(array("\r", "\n"), '', $item->get_description()),
	'availability'		=> ($item->get_quantity() > 0) ? 'instock' : 'out of stock',
);

$images = $item->get_images();
$shipping_bands = $item->fetch_shipping_options();

$preview_index = (isset($_GET['preview_index']) && isset($images[$_GET['preview_index']])) ? $_GET['preview_index'] : 0;

?>
<div class="shop-product-images">
	<a href="<?php echo $images[$preview_index]->get_full_relative_path(); ?>" id="preview_link">
		<img src="<?php echo $images[$preview_index]->get_preview_relative_path(); ?>" alt="" id="preview_image" />
	</a>
	<div>
	<?php
	
	foreach ($images as $index => $image){
		echo '<a href="?item_id=', $item->get_id(), '&amp;preview_index=', $index, '" data-preview-path="', htmlentities($image->get_preview_relative_path()), '" data-full-path="', htmlentities($image->get_full_relative_path()), '"><img src="', $image->get_thumb_relative_path(), '" alt="" /></a>';
	}
	
	?>
	</div>
</div>
<div class="shop-product-details">
	<div class="product-details">
		<div>
			<h2><?php echo htmlentities($item->get_title()); ?></h2>
			<?php
			
			if ($item->get_quantity() > 0){
				?>
				<form action="https://www.<?php echo paypal::BASE_DOMAIN; ?>/cgi-bin/webscr" method="post">
					<div>
						<select name="encrypted" id="postage">
							<?php
							
							foreach ($shipping_bands as $band){
								$paypal_data = paypal::create_encryted_button(array(
									'cmd'			=> '_xclick',
									'business'		=> config::PAYPAL_EMAIL,
									'item_name'		=> $item->get_title(),
									'item_number'	=> $item->get_id(),
									'amount'		=> $item->get_price(),
									'shipping'		=> $band->get_price(),
									'currency_code'	=> 'GBP',
									'no_shipping'	=> 2,
									'notify_url'	=> config::BASE_URL . 'paypal_listener.php',
									'return'		=> config::BASE_URL . 'shop.html?category_id=' . $item->get_category()->get_id(),
									'cancel_return'	=> config::BASE_URL . 'view_item.html?item_id=' . $item->get_id(),
								));
								
								echo '<option value="', $paypal_data, '">P&P - ', $band->get_destination()->get_name(), ' (£', money_format('%.2i', $band->get_price()), ')</option>';
							}
							
							?>
						</select>
						<input type="hidden" name="cmd" value="_s-xclick" />
						<input type="submit" class="button" value="Buy now £<?php echo money_format('%.2i', $item->get_price()); ?>" />
					</div>
				</form>
				<?php
			}
			
			?>
		</div>
		
		<div>
			<?php echo format_description(htmlentities($item->get_description())); ?>
		</div>
		
		<div class="item-shipping-info">
			<p>Your item will be well-packaged and posted within 24 hours via Royal Mail 24.</p>
			<a href="https://www.paypal.com/uk/webapps/mpp/paypal-popup" target="_blank"><img src="ext/img/paypal_logo.png" alt="Secure payment through PayPal" /></a>
			<p>Payments are processed securely through PayPal. All major cards accepted.</p>
			<p>Thank you for visiting The Vintage Pantry!</p>
		</div>
	</div>
</div>
