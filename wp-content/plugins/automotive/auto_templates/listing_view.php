<?php
global $lwp_options, $Listing;

$get_holder = (isset($fake_get) && !is_null($fake_get) && !empty($fake_get) ? $fake_get : $_GET);

$listings = $Listing->listing_args($get_holder, true);
$listings[0]['posts_per_page'] = -1;
$listings = count(get_posts($listings[0]));

//********************************************
//	Language Variables
//***********************************************************
$keywords         = __("Keywords", "listings");
$select_view      = __('Select View', 'listings');
$all_listings     = __("All Listings", "listings");
$matching         = __('Matching', 'listings');
$yes              = __("Yes", "listings");

$vehicle_singular = (isset($lwp_options['vehicle_singular_form']) && !empty($lwp_options['vehicle_singular_form']) ? $lwp_options['vehicle_singular_form'] : __('Vehicle', 'listings') );
$vehicle_plural   = (isset($lwp_options['vehicle_plural_form'])   && !empty($lwp_options['vehicle_plural_form'])   ? $lwp_options['vehicle_plural_form']   : __('Vehicles', 'listings') );

echo '<div class="listing-view margin-bottom-20"' . (isset($Listing->current_categories) && !empty($Listing->current_categories) ? " data-selected-categories='" . json_encode($Listing->current_categories) . "'" : "") . '>';
echo '<div class="row">';
echo '<div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 padding-none"> <span class="ribbon"><strong><span class="number_of_listings">' . $listings . '</span> <span class="listings_grammar">' . ($listings == 1 ? $vehicle_singular : $vehicle_plural) . '</span> ' . $matching . ':</strong></span> <ul class="ribbon-item filter margin-bottom-none" data-all-listings="' . $all_listings . '">';

$filters    = "";
$filterable = $Listing->get_filterable_listing_categories();

if(!empty($Listing->current_categories)) {
	foreach ( $Listing->current_categories as $slug => $slug_value ) {
		$category = $filterable[ ( $slug == "yr" ? "year" : $slug ) ];

		if ( is_array( $slug_value ) ) {
			$min = $min_label = $slug_value[0];
			$max = $max_label = $slug_value[1];

			// apply currency on labels if needed
			if ( isset( $category['currency'] ) && ! empty( $category['currency'] ) ) {
				$min_label = $Listing->format_currency( $min_label );
				$max_label = $Listing->format_currency( $max_label );
			}

			$filters .= "<li data-type='" . $slug . "[]' data-min='" . $min . "' data-max='" . $max . "'><a href=''><i class='fa fa-times-circle'></i> " . $category['singular'] . ": <span> " . $min_label . " - " . $max_label . "</span></a></li>";

		} else {

			$label = $category['terms'][ $slug_value ];

			// apply currency on label if needed
			if ( isset( $category['currency'] ) && ! empty( $category['currency'] ) ) {
				$label = $Listing->format_currency( $label );
			}

			$filters .= "<li data-type='" . $slug . "'><a href=''><i class='fa fa-times-circle'></i> " . $category['singular'] . ": " . ( $category['compare_value'] != "=" ? $category['compare_value'] . " " : "" ) . " <span data-key='" . $slug_value . "'>" . stripslashes( $label ) . "</span></a></li>";
		}
	}
}

// additional categories
$additional_categories = "additional_categories";

if($Listing->is_wpml_active()){
	$additional_categories .= '_' . ICL_LANGUAGE_CODE;
}

if(!empty($lwp_options[$additional_categories]['value'])){
	foreach($lwp_options[$additional_categories]['value'] as $additional_category){
		$check_handle = str_replace(" ", "_", mb_strtolower($additional_category));

		// in url
		if(isset($get_holder[$check_handle]) && !empty($get_holder[$check_handle])){
			$filters .= (isset($get_holder[$check_handle]) && !empty($get_holder[$check_handle]) ? "<li data-type='" . $check_handle . "'><a href=''><i class='fa fa-times-circle'></i> " . $additional_category . ": <span>" . $yes . "</span></a></li>" : "");
		}
	}
}

// keyword
if(isset($get_holder['keywords']) && !empty($get_holder['keywords'])){
	$keywords_get = sanitize_text_field($get_holder['keywords']);
	$filters .= "<li data-type='keywords'><a href=''><i class='fa fa-times-circle'></i> " . $keywords . ": <span data-key='" . $keywords_get . "'>" . $keywords_get . "</span></a></li>";
}

// if none set then show all listings
echo (!empty($filters) ? $filters : "<li data-type='All' data-filter='All'>" . $all_listings . "</li>");

echo '</ul></div>';
echo '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 pull-right select_view padding-none" data-layout="' . $layout . '">';

$lwp_options['inventory_listing_toggle'] = (isset($hide_select_view) && $hide_select_view != "true" ? 1 : $lwp_options['inventory_listing_toggle']);

if($lwp_options['inventory_listing_toggle'] == 1){
	echo ' <span class="align-right">' . $select_view . ':</span><ul class="page-view nav nav-tabs">';

	$buttons = array("wide_fullwidth", "wide_left", "wide_right", "boxed_fullwidth", "boxed_left", "boxed_right");

	foreach($buttons as $button){
		echo "<li" . ($button == $layout ? " class='active'" : "") . " data-layout='" . $button . "'><a href=\"#\"><i class=\"fa\"></i></a></li>";
	}
	echo '</ul>';
}
echo '</div>';
echo '</div>';
echo '</div>';