<?php
global $post, $lwp_options, $Listing;

wp_enqueue_script( 'google-maps' );
wp_enqueue_script( 'bxslider' );

wp_enqueue_style( 'social-likes' );

//********************************************
//	Language Variables
//***********************************************************
$no_features_text       = __( "There are no features available", "listings" );
$sold_text              = __("Sold", "listings");
$no_location_text       = __("No location available", "listings");
$none_text              = __("None", "listings");
$share_facebook_text    = __("Share link on Facebook", "listings");
$share_google_text      = __("Share link on Google+", "listings");
$share_pinterest_text   = __("Share link on Pinterest", "listings");
$share_twitter_text     = __("Share link on Twitter", "listings");


$post_meta       = $Listing->get_listing_meta($post->ID);
$listing_options = (isset($post_meta['listing_options']) && !empty($post_meta['listing_options']) ? $post_meta['listing_options'] : "");
$gallery_images  = (isset($post_meta['gallery_images']) && !empty($post_meta['gallery_images']) ? $post_meta['gallery_images'] : "");
$multi_options   = (isset($post_meta['multi_options']) && !empty($post_meta['multi_options']) ? $post_meta['multi_options'] : "");
$location        = (isset($post_meta['location_map']) && !empty($post_meta['location_map']) ? $post_meta['location_map'] : "");

