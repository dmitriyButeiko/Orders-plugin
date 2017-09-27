<?php 
	/*
		Plugin Name: Order management by Dmitriy Buteiko
	*/
	$defaultOrderTypes = array(
		array(
			"name" => "Website Templates",
			"predefinedText" => "",
		),
		array(
			"name" => "Logo Templates",
			"predefinedText" => "",
		),
		array(
			"name" => "Icon Templates",
			"predefinedText" => "",
		),
	);
	$defaultQuantities = array(
		"Quantity1",
		"Quantity2"
	);
	
	/*
		Start shortcodes section
	*/
	function ordermanage_add( $atts, $content = "" ) {
		require_once plugin_dir_path(__FILE__) . "/templates/content-order.php";
		return $content;
	}
	add_shortcode( 'ordermanage_add', 'ordermanage_add' );

	function ordermanage_reports( $atts, $content = "" ) {
		require_once plugin_dir_path(__FILE__) . "/templates/content-reports.php";
		return $content;
	}
	add_shortcode( 'ordermanage_reports', 'ordermanage_reports' );
	
	/*
		Finish shortcodes section
	*/


	$orderAlreadyAdded = false;

	function register_init_things() {
		global $defaultOrderTypes, $defaultQuantities;
		
		if(!get_option("ord_types"))
		{
			add_option("ord_types", $defaultOrderTypes);

		}
		if(!get_option("ord_quantities"))
		{
			add_option("ord_quantities", $defaultQuantities);
		}
		
		$categorySettings = array(
			'Orders',
			'category',
			array(
				'slug' => 'orders',
				'description' => 'Orders category by dmitiry buteiko plugin'
			)
		);
		
		// Create the category
		$my_cat_id = wp_insert_term('Orders', 'category', array(
				'slug' => 'orders',
				'description' => 'Orders category by dmitiry buteiko plugin'
		));
	}
	
	add_action( 'init', 'register_init_things' );
	add_action( 'init', 'orders_handle_actions');
	
	function orders_handle_actions()
	{
			if(array_key_exists("action", $_GET))
			{
				if($_GET["action"] == "downloadReport")
				{
					$reportsFileName = "report.txt";
					$orderMeta = get_post_meta($_GET["orderId"]);
					$orderMeta["orderId"] = $_GET["orderId"];
					$reportText = generateReportText($orderMeta);

					file_put_contents($reportsFileName, $reportText);
					
					header('Content-Disposition: attachment; filename="' . basename($reportsFileName) . '"');
					header("Content-Length: " . filesize($reportsFileName));
					header("Content-Type: application/octet-stream;");
					readfile($reportsFileName);
				}
			}
	}
	
	function generateReportText($orderMeta)
	{
		$reportText = "";
		
		$allOrderTypes = get_option("ord_types");;
		
		$predefinedText = "";
		
		$arrayToReplace = array(
			"((orderurl))"
		);
		
		
		foreach($allOrderTypes as $singleOrderType)
		{
			if($singleOrderType["name"] == $orderMeta["orderType"][0])
			{
				$predefinedText = $singleOrderType["predefinedText"];
			}
		}
		
		$reportText .= "Url: " . $orderMeta["url"][0] . "\r\n" . PHP_EOL;
		$reportText .= "Keywords: " . $orderMeta["keywords"][0] . "\r\n"  . PHP_EOL;
		$reportText .= "Order type: " . $orderMeta["orderType"][0] . "\r\n" . PHP_EOL;
		$reportText .= "Quantity: " . $orderMeta["quantity"][0] . "\r\n" . PHP_EOL;
		$reportText .= "Status: " . $orderMeta["status"][0] . "\r\n" . PHP_EOL . "\r\n" . PHP_EOL;		
		$reportText .= str_replace($arrayToReplace, $orderMeta["url"][0], $predefinedText) . "\r\n" . PHP_EOL;
		
		$post = get_post($orderMeta["orderId"]);
		$predefinedPostText = $post->post_content;
		$predefinedPostText = str_replace($arrayToReplace, $orderMeta["url"][0], $predefinedPostText);
		$reportText .= $predefinedPostText . "\r\n" . PHP_EOL;
		
		return $reportText;
	}
	
	
	function processAction($currentAction)
	{
			switch($currentAction)
			{
				case "addOrder" : 
					addNewOrder();			
			}
	}
	function addNewOrder()
	{
		global $orderAlreadyAdded;

		if($orderAlreadyAdded)
		{
			return true;
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$orderData = array();
			
			$orderData["url"] = str_replace(array("http://", "https://", "www."), '',$_POST["url"]);
			$orderData["keywords"] = $_POST["keywords"];
			$orderData["category"] = $_POST["category"];
			$orderData["timing"] = $_POST["timing"];
			$orderData["orderType"] = $_POST["orderType"];
			$orderData["quantity"] = $_POST["quantity"];
			$orderData["status"] = "Completed";
			$orderData["orderPredefinedText"] = "orderPredefinedText";
			
			
			$user_id = get_current_user_id();
			$category = get_category_by_slug("orders");
			$categoryID = $category->cat_ID;

			$postData = array(
				'post_title' => $orderData["url"],
				'post_content' => "",
				'post_excerpt' => "Post exerpt",
				'post_author' => $user_id,
				'post_category' => array($categoryID)
			);
	
			$addedPostId = wp_insert_post($postData, true);
			
			foreach($orderData as $singleDataKey => $singleDataValue)
			{
				add_post_meta($addedPostId, $singleDataKey, $singleDataValue);
			}
			
			$orderAlreadyAdded = true;
		}
	}
	function getOrdersActionsTemplates()
	{
		return array(
			"addOrder" => "order",
			"showReports" => "reports"
		);
	}
	
	
	add_action( 'admin_menu', 'my_admin_menu' );
	function my_admin_menu() 
	{	
		add_menu_page("Order customization", "Order customization", "manage_options", "order-customization", "order_customization_page_show");
	}
	
	function getExistingPostTypes()
	{
		$pluginPostTypes = array();
		$allPostTypes = get_post_types();
		
		foreach($allPostTypes as $singlePostType)
		{
			$plugin_post_types_prefix = "ord_";
			
			if(strpos($singlePostType, $plugin_post_types_prefix) === 0)
			{
				$pluginPostTypes[] = $singlePostType;
			}
		}
		
		return $pluginPostTypes;
	}
	
	function order_customization_page_show()
	{
		?>
			<?php 
				$orderTypes = get_option("ord_types");
				$orderQuantities = get_option("ord_quantities");
			?>
			
			<?php 
			
				if ($_SERVER['REQUEST_METHOD'] === 'POST') 
				{
					if(isset($_POST["orderType"]))
					{
						$counter = 0;
						$updatedOrders = array();
						foreach($_POST["orderType"] as $singleOrderType)
						{
							$singleUpdatedOrder = array();
							
							$singleUpdatedOrder["name"] = $singleOrderType;
							$singleUpdatedOrder["predefinedText"] = $_POST["orderPredefinedText"][$counter];
							
							$counter++;
							$updatedOrders[] = $singleUpdatedOrder;
						}
						
						
						
						//$orderTypes[] = $_POST["orderType"];
						if(update_option("ord_types", $updatedOrders))
						{
							echo "<p>Order type added</p>";
						}
						else
						{
							echo "<p>Error happened!</p>";
						}
						/*var_dump($updatedOrders);
						exit;*/
					}
					if(isset($_POST["newOrderType"]))
					{
						$newOrder = array();
						$newOrder["name"] = $_POST["newOrderType"];
						$newOrder["predefinedText"] = $_POST["newOrderPredefinedText"];
						$orderTypes[] = $newOrder;
						if(update_option("ord_types", $orderTypes))
						{
							echo "<p>Order type added</p>";
						}
						else
						{
							echo "<p>Error happened!</p>";
						}
					}
					if(isset($_POST["quantity"]))
					{
						$orderQuantities[] = $_POST["quantity"];
						if(update_option("ord_quantities", $orderQuantities))
						{
							echo "<p>Quantity added</p>";
						}
						else
						{
							echo "<p>Error happened!</p>";
						}
					}
				}
			
				$orderTypes = get_option("ord_types");
				$orderQuantities = get_option("ord_quantities");
			?>
		
			<h1>Order Types</h1>
			<form id="orderTypes" method="post">
				<?php 
				
					foreach($orderTypes as $singleOrderType)
					{
						?>
							<p><label>Order type</label><input type="text" name="orderType[]" value="<?php echo $singleOrderType["name"]; ?>"></input></p>
							<p><textarea name="orderPredefinedText[]"><?php echo $singleOrderType["predefinedText"] ?></textarea></p>				
						<?php
					}
				?>
				<input type="submit" value="Update"></input>
			</form>
			<form method="post">
				<h1>New order add</h1>
				<p>
				<label>Order type</label>
				<input type="text" name="newOrderType" id="newOrderType"></input>
				</p>
				<p>
				<label>Prdefined text</label>
				<textarea name="newOrderPredefinedText"></textarea>
				</p>
				<input type="submit" value="Add order type"></input>
			</form>
			<h1>Quantities</h1>
			<div id="quantitiesWrapper">
				<?php 
				
					foreach($orderQuantities as $orderQuantities)
					{
						?>
							<p><span><?php echo $orderQuantities; ?> |</span><a href="#deleteLink">Delete</a></p>	
						<?php
					}
				?>
			</div>
			<form method="post">
				<input type="text" name="quantity" id="quantity"></input>
				<input type="submit" value="Add quantity"></input>
			</form>
		<?php
	}
?>