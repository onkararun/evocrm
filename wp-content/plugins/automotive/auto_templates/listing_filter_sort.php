<?php
global $lwp_options, $Listing;

$fake_get          = (isset($fake_get) && !empty($fake_get) ? $fake_get : null);
$get_holder        = (!is_null($fake_get) && !empty($fake_get) ? $fake_get : $_GET);
$sold_dependancies = (isset($sold_dependancies) && !empty($sold_dependancies) ? $sold_dependancies : false);

//********************************************
//	Language Variables
//***********************************************************
$select_prefix      = __("All", "listings");
$sortby_text        = __("Sort By", "listings");
$date_added_text    = __("Date Added", "listings");
$title_text         = __("Title", "listings");
$random_text        = __("Random", "listings");
$ascending_text     = __("Ascending", "listings");
$descending_text    = __("Descending", "listings");
$configure_text     = __("Configure under Listing Options &lt;&lt; Inventory Page &lt;&lt; Sort By Categories", "listings");
$reset_filters_text = __("Reset Filters", "listings");
$deselect_text      = __("Deselect All", "listings");
$vehicle_singular   = (isset($lwp_options['vehicle_singular_form']) && !empty($lwp_options['vehicle_singular_form']) ? $lwp_options['vehicle_singular_form'] : __('Vehicle', 'listings') );
$vehicle_plural     = (isset($lwp_options['vehicle_plural_form']) && !empty($lwp_options['vehicle_plural_form']) ? $lwp_options['vehicle_plural_form'] : __('Vehicles', 'listings') );

?>
<div class="clearfix"></div>
<form method="post" action="#" class="listing_sort">
	<div class="select-wrapper listing_select clearfix margin-bottom-15"<?php echo (isset($get_holder['sold_only']) && !empty($get_holder['sold_only']) ? " data-sold_only='true'" : ""); ?>
	<?php echo (isset($hide_dropdown_filters) && $hide_dropdown_filters == "true" ? "style='display: none;'" : ""); ?>>
		<?php
		$filterable_categories = $Listing->get_filterable_listing_categories();
		$dependancies = $Listing->process_dependancies($get_holder, $sold_dependancies);

		foreach($filterable_categories as $filter){
			$slug     = $filter['slug'];
			$get_slug = ($slug == "year" ? "yr" : $slug);
			$current  = (isset($get_holder[$get_slug]) && !empty($get_holder[$get_slug]) ? $get_holder[$get_slug] : "");

			echo '<div class="my-dropdown ' . $slug . '-dropdown">';
			$Listing->listing_dropdown($filter, $select_prefix, "listing_filter", (isset($dependancies[$slug]) && !empty($dependancies[$slug]) ? $dependancies[$slug] : array()), array("current_option" => $current));
			echo '</div>';
		} ?>

		<div class="loading_results">
			<i class="fa fa-circle-o-notch fa-spin"></i>
		</div>
	</div>
	<div class="select-wrapper pagination clearfix margin-bottom-15">
		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 sort-by-menu">
				<?php
				$lwp_options['sortby'] = (isset($hide_sortby) && $hide_sortby == "true" ? 2 : $lwp_options['sortby']);

				if(isset($lwp_options['sortby']) && $lwp_options['sortby'] == 1){ ?>
					<span class="sort-by"><?php echo $sortby_text; ?>:</span>
					<div class="my-dropdown price-ascending-dropdown">
						<select name="price_order" class="listing_filter" tabindex="1" >
							<?php
							$listing_orderby = $Listing->sort_by();

							if(!empty($listing_orderby)){

								$order_selected = (isset($_GET['order']) && !empty($_GET['order']) ? $_GET['order'] : "");

								if(empty($order_selected)){
									reset($listing_orderby);
									$selected = key($listing_orderby);

									$order_selected = $selected . "|" . (isset($lwp_options['sortby_default']) && !empty($lwp_options['sortby_default']) && $lwp_options['sortby_default'] == 1 ? "ASC" : "DESC");
								}

								foreach($listing_orderby as $key => $value){
									if($key == "date"){
										$option_label = $date_added_text;

									} elseif($key == "title"){
										$option_label = $title_text;

									} elseif($key == "random"){
										$option_label = $random_text;

									} else {
										$orderby_category = $Listing->get_single_listing_category($key);

										$option_label     = $orderby_category['singular'];
									}

									if($key == "random"){
										echo "<option value='" . $key . "'" . selected( $order_selected, $key, false ) . ">" . $option_label . "</option>\n";
									} else {
										echo "<option value='" . $key . "|ASC'" . selected( $order_selected, $key . "|ASC", false ) . ">" . $option_label . " " . $ascending_text . "</option>\n";
										echo "<option value='" . $key . "|DESC'" . selected( $order_selected, $key . "|DESC", false ) . ">" . $option_label . " " . $descending_text . "</option>\n";
									}
								}
							} else {
								echo "<option value='none'>" . $configure_text . "</option>";
							} ?>
						</select>
					</div>
				<?php } ?>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 col-lg-offset-1">
				<?php echo page_of_box(false, $fake_get); ?>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pull-right">
				<ul class="form-links top_buttons">
					<li><a href="#" class="gradient_button reset"><?php echo $reset_filters_text; ?></a></li>
					<?php if(isset($lwp_options['car_comparison']) && $lwp_options['car_comparison']){ ?>
						<li><a href="#" class="gradient_button deselect"><?php echo $deselect_text; ?></a></li>
						<?php $comparison_page  = (isset($lwp_options['comparison_page']) && !empty($lwp_options['comparison_page']) ? get_permalink(apply_filters("wpml_object_id", $lwp_options['comparison_page'], "page", true)) : "#");?>
						<li><a href="<?php echo $comparison_page; ?>" class="gradient_button compare"><?php echo sprintf(__("Compare <span class='number_of_vehicles'>%s</span> %s", "listings"), (isset($_COOKIE['compare_vehicles']) && !empty($_COOKIE['compare_vehicles']) ? count(explode(",", urldecode($_COOKIE['compare_vehicles']))) : 0), $vehicle_plural); ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</form>