$multi_text      = $multi_pdf = "";
if(isset($multi_options) && !empty($multi_options)) {

	natcasesort( $multi_options );

	if ( ! empty( $multi_options ) ) {
		foreach ( $multi_options as $option ) {
			$multi_text .= "<li><i class=\"fa-li fa fa-check\"></i> " . $option . "</li>";

			$multi_pdf .= $option . ", ";
		}
	}

	$multi_pdf = rtrim( $multi_pdf, ", " );
} else {
	$multi_text .= "<li>" . $no_features_text . "</li>";
	$multi_pdf .= $no_features_text;
} ?>
<div class="inner-page inventory-listing">
	<div class="inventory-heading margin-bottom-10 clearfix">
		<div class="row">
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 xs-padding-none">
				<h2><?php the_title(); ?></h2>
				<?php echo (isset($post_meta['secondary_title']) && !empty($post_meta['secondary_title']) ? "<span class='margin-top-10'>" . $post_meta['secondary_title'] . "</span>" : ""); ?>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 text-right xs-padding-none">

				<?php
				if(isset($lwp_options['show_vehicle_history_inventory']) && !empty($lwp_options['show_vehicle_history_inventory']) && $lwp_options['show_vehicle_history_inventory'] == true){
					if(isset($lwp_options['vehicle_history']['url']) && !empty($lwp_options['vehicle_history']['url']) && isset($post_meta['verified'])){
						if(isset($lwp_options['carfax_linker']['url']) && !empty($lwp_options['carfax_linker']['url']) && isset($lwp_options['carfax_linker']['category']) && !empty($lwp_options['carfax_linker']['category'])){
							$url = str_replace("{vin}", $post_meta[$lwp_options['carfax_linker']['category']], $lwp_options['carfax_linker']['url']);
							echo "<a href='" . $url . "' target='_blank'>";
						}
						?>
						<img src="<?php echo $lwp_options['vehicle_history']['url']; ?>" alt="<?php echo (isset($lwp_options['vehicle_history_label']) && !empty($lwp_options['vehicle_history_label']) ? $lwp_options['vehicle_history_label'] : ""); ?>" class="carfax_title" />
						<?php
						if(isset($lwp_options['carfax_linker']['url']) && !empty($lwp_options['carfax_linker']['url']) && isset($lwp_options['carfax_linker']['category']) && !empty($lwp_options['carfax_linker']['category'])){
							echo "</a>";
						}
					}
				} ?>

				<?php
				if(isset($listing_options['price']['value']) && !empty($listing_options['price']['value'])){
					$original = (isset($listing_options['price']['original']) && !empty($listing_options['price']['original']) ? $listing_options['price']['original'] : "");

					echo (!empty($original) ? "<h2 class='strikeout original_price'>" . $Listing->format_currency($original) . "</h2>" : "");

					if(isset($lwp_options['price_text_replacement']) && !empty($lwp_options['price_text_replacement']) && $lwp_options['price_text_all_listings'] == 0){
						echo do_shortcode($lwp_options['price_text_replacement']);
					} else {
						echo '<h2>' . $Listing->format_currency($listing_options['price']['value']) . '</h2>';

						if(isset($listing_options['custom_tax_page']) && !empty($listing_options['custom_tax_page'])){
							echo do_shortcode($listing_options['custom_tax_page']);
						} elseif(isset($lwp_options['tax_label_page']) && !empty($lwp_options['tax_label_page'])) {
							echo '<em>' . $lwp_options['tax_label_page'] . '</em>';
						}
					}
				} elseif( (empty($listing_options['price']['value']) && isset($lwp_options['price_text_all_listings']) && $lwp_options['price_text_all_listings'] == 1 ) || (isset($lwp_options['price_text_replacement']) && !empty($lwp_options['price_text_replacement']) && $lwp_options['price_text_all_listings'] == 0) ){
					echo do_shortcode($lwp_options['price_text_replacement']);
				}

				if(isset($post_meta['car_sold']) && $post_meta['car_sold'] == 1){
					echo '<span class="sold_text' . (!isset($listing_options['price']['value']) || empty($listing_options['price']['value']) ? ' no_price' : '') . '">' . $sold_text . '</span>';
				} ?>
			</div>
		</div>
	</div>
	<div class="content-nav margin-bottom-30">
		<ul>
			<?php $next_link = (get_permalink(get_adjacent_post(false,'',false)) == get_permalink() ? "#" : get_permalink(get_adjacent_post(false,'',false)));
			$prev_link = (get_permalink(get_adjacent_post(false,'',true)) == get_permalink() ? "#" : get_permalink(get_adjacent_post(false,'',true))); ?>

			<?php if(isset($lwp_options['previous_vehicle_show']) && !empty($lwp_options['previous_vehicle_show']) && $lwp_options['previous_vehicle_show'] == 1){ ?>
				<li class="prev1 gradient_button"><a href="<?php echo $prev_link; ?>"><?php echo $lwp_options['previous_vehicle_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['request_more_show']) && !empty($lwp_options['request_more_show']) && $lwp_options['request_more_show'] == 1){ ?>
				<li class="request gradient_button"><a href="#request_fancybox_form" class="fancybox_div"><?php echo $lwp_options['request_more_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['schedule_test_show']) && !empty($lwp_options['schedule_test_show']) && $lwp_options['schedule_test_show'] == 1){ ?>
				<li class="schedule gradient_button"><a href="#schedule_fancybox_form" class="fancybox_div"><?php echo $lwp_options['schedule_test_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['make_offer_show']) && !empty($lwp_options['make_offer_show']) && $lwp_options['make_offer_show'] == 1){ ?>
				<li class="offer gradient_button"><a href="#offer_fancybox_form" class="fancybox_div"><?php echo $lwp_options['make_offer_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['tradein_show']) && !empty($lwp_options['tradein_show']) && $lwp_options['tradein_show'] == 1){ ?>
				<li class="trade gradient_button"><a href="#trade_fancybox_form" class="fancybox_div"><?php echo $lwp_options['tradein_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['pdf_brochure_show']) && !empty($lwp_options['pdf_brochure_show']) && $lwp_options['pdf_brochure_show'] == 1){
				$pdf_brochure = get_post_meta($post->ID, "pdf_brochure_input", true);
				$pdf_link		= wp_get_attachment_url( $pdf_brochure ); ?>
				<li class="pdf gradient_button"><a href="<?php echo (isset($pdf_link) && !empty($pdf_link) ? $pdf_link : ''); ?>" class="<?php echo (isset($pdf_link) && !empty($pdf_link) ? '' : 'generate_pdf'); ?>" <?php echo (isset($pdf_link) && !empty($pdf_link) ? "target='_blank'" : ''); ?>><?php echo $lwp_options['pdf_brochure_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['print_vehicle_show']) && !empty($lwp_options['print_vehicle_show']) && $lwp_options['print_vehicle_show'] == 1){ ?>
				<li class="print gradient_button"><a class="print_page"><?php echo $lwp_options['print_vehicle_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['email_friend_show']) && !empty($lwp_options['email_friend_show']) && $lwp_options['email_friend_show'] == 1){ ?>
				<li class="email gradient_button"><a href="#email_fancybox_form" class="fancybox_div"><?php echo $lwp_options['email_friend_label']; ?></a></li>
			<?php } ?>

			<?php if(isset($lwp_options['next_vehicle_show']) && !empty($lwp_options['next_vehicle_show']) && $lwp_options['next_vehicle_show'] == 1){ ?>
				<li class="next1 gradient_button"><a href="<?php echo $next_link; ?>"><?php echo $lwp_options['next_vehicle_label']; ?></a></li>
			<?php } ?>

		</ul>
	</div>
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 left-content padding-left-none">
			<!--OPEN OF SLIDER-->
			<?php
			$full_images = $thumb_images = "";

			if(!empty($gallery_images)){
				foreach($gallery_images as $gallery_image){
					$gallery_thumb  = $Listing->auto_image($gallery_image, "auto_thumb", true);
					$gallery_slider = $Listing->auto_image($gallery_image, "auto_slider", true);
//					$full 			= wp_get_attachment_image_src($gallery_image, "full");
//					$full 			= $full[0];
					$full           = $Listing->auto_image($gallery_image, "full", true);
					$alt 			= get_post_meta($gallery_image, "_wp_attachment_image_alt", true);

					$full_images  .= "<li data-thumb=\"" . $gallery_thumb . "\"> <img src=\"" . $gallery_slider . "\" alt=\"" . $alt . "\" data-full-image=\"" . $full . "\" /> </li>\n";
					$thumb_images .= "<li data-thumb=\"" . $gallery_thumb . "\"> <a href=\"#\"><img src=\"" . $gallery_thumb . "\" alt=\"" . $alt . "\" /></a> </li>\n";
				}
			} elseif(empty($gallery_images[0]) && isset($lwp_options['not_found_image']['id']) && !empty($lwp_options['not_found_image']['id'])){
				$gallery_thumb  = $Listing->auto_image($lwp_options['not_found_image']['id'], "auto_thumb", true);
				$gallery_slider = $Listing->auto_image($lwp_options['not_found_image']['id'], "auto_slider", true);
				$full 			= wp_get_attachment_image_src($lwp_options['not_found_image']['id'], "full");
				$full 			= $full[0];
				$alt 			= get_post_meta($lwp_options['not_found_image']['id'], "_wp_attachment_image_alt", true);

				$full_images  .= "<li data-thumb=\"" . $gallery_thumb . "\"> <img src=\"" . $gallery_slider . "\" alt=\"" . $alt . "\" data-full-image=\"" . $full . "\" /> </li>\n";
				$thumb_images .= "<li data-thumb=\"" . $gallery_thumb . "\"> <a href=\"#\"><img src=\"" . $gallery_thumb . "\" alt=\"" . $alt . "\" /></a> </li>\n";
			} ?>
			<div class="listing-slider">
				<?php
				// if sold auto add badge
				$auto_sold_badge = (isset($lwp_options['sold_attach_badge']) && $lwp_options['sold_attach_badge'] == 1 && isset( $post_meta['car_sold'] ) && ! empty( $post_meta['car_sold'] ) && $post_meta['car_sold'] == 1 ? true : false);
				if($auto_sold_badge && !isset($listing_options['custom_badge'])){
					$listing_options['custom_badge'] = "sold";
				}

				if ( isset( $listing_options['custom_badge'] ) && ! empty( $listing_options['custom_badge'] ) && isset($lwp_options['listing_badge_slider']) && $lwp_options['listing_badge_slider'] == true ) {
					$listing_badge = $Listing->get_listing_badge($listing_options['custom_badge'], $auto_sold_badge); ?>
					<div class="angled_badge <?php echo $listing_badge['css']; ?>">
						<span<?php echo( strlen( $listing_badge['name'] ) >= 7 ? " class='smaller'" : "" ); ?>><?php echo $listing_badge['name']; ?></span>
					</div>
				<?php } ?>
				<section class="slider home-banner">
					<div class="flexslider loading" id="home-slider-canvas">
						<ul class="slides">
							<?php echo (!empty($full_images) ? $full_images : ""); ?>
						</ul>
					</div>
				</section>
				<section class="home-slider-thumbs">
					<div class="flexslider" id="home-slider-thumbs">
						<ul class="slides">
							<?php echo (!empty($thumb_images) ? $thumb_images : ""); ?>
						</ul>
					</div>
				</section>
			</div>
			<!--CLOSE OF SLIDER-->
			<!--Slider End-->
			<div class="clearfix"></div>
			<div class="bs-example bs-example-tabs example-tabs margin-top-50">
				<ul id="myTab" class="nav nav-tabs">
					<?php
					$first_tab 	= (isset($lwp_options['first_tab']) && !empty($lwp_options['first_tab']) ? $lwp_options['first_tab'] : "" );
					$second_tab = (isset($lwp_options['second_tab']) && !empty($lwp_options['second_tab']) ? $lwp_options['second_tab'] : "" );
					$third_tab 	= (isset($lwp_options['third_tab']) && !empty($lwp_options['third_tab']) ? $lwp_options['third_tab'] : "" );
					$fourth_tab = (isset($lwp_options['fourth_tab']) && !empty($lwp_options['fourth_tab']) ? $lwp_options['fourth_tab'] : "" );
					$fifth_tab 	= (isset($lwp_options['fifth_tab']) && !empty($lwp_options['fifth_tab']) ? $lwp_options['fifth_tab'] : "" ); ?>

					<?php echo (!empty($first_tab) ? '<li class="active"><a href="#vehicle" data-toggle="tab">' . $first_tab . '</a></li>' : ''); ?>
					<?php echo (!empty($second_tab) ? '<li><a href="#features" data-toggle="tab">' . $second_tab . '</a></li>' : ''); ?>
					<?php echo (!empty($third_tab) ? '<li><a href="#technical" data-toggle="tab">' . $third_tab . '</a></li>' : ''); ?>
					<?php echo (!empty($fourth_tab) ? '<li><a href="#location" data-toggle="tab">' . $fourth_tab . '</a></li>' : ''); ?>
					<?php echo (!empty($fifth_tab) ? '<li><a href="#comments" data-toggle="tab">' . $fifth_tab . '</a></li>' : ''); ?>
				</ul>
				<div id="myTabContent" class="tab-content margin-top-15 margin-bottom-20">
					<?php if(!empty($first_tab)){ ?>
						<div class="tab-pane fade in active" id="vehicle">
							<?php the_content(); ?>
						</div>
					<?php } ?>

					<?php if(!empty($second_tab)){ ?>
						<div class="tab-pane fade" id="features">
							<ul class="fa-ul" data-list="<?php echo $multi_pdf; ?>">
								<?php echo $multi_text; ?>
							</ul>
						</div>
					<?php } ?>

					<?php if(!empty($third_tab)){ ?>
						<div class="tab-pane fade" id="technical">
							<?php
							if(isset($post_meta['technical_specifications']) && !empty($post_meta['technical_specifications'])){
								echo wpautop(do_shortcode($post_meta['technical_specifications']));
							}
							?>
						</div>
					<?php } ?>

					<?php if(!empty($fourth_tab)){ ?>
						<div class="tab-pane fade" id="location">
							<?php
							$latitude  = (isset($location['latitude']) && !empty($location['latitude']) ? $location['latitude'] : "");
							$longitude = (isset($location['longitude']) && !empty($location['longitude']) ? $location['longitude'] : "");
							$zoom      = (isset($location['zoom']) && !empty($location['zoom']) ? $location['zoom'] : 11);

							if(!empty($latitude) && !empty($longitude)){ ?>
								<div class='google_map_init contact' data-longitude='<?php echo $longitude; ?>' data-latitude='<?php echo $latitude; ?>' data-zoom='<?php echo $zoom; ?>' data-scroll="false" style="height: 350px;" data-parallax="false"></div>
							<?php } else { ?>
								<?php echo $no_location_text; ?>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if(!empty($fifth_tab)){ ?>
						<div class="tab-pane fade" id="comments">
							<?php echo (isset($post_meta['other_comments']) && !empty($post_meta['other_comments']) ? wpautop(do_shortcode($post_meta['other_comments'])) : ""); ?>
						</div>
					<?php } ?>
				</div>
			</div>

			<?php
			$sold_listing_comment = (isset($lwp_options['sold_listing_comment']) && !empty($lwp_options['sold_listing_comment']) ? $lwp_options['sold_listing_comment'] : "");

			if($post_meta['car_sold'] && $post_meta['car_sold'] == 1) {
				echo wpautop( do_shortcode( $sold_listing_comment ) );
			} ?>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4 right-content padding-right-none">
			<div class="side-content margin-bottom-50">
				<div class="car-info margin-bottom-50">
					<div class="table-responsive">
						<table class="table">
							<tbody>
							<?php
							$listing_categories = $Listing->get_listing_categories();

							if(!empty($listing_categories)){
								foreach($listing_categories as $key => $category){
									$slug  = $category['slug'];
									$value = (isset($post_meta[$slug]) && !empty($post_meta[$slug]) ? $post_meta[$slug] : "");

									if(empty($post_meta[$slug]) && isset($category['compare_value']) && $category['compare_value'] != "="){
										$post_meta[$slug] = 0;
									} elseif(empty($post_meta[$slug])) {
										$post_meta[$slug] = $none_text;
									}

									// price
									if(isset($category['currency']) && $category['currency'] == 1){
										$value = $Listing->format_currency($value);
									}

									if(!isset($category['hide_category']) || $category['hide_category'] == 0){
										echo ((mb_strtolower($value) != "none" && !empty($value)) && $value != __("None", "listings") ? "<tr><td>" . $category['singular'] . ": </td><td>" . html_entity_decode($value) . "</td></tr>" : "");
									}
								}
							} ?>
							</tbody>
						</table>
					</div>
				</div>

				<?php
				if(isset($post_meta['woocommerce_integration_id']) && !empty($post_meta['woocommerce_integration_id'])){
					$Listing->woocommerce_integration($post_meta['woocommerce_integration_id']);
				} ?>

				<?php if(isset($lwp_options['fuel_efficiency_show']) && $lwp_options['fuel_efficiency_show'] == 1){ ?>
					<div class="efficiency-rating text-center padding-vertical-15 margin-bottom-40">
						<h3><?php _e("Fuel Efficiency Rating", "listings"); ?></h3>
						<ul>
							<?php $fuel_icon = (isset($lwp_options['fuel_efficiency_image']) && !empty($lwp_options['fuel_efficiency_image']) ? $lwp_options['fuel_efficiency_image']['url'] : ICON_DIR . "fuel_pump.png"); ?>
							<li class="city_mpg"><small><?php echo (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : ""); ?>:</small> <strong><?php echo (isset($listing_options['city_mpg']['value']) && !empty($listing_options['city_mpg']['value']) ? $listing_options['city_mpg']['value'] : __("N/A", "listings")); ?></strong></li>
							<li class="fuel"><?php echo (!empty($fuel_icon) ? '<img src="'.$fuel_icon.'" alt="" class="aligncenter">' : ""); ?></li>
							<li class="hwy_mpg"><small><?php echo (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : ""); ?>:</small> <strong><?php echo (isset($listing_options['highway_mpg']['value']) && !empty($listing_options['highway_mpg']['value']) ? $listing_options['highway_mpg']['value'] : __("N/A", "listings")); ?></strong></li>
						</ul>
						<p><?php echo (isset($lwp_options['fuel_efficiency_text']) ? $lwp_options['fuel_efficiency_text'] : ""); ?></p>
					</div>
				<?php } ?>

				<?php if(isset($lwp_options['display_vehicle_video']) && $lwp_options['display_vehicle_video'] == 1 && !empty($listing_options['video'])){ ?>
					<?php
					if ( isset( $listing_options['video'] ) && ! empty( $listing_options['video'] ) ) {

						$video_id = $Listing->get_video_id($listing_options['video']);

						if($video_id){
							echo "<br>";

							if($video_id[0] == "youtube"){
								echo "<iframe width=\"560\" height=\"315\" src=\"http://www.youtube.com/embed/" . $video_id[1] . "\" frameborder=\"0\" allowfullscreen></iframe>";
							} elseif($video_id[0] == "vimeo"){
								echo "<iframe width=\"560\" height=\"315\" src=\"http://player.vimeo.com/video/" . $video_id[1] . "\" frameborder=\"0\" allowfullscreen></iframe>";
							}
						} else {
							echo __( "Not a valid YouTube/Vimeo link", "listings" ) . "...";
						}
					}
					?>
				<?php } ?>

				<?php if(isset($lwp_options['social_icons_show']) && $lwp_options['social_icons_show'] == 1){ ?>
					<ul class="social-likes pull-right listing_share" data-url="<?php echo get_permalink(); ?>" data-title="<?php the_title(); ?>">
						<li class="facebook" title="<?php echo $share_facebook_text; ?>"></li>
						<li class="plusone" title="<?php echo $share_google_text; ?>"></li>
						<li class="pinterest" title="<?php echo $share_pinterest_text; ?>" data-media="<?php echo (isset($gallery_images[0]) && !empty($gallery_images[0]) ? $Listing->auto_image($gallery_images[0], "full", true) : ""); ?>"></li>
						<li class="twitter" title="<?php echo $share_twitter_text; ?>"></li>
					</ul>
				<?php } ?>

				<div class="clearfix"></div>
				<?php if(isset($lwp_options['calculator_show']) && $lwp_options['calculator_show'] == 1){
					if( class_exists("Loan_Calculator") ){
						the_widget("Loan_Calculator", array("text_below" => (isset($Listing->lwp_options['calculator_below_text']) && !empty($Listing->lwp_options['calculator_below_text']) ? $Listing->lwp_options['calculator_below_text'] : ""), "rate" => $lwp_options['calculator_rate'], "down_payment" => $lwp_options['calculator_down_payment'], "loan_years" => $lwp_options['calculator_loan'], "price" => (isset($listing_options['price']['value']) && !empty($listing_options['price']['value']) ? $listing_options['price']['value'] : "")), array('before_widget' => '<div class="widget loan_calculator margin-top-40">', 'before_title' => '<h3 class="side-widget-title margin-bottom-25">', 'after_title' => '</h3>'));
					}
				} ?>
			</div>

			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>

		<?php if(isset($lwp_options['listing_comment_footer']) && !empty($lwp_options['listing_comment_footer'])){ ?>
			<div class="listing_bottom_message margin-top-30">
				<?php echo do_shortcode(wpautop($lwp_options['listing_comment_footer'])); ?>
			</div>
		<?php } ?>

		<?php if(isset($lwp_options['recent_vehicles_show']) && $lwp_options['recent_vehicles_show'] == 1){
			$other_options = ((isset($lwp_options['related_category']) && !empty($lwp_options['related_category']) ? $lwp_options['related_category'] : "") ? array("related_val" => $post_meta[$lwp_options['related_category']], "current_id" => $post->ID) : array());
			echo vehicle_scroller($lwp_options['recent_vehicles_title'], $lwp_options['recent_vehicles_desc'],  $lwp_options['recent_vehicles_limit'], (isset($lwp_options['recent_related_vehicles']) && $lwp_options['recent_related_vehicles'] == 0 ? "related" : "newest"), null, $other_options );
		} ?>
	</div>

	<?php
	if( isset($lwp_options['listing_comments']) && $lwp_options['listing_comments'] == 1 ){
		echo '<div class="comments page-content margin-top-30 margin-bottom-40">';
		comments_template();
		echo '</div>';
	} ?>
</div>