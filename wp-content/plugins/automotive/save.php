<?php
//********************************************
//	Save custom meta fields
//***********************************************************
function plugin_save_custom_meta($post_id){
	// page/post options
	global $lwp_options, $Listing;
	
	$post_types = get_post_types();
	
	unset($post_types['listings']);
	
	$layout = (isset($_POST['layout']) && !empty($_POST['layout']) ? $_POST['layout'] : "");
	if(isset($layout) && !empty($layout)){
		update_post_meta((int)$post_id, "layout", (string)$layout);
	}
	
	if(get_post_type() == "listings"){
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		   return $post_id;
		} else {
			if(isset($_POST['location_map']) && !empty($_POST['location_map'])){
				update_post_meta((int)$post_id, "location_map", $_POST['location_map']);
			}
			
			if(isset($_POST['other_comments'])){
				update_post_meta((int)$post_id, "other_comments", $_POST['other_comments']);
			}
			
			if(isset($_POST['technical_specifications']) && !empty($_POST['technical_specifications'])){
				update_post_meta((int)$post_id, "technical_specifications", $_POST['technical_specifications']);
			}
			
			if(isset($_POST['verified']) && !empty($_POST['verified'])){
				update_post_meta((int)$post_id, "verified", $_POST['verified']);
			} else {
				delete_post_meta((int)$post_id, "verified" );
			}
			
			if(isset($_POST['additional_details']) && !empty($_POST['additional_details'])){
				update_post_meta((int)$post_id, "additional_details", $_POST['additional_details']);
			}
			
			// secondary title
			if(isset($_POST['secondary_title']) && !empty($_POST['secondary_title'])){
				update_post_meta((int)$post_id, "secondary_title", $_POST['secondary_title']);
			}

			// car sold
			$car_sold = (isset($_POST['car_sold']) && !empty($_POST['car_sold']) ? $_POST['car_sold'] : "");
			if(!empty($car_sold)){
				update_post_meta($post_id, "car_sold", $car_sold);
			} else {
				update_post_meta($post_id, "car_sold", 2);
			}

			// custom post meta
			$listing_categories = $Listing->get_listing_categories();

			// dont run for trashed posts
			if(!empty($listing_categories) && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] != "trash") && (isset($_GET['action']) && $_GET['action'] != "untrash"))) {
				$new_listing_categories_values = array();

				foreach ( $listing_categories as $category ) {
					$slug = $category['slug'];

					$value = $org_key = ( isset( $_POST[ $slug ] ) && ! empty( $_POST[ $slug ] ) ? $_POST[ $slug ] : "" );

					//if(!empty($value)){
					if ( empty( $value ) && $category['compare_value'] != "=" ) {
						$value = 0;
					} elseif ( empty( $value ) || $value == "None" ) {
						$value = "";//__( "None", "listings" );
					}

					// linked values
					$category['link_value'] = ( isset( $category['link_value'] ) && ! empty( $category['link_value'] ) ? $category['link_value'] : "" );

					if ( isset( $_POST['options'] ) && ! empty( $_POST['options'] ) ) {
						if ( ! empty( $category['link_value'] ) && $category['link_value'] != "none" ) {
							if ( $category['link_value'] == "price" ) {
								$value = $_POST['options']['price']['value'];
								update_post_meta( (int) $post_id, $slug, $value ); // because $org_key wont be set
							} else if ( $category['link_value'] == "mpg" ) {
                                $city_text = (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : "");
                                $hwy_text  = (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : "");

								$value = $_POST['options']['city_mpg']['value'] . " " . $city_text . " / " . $_POST['options']['highway_mpg']['value'] . " " . $hwy_text;
								update_post_meta( (int) $post_id, $slug, $value ); // because $org_key wont be set
							}
						}
					}

					if ( ! empty( $org_key ) || $org_key == 0 ) {
						update_post_meta( (int) $post_id, $slug, html_entity_decode($value, ENT_QUOTES) );
						$new_listing_categories_values[$slug] = array($Listing->slugify(html_entity_decode($value, ENT_QUOTES)) => html_entity_decode($value, ENT_QUOTES));
					}
				}

				// dont run for trashed posts
				if(!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] != "trash") && (isset($_GET['action']) && $_GET['action'] != "untrash")){
					$Listing->update_dependancy_option($post_id, $new_listing_categories_values, $car_sold);
				}
			}

			$additional_categories = "additional_categories";

			if($Listing->is_wpml_active()){
				$additional_categories .= '_' . ICL_LANGUAGE_CODE;
			}

			if(!empty($lwp_options[$additional_categories]['value'])){
				foreach($lwp_options[$additional_categories]['value'] as $category){
					$safe_category = str_replace(" ", "_", strtolower($category));
					$value         = (isset($_POST[$additional_categories]['value'][$safe_category]) && !empty($_POST[$additional_categories]['value'][$safe_category]) ? $_POST[$additional_categories]['value'][$safe_category] : "");

					update_post_meta($post_id, $safe_category, $value);
				}
			}

			// featured
			if(isset($lwp_options['featured_vehicle_widget']) && $lwp_options['featured_vehicle_widget'] == 1) {
				$car_featured = ( isset( $_POST['car_featured'] ) && ! empty( $_POST['car_featured'] ) ? $_POST['car_featured'] : "" );
				update_post_meta($post_id, "car_featured", $car_featured);
			}

			// pdf_brochure_input
			if(isset($_POST['pdf_brochure_input'])){
				update_post_meta($post_id, "pdf_brochure_input", $_POST['pdf_brochure_input']);
			}
			
			$multi_options = (isset($_POST['multi_options']) && !empty($_POST['multi_options']) ? $_POST['multi_options'] : "");
			update_post_meta($post_id, "multi_options", $multi_options);
			

			$_POST['options']['price']['value']       = (isset($_POST['options']['price']['value']) && !empty($_POST['options']['price']['value']) ? $_POST['options']['price']['value'] : "");
			$_POST['options']['price']['original']    = (isset($_POST['options']['price']['original']) && !empty($_POST['options']['price']['original']) ? $_POST['options']['price']['original'] : "");

			$post_options = (isset($_POST['options']) ? serialize($_POST['options']) : null);
			
			update_post_meta($post_id, "listing_options", $post_options);

			// woocommerce product association
			if(isset($_POST['woocommerce_integration_id'])){
				update_post_meta($post_id, "woocommerce_integration_id", $_POST['woocommerce_integration_id']);
			}
						
			if( isset($_POST['gallery_images']) && !empty($_POST['gallery_images']) ){
				if(!empty($_POST['gallery_images'])){
					$save_gallery_images = array();

					if($Listing->is_hotlink()){
						foreach ( $_POST['gallery_images'] as $gallery_image ) {
							if(filter_var($gallery_image, FILTER_VALIDATE_URL)) {
								$save_gallery_images[] = $gallery_image;
							}
						}
					} else {
						foreach ( $_POST['gallery_images'] as $gallery_image ) {
							$save_gallery_images[] = $gallery_image;
						}
					}
										
					update_post_meta($post_id, "gallery_images", $save_gallery_images);					
				}
			}
		}
	}
}

add_action('save_post', 'plugin_save_custom_meta', 999);