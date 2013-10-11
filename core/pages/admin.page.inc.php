<ul class="admin-navigation">
	<li><a href="admin.html">View Sales</a></li>
	<li><a href="admin_categories.html">Manage shop categories</a></li>
	<li><a href="admin_shipping_destinations.html">Manage shipping bands</a></li>
	<li><a href="admin_items.html">Manage Items</a></li>
</ul>
<h1>Recent Sales</h1>
<table>
	<tr>
		<th>Item Name</th>
		<th>Total Paid</th>
		<th>Time</th>
		<th>TXN ID</th>
		<th>Address</th>
	</tr>
	<?php
	
	foreach (sale::fetch_all(7) as $sale){
		?>
		<tr>
			<td><?php echo htmlentities($sale->get_item()->get_title()); ?></td>
			<td>£<?php echo money_format('%.2n', $sale->get_payment()->get_amount() + $sale->get_payment()->get_shipping_amount()); ?></td>
			<td><?php echo date('d/m/Y H:i:s', $sale->get_payment()->get_time()); ?></td>
			<td><?php echo $sale->get_payment()->get_txn_id(); ?></td>
			<td>
				<?php
				
				foreach ($sale->get_shipping_address() as $line){
					if (!empty($line)){
						echo '<div>', $line, '</div>';
					}
				}
				
				?>
			</td>
		</tr>
		<?php
	}
	
	?>
</table>
