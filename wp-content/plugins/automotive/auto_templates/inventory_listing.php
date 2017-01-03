<?php
global $lwp_options, $Listing;

//********************************************
//	Language Variables
//***********************************************************
$sold_text          = __( "Sold", "listings" );
$none_text          = __( "None", "listings" );
$view_details_text  = __( "View Details", "listings" );
$view_video_text    = __( "View Video", "listings" );


$listing   = get_post( $id );
$post_meta = $Listing->get_listing_meta($id);

$listing_options = ( isset( $post_meta['listing_options'] ) && ! empty( $post_meta['listing_options'] ) ? $post_meta['listing_options'] : array() );

if ( $layout == "boxed_fullwidth" ) {
	echo "<div class=\"col-lg-3 col-md-4 col-sm-6 col-xs-12\">";
} elseif ( $layout == "boxed_left" ) {
	echo "<div class=\"col-lg-4 col-md-6 col-sm-6 col-xs-12\">";
} elseif ( $layout == "boxed_right" ) {
	echo "<div class=\"col-lg-4 col-md-6 col-sm-6 col-xs-12\">";
}

// determine image
$gallery_images = ( isset( $post_meta['gallery_images'] ) && ! empty( $post_meta['gallery_images'] ) ? $post_meta['gallery_images'] : "" );

if ( isset( $gallery_images ) && ! empty( $gallery_images ) && isset( $gallery_images[0] ) ) {
	$image_src = $Listing->auto_image( $gallery_images[0], "auto_listing", true );
} elseif ( empty( $gallery_images[0] ) && isset( $lwp_options['not_found_image']['url'] ) && ! empty( $lwp_options['not_found_image']['url'] ) ) {
	$image_src = $lwp_options['not_found_image']['url'];
} else {
	$image_src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
}

// get youtube id
if ( isset( $listing_options['video'] ) && ! empty( $listing_options['video'] ) ) {
	$url = parse_url( $listing_options['video'] );

	if ( ( $url['host'] == "www.youtube.com" || $url['host'] == "youtube.com" ) && isset( $url['query'] ) ) {
		$video_id = str_replace( "v=", "", $url['query'] );
	} elseif ( $url['host'] == "www.vimeo.com" || $url['host'] == "vimeo.com" ) {
		$video_id = $url['path'];
		$is_vimeo = true;
	}
}

$is_custom_price_text = ( ( isset( $lwp_options['price_text_replacement'] ) && ! empty( $lwp_options['price_text_replacement'] ) && $lwp_options['price_text_all_listings'] == 0 ) ||
                        ( isset( $lwp_options['price_text_all_listings'] ) && $lwp_options['price_text_all_listings'] == 1 && empty( $listing_options['price']['value'] )) ? true : false);

// determine if checked
if ( isset( $_COOKIE['compare_vehicles'] ) && ! empty( $_COOKIE['compare_vehicles'] ) ) {
	$compare_vehicles = explode( ",", urldecode( $_COOKIE['compare_vehicles'] ) );
} ?>

	<div
		class="inventory clearfix margin-bottom-20 styled_input <?php echo( isset( $post_meta['car_sold'] ) && $post_meta['car_sold'] == 1 ? "car_sold" : "" );
		echo ( empty( $listing_options['price']['value'] ) && $is_custom_price_text && empty($lwp_options['price_text_replacement']) ? " no_price" : "" ); ?>">
		<?php if ( isset( $lwp_options['car_comparison'] ) && $lwp_options['car_comparison'] ) { ?>
			<input type="checkbox" class="checkbox compare_vehicle" id="vehicle_<?php echo $id; ?>"
			       data-id="<?php echo $id; ?>"<?php echo( isset( $compare_vehicles ) && in_array( $id, $compare_vehicles ) ? " checked='checked'" : "" ); ?> />
			<label for="vehicle_<?php echo $id; ?>"></label>
		<?php } ?>

		<?php
		// if sold auto add badge
		$auto_sold_badge = (isset($lwp_options['sold_attach_badge']) && $lwp_options['sold_attach_badge'] == 1 && isset( $post_meta['car_sold'] ) && ! empty( $post_meta['car_sold'] ) && $post_meta['car_sold'] == 1 ? true : false);
		if($auto_sold_badge && (!isset($listing_options['custom_badge']) || empty($listing_options['custom_badge']))){
			$listing_options['custom_badge'] = "sold";
		}

		if ( isset( $listing_options['custom_badge'] ) && ! empty( $listing_options['custom_badge'] )  ) {
			$listing_badge = $Listing->get_listing_badge($listing_options['custom_badge'], $auto_sold_badge); ?>
			<div class="angled_badge <?php echo $listing_badge['css']; ?>">
				<span<?php echo( strlen( $listing_badge['name'] ) >= 7 ? " class='smaller'" : "" ); ?>><?php echo $listing_badge['name']; ?></span>
			</div>
		<?php } ?>

		<a class="inventory<?php echo( isset( $listing_options['custom_badge'] ) && ! empty( $listing_options['custom_badge'] ) ? " has_badge" : "" ); ?>"
		   href="<?php echo get_permalink( $id ); ?>">
			<div class="title"><?php echo $listing->post_title; ?></div>
			<img src="<?php echo $image_src; ?>" class="preview"
			     alt="<?php _e( "preview", "listings" ); ?>" <?php echo( isset( $lwp_options['thumbnail_slideshow'] ) && $lwp_options['thumbnail_slideshow'] == 1 ? 'data-id="' . $id . '"' : "" ); ?>>

			<?php
			if(isset($lwp_options['vehicle_overview_listings']) && $lwp_options['vehicle_overview_listings'] == 1){
                $visual_composer_used = get_post_meta($id, "_wpb_vc_js_status", true);

				$limit        = (isset($lwp_options['vehicle_overview_listings_limit']) && !empty($lwp_options['vehicle_overview_listings_limit']) ? $lwp_options['vehicle_overview_listings_limit'] : 250);
		        $ellipsis     = (isset($lwp_options['vehicle_overview_ellipsis']) ? $lwp_options['vehicle_overview_ellipsis'] : "[...]");
				$stripp       = "<br><p><b><u><i><span><a><img>";

				$vehicle_excerpt  = get_post_field("post_excerpt", $id);
				$vehicle_overview = get_post_field("post_content", $id);

				$vehicle_desc     = (!empty($vehicle_excerpt) ? $vehicle_excerpt : $vehicle_overview);

                if($visual_composer_used){
	                $post_content = preg_replace( '/\[[^\]]+\]/', '', $vehicle_desc );
	                $post_content = substr(strip_tags($post_content, $stripp), 0, $limit) . " " . (strlen(strip_tags($post_content, $stripp)) > $limit ? $ellipsis : "");
                } else {
	                $post_content = substr(strip_tags($vehicle_desc, $stripp), 0, $limit) . " " . (strlen(strip_tags($vehicle_desc, $stripp)) > $limit ? $ellipsis : "");
                }

				echo "<p class='vehicle_overview'>" . $post_content . "</p>";
			} else {
				$listing_details = $Listing->get_use_on_listing_categories();

				if ( count( $listing_details ) > 5 ) {
					$first_details  = array_slice( $listing_details, 0, 5, true );
					$second_details = array_slice( $listing_details, 5, count( $listing_details ), true );
				} else {
					$single_table  = true;
					$first_details = $listing_details;
				}

				if ( isset( $post_meta['car_sold'] ) && ! empty( $post_meta['car_sold'] ) && $post_meta['car_sold'] == 1 ) {
					echo "<span class='sold_text'>" . $sold_text . "</span>";
				}

				echo "<table class='options-primary'>";
				foreach ( $first_details as $detail ) {
					$slug  = $detail['slug'];
					$value = ( isset( $post_meta[ $slug ] ) && ! empty( $post_meta[ $slug ] ) ? $post_meta[ $slug ] : "" );

					if ( empty( $value ) && isset( $detail['compare_value'] ) && $detail['compare_value'] != "=" ) {
						$value = 0;
					} elseif ( empty( $value ) ) {
						$value = $none_text;
					}

					// currency
					if ( isset( $detail['currency'] ) && ! empty( $detail['currency'] ) ) {
						$value = $Listing->format_currency( $value );
					}

					echo "<tr>";
					echo "<td class='option primary'>" . $detail['singular'] . ": </td>";
					echo "<td class='spec'>" . html_entity_decode( $value ) . "</td>";
					echo "</tr>";
				}
				echo "</table>";

				if ( ! isset( $single_table ) ) {
					echo "<table class='options-secondary'>";
					foreach ( $second_details as $detail ) {
						$slug  = $detail['slug'];
						$value = ( isset( $post_meta[ $slug ] ) && ! empty( $post_meta[ $slug ] ) ? $post_meta[ $slug ] : "" );

						if ( empty( $value ) && isset( $detail['compare_value'] ) && $detail['compare_value'] != "=" ) {
							$value = 0;
						} elseif ( empty( $value ) ) {
							$value = $none_text;
						}

						// currency
						if ( isset( $detail['currency'] ) && ! empty( $detail['currency'] ) ) {
							$value = $Listing->format_currency( $value );
						}

						echo "<tr>";
						echo "<td class='option secondary'>" . $detail['singular'] . ": </td>";
						echo "<td class='spec'>" . html_entity_decode( $value ) . "</td>";
						echo "</tr>";
					}
					echo "</table>";
				}
			} ?>

			<div class="view-details gradient_button"><i
					class='fa fa-plus-circle'></i> <?php echo $view_details_text; ?> </div>
			<div class="clearfix"></div>
		</a>

		<?php //
		if ( ( isset( $listing_options['price']['value'] ) && ! empty( $listing_options['price']['value'] ) ) || ( isset( $lwp_options['price_text_replacement'] ) && ! empty( $lwp_options['price_text_replacement'] ) ) ) {
			$original = ( isset( $listing_options['price']['original'] ) && ! empty( $listing_options['price']['original'] ) ? $listing_options['price']['original'] : "" ); ?>

			<div
				class="price<?php echo ( $is_custom_price_text ? " custom_message price_replacement" : ''); ?>">
				<?php if ( $is_custom_price_text ) { ?>
					<?php echo do_shortcode( $lwp_options['price_text_replacement'] ); ?>
				<?php } else { ?>
					<b><?php echo ( ! empty( $original ) ? $lwp_options['sale_value'] : "" ) . ( isset( $lwp_options['default_value_price'] ) ? $lwp_options['default_value_price'] : __( "Price", "listings" ) ); ?>
						:</b><br>
					<div class="figure"><?php echo $Listing->format_currency( $listing_options['price']['value'] ); ?>
						<br></div>
					<?php
					if ( isset( $listing_options['custom_tax_inside'] ) && ! empty( $listing_options['custom_tax_inside'] ) ) {
						echo $listing_options['custom_tax_inside'];
					} elseif ( isset( $lwp_options['tax_label_box'] ) && ! empty( $lwp_options['tax_label_box'] ) ) {
						echo '<div class="tax">' . $lwp_options['tax_label_box'] . '</div>';
					} ?>
				<?php } ?>
			</div>
		<?php } ?>

		<?php
		if ( isset( $lwp_options['vehicle_history']['url'] ) && ! empty( $lwp_options['vehicle_history']['url'] ) && isset( $post_meta['verified'] ) ) {
			if ( isset( $lwp_options['carfax_linker']['url'] ) && ! empty( $lwp_options['carfax_linker']['url'] ) && isset( $lwp_options['carfax_linker']['category'] ) && ! empty( $lwp_options['carfax_linker']['category'] ) ) {
				$url = str_replace( "{vin}", $post_meta[ $lwp_options['carfax_linker']['category'] ], $lwp_options['carfax_linker']['url'] );
				echo "<a href='" . $url . "' target='_blank'>";
			}
			?>
			<img src="<?php echo $lwp_options['vehicle_history']['url']; ?>"
			     alt="<?php echo( isset( $lwp_options['vehicle_history_label'] ) && ! empty( $lwp_options['vehicle_history_label'] ) ? $lwp_options['vehicle_history_label'] : "" ); ?>"
			     class="carfax"/>
			<?php
			if ( isset( $lwp_options['carfax_linker']['url'] ) && ! empty( $lwp_options['carfax_linker']['url'] ) && isset( $lwp_options['carfax_linker']['category'] ) && ! empty( $lwp_options['carfax_linker']['category'] ) ) {
				echo "</a>";
			}
		} ?>

		<?php if ( isset( $video_id ) && ! empty( $video_id ) ) { ?>
			<div class="view-video gradient_button"
			     data-youtube-id="<?php echo $video_id; ?>"<?php echo( isset( $is_vimeo ) && $is_vimeo == true ? " data-video='vimeo'" : "" ); ?>>
				<i class="fa fa-video-camera"></i> <?php echo $view_video_text; ?></div>
		<?php } ?>
	</div>

<?php

if ( $layout == "boxed_fullwidth" || $layout == "boxed_left" || $layout == "boxed_right" ) {
	echo "</div>";
}