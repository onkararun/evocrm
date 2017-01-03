<?php 
add_action( 'wp_enqueue_scripts', 'wp_custom_scripts');
function wp_custom_scripts() {
	wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), '1.0', true );
}
show_admin_bar(false); //disable toolbar for all the users
function app_output_buffer() {
	ob_start();
} // soi_output_buffer
add_action('init', 'app_output_buffer');
function app_destroy_person( $person_id ) {

	// Include the necessary library to delete a person
	include_once( 'wp-admin/includes/user.php' );
	wp_delete_user( $person_id );
	// Redirect back to the Person listing
	wp_redirect( app_get_permalink_by_slug( 'all', 'person' ) );
	exit;

} // end app_destroy_person

function dvla_api_form(){
	$output = '<div class="wrap">';
	$output.= '<form method="post" class="dvla-search" action="'.home_url().'/add-vehicle/">';
	$output.= '<input type="hidden" name="dvla_hidden" value="Y">';
	$output.= '<p><input type="text" name="dvla_licencePlate" class="regular-text" value="'. isset($licenceplate).'" size="20" required>';
	$output.= '<input type="submit" name="Submit" class="button-primary" value="Search DVLA" />';
	$output.= '</p></form></div>';
	echo $output;
}

function dvla_add_vehicle_form() {
	if(isset($_POST['dvla_hidden'])){
		if($_POST['dvla_hidden'] == 'Y') {
			//Form data sent
			$licenceplate = $_POST['dvla_licencePlate'];
			$response = wp_remote_get( 'https://dvlasearch.appspot.com/DvlaSearch?licencePlate='.$licenceplate.'&apikey=DvlaSearchDemoAccount', array( 'body' => array()) );
			$result = json_decode($response['body']);
			if(count($result) == 1 && isset($result->message)) {
				session_start();
				$_SESSION['dvla_invalide'] = $result->message;
				wp_redirect(home_url().'/vehicles');
				exit;
			} else { 
				dvla_vehicle_form($result, $licenceplate);
			}
		}
	}
} 
function dvla_vehicle_form($result, $licenceplate) {
	$first_reg = strtotime($result->dateOfFirstRegistration);
	$reg_date = date("m/d/Y",$first_reg);
	$motDetails = strtotime(substr($result->motDetails,8));
	$motDate = date("m/d/Y",$motDetails);
	?>
	<form method="post" class="add-vehicle-form" action="<?php echo home_url().'/vehicles/'; ?> ">
		<input type="hidden" name="vehicle-hidden" value="vehicles">
		<div class="vehicle-details">
			<p class="heading">Vechile Details-Stock Information</p>
			<div class="inner">
				<div>
					<label for="">Stock</label>
					<div>(New - will be assign)</div>
				</div>
			<div>
				<label for="">Reg No.</label>
				<div><input type="text" name="regNo" value="<?php echo $licenceplate; ?>" placeholder="123aasf"/></div>
			</div>
			<div class="reg-letter">
				<label for="">Reg Letter</label>
				<div class="select-style">
					<select name="regLetter" id="reg_letter">
						<option value="s">S</option>
						<option value="f">F</option>
						<option value="v">v</option>
						<option value="h">H</option>
						<option value="g">G</option>
					</select>
					<span></span>
				</div>
			</div>
			<div>
				<label for="">Reg Date</label>
				<div>
					<input type="text" id="date_1" class="datepick" name="regDate" value="<?php echo $reg_date; ?>"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Priv Plate</label>
					<div>
						<input type="text" name="privPlate" placeholder="Private Plate" id="priv_plate">
					</div>
				</div>
				<div>
					<label for="">Sale type</label>
					<div class="select-style">
						<select name="saleType" id="sale_type">
							<option value="">Van-used</option>
							<option value="">Van not used</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Vat type</label>
					<div class="select-style">
						<select name="vatType" id="vat_type">
							<option value="">Commercial</option>
							<option value="">illigal</option>
						</select>
					</div>
				</div>
				<div>
					<label for="">Retail Price</label>
					<div><input type="text" name="retailPrice" id="retail_price" value="" placeholder="Enter Vehicle Price"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Make</label>
					<div>
						<input type="text" name="vmake" id="make" value="<?php echo strtolower($result->make); ?>" readonly>
					</div>
				</div>
				<div>
					<label for="">Model</label>
					<div>
						<input type="text" name="vmodel" id="model" value="<?php echo strtolower($result->model); ?>">
					</div>
				</div>
				<div>
					<label for="">Year</label>
					<div>
						<input type="text" name="vyear" id="year" value="<?php echo $result->yearOfManufacture; ?>">
					</div>
				</div>
				<div>
					<label for="">Description</label>
					<input type="text" name="vehDes" placeholder="Vehicle Short Description" id="veh_desc" value="">
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Import</label>
					<div class="select-style">
						<select name="import" id="import">
							<option value="">Yes</option>
							<option value="">No</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Hide Finance</label>
					<div><input type="checkbox" name="hideFinance" id="hide_finance" value="1">Hide finance on website</div>
				</div>
				<div>
					<label for="">VIN/Chassis No.</label>
					<div><input type="text" name="vin" id="vin" value="<?php echo $result->vin; ?>"></div>
				</div>
				<div>
					<label for="">Engine No.</label>
					<div><input type="text" name="engineNo" id="engine_no" value="sdlkja"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Advert Mileage</label>
					<div><input type="text" placeholder="Advert Mileage" name="advertMileage"></div>
				</div>
				<div>
					<label for="">Actual Mileage</label>
					<div><input name="actualMileage" placeholder="Actual Mileage" id="actual_mileage" type="text"></div>
				</div>
				<div>
					<label for="">Ins Group</label>
					<div class="select-style">
						<select name="insGroup" id="ins_group">
							<option value="">34</option>
							<option value="">32</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Colour</label>
					<div class="select-style">
						<select name="colour" id="colour">
						<?php 
							$colours = array('Silver','Red','Green','Black','White','Grey');
							foreach ($colours as $colour) {
								$colour  = strtolower($colour);
								$result_colour = strtolower($result->colour);
								?>
								<option value="<?php echo $colour; ?>" <?php if( ($result_colour) && !empty($result_colour) ) selected($result_colour, $colour); ?>><?php echo esc_attr($colour); ?></option>
						<?php } ?>
						</select>
						<span></span>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Former Oweners (view)</label>
					<div class="select-style">
						<select name="formerOwner" id="">
							<option value="">1</option>
							<option value="">2</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Status</label>
					<div class="select-style">
						<select name="status" id="status">
							<option value="due in 7 days">Due in 7 days</option>
							<option value="due in 14 days">Due in 14 days</option>
							<option value="due in 21 days">Due in 21 days</option>
							<option value="due in 28 days">Due in 21 days</option>
							<option value="on hire">On hire</option>
							<option value="in stock s2">In Stock S2</option>
							<option value="in stock s3">In Stock S3</option>
							<option value="in stock">In Stock</option>
							<option value="in transit">In Transit</option>
							<option value="ordered">Ordered</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Grading</label>
					<div class="select-style">
						<select name="grading" id="grading">
							<option value="">Select</option>
							<option value="">One</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Key Tag</label>
					<div class="select-style">
						<select name="keyTag" id="key_tag">
							<option value="">--</option>
							<option value=""></option>
						</select>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Location</label>
					<div class="select-style">
						<select name="location" id="location">
							<option value="">Location 1</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Current MOT Date</label>
					<div><input type="text" id="date_2" class="datepick" name="motDate1" value="30/09/2011"></div>
				</div>
				<div>
					<label for="">MOT Number</label>
					<div><input type="text" name="motNumber" value="<?php echo $result->mot; ?>"></div>
				</div>
				<div>
					<label for="">Next MOT Date</label>
					<div><input type="text" id="date_3" class="datepick" name="motDate2" value="<?php echo $motDate; ?>"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Motability</label>
					<div class="select-style">
						<select name="motability" id="motability">
							<option value="">NO</option>
							<option value="">YES</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Radio code</label>
					<div><input type="text" placeholder="Radio code" name="radioCode" id="radio_code"></div>
				</div>
				<div>
					<label for="">Alarm code</label>
					<div><input type="text" placeholder="Alarm code" name="alarmCode" id="alarm_code"></div>
				</div>
				<div>
					<label for="">Ig Key No</label>
					<div><input type="text" placeholder="Ig key no" name="igKey" id="ig_key"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Door Key No</label>
					<div><input type="text" placeholder="Door key no" name="doorKey" id="door_key"></div>
				</div>
				<div>
					<label for="">Spare Keys</label>
					<div><input type="checkbox" placeholder="Spare keys" name="spareKey" id="spare_keys" value="1"></div>
				</div>
				<div>
					<label for="">V5</label>
					<div class="select-style">
						<select name="v5" id="v5">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">V5 Number</label>
					<div><input type="text" placeholder="V5 number" name="v5No" id="v5_no"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Tax</label>
					<div class="select-style">
						<select name="tax" id="tax">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">HPI</label>
					<div class="select-style">
						<select name="hpi" id="hpi">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Mileage Check</label>
					<div class="select-style">
						<select name="mileageCheck" id="mileage_check">
							<option value="checked">Checked</option>
							<option value="not checked">Not Checked</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Service Check</label>
					<div class="select-style">
						<select name="serviceCheck" id="service_check">
							<option value="cheked">Checked</option>
							<option value="not checked">Not Checked</option>
						</select>
						<span></span>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Purchase date</label>
					<div><input type="text" placeholder="Purchase date" name="purchaseDate" id="date_4" class="datepick" value="09/07/2011"></div>
				</div>
				<div>
					<label for="">Dealer PI ref</label>
					<div><a href="">Save vechile first</a></div>
				</div>
				<div>
					<label for="">Part-ex ref</label>
					<div></div>
				</div>
				<div>
					<label for="">Purchase price</label>
					<div><input type="text" placeholder="Purchase price" name="purchasePrice" id="purchase_price"></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Supplier Inv No</label>
					<div><input type="text" placeholder="Supplier invoice number" name="supplierInvNo" id="supplier_inv_no"></div>
				</div>
				<div>
					<label for="">Source</label>
					<div class="select-style">
						<select name="source" id="source">
							<option value="">Trade</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Supplier</label>
					<div class="select-style">
						<select name="supplier" id="supplier">
							<option value=""> --Choose-- </option>
							<?php
							$suppliers = array('Full Dealer + Other','Full Dealer','Full Non Dealer','None','Part Dealer','Part Dealer + Other','Part Non Dealer');
							foreach ($suppliers as $supplier) {
								$result_supplier;
								?>
								<option value="<?php echo $supplier; ?>" <?php if( ($result_supplier) && !empty($result_supplier) ) selected($result_supplier, $supplier); ?>><?php echo esc_attr($supplier); ?></option>
							<?php } ?>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Buyer</label>
					<div class="select-style">
						<select name="buyer" id="buyer">
							<option value="">--Choose--</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Hide on Web</label>
					<div>
						<input type="checkbox" name="hideWeb" value="1">
					</div>
				</div>
				<div>
					<label for="">Sale or return</label>
					<div class="select-style">
						<select name="saleOrReturn" id="sale_or_return">
							<option value="">Yes</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Supplier details</label>
					<div>no trade</div>
				</div>
				<div>
					<label for="">Stock comments</label>
					<div><textarea name="stockComments" placeholder="Stock comments" id="stock_comments" cols="30" rows="10"></textarea></div>
				</div>
			</div>
		</div>
		<div class="vehicle-details">
		<p class="heading">Vechile Details-Details</p>
			<div class="inner">
				<div>
					<label for="">History</label>
					<div class="select-style">
						<select name="history" id="history">
							<option value="">--Choose--</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="check">FSH</label>
					<div><input type="checkbox" name="fsh" id="check" value="1"></div>
				</div>
				<div>
					<label for="">VAN</label>
					<div><button>Click Here</button></div>
				</div>
				<div>
					<label for="">BHP</label>
					<div><input type="text" name="bhp" placeholder="BHP" id="bhp" value=""></div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Warrenty</label>
					<div class="select-style">
						<select name="warrenty" id="warrenty">
							<option value="">none</option>
							<option value="balance of dealers">Balance of Dealers</option>
							<option value="balance of makers">Balance of Makers</option>
							<option value="3 months">3 Months</option>
							<option value="6 months">6 Months</option>
							<option value="12 months">12 Months</option>
							<option value="24 months">24 Months</option>
							<option value="36 months">36 Months</option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Engine size</label>
					<div><input type="text" name="engineSize" placeholder="Engine size" id="engine_size" value="<?php echo $result->cylinderCapacity.' /'.$result->co2Emissions ; ?>"></div>
				</div>
				<div>
					<label for="">Fuel type</label>
					<div class="select-style">
						<select name="fuelType" id="fuel_type">
						<?php 
						$fuelTypes = array('Diesel','Petrol');
						foreach ($fuelTypes as $fuelType) {
							$fuelType  = strtolower($fuelType);
							$result_fuel = strtolower($result->fuelType);
							?>
							<option value="<?php echo $fuelType; ?>" <?php if( ($result_fuel) && !empty($result_fuel) ) selected($result_fuel, $fuelType); ?>><?php echo esc_attr($fuelType); ?></option>
						<?php } ?>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Engine type</label>
					<div class="select-style">
						<select name="engineType" id="engine_type">
							<option value="">--choose--</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Transmission</label>
					<div class="select-style">
						<select name="vtransmission" id="transmission">
						<?php 
						$transmissions = array('Automatic','Manual');
						foreach ($transmissions as $transmission) { 
							$transmission  = strtolower($transmission);
							$result_trans = strtolower($result->transmission);
							?>
							<option value="<?php echo $transmission; ?>" <?php if( ($result_trans) && !empty($result_trans) ) selected($result_trans, $transmission); ?>><?php echo esc_attr($transmission); ?></option>
						<?php } ?>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Gears</label>
					<div class="select-style">
						<select name="gears" id="gears">
							<option value="">6</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Drive</label>
					<div class="select-style">
						<select name="drive" id="drive">
							<option value=""> --Choose-- </option>
							<?php
							$drives = array('FWD','RWD','4WD');
							foreach ($drives as $drive) {
								$result_drive;
								?>
								<option value="<?php echo $drive; ?>" <?php if( ($result_drive) && !empty($result_drive) ) selected($result_drive, $drive); ?>><?php echo esc_attr($drive); ?></option>
							<?php } ?>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Body type</label>
					<div class="select-style">
						<select name="bodyType" id="body_type">
							<option value="">Couple</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">Doors</label>
					<div class="select-style">
						<select name="doors" id="doors">
						<?php 
						$doors = array('1','2','3','4','5','6');
						foreach ($doors as $door) { ?>
							<option value="<?php echo $door; ?>" <?php if( ($result->numberOfDoors) && !empty($result->numberOfDoors) ) selected($result->numberOfDoors, $door); ?>><?php echo esc_attr($door); ?></option>
						<?php } ?>
						</select>
						<span></span>
					</div>
				</div>
				<div>
					<label for="">Group</label>
					<div>(+3)</div>
				</div>
				<div>
					<label for="">Trim type</label>
					<div class="select-style">
						<select name="trimType" id="trim_type">
							<option value="">--choose--</option>
							<option value=""></option>
							<span></span>
						</select>
					</div>
				</div>
				<div>
					<label for="">Trim Colour</label>
					<div class="select-style">
						<select name="trimColour" id="trim_colour">
							<option value="">--choose--</option>
							<option value=""></option>
						</select>
						<span></span>
					</div>
				</div>
			</div>
			<div class="inner">
				<div>
					<label for="">YouTube ID</label>
					<div><input name="youtube_id" placeholder="Youtube ID" id="youtube_id" type="text"></div>
				</div>
				<div>
					<label for="">Service Comments</label>
					<div><textarea name="serviceComments" placeholder="Service comments" id="service_comments" cols="30" rows="10"></textarea></div>
				</div>
			</div>
		</div>
		<p class="add-vehicle-button"><input type="submit" name="submit" class="button-primary" value="Add Vehicle" /></p>
	</form>
<?php 
}
function dvla_data_save() {
	if(isset($_POST['vehicle-hidden'])){
		$postTitle = $_POST['regNo'];
		$make = $_POST['vmake'];
		$year = $_POST['vyear'];
		$regLetter = $_POST['regLetter'];
		$regDate = $_POST['regDate'];
		$privPlate = $_POST['privPlate'];
		$saleType = $_POST['saleType'];
		$vatType = $_POST['vatType'];
		$retailPrice = $_POST['retailPrice'];
		$model = $_POST['vmodel'];
		$vehDes = $_POST['vehDes'];
		$import = $_POST['import'];
		$hideFinance = $_POST['hideFinance'];
		$vin = $_POST['vin'];
		$engineNo = $_POST['engineNo'];
		$advertMileage = $_POST['advertMileage'];
		$actualMileage = $_POST['actualMileage'];
		$insGroup = $_POST['insGroup'];
		$colour = strtolower($_POST['colour']);
		$formerOwner = $_POST['formerOwner'];
		$hideWeb = $_POST['hideWeb'];
		$status = $_POST['status'];
		$grading = $_POST['grading'];
		$keyTag = $_POST['keyTag'];
		$location = $_POST['location'];
		$motDate1 = $_POST['motDate1'];
		$motNumber = $_POST['motNumber'];
		$motDate2 = $_POST['motDate2'];
		$motability = $_POST['motability'];
		$radioCode = $_POST['radioCode'];
		$alarmCode = $_POST['alarmCode'];
		$igKey = $_POST['igKey'];
		$vehTax = $_POST['tax'];
		$purchasePrice = $_POST['purchasePrice'];
		$doorKey = $_POST['doorKey'];
		$spareKey = $_POST['spareKey'];
		$v5 = $_POST['v5'];
		$v5No = $_POST['v5No'];
		$hpi = $_POST['hpi'];
		$mileageCheck = $_POST['mileageCheck'];
		$serviceCheck = $_POST['serviceCheck'];
		$purchaseDate = $_POST['purchaseDate'];
		$supplierInvNo = $_POST['supplierInvNo'];
		$source = $_POST['source'];
		$supplier = $_POST['supplier'];
		$buyer = $_POST['buyer'];
		$saleOrReturn = $_POST['saleOrReturn'];
		$stockComments = $_POST['stockComments'];
		$history = $_POST['history'];
		$fsh = $_POST['fsh'];
		$bhp = $_POST['bhp'];
		$warrenty = $_POST['warrenty'];
		$engineSize = $_POST['engineSize'];
		$fuelType = $_POST['fuelType'];
		$engineType = $_POST['engineType'];
		$transmission = $_POST['vtransmission'];
		$gears = $_POST['gears'];
		$drive = $_POST['drive'];
		$bodyType = $_POST['bodyType'];
		$doors = $_POST['doors'];
		$trimType = $_POST['trimType'];
		$trimColour = $_POST['trimColour'];
		$youTube = $_POST['youtube_id'];
		$serviceComments = $_POST['serviceComments'];
		$vehStatus = $_POST['status'];
		global $wpdb;

		$query = $wpdb->prepare(
			'SELECT ID FROM ' . $wpdb->posts . '
			WHERE post_title = %s
			AND post_type = \'listings\'',
			$postTitle
		);
		$wpdb->query( $query );
		if ( $wpdb->num_rows ) {
			echo '<div class="error"><h2>Vehicle with reg no: '.$postTitle.' already exists!!</h2></div>';
			return false;
		} else {
			$current_user = get_current_user_id();
			$listing = array(
				'post_title' => $postTitle,
				'post_content' => '',
				'post_status' => 'publish',
				'post_date' => date('Y-m-d H:i:s'),
				'post_author' => $current_user,
				'post_type' => 'listings'
			);
			$listing_id = wp_insert_post($listing);
			update_post_meta($listing_id, 'make', $make);
			update_post_meta($listing_id, 'model', $model);
			update_post_meta($listing_id, 'year', $year);
			update_post_meta($listing_id, 'body-style', $bodyType);
			update_post_meta($listing_id, 'mileage', $actualMileage);
			update_post_meta($listing_id, 'advert-mileage', $advertMileage);
			update_post_meta($listing_id, 'transmission', $transmission);
			update_post_meta($listing_id, 'condition', $telMob);
			update_post_meta($listing_id, 'price', $retailPrice);
			update_post_meta($listing_id, 'drivetrain', $drive);
			update_post_meta($listing_id, 'engine', $custHis);
			update_post_meta($listing_id, 'exterior-color', $colour);
			update_post_meta($listing_id, 'interior-color', $speNotes);
			update_post_meta($listing_id, 'vin-number', $vin);
			update_post_meta($listing_id, 'secondary_title', $vehDes);
			update_post_meta($listing_id, 'car_sold', $lastName);
			update_post_meta($listing_id, 'stock-number', $stock);
			update_post_meta($listing_id, 'reg-letter', $regLetter);
			update_post_meta($listing_id, 'reg-date', $regDate);
			update_post_meta($listing_id, 'priv-plate', $privPlate);
			update_post_meta($listing_id, 'sale-type', $saleType);
			update_post_meta($listing_id, 'vat-type', $vatType);
			update_post_meta($listing_id, 'import', $import);
			update_post_meta($listing_id, 'engine-number', $engineNo);
			update_post_meta($listing_id, 'engine-type', $engineType);
			update_post_meta($listing_id, 'ins-group', $insGroup);
			update_post_meta($listing_id, 'former-owner', $formerOwner);
			update_post_meta($listing_id, 'grading', $grading);
			update_post_meta($listing_id, 'key-tag', $keyTag);
			update_post_meta($listing_id, 'location', $location);
			update_post_meta($listing_id, 'mot-date1', $motDate1);
			update_post_meta($listing_id, 'mot-umber', $motNumber);
			update_post_meta($listing_id, 'mot-date2', $motDate2);
			update_post_meta($listing_id, 'motability', $motability);
			update_post_meta($listing_id, 'radio-code', $radioCode);
			update_post_meta($listing_id, 'alarm-code', $alarmCode);
			update_post_meta($listing_id, 'ig-key', $igKey);
			update_post_meta($listing_id, 'vehicle-tax', $vehTax);
			update_post_meta($listing_id, 'purchase-price', $purchasePrice);
			update_post_meta($listing_id, 'door-key', $doorKey);
			update_post_meta($listing_id, 'spare-key', $spareKey);
			update_post_meta($listing_id, 'v5', $v5);
			update_post_meta($listing_id, 'v5-no', $v5No);
			update_post_meta($listing_id, 'hpi', $hpi);
			update_post_meta($listing_id, 'mileage-check', $mileageCheck);
			update_post_meta($listing_id, 'service-check', $serviceCheck);
			update_post_meta($listing_id, 'purchase-date', $purchaseDate);
			update_post_meta($listing_id, 'supplier-inv-no', $supplierInvNo);
			update_post_meta($listing_id, 'source', $source);
			update_post_meta($listing_id, 'supplier', $supplier);
			update_post_meta($listing_id, 'buyer', $buyer);
			update_post_meta($listing_id, 'sale-return', $saleOrReturn);
			update_post_meta($listing_id, 'stock-comments', $stockComments);
			update_post_meta($listing_id, 'history', $history);
			update_post_meta($listing_id, 'fsh', $fsh);
			update_post_meta($listing_id, 'hide-web', $fsh);
			update_post_meta($listing_id, 'hide-finance', $fsh);
			update_post_meta($listing_id, 'bhp', $bhp);
			update_post_meta($listing_id, 'warrenty', $warrenty);
			update_post_meta($listing_id, 'engine-size', $engineSize);
			update_post_meta($listing_id, 'fuel-type', $fuelType);
			update_post_meta($listing_id, 'gears', $gears);
			update_post_meta($listing_id, 'doors', $doors);
			update_post_meta($listing_id, 'hpi', $hpi);
			update_post_meta($listing_id, 'trim-type', $trimType);
			update_post_meta($listing_id, 'trim-colour', $trimColour);
			update_post_meta($listing_id, 'youtube-id', $youTube);
			update_post_meta($listing_id, 'service-comments', $serviceComments);
			update_post_meta($listing_id, 'vehicle-status', $vehStatus);
			echo '<div class="error"><h2>'.$postTitle.' vehicle Created!!</h2></div>';
		}
	}
}
function get_vahicles() { 
	vehicle_search_form();
	if(isset($_POST['search-vehicle'])) {

		if ( (isset($_POST['searchMake'])) && !empty($_POST['searchMake']) ) {
		   $meta[] = array(
		    'key' => 'make',
		    'compare' => 'LIKE',
		    'value' => $_POST['searchMake'] );
		} 
		if ( (isset($_POST['searchMinPrice'])) && !empty($_POST['searchMaxPrice']) ) {
		    $meta[] = array(
		    'key' => 'price',
		    'type' => 'numeric',
	        'value' => array($_POST['searchMinPrice'], $_POST['searchMaxPrice']),
	        'compare' => 'BETWEEN' );
		}
		if ( (isset($_POST['searchModel'])) && !empty($_POST['searchModel']) ) {
		    $meta[] = array(
		    'key' => 'model',
		    'value' => $_POST['searchModel'],
		    'compare' => 'LIKE' );
		}
		if ( (isset($_POST['searchAge'])) && !empty($_POST['searchAge']) ) {
		    $meta[] = array(
		    'key' => 'age',
		    'compare' => 'LIKE',
		    'value' => $_POST['searchAge'] );
		}
		if ( (isset($_POST['searchMaxMileage'])) && !empty($_POST['searchMaxMileage']) ) {
		    $meta[] = array(
		    'key' => 'mileage',
		    'compare' => '=',
		    'value' => $_POST['searchMaxMileage'] );
		}
		if ( (isset($_POST['searchBody'])) && !empty($_POST['searchBody']) ) {
		    $meta[] = array(
		    'key' => 'body-style',
		    'compare' => 'LIKE',
		    'value' => $_POST['searchBody'] );
		}
		if ( (isset($_POST['searchGear'])) && !empty($_POST['searchGear']) ) {
		    $meta[] = array(
		    'key' => 'gears',
		    'compare' => 'LIKE',
		    'value' => $_POST['searchGear'] );
		}
		if ( (isset($_POST['searchEngine'])) && !empty($_POST['searchEngine']) ) {
		    $meta[] = array(
		    'key' => 'engine-size',
		    'compare' => 'LIKE',
		    'value' => $_POST['searchEngine'] );
		}
		if ( (isset($_POST['searchColour'])) && !empty($_POST['searchColour']) ) {
		    $meta[] = array(
		    'key' => 'exterior-color',
		    'compare' => 'LIKE',
		    'value' => $_POST['searchColour'] );
		}
		if ( (isset($_POST['searchMinSeats'])) && !empty($_POST['searchMaxSeats']) ) {
		    $meta[] = array(
		    'key' => 'seats',
		    'type' => 'numeric',
	        'value' => array($_POST['searchMinSeats'], $_POST['searchMaxSeats']),
	        'compare' => 'BETWEEN' );
		}
		$args = array(
		'post_type' => 'listings',
        'orderby'=>'date',
        'order'=>'DESC',
        'posts_per_page' => -1,
        'paged' => $paged,
		'meta_query' => $meta
		);
		$args['meta_query']['relation'] = 'AND';
		$wp_query = new WP_Query();
	    $wp_query->query($args);
	} 
	else {
		global $wp_query, $paged;

	    if( get_query_var('paged') ) {
	        $paged = get_query_var('paged');
	    }else if ( get_query_var('page') ) {
	        $paged = get_query_var('page');
	    }else{
	        $paged = 1;
	    }
	    $wp_query = null;
	    $args = array(
	        'post_type' => array("listings"),
	        'orderby'=>'date',
	        'order'=>'DESC',
	        'posts_per_page' => 10,
	        'paged' => $paged
	    );
	    $wp_query = new WP_Query();
	    $wp_query->query( $args );
	}
	if($wp_query->post_count == 0) {
		echo '<div class="post_status">No data found!!</div>';
	}
	else {
		//echo '<div class="post_count">'.$wp_query->post_count.'</div>';
	}
    echo '<div class="search-listing">';
    while ($wp_query->have_posts()) : $wp_query->the_post();
        $post_id = get_the_ID();
        $regNo = get_the_title( $post_id );
        $make = get_post_meta($post_id,'make',true);   	    
    	$model = get_post_meta($post_id,'model',true);
    	$colour = get_post_meta($post_id,'exterior-color',true);
    	$price = get_post_meta($post_id,'price',true);
    	$mileage = get_post_meta($post_id,'mileage',true);
    	$thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
    	$thumbnail = wp_get_attachment_image($thumbnail_id[0]);
    	echo '<div class="row-list">';
    	if(!empty($thumbnail)) {
    		echo "<figure>".$thumbnail."</figure>";
    	} else {
    		echo "<figure class='no-image'>No Image</figure>";;
    	}
    	echo '<div class="row-wrapper">';
    	echo '<div class="col-one">
            	<div class="reg">
	                <span>'.$regNo.'</span>
            	</div>   
        	</div>
        	<div class="col-two">
        	<div class="make">
                <label> Make:</label>
                <span>'.$make.'</span>
        	</div>
        	<div class="model">
                <label> Model:</label>
                <span>'.$model.'</span>
        	</div> 
            <div class="color">
                <label> Colour:</label>
                <span>'.$colour.'</span>
            </div>
            <div class="vehicle-price">
                <label> Price:</label>
                <span>'.$price.'</span>
            </div>
            <div class="milage">
                <label> Milage:</label>
                <span>'.$mileage.'</span>
            </div>    
	        </div>
			</div>
			<div class="button-action"> 
	        	<div class="view-button"><a href ="'.home_url().'/view-vehicle/?pid='.$post_id.'">View</a></div>   
	            <div class="edit-button"><a href ="'.home_url().'/edit-vehicle/?pid='.$post_id.'">Edit</a></div>
	            <div class="gallery-button"><a href ="'.home_url().'/vehicle-gallery/?pid='.$post_id.'">Add Gallery</a></div>
	        	<a href="#" class="button-delete" data-href="'.get_delete_post_link( $post_id ).'" data-toggle="modal" data-target="#confirm-delete">Delete</a>
	        </div> 
	        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			    <div class="modal-dialog">
			        <div class="modal-content">
			            <div class="modal-body">
			               <p>Are you sure you want to delete this vehicle?</p>
			            </div>
			            <div class="modal-footer">
			                <a class="btn btn-danger btn-ok">Delete</a>
			            	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			            </div>
			        </div>
			    </div>
	        </div>   
    		</div>';
    endwhile;
    wp_pagenavi();
    wp_reset_query();
}
function update_vehicle_form($listing_id, $type) { 
	$regNo = get_the_title($listing_id);
	$getMake = get_post_meta($listing_id, 'make', true);
	$getModel = get_post_meta($listing_id, 'model', true);
	$getYear = get_post_meta($listing_id, 'year', true);
	$getBody = get_post_meta($listing_id, 'body-style', true);
	$getMileage = get_post_meta($listing_id, 'mileage', true);
	$getAM = get_post_meta($listing_id, 'advert-mileage', true);
	$getTransmission = get_post_meta($listing_id, 'transmission', true);
	$getCondition = get_post_meta($listing_id, 'condition', true);
	$getPrice = get_post_meta($listing_id, 'price',	true);
	$getDrive = get_post_meta($listing_id, 'drivetrain', true);
	$getEngine = get_post_meta($listing_id, 'engine', true);
	$getExtColour = get_post_meta($listing_id, 'exterior-color', true);
	$getIntColour = get_post_meta($listing_id, 'interior-color', true);
	$getVin = get_post_meta($listing_id, 'vin-number', true);
	$getSecTitle = get_post_meta($listing_id, 'secondary_title', true);
	$getCarSold = get_post_meta($listing_id, 'car_sold', true);
	$getStock = get_post_meta($listing_id, 'stock-number', true);
	$getregLetter = get_post_meta($listing_id, 'reg-letter', true);
	$getRegDate = get_post_meta($listing_id, 'reg-date', true);
	$getPrivPlate = get_post_meta($listing_id, 'priv-plate', true);
	$getSaleType = get_post_meta($listing_id, 'sale-type', true);
	$getVatType = get_post_meta($listing_id, 'vat-type', true);
	$getImport = get_post_meta($listing_id, 'import', true);
	$getEngineNumber = get_post_meta($listing_id, 'engine-number', true);
	$getET = get_post_meta($listing_id, 'engine-type',true);
	$getInsGroup = get_post_meta($listing_id, 'ins-group', true);
	$getFormerOwner = get_post_meta($listing_id, 'former-owner', true);
	$getGrading = get_post_meta($listing_id, 'grading', true);
	$getKeyTag = get_post_meta($listing_id, 'key-tag', true);
	$getLocation = get_post_meta($listing_id, 'location', true);
	$getMD1 = get_post_meta($listing_id, 'mot-date1', true);
	$getMN = get_post_meta($listing_id, 'mot-number', true);
	$getMD2 = get_post_meta($listing_id, 'mot-date2', true);
	$getMotability = get_post_meta($listing_id, 'motability', true);
	$getRadioCode = get_post_meta($listing_id, 'radio-code', true);
	$getAlarmCode = get_post_meta($listing_id, 'alarm-code', true);
	$getIK = get_post_meta($listing_id, 'ig-key', true);
	$getDK = get_post_meta($listing_id, 'door-key', true);
	$getSpareKey = get_post_meta($listing_id, 'spare-key', true);
	$getv5 = get_post_meta($listing_id, 'v5', true);
	$getv5No = get_post_meta($listing_id, 'v5-no', true);
	$getTax = get_post_meta($listing_id, 'vehicle-tax', true);
	$getPP = get_post_meta($listing_id, 'purchase-price',true);
	$gethpi = get_post_meta($listing_id, 'hpi', true);
	$getMileageCheck = get_post_meta($listing_id, 'mileage-check', true);
	$getServiceCheck = get_post_meta($listing_id, 'service-check', true);
	$getPD = get_post_meta($listing_id, 'purchase-date', true);
	$getSIN = get_post_meta($listing_id, 'supplier-inv-no', true);
	$getSource = get_post_meta($listing_id, 'source', true);
	$getSupplier = get_post_meta($listing_id, 'supplier', true);
	$getBuyer = get_post_meta($listing_id, 'buyer', true);
	$getSR = get_post_meta($listing_id, 'sale-return', true);
	$getSC = get_post_meta($listing_id, 'stock-comments', true);
	$getHistory = get_post_meta($listing_id, 'history', true);
	$getfsh = get_post_meta($listing_id, 'fsh', true);
	$getbhp= get_post_meta($listing_id, 'bhp', true);
	$getWarrenty = get_post_meta($listing_id, 'warrenty', true);
	$getES = get_post_meta($listing_id, 'engine-size', true);
	$getFT = get_post_meta($listing_id, 'fuel-type', true);
	$getGears = get_post_meta($listing_id, 'gears', true);
	$getDoors = get_post_meta($listing_id, 'doors', true);
	$getTT = get_post_meta($listing_id, 'trim-type', true);
	$getTC = get_post_meta($listing_id, 'trim-colour', true);
	$getStatus = get_post_meta($listing_id, 'Status', true);
	$getYT = get_post_meta($listing_id, 'youtube-id', true);
	$getFinance = get_post_meta($listing_id, 'hide-finance', true);
	$getWeb = get_post_meta($listing_id, 'hide-web', true);
	$getComments = get_post_meta($listing_id, 'service-comments', true);
	$vehicleKeys = array('reg-no', 'make', 'model', 'year', 'body', 'mileage' , 'advert-mileage', 'transmission', 'condition', 'price', 'drive-train', 'engine', 'exterior-color', 'interior-color', 'vin-number', 'secondary_title', 'car_sold', 'stock-number', 'reg-letter', 'reg-date', 'priv-plate', 'sale-type', 'vat-type', 'import', 'engine-number', 'engine-type', 'ins-group', 'former-owner', 'grading', 'key-tag', 'location', 'mot-date1', 'mot-number', 'mot-date2', 'motability', 'radio-code', 'alarm-code', 'ig-key', 'door-key', 'spare-key', 'v5', 'v5-no', 'vehicle-tax', 'purchase-price', 'hpi', 'mileage-check', 'service-check', 'purchase-date', 'supplier-inv-no', 'source', 'supplier', 'buyer', 'sale-return', 'stock-comments', 'history', 'fsh', 'bhp', 'warrenty', 'engine-size', 'fuel-type', 'gears', 'doors', 'trim-type', 'trim-colour', 'status', 'youtube-id', 'hide-finance', 'hide-web', 'service-comments');
	$vehicleValues = array($regNo, $getMake, $getModel, $getYear, $getBody, $getMileage, $getAM, $getTransmission, $getCondition, $getPrice, $getDrive, $getEngine, $getExtColour, $getIntColour, $getVin, $getSecTitle, $getCarSold, $getStock, $getregLetter, $getRegDate, $getPrivPlate, $getSaleType, $getVatType, $getImport, $getEngineNumber, $getET, $getInsGroup, $getFormerOwner, $getGrading, $getKeyTag, $getLocation, $getMD1, $getMN, $getMD2, $getMotability, $getRadioCode, $getAlarmCode, $getIK, $getDK, $getSpareKey, $getv5, $getv5No, $getTax, $getPP, $gethpi, $getMileageCheck, $getServiceCheck, $getPD, $getSIN, $getSource, $getSupplier, $getBuyer, $getSR, $getSC, $getHistory, $getfsh, $getbhp, $getWarrenty, $getES, $getFT, $getGears, $getDoors, $getTT, $getTC, $getStatus, $getYT, $getFinance, $getWeb, $getComments);
	$vehicleDetails = array_combine($vehicleKeys, $vehicleValues);
	if ($type == 'edit') {
	?>
	<form method="post" class="add-vehicle-form">
	<input type="hidden" name="edit-vehicle" value="editVehicle">
	<div class="vehicle-details">
		<p class="heading">Vechile Details-Stock Information</p>
		<div class="inner">
			<div>
				<label for="">Stock</label>
				<div>(New - will be assign)</div>
			</div>
		<div>
			<label for="">Reg No.</label>
			<div><input type="text" name="regNo" value="<?php echo $regNo; ?>" placeholder="Vehicle Registration Number"></div>
		</div>
		<div class="reg-letter">
			<label for="">Reg Letter</label>
			<div>
			<select name="regLetter" id="reg_letter">
				<?php 
				$fieldArray = array('s','f','v','h','g');
				foreach ($fieldArray as $singleField) {
					$input  = strtolower($singleField);
					$result = strtolower($getregLetter);
					?>
					<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
				<?php } ?>
			</select>
			</div>

		</div>
		<div>
			<label for="">Reg Date</label>
			<div>
				<input type="text" id="date_1" class="datepick" name="regDate" value="<?php echo $getRegDate; ?>"></div>
			</div>
		<div>
			<label for="">Priv Plate</label>
			<div><input type="text" name="privPlate" id="priv_plate" placeholder="Private Plate" value="<?php echo $getPrivPlate; ?>"></div>
		</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Sale type</label>
				<div>
					<select name="saleType" id="sale_type">
						<?php 
						$fieldArray = array('van used','2000km driven');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getSaleType);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Vat type</label>
				<div>
					<select name="vatType" id="vat_type">
						<?php 
						$fieldArray = array('commercial','legal');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getVatType);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Retail Price</label>
				<div><input type="text" name="retailPrice" id="retail_price" value="<?php echo $getPrice; ?>"></div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Make</label>
				<div>
					<input type="text" name="vmake" id="make" value="<?php echo strtolower($getMake); ?>" >
				</div>
			</div>
			<div>
				<label for="">Model</label>
				<div>
					<input type="text" name="vmodel" id="model" value="<?php echo strtolower($getModel); ?>">
				</div>
			</div>
			<div>
				<label for="">Year</label>
				<div>
					<input type="text" name="vyear" id="year" value="<?php echo $getYear; ?>">
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Description</label>
				<input type="text" name="vehDes" id="veh_desc" placeholder="Vehicle Short Desciption" value="<?php echo $getSecTitle; ?>">
			</div>
			<div>
				<label for="">Import</label>
				<div>
					<select name="import" id="import">
						<?php 
						$fieldArray = array('yes','no');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getImport);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Hide Finance</label>
				<div><input type="checkbox" name="hideFinance" id="hide_finance" value="1" <?php echo ($getFinance == 1 ? 'checked' : '');?>>Hide finance on website</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">VIN/Chassis No.</label>
				<div><input type="text" name="vin" id="vin" value="<?php echo $getVin; ?>"></div>
			</div>
			<div>
				<label for="">Engine No.</label>
				<div><input type="text" name="engineNo" id="engine_no" value="<?php echo $getEngineNumber; ?>"></div>
			</div>
			<div>
				<label for="">Advert Mileage</label>
				<div><input type="text" name="advertMileage" value="<?php echo $getAM; ?>"></div>
			</div>
			<div>
				<label for="">Actual Mileage</label>
				<div><input name="actualMileage" id="actual_mileage" type="text" value="<?php echo $getMileage; ?>"></div>
			</div>
			<div>
				<label for="">Ins Group</label>
				<div>
					<select name="insGroup" id="ins_group">
						<?php 
						$fieldArray = array('34','32');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getInsGroup);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Colour</label>
				<div>
					<select name="colour" id="colour">
					<?php 
						$colours = array('silver','red','green','black','white','grey');
						foreach ($colours as $colour) {
							$colour  = strtolower($colour);
							$result_colour = strtolower($getExtColour);
							?>
							<option value="<?php echo $colour; ?>" <?php if( ($result_colour) && !empty($result_colour) ) selected($result_colour, $colour); ?>><?php echo esc_attr($colour); ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Former Oweners (view)</label>
				<div>
					<select name="formerOwner" id="">
						<?php 
						$fieldArray = array('1','2');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getFormerOwner);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
					<input type="checkbox" name="hideWeb" value="1" <?php echo ($getWeb == 1 ? 'checked' : '');?>>Hide on web
				</div>
			</div>
			<div>
				<label for="">Status</label>
				<div>
					<select name="status" id="status">
						<?php 
						$fieldArray = array('due in 7 days','due in 14 days','due in 21 days','due in 28 days','in stock s2','in stock s3','in stock','in transit','ordered');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getStatus);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Grading</label>
				<div>
					<select name="grading" id="grading">
						<option value="">Select</option>
						<?php 
						$fieldArray = array('grade 1','grade 2','grade 3');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getGrading);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Key Tag</label>
				<div>
					<select name="keyTag" id="key_tag">
						<option value="">Select</option>
						<?php 
						$fieldArray = array('yes','no');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getKeyTag);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Location</label>
				<div>
					<select name="location" id="location">
						<option value="">Select Location</option>
						<?php 
						$fieldArray = array('Location 1','Location 2','Location 3');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getLocation);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			
		</div>
		<div class="inner">
			<div>
				<label for="">Current MOT Date</label>
				<div><input type="text" id="date_2" class="datepick" name="motDate1" value="<?php echo $getMD1; ?>"></div>
			</div>
			<div>
				<label for="">MOT Number</label>
				<div><input type="text" name="motNumber" value="<?php echo $getMN; ?>"></div>
			</div>
			<div>
				<label for="">Next MOT Date</label>
				<div><input type="text" id="date_3" class="datepick" name="motDate2" value="<?php echo $getMD2; ?>"></div>
			</div>
			<div>
				<label for="">Motability</label>
				<div>
					<select name="motability" id="motability">
						<?php 
						$fieldArray = array('yes','no');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getMotability);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Radio code</label>
				<div><input type="text" name="radioCode" id="radio_code" value="<?php echo $getRadioCode; ?>"></div>
			</div>
			<div>
				<label for="">Alarm code</label>
				<div><input type="text" name="alarmCode" id="alarm_code" value="<?php echo $getAlarmCode; ?>"></div>
			</div>
			<div>
				<label for="">Ig Key No</label>
				<div><input type="text" name="igKey" id="ig_key" value="<?php echo $getIK; ?>"></div>
			</div>
			<div>
				<label for="">Door Key No</label>
				<div><input type="text" name="doorKey" id="door_key" value="<?php echo $getDK; ?>"></div>
			</div>
			<div>
				<label for="">Spare Keys</label>
				<div><input type="checkbox" name="spareKey" id="spare_keys" value="1" <?php echo ($getSpareKey == 1 ? 'checked' : '');?>></div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">V5</label>
				<div>
					<select name="v5" id="v5">
						<?php 
						$fieldArray = array('yes','no');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getv5);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">V5 Number</label>
				<div><input type="text" name="v5No" id="v5_no" value="<?php echo $getv5No; ?>"></div>
			</div>
			<div>
				<label for="">Tax</label>
				<div>
					<select name="tax" id="tax">
						<?php 
						$fieldArray = array('yes','no');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getTax);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">HPI</label>
				<div>
					<select name="hpi" id="hpi">
						<?php 
						$fieldArray = array('yes','no');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($gethpi);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Mileage Check</label>
				<div>
					<select name="mileageCheck" id="mileage_check">
						<?php 
						$fieldArray = array('checked','not checked');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getMileageCheck);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Service Check</label>
				<div>
					<select name="serviceCheck" id="service_check">
						<?php 
						$fieldArray = array('checked','not checked');
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getServiceCheck);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Purchase date</label>
				<div><input type="text" name="purchaseDate" id="date_4" class="datepick" value="<?php echo $getPD; ?>"></div>
			</div>
			<div>
				<label for="">Dealer PI ref</label>
				<div><a href="">Save vechile first</a></div>
			</div>
			<div>
				<label for="">Part-ex ref</label>
				<div></div>
			</div>
			<div>
				<label for="">Purchase price</label>
				<div><input type="text" name="purchasePrice" id="purchase_price" value="<?php echo $getPP; ?>"></div>
			</div>
			<div>
				<label for="">Supplier Inv No</label>
				<div><input type="text" name="supplierInvNo" id="supplier_inv_no" value="<?php echo $getSIN; ?>"></div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Source</label>
				<div>
					<select name="source" id="source">
						<option value=""> --Choose-- </option>
						<?php
						$fieldArray = array('trade','Non trade'); 
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getSource);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Supplier</label>
				<div>
					<select name="supplier" id="supplier">
						<option value=""> --Choose-- </option>
						<?php
						$fieldArray = array('full dealer + other','full dealer','full non dealer','none','part dealer','part dealer + other','part non dealer'); 
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getSupplier);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Buyer</label>
				<div>
					<select name="buyer" id="buyer">
						<option value=""> --Choose-- </option>
						<?php
						$fieldArray = array('buyer 1','buyer2'); 
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getBuyer);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Sale or return</label>
				<div>
					<select name="saleOrReturn" id="sale_or_return">
						<?php
						$fieldArray = array('yes','no'); 
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getSR);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Supplier details</label>
				<div>no trade</div>
			</div>
			<div>
				<label for="">Stock comments</label>
				<div>
					<textarea name="stockComments" id="stock_comments" cols="30" rows="10"><?php echo $getSC; ?></textarea>
				</div>
			</div>
		</div>
		
	</div>
	<div class="vehicle-details">
	<p class="heading">Vechile Details-Details</p>
		<div class="inner">
			<div>
				<label for="">History</label>
				<div><select name="history" id="history">
						<option value="">--Choose--</option>
						<?php
						$fieldArray = array('yes','no'); 
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getHistory);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="check">FSH</label>
				<div><input type="checkbox" name="fsh" id="check" value="1" <?php echo ($getfsh == 1 ? 'checked' : '');?>></div>
			</div>
			<div>
				<label for="">VAN</label>
				<div><button>Click Here</button></div>
			</div>
			<div>
				<label for="">BHP</label>
				<div><input type="text" name="bhp" id="bhp" value="<?php echo $getbhp; ?>"></div>
			</div>
			<div>
				<label for="">Warrenty</label>
				<div>
					<select name="warrenty" id="warrenty">
						<option value="">none</option>
					
					<?php
					$fieldArray = array('balance of dealers','balance of makers','3 months','6 months','12 months','24 months','36 months'); 
						foreach ($fieldArray as $singleField) {
							$input  = strtolower($singleField);
							$result = strtolower($getWarrenty);
							?>
							<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Engine size</label>
				<div><input type="text" name="engineSize" id="engine_size" value="<?php echo $getES ?>"></div>
			</div>
			<div>
				<label for="">Fuel type</label>
				<div>
					<select name="fuelType" id="fuel_type">
					<?php 
					$fuelTypes = array('Diesel','Petrol');
					foreach ($fuelTypes as $fuelType) {
						$fuelType  = strtolower($fuelType);
						$result_fuel = strtolower($getFT);
						?>
						<option value="<?php echo $fuelType; ?>" <?php if( ($result_fuel) && !empty($result_fuel) ) selected($result_fuel, $fuelType); ?>><?php echo esc_attr($fuelType); ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Engine type</label>
				<div><select name="engineType" id="engine_type">
						<option value="">--choose--</option>
						<?php
							$fieldArray = array('1','2','3','6','12','24','36'); 
							foreach ($fieldArray as $singleField) {
								$input  = strtolower($singleField);
								$result = strtolower($getET);
								?>
								<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Transmission</label>
				<div>
					<select name="vtransmission" id="transmission">
					<?php 
						$transmissions = array('Automatic','Manual');
						foreach ($transmissions as $transmission) { 
							$transmission  = strtolower($transmission);
							$result_trans = strtolower($getTransmission);
							?>
							<option value="<?php echo $transmission; ?>" <?php if( ($result_trans) && !empty($result_trans) ) selected($result_trans, $transmission); ?>><?php echo esc_attr($transmission); ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Gears</label>
				<div><select name="gears" id="gears">
						<?php
							$fieldArray = array('1','2','3','6','12','24','36'); 
							foreach ($fieldArray as $singleField) {
								$input  = strtolower($singleField);
								$result = strtolower($getGears);
								?>
								<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Drive</label>
				<div>
					<select name="drive" id="drive">
						<option value=""> --Choose-- </option>
						<?php
						$drives = array('FWD','RWD','4WD');
						foreach ($drives as $drive) {
							$result_drive = $getDrive;
							?>
							<option value="<?php echo $drive; ?>" <?php if( ($result_drive) && !empty($result_drive) ) selected($result_drive, $drive); ?>><?php echo esc_attr($drive); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Body type</label>
				<div>
					<select 202name="bodyType" id="body_type">
						<?php
							$fieldArray = array('1','2','3','6','12','24','36'); 
							foreach ($fieldArray as $singleField) {
								$input  = strtolower($singleField);
								$result = strtolower($getBody);
								?>
								<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Doors</label>
				<div>
					<select name="doors" id="doors">
					<?php 
					$doors = array('1','2','3','4','5','6');
					foreach ($doors as $door) { ?>
						<option value="<?php echo $door; ?>" <?php if( ($getDoors) && !empty($getDoors) ) selected($getDoors, $door); ?>><?php echo esc_attr($door); ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Group</label>
				<div>(+3)</div>
			</div>
			<div>
				<label for="">Trim type</label>
				<div><select name="trimType" id="trim_type">
						<option value="">--choose--</option>
						<?php
							$fieldArray = array('1','2','3','6','12','24','36'); 
							foreach ($fieldArray as $singleField) {
								$input  = strtolower($singleField);
								$result = strtolower($getTT);
								?>
								<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">Trim Colour</label>
				<div><select name="trimColour" id="trim_colour">
						<option value="">--choose--</option>
						<?php
							$fieldArray = array('1','2','3','6','12','24','36'); 
							foreach ($fieldArray as $singleField) {
								$input  = strtolower($singleField);
								$result = strtolower($getTC);
								?>
								<option value="<?php echo $input; ?>" <?php if( ($result) && !empty($result) ) selected($result, $input); ?>><?php echo esc_attr($input); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<label for="">YouTube ID</label>
				<div><input name="youtube_id" id="youtube_id" type="text" value="<?php echo $getYT; ?>"></div>
			</div>
		</div>
		<div class="inner">
			<div>
				<label for="">Service Comments</label>
				<div><textarea name="serviceComments" id="service_comments" cols="30" rows="10"><?php echo $getComments; ?></textarea></div>
			</div>
		</div>
	</div>
	<p class="edit-vehicle-button"><input type="submit" name="submit" class="button-primary" value="Update Vehicle" /></p>
</form>
<?php
 update_vehicle($listing_id); 
}elseif($type == 'view') {
	return $vehicleDetails;
}
	
}
function update_vehicle($post_id) {
	if(isset($_POST['edit-vehicle'])){
		$postTitle = $_POST['regNo'];
		$make = $_POST['vmake'];
		$year = $_POST['vyear'];
		$regLetter = $_POST['regLetter'];
		$regDate = $_POST['regDate'];
		$privPlate = $_POST['privPlate'];
		$saleType = $_POST['saleType'];
		$vatType = $_POST['vatType'];
		$retailPrice = $_POST['retailPrice'];
		$model = $_POST['vmodel'];
		$vehDes = $_POST['vehDes'];
		$import = $_POST['import'];
		$hideFinance = $_POST['hideFinance'];
		$vin = $_POST['vin'];
		$engineNo = $_POST['engineNo'];
		$advertMileage = $_POST['advertMileage'];
		$actualMileage = $_POST['actualMileage'];
		$insGroup = $_POST['insGroup'];
		$colour = strtolower($_POST['colour']);
		$formerOwner = $_POST['formerOwner'];
		$hideWeb = $_POST['hideWeb'];
		$status = $_POST['status'];
		$grading = $_POST['grading'];
		$keyTag = $_POST['keyTag'];
		$location = $_POST['location'];
		$motDate1 = $_POST['motDate1'];
		$motNumber = $_POST['motNumber'];
		$motDate2 = $_POST['motDate2'];
		$motability = $_POST['motability'];
		$radioCode = $_POST['radioCode'];
		$alarmCode = $_POST['alarmCode'];
		$igKey = $_POST['igKey'];
		$vehTax = $_POST['tax'];
		$purchasePrice = $_POST['purchasePrice'];
		$doorKey = $_POST['doorKey'];
		$spareKey = $_POST['spareKey'];
		$v5 = $_POST['v5'];
		$v5No = $_POST['v5No'];
		$hpi = $_POST['hpi'];
		$mileageCheck = $_POST['mileageCheck'];
		$serviceCheck = $_POST['serviceCheck'];
		$purchaseDate = $_POST['purchaseDate'];
		$supplierInvNo = $_POST['supplierInvNo'];
		$source = $_POST['source'];
		$supplier = $_POST['supplier'];
		$buyer = $_POST['buyer'];
		$saleOrReturn = $_POST['saleOrReturn'];
		$stockComments = $_POST['stockComments'];
		$history = $_POST['history'];
		$fsh = $_POST['fsh'];
		$bhp = $_POST['bhp'];
		$warrenty = $_POST['warrenty'];
		$engineSize = $_POST['engineSize'];
		$fuelType = $_POST['fuelType'];
		$engineType = $_POST['engineType'];
		$transmission = $_POST['vtransmission'];
		$gears = $_POST['gears'];
		$drive = $_POST['drive'];
		$bodyType = $_POST['bodyType'];
		$doors = $_POST['doors'];
		$trimType = $_POST['trimType'];
		$trimColour = $_POST['trimColour'];
		$youTube = $_POST['youtube_id'];
		$serviceComments = $_POST['serviceComments'];
		$vehStatus = $_POST['status'];
		$updateVehicle = array(
			'post_title' => $postTitle,
			'ID' => $post_id,
		);
		$listing_id = wp_update_post($updateVehicle);
		update_post_meta($listing_id, 'make', $make);
		update_post_meta($listing_id, 'model', $model);
		update_post_meta($listing_id, 'year', $year);
		update_post_meta($listing_id, 'body-style', $bodyType);
		update_post_meta($listing_id, 'mileage', $actualMileage);
		update_post_meta($listing_id, 'advert-mileage', $advertMileage);
		update_post_meta($listing_id, 'transmission', $transmission);
		update_post_meta($listing_id, 'condition', $telMob);
		update_post_meta($listing_id, 'price', $retailPrice);
		update_post_meta($listing_id, 'drivetrain', $drive);
		update_post_meta($listing_id, 'exterior-color', $colour);
		update_post_meta($listing_id, 'interior-color', $speNotes);
		update_post_meta($listing_id, 'vin-number', $vin);
		update_post_meta($listing_id, 'secondary_title', $vehDes);
		update_post_meta($listing_id, 'car_sold', $lastName);
		update_post_meta($listing_id, 'stock-number', $stock);
		update_post_meta($listing_id, 'reg-letter', $regLetter);
		update_post_meta($listing_id, 'reg-date', $regDate);
		update_post_meta($listing_id, 'priv-plate', $privPlate);
		update_post_meta($listing_id, 'sale-type', $saleType);
		update_post_meta($listing_id, 'vat-type', $vatType);
		update_post_meta($listing_id, 'import', $import);
		update_post_meta($listing_id, 'engine-number', $engineNo);
		update_post_meta($listing_id, 'engine-type', $engineType);
		update_post_meta($listing_id, 'ins-group', $insGroup);
		update_post_meta($listing_id, 'former-owner', $formerOwner);
		update_post_meta($listing_id, 'grading', $grading);
		update_post_meta($listing_id, 'key-tag', $keyTag);
		update_post_meta($listing_id, 'location', $location);
		update_post_meta($listing_id, 'mot-date1', $motDate1);
		update_post_meta($listing_id, 'mot-umber', $motNumber);
		update_post_meta($listing_id, 'mot-date2', $motDate2);
		update_post_meta($listing_id, 'motability', $motability);
		update_post_meta($listing_id, 'radio-code', $radioCode);
		update_post_meta($listing_id, 'alarm-code', $alarmCode);
		update_post_meta($listing_id, 'ig-key', $igKey);
		update_post_meta($listing_id, 'vehicle-tax', $vehTax);
		update_post_meta($listing_id, 'purchase-price', $purchasePrice);
		update_post_meta($listing_id, 'door-key', $doorKey);
		update_post_meta($listing_id, 'spare-key', $spareKey);
		update_post_meta($listing_id, 'v5', $v5);
		update_post_meta($listing_id, 'v5-no', $v5No);
		update_post_meta($listing_id, 'hpi', $hpi);
		update_post_meta($listing_id, 'mileage-check', $mileageCheck);
		update_post_meta($listing_id, 'service-check', $serviceCheck);
		update_post_meta($listing_id, 'purchase-date', $purchaseDate);
		update_post_meta($listing_id, 'supplier-inv-no', $supplierInvNo);
		update_post_meta($listing_id, 'source', $source);
		update_post_meta($listing_id, 'supplier', $supplier);
		update_post_meta($listing_id, 'buyer', $buyer);
		update_post_meta($listing_id, 'sale-return', $saleOrReturn);
		update_post_meta($listing_id, 'stock-comments', $stockComments);
		update_post_meta($listing_id, 'history', $history);
		update_post_meta($listing_id, 'fsh', $fsh);
		update_post_meta($listing_id, 'hide-web', $hideWeb);
		update_post_meta($listing_id, 'hide-finance', $hideFinance);
		update_post_meta($listing_id, 'bhp', $bhp);
		update_post_meta($listing_id, 'warrenty', $warrenty);
		update_post_meta($listing_id, 'engine-size', $engineSize);
		update_post_meta($listing_id, 'fuel-type', $fuelType);
		update_post_meta($listing_id, 'gears', $gears);
		update_post_meta($listing_id, 'doors', $doors);
		update_post_meta($listing_id, 'hpi', $hpi);
		update_post_meta($listing_id, 'trim-type', $trimType);
		update_post_meta($listing_id, 'trim-colour', $trimColour);
		update_post_meta($listing_id, 'youtube-id', $youTube);
		update_post_meta($listing_id, 'service-comments', $serviceComments);
		update_post_meta($listing_id, 'vehicle-status', $vehStatus);
		if ( is_wp_error( $listing_id ) ) {
     		echo $listing_id->get_error_message();
		}
		else {
			session_start();
			$_SESSION['vehicle-updated'] = $postTitle;
			wp_redirect(home_url().'/vehicles/');
			exit();
		}
	}
}
function vehicle_search_form() {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$filters = array(
			    "make" => $_POST['searchMake'],
			    "fuel" => $_POST['searchFuel'],
			    "minPrice" => $_POST['searchMinPrice'],
			    "maxPrice" => $_POST['searchMaxPrice'],
			    "model" => $_POST['searchModel'],
			    "age" => $_POST['searchAge'],
			    "maxMileage" => $_POST['searchMaxMileage'],
			    "body" => $_POST['searchBody'],
			    "gear" => $_POST['searchGear'],
			    "engine" => $_POST['searchEngine'],
			    "colour" => $_POST['searchColour'],
			    "minSeats" => $_POST['searchMinSeats'],
			    "maxSeats" => $_POST['searchMaxSeats'],
			    "keyword" => $_POST['searchKeyword']
			);
	}
	$search = '<form  id="vfilters" method="post" action="" class="vehicle-filters">';
	$search.= '<input type="hidden" name="search-vehicle" value="searchVahicle">';
	$search.= '<div class="vehicle-form-first-row">';

	$types = array('volkswagen','Porsche');
	$type = isset($filters['make']) && in_array($filters['make'],$types)?$filters['make']:'Make (any)';
	$search.= '<select name="searchMake" id="search-make">';
	$search.= '<option value=""> Make (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$search.= '<input type=text id="vehicle-search" placeholder="Fuel Type (any)" name="searchFuel" value='.$filters['fuel'].'>';

	$types = array('5000','10000', '50000', '100000');
	$type = isset($filters['minPrice']) && in_array($filters['minPrice'],$types)?$filters['minPrice']:'';
	$search.= '<select name="searchMinPrice" id="search-min-price">';
	$search.= '<option value=""> Min Price </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$types = array('5000','10000', '50000', '100000');
	$type = isset($filters['maxPrice']) && in_array($filters['maxPrice'],$types)?$filters['maxPrice']:'';
	$search.= '<select name="searchMaxPrice" id="search-max-price">';
	$search.= '<option value=""> Max Price </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$search.= '</div>';
	$search.= '<div class="vehicle-form-second-row">';
	$search.= '<input type=text id="vehicle-search-model" placeholder="Model (any)" name="searchModel" value='.$filters['model'].'>';
	
	$types = array('10','12', '15', '20');
	$type = isset($filters['age']) && in_array($filters['age'],$types)?$filters['age']:'';
	$search.= '<select name="searchAge" id="search-age">';
	$search.= '<option value=""> Age (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$types = array('10','12', '15', '17', '20', '22', '25');
	$type = isset($filters['age']) && in_array($filters['age'],$types)?$filters['age']:'';
	$search.= '<select name="searchMaxMileage" id="search-max-mileage">';
	$search.= '<option value=""> Max Mileage </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$search.= '</div>';
	$search.= '<div class="vehicle-form-third-row">';

	$types = array('volkswagen','porsche');
	$type = isset($filters['body']) && in_array($filters['body'],$types)?$filters['body']:'';
	$search.= '<select name="searchBody" id="search-body">';
	$search.= '<option value=""> Body Type (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$types = array('gear box 5rx','gear box 12rx');
	$type = isset($filters['gear']) && in_array($filters['gear'],$types)?$filters['gear']:'';
	$search.= '<select name="searchGear" id="search-gearbox">';
	$search.= '<option value=""> Gearbox (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$types = array('1968cc','2268cc');
	$type = isset($filters['engine']) && in_array($filters['engine'],$types)?$filters['engine']:'';
	$search.= '<select name="searchEngine" id="search-engine">';
	$search.= '<option value=""> Engine Size (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$search.= '</div>';
	$search.= '<div class="vehicle-form-fourth-row">';
	
	$types = array('silver','red', 'green', 'black', 'white', 'grey');
	$type = isset($filters['colour']) && in_array($filters['colour'],$types)?$filters['colour']:'';
	$search.= '<select name="searchColour" id="search-colour">';
	$search.= '<option value=""> Colour (all) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';
	
	$types = array('2','5', '7', '9');
	$type = isset($filters['minSeats']) && in_array($filters['minSeats'],$types)?$filters['minSeats']:'';
	$search.= '<select name="searchMinSeats" id="search-min-seats">';
	$search.= '<option value=""> Min Seats (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$types = array('2','5', '7', '9');
	$type = isset($filters['maxSeats']) && in_array($filters['maxSeats'],$types)?$filters['minSeats']:'';
	$search.= '<select name="searchMaxSeats" id="search-max-seats">';
	$search.= '<option value=""> Max Seats (any) </option>';
	foreach($types as $option) {
	    $search.= '<option value="'.$option.'"'.(strcmp($option,$type)==0?' selected="selected"':'').'>'.$option.'</option>';
	}
	$search.= '</select>';

	$search.= '</div>';
	$search.= '<div class="vehicle-form-last-row">';
	$search.= '<input type=text id="search-key" placeholder="Add keyword: e.g white transit tips" name="searchKeyword" value='.$filters['keyword'].'>';
	$search.= '<input type="submit" name="vehicle-search-form" id="vehicle-submit" value="Search"/></div></form>';
	echo $search;
} 


function dvla_api_errors() {
	if( isset($_SESSION["dvla_invalide"]) ) {
		echo '<div class="error"><h2>'.$_SESSION["dvla_invalide"].'</h2></div>';
		unset($_SESSION["dvla_invalide"]);
	}
	if( isset($_SESSION["customerCreated"]) ) {
		echo '<div class="error"><h2>'.$_SESSION["customerCreated"].' user Created!!</h2></div>';
		unset($_SESSION["customerCreated"]);
	}
	if( isset($_SESSION["customerExists"]) ) {
		echo '<div class="error"><h2>Customer with Post Code: '.$_SESSION["customerExists"].' already exists</h2></div>';
		unset($_SESSION["customerExists"]);
	}
	if( isset($_SESSION["customerUpdated"]) ) {
		echo '<div class="updated"><h2>Customer '.$_SESSION["customerUpdated"].' has been Updated!!</h2></div>';
		unset($_SESSION["customerUpdated"]);
	}
	if( isset($_SESSION["vehicle-updated"]) ) {
		echo '<div class="updated"><h2>Vehicle with Reg No: '.$_SESSION["vehicle-updated"].' has been Updated!!</h2></div>';
		unset($_SESSION["vehicle-updated"]);
	}
}

/************* Customers Page ***********/

function get_customers() {
	customer_search_form();
	if(isset($_POST['search-customer']) && $_POST['searchKey'] != ''){
		$searchKey = $_POST['searchKey'];
		$args = array(
		'post_type' => 'vehicle-customer',
        'orderby'=>'date',
        'order'=>'DESC',
        'posts_per_page' => -1,
        'paged' => $paged,
		'meta_query' => array(
				'relation' => 'OR',
				array(
				'key'     => 'wpcf-first-name',
				'value'   => $searchKey,
				'compare' => 'LIKE',
				),
	        	array(
	                'key' => 'wpcf-surname',
	                'value' => $searchKey,
	                'compare' => 'LIKE',
	            ),
	        	array(
	                'key' => 'wpcf-company',
	                'value' => $searchKey,
	                'compare' => 'LIKE',
	            ),
			),
		);
		$wp_query = new WP_Query();
	    $wp_query->query($args);
	} 
	else {
		global $wp_query, $paged;

	    if( get_query_var('paged') ) {
	        $paged = get_query_var('paged');
	    }else if ( get_query_var('page') ) {
	        $paged = get_query_var('page');
	    }else{
	        $paged = 1;
	    }
	    $wp_query = null;
	    $args = array(
	        'post_type' => array("vehicle-customer"),
	        'orderby'=>'date',
	        'order'=>'DESC',
	        'posts_per_page' => 10,
	        'paged' => $paged
	    );
	    $wp_query = new WP_Query();
	    $wp_query->query( $args );
	}
    echo '<a class="add_button" href="'.home_url().'/add-customer/">Add New Customer</a>';
    echo '<table class="customer-details"><th>Fisrt Name</th><th>Last Name</th><th>Company Name</th><th>Telephone</th><th>Email</th><th>VAT</th>';
    while ($wp_query->have_posts()) : $wp_query->the_post();
        $post_id = get_the_ID();
        $vat = get_the_title( $post_id );
        $firstName = get_post_meta($post_id,'wpcf-first-name',true);   	    
    	$middleName = get_post_meta($post_id,'wpcf-middle-name',true);
    	$surname = get_post_meta($post_id,'wpcf-surname',true);
    	$address = get_post_meta($post_id,'wpcf-address',true);
    	$mobNo = get_post_meta($post_id,'wpcf-telephone-mobile',true);
    	$email = get_post_meta($post_id,'wpcf-email',true);
    	$compName = get_post_meta($post_id,'wpcf-company',true);
    	$purHis = get_post_meta($post_id,'wpcf-customer-purchase-history',true);
    	$secQue = get_post_meta($post_id,'wpcf-secret-question',true);
    	$spNotes = get_post_meta($post_id, 'wpcf-special-notes',true);
    	echo '<tr><td>'.$firstName.'</td><td>'.$surname .'</td><td>'.$compName .'</td><td>'.$mobNo.'</td><td>'.$email.'</td><td>'.$vat.'</td></tr>';
    	echo '<tr><td class="edit_button"><a href ="'.home_url().'/edit-customer/?pid='.$post_id.'">Edit</a></td>';
    	echo '<td class="edit_button"><a href="#" data-href="'.get_delete_post_link( $post_id ).'" data-toggle="modal" data-target="#confirm-delete">Delete</a>';
    	echo "</td><td></td><td></td><td></td><td></td></tr>";
    	echo '<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			    <div class="modal-dialog">
			        <div class="modal-content">
			            <div class="modal-body">
			               <p>Are you sure you want to delete this customer?</p>
			            </div>
			            <div class="modal-footer">
			                <a class="btn btn-danger btn-ok">Delete</a>
			            	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			            </div>
			        </div>
			    </div>
			</div>';
    endwhile;
    echo "</table>";
    wp_pagenavi();
    wp_reset_query();
}

function add_customer_form() {
	$customerForm = '<form method="post" action="'.home_url().'/add-customer/">';
	$customerForm.= '<div class="vehicle-details"><input type="hidden" name="customer-hidden" value="customers">';
	$customerForm.= '<p class="heading">Add New Customer</p>';
	$customerForm.=	'<div class="inner">';
	$customerForm.= '<div><label for="">First Name</label><div>';
	$customerForm.= '<input type="text" name="firstName" value="" placeholder="First Name"/></div></div>';
	$customerForm.= '<div><label for="">Middle Name</label><div>';
	$customerForm.= '<input type="text" name="middleName" value="" placeholder="Middle Name"/></div></div>';
	$customerForm.= '<div><label for="">Last Name</label><div>';
	$customerForm.= '<input type="text" name="lastName" value="" placeholder="Last Name"/></div></div></div>';
	$customerForm.=	'<div class="inner">';
	$customerForm.= '<div><label for="">Telephone Mobile</label><div>';
	$customerForm.= '<input type="text" name="telMob" value="" placeholder="Telephone/Mobile Number "/></div></div>';
	$customerForm.= '<div><label for="">Email</label><div>';
	$customerForm.= '<input type="email" name="custEmail" value="" placeholder="Email Address"/></div></div>';
	$customerForm.= '<div><label for="">Vat Registration Number</label><div>';
	$customerForm.= '<input type="text" name="vat" value="" placeholder="Vat Registration Number"/></div></div></div>';
	$customerForm.=	'<div class="inner">';
	$customerForm.= '<div><label for="">Company Name</label><div class="company-name">';
	$customerForm.= '<input type="text" id="" name="compName" value="" placeholder="Company Name"/></div></div></div>';
	$customerForm.=	'<div class="inner">';
	$customerForm.= '<div><label for="">Address</label><div>';
	$customerForm.= '<textarea name="address" value="" placeholder="Customer Address" cols="15" rows="5"></textarea></div></div>';
	$customerForm.= '<div><label for="">Secret Question</label><div>';
	$customerForm.= '<textarea name="secQue" value="" placeholder="Secret Question" cols="15" rows="5"></textarea></div></div></div>';
	$customerForm.=	'<div class="inner">';
	$customerForm.= '<div><label for="">Customer Purchase History</label><div>';
	$customerForm.= '<textarea name="custHis" value="" placeholder="Customer Purchase History" cols="30" rows="10"></textarea></div></div>';
	$customerForm.= '<div><label for="">Special Notes</label><div>';
	$customerForm.= '<textarea name="speNotes" value="" placeholder="Special Notes" cols="30" rows="10"></textarea></div></div></div>';
	$customerForm.= '<p class="button-submit"><input type="submit" name="submit" class="add-customer" value="Add Customer" /></p>';
	echo $customerForm;
}

function add_customer() {
	if(isset($_POST['customer-hidden'])){
		if ($_POST['customer-hidden'] == 'customers') {
		$postTitle = $_POST['vat']; 
		$firstName = $_POST['firstName'];
		$middleName = $_POST['middleName'];
		$lastName = $_POST['lastName'];
		$telMob = $_POST['telMob'];
		$custEmail = $_POST['custEmail'];
		$compName = $_POST['compName'];
		$address = $_POST['address'];
		$secQue = $_POST['secQue'];
		$custHis = $_POST['custHis'];
		$speNotes = $_POST['speNotes'];
		$secQue = $_POST['secQue'];
		}
		global $wpdb;

		$query = $wpdb->prepare(
			'SELECT ID FROM ' . $wpdb->posts . '
			WHERE post_title = %s
			AND post_type = \'vehicle-customer\'',
			$postTitle
		);
		$wpdb->query( $query );
		if ( $wpdb->num_rows ) {
			wp_redirect(home_url().'/customer');
			session_start();
			$_SESSION['customerExists'] = $postTitle;
			wp_redirect(home_url().'/customer');
			exit();
		} else {
			$current_user = get_current_user_id();
			$listing = array(
				'post_title' => $postTitle,
				'post_content' => '',
				'post_status' => 'publish',
				'post_date' => date('Y-m-d H:i:s'),
				'post_author' => $current_user,
				'post_type' => 'vehicle-customer'
			);
			$cust_id = wp_insert_post($listing);
			update_post_meta($cust_id, 'wpcf-first-name', $firstName);
			update_post_meta($cust_id, 'wpcf-middle-name', $middleName);
			update_post_meta($cust_id, 'wpcf-surname', $lastName);
			update_post_meta($cust_id, 'wpcf-address', $address);
			update_post_meta($cust_id, 'wpcf-telephone-mobile', $telMob);
			update_post_meta($cust_id, 'wpcf-email', $custEmail);
			update_post_meta($cust_id, 'wpcf-company', $compName);
			update_post_meta($cust_id, 'wpcf-customer-purchase-history', $custHis);
			update_post_meta($cust_id, 'wpcf-secret-question', $secQue);
			update_post_meta($cust_id, 'wpcf-special-notes', $speNotes);
		    session_start();
			$_SESSION['customerCreated'] = $firstName;
			wp_redirect(home_url().'/customer');
			exit();
		}
	}

}

function update_customer_form( $post_id ) {
	if( $post_id != ''){
		$vat = get_the_title( $post_id );
		$firstName = get_post_meta($post_id,'wpcf-first-name',true);   	    
		$middleName = get_post_meta($post_id,'wpcf-middle-name',true);
		$lastName = get_post_meta($post_id,'wpcf-surname',true);
		$address = get_post_meta($post_id,'wpcf-address',true);
		$mobNo = get_post_meta($post_id,'wpcf-telephone-mobile',true);
		$email = get_post_meta($post_id,'wpcf-email',true);
		$compName = get_post_meta($post_id,'wpcf-company',true);
		$purHis = get_post_meta($post_id,'wpcf-customer-purchase-history',true);
		$secQue = get_post_meta($post_id,'wpcf-secret-question',true);
		$spNotes = get_post_meta($post_id, 'wpcf-special-notes',true);
		$customerForm = '<form method="post">';
		$customerForm.= '<div class="vehicle-details"><input type="hidden" name="edit-customer" value="editCustomer">';
		$customerForm.= '<p class="heading">Add New Customer</p>';
		$customerForm.=	'<div class="inner">';
		$customerForm.= '<div><label for="">First Name</label><div>';
		$customerForm.= '<input type="text" name="firstName" value="' .$firstName. '" placeholder="First Name"/></div></div>';
		$customerForm.= '<div><label for="">Middle Name</label><div>';
		$customerForm.= '<input type="text" name="middleName" value="' .$middleName. '" placeholder="Middle Name"/></div></div>';
		$customerForm.= '<div><label for="">Last Name</label><div>';
		$customerForm.= '<input type="text" name="lastName" value="' .$lastName. '" placeholder="First Name"/></div></div></div>';
		$customerForm.=	'<div class="inner">';
		$customerForm.= '<div><label for="">Telephone Mobile</label><div>';
		$customerForm.= '<input type="text" name="telMob" value="' .$mobNo. '" placeholder="Telephone Mobile"/></div></div>';
		$customerForm.= '<div><label for="">Email</label><div>';
		$customerForm.= '<input type="email" name="custEmail" value="' .$email. '" placeholder="Email"/></div></div>';
		$customerForm.= '<div><label for="">Vat Registration Number</label><div>';
		$customerForm.= '<input type="text" name="vat" value="' .$vat. '" placeholder="Vat Registration Number"/></div></div></div>';
		$customerForm.=	'<div class="inner">';
		$customerForm.= '<div><label for="">Company Name</label><div class="company-name">';
		$customerForm.= '<input type="text" name="compName" value="' .$compName. '" placeholder="Company Name"/></div></div></div>';
		$customerForm.= '<div class="inner">';
		$customerForm.= '<div><label for="">Address</label><div>';
		$customerForm.= '<textarea name="address" placeholder="Address" cols="15" rows="5">' .$address. '</textarea></div></div>';
		$customerForm.= '<div><label for="">Secret Question</label><div>';
		$customerForm.= '<textarea name="secQue" placeholder="Secret Question" cols="15" rows="5">' .$secQue. '</textarea></div></div></div>';
		$customerForm.=	'<div class="inner">';
		$customerForm.= '<div><label for="">Customer Purchase History</label><div>';
		$customerForm.= '<textarea name="custHis" placeholder="Customer Purchase History" cols="30" rows="10">' .$purHis. '</textarea></div></div>';
		$customerForm.= '<div><label for="">Special Notes</label><div>';
		$customerForm.= '<textarea name="speNotes" placeholder="Special Notes" cols="30" rows="10">' .$spNotes. '</textarea></div></div></div>';
		$customerForm.= '<p class="button-submit"><input type="submit" name="submit" class="edit-customer" value="Update Customer" /></p>';
		echo $customerForm;
		update_customer($post_id);
	}
}

function update_customer( $post_id ) {
	if(isset($_POST['edit-customer'])){
		$postTitle = $_POST['vat']; 
		$firstName = $_POST['firstName'];
		$middleName = $_POST['middleName'];
		$lastName = $_POST['lastName'];
		$telMob = $_POST['telMob'];
		$custEmail = $_POST['custEmail'];
		$compName = $_POST['compName'];
		$address = $_POST['address'];
		$secQue = $_POST['secQue'];
		$custHis = $_POST['custHis'];
		$speNotes = $_POST['speNotes'];
		global $wpdb;
		$updateCustomer = array(
			'post_title' => $postTitle,
			'ID' => $post_id,
		);
		$post_id = wp_update_post($updateCustomer);
		update_post_meta($post_id, 'wpcf-first-name', $firstName);
		update_post_meta($post_id, 'wpcf-middle-name', $middleName);
		update_post_meta($post_id, 'wpcf-surname', $lastName);
		update_post_meta($post_id, 'wpcf-address', $address);
		update_post_meta($post_id, 'wpcf-telephone-mobile', $telMob);
		update_post_meta($post_id, 'wpcf-email', $custEmail);
		update_post_meta($post_id, 'wpcf-company', $compName);
		update_post_meta($post_id, 'wpcf-customer-purchase-history', $custHis);
		update_post_meta($post_id, 'wpcf-secret-question', $secQue);
		update_post_meta($post_id, 'wpcf-special-notes', $speNotes);
		if ( is_wp_error( $post_id ) ) {
     		echo $post_id->get_error_message();
		}
		else {
			session_start();
			$_SESSION['customerUpdated'] = $firstName;
			wp_redirect(home_url().'/customer');
			exit();
		}
	}
}

function customer_search_form() {
	$search = '<form  method="post" action="">';
	$search.= '<input type="hidden" name="search-customer" value="searchCustomer"/>';
	$search.= '<input type=text id="customer-search" name="searchKey"/>';
	$search.= '<input type="submit" id="customer-submit" value="Search Customer"/></form>';
	echo $search;
}

// Reports page functions

add_action( 'wp_ajax_reportFilters', 'reportFilters' );
add_action( 'wp_ajax_nopriv_reportFilters', 'reportFilters' );
function reportFilters() {
	if (! isset( $_POST['reports_nonce_field'] )|| ! wp_verify_nonce( $_POST['reports_nonce_field'], 'reports_action_nonce')) {
        exit('The form is not valid');
    }
    // Example for creating an response with error information, to know in our js file
    // about the error and behave accordingly, like adding error message to the form with JS
    if (trim($_POST['email']) == '') {
    	$response['error'] = true;
    	$response['error_message'] = 'Email is required';
 
    	// Exit here, for not processing further because of the error
    	exit(json_encode($response));
    }
    $response['name'] = $_POST['name'];
    $response['email'] = $_POST['email'];
    // Don't forget to exit at the end of processing
    exit(json_encode($response));
}
