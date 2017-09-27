		<style>
			.order-form-row
			{
				width: 100%;
			}
			.order-form-row input
			{
				width: 50%;
			}
			.order-form-row span
			{
				padding-right: 10px;
				width: 130px;
				display: inline-block;
			}
			.order-form-row label
			{
				display: block;
			}
		</style>
		<?php 
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				processAction("addOrder");
			}
		?>
		<h1>New order</h1>
		<form method="POST">
			<p class="order-form-row">
			   <!-- <label for="url" class="order-form-label">Url</label>-->
				<span>Url</span><input type="text" name="url" id="url"></input>
			</p>
			<p class="order-form-row">
				<!--<label for="keywords" class="order-form-label">Keywords(separate by space)</label>-->
				<span>Keywords</span><input type="text" name="keywords" id="keywords"></input>
			</p>
			<p class="order-form-row">
				<!--<label for="category" class="order-form-label">Category</label>-->
				<span>Category</span>
					<select id="category" name="category">
						<option value="Arts & Humanities">Arts & Humanities</option>
						<option value="Blogs">Blogs</option>
						<option value="Business & Economy">Business & Economy</option>
						<option value="Computers & Internet">Computers & Internet</option>
						<option value="Education">Education</option>
						<option value="Entertainment">Entertainment</option>
						<option value="Health">Health</option>
						<option value="News & Media">News & Media</option>
						<option value="Recreation & Sports">Recreation & Sports</option>
						<option value="Reference">Reference</option>
						<option value="Science and Technology">Science and Technology</option>
						<option value="Shopping">Shopping</option>
						<option value="Society">Society</option>
						<option value="Society">Others</option>
					</select>
			</p>
			<p class="order-form-row">
				<!--<label for="timing" class="order-form-label">Timing</label>-->
				<span>Timing</span>
				<select id="timing" name="timing">
					<option value="2 Days">2 Days</option>
					<option value="2 Days">3 Days</option>
					<option value="2 Days">5 Days</option>
				</select>
			</p>
			<?php 
			
				$orderTypes = get_option("ord_types");
				$orderQuantities = get_option("ord_quantities");
				//var_dump($orderQuantities);
			
			?>
			<p class="order-form-row">
			<!--<label for="order-types">Order types</label>-->
			<span>Order types</span>
				<select name="orderType">
					<?php 
						foreach($orderTypes as $singleOrderType)
						{
							?>
								<option value="<?php echo $singleOrderType["name"]; ?>"><?php echo $singleOrderType["name"]; ?></option>
							<?php 
						}
					?>
				</select>
			</p>
						<p class="order-form-row">
			<!--<label for="order-types">Quantities</label>-->
			<span>Quantities</span>
				<select name="quantity">
					<?php 
						foreach($orderQuantities as $singleQuantity)
						{
							?>
								<option value="<?php echo $singleQuantity; ?>"><?php echo $singleQuantity; ?></option>
							<?php 
						}
					?>
				</select>
			</p>
			<input type="submit" value="Add order"></input>
		</form>