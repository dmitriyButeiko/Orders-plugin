<html>
	<head>
		
	</head>
	<body>
		<style>
			table, th, td {
    			border: 1px solid black;
			}
		</style>
		<h1>Reports</h1>
		<?php 
		
			//echo "<p>Current user: " . get_current_user_id() . "</p>"
		
		?>
		<!--<a href="#">Download report</a>-->
		<table style="table-layout: fixed;">
			<tr>
    			<th>Order Id</th>
    			<th width="30%">Url</th> 
				<th>Order Type</th> 
				<th>Time</th>
				<th>Status</th>
				<th>Download report link</th> 
  			</tr
			<?php 
			
				$user_id = get_current_user_id();
				
				$ordersCategory = get_category_by_slug("orders");
				//var_dump($ordersCategory);
				
				$allOrders = get_posts(array(
					'category' => 2,
					'post_status' => 'any',
					'author' => get_current_user_id()
				));
				
				//var_dump($allOrders);
			?>
			
			<?php 
			
				foreach($allOrders as $singleOrder)
				{
					?>
						<?php 
						
							$currentPostMeta = get_post_meta($singleOrder->ID);
							$orderId =  $singleOrder->ID;
							$orderUrl = $currentPostMeta["url"][0];
							$orderType = $currentPostMeta["orderType"][0];
							$timing = $currentPostMeta["timing"][0];
							$status = $currentPostMeta["status"][0];
						?>
						<tr>
							<td><?php echo $orderId; ?></td>
							<td><?php echo $orderUrl; ?></td> 
							<td><?php echo $orderType; ?></td> 
							<td><?php echo $timing; ?></td>
							<td><?php echo $status; ?></td>
							<td><a href="?action=downloadReport&orderId=<?php echo $orderId; ?>">download</a></td>
						</tr>
					<?php
				}
			
			
			?>
		</table>
	</body>
</html>