<?php
//********************************************
//	Custom meta boxes
//***********************************************************
function plugin_add_custom_boxes() {
	add_meta_box( "listing", __( "Listing Tabs", "listings" ), "listing_tabs", "listings", "normal", "core", null );
	add_meta_box( "gallery", __( "Listing Options", "listings" ), "gallery_images", "listings", "normal", "core", null );
}

function listing_tabs() {
	global $post, $lwp_options, $Listing; ?>
	<div id="listing_tabs" class="automotive_meta_tabs loading_status_tabs">
		<div class="loading_tabs_overlay">
			<span class="spinner is-active"></span>
		</div>

		<div style="display:none;" class="hidden_content">
			<ul>
				<?php
				$first_tab  = ( isset( $lwp_options['first_tab'] ) && ! empty( $lwp_options['first_tab'] ) ? $lwp_options['first_tab'] : "" );
				$second_tab = ( isset( $lwp_options['second_tab'] ) && ! empty( $lwp_options['second_tab'] ) ? $lwp_options['second_tab'] : "" );
				$third_tab  = ( isset( $lwp_options['third_tab'] ) && ! empty( $lwp_options['third_tab'] ) ? $lwp_options['third_tab'] : "" );
				$fourth_tab = ( isset( $lwp_options['fourth_tab'] ) && ! empty( $lwp_options['fourth_tab'] ) ? $lwp_options['fourth_tab'] : "" );
				$fifth_tab  = ( isset( $lwp_options['fifth_tab'] ) && ! empty( $lwp_options['fifth_tab'] ) ? $lwp_options['fifth_tab'] : "" ); ?>

				<?php echo( ! empty( $first_tab ) ? "<li><a href=\"#tabs-1\"><i class='fa fa-list-alt'></i> " . $first_tab . "</a></li>" : "" ); ?>
				<?php echo( ! empty( $second_tab ) ? "<li data-action=\"options\"><a href=\"#tabs-2\"><i class='fa fa-list-ul'></i> " . $second_tab . "</a></li>" : "" ); ?>
				<?php echo( ! empty( $third_tab ) ? "<li><a href=\"#tabs-3\"><i class='fa fa-cogs'></i> " . $third_tab . "</a></li>" : "" ); ?>
				<?php echo( ! empty( $fourth_tab ) ? "<li data-action=\"map\"><a href=\"#tabs-4\"><i class='fa fa-map-marker'></i> " . $fourth_tab . "</a></li>" : "" ); ?>
				<?php echo( ! empty( $fifth_tab ) ? "<li><a href=\"#tabs-5\"><i class='fa fa-comments-o'></i> " . $fifth_tab . "</a></li>" : "" ); ?>
			</ul>

			<?php if ( ! empty( $first_tab ) ) { ?>
				<div id="tabs-1">
					<div class="tab_content">
					<?php wp_editor( $post->post_content, "content", array( "textarea_rows" => 12 ) ); ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( ! empty( $second_tab ) ) { ?>
				<div id="tabs-2">
					<div class="tab_content">
					<?php
					$single_category = $Listing->get_single_listing_category( 'options' );
					$options         = ( isset( $single_category['terms'] ) && ! empty( $single_category['terms'] ) ? $single_category['terms'] : "" );

					if ( ! empty( $options ) ) {
						/* Default Options */
						$default_options = get_option( "options_default_auto" );

						$multi_options = get_post_meta( $post->ID, "multi_options", true );

						natcasesort( $options );

						$numberOfColumns = 3;
						$numberOfRows = ceil(sizeof($options) / $numberOfColumns);
						$data = array_fill_keys(range(1, $numberOfRows), array());
						$i = 1;
						foreach ( $options as $k => $v ) {
							$data[$i][] = $v;
							if ( $i == $numberOfRows ) {
								$i = 1;
							} else {
								$i++;
							}
						}

						echo "<table>";

						foreach($data as $row){
							$row[1] = stripslashes($row[1]);
							if(isset($row[2])) {
								$row[2] = stripslashes( $row[2] );
							}

							echo "<tr>";
							echo "<td><label><input type='checkbox' value='" . $row[0] . "' name='multi_options[]'" . ( is_array( $multi_options ) && ( in_array( $row[0], $multi_options ) ) || ( $Listing->is_edit_page( 'new' ) && is_array( $default_options ) && in_array( $row[0], $default_options ) ) ? " checked='checked'" : "" ) . ">" . $row[0] . "</label></td>\n";
							echo (isset($row[1]) ? "<td><label><input type='checkbox' value='" . $row[1] . "' name='multi_options[]'" . ( is_array( $multi_options ) && ( in_array( $row[1], $multi_options ) ) || ( $Listing->is_edit_page( 'new' ) && is_array( $default_options ) && in_array( $row[1], $default_options ) ) ? " checked='checked'" : "" ) . ">" . $row[1] . "</label></td>\n" : "");
							echo (isset($row[2]) ? "<td><label><input type='checkbox' value='" . $row[2] . "' name='multi_options[]'" . ( is_array( $multi_options ) && ( in_array( $row[2], $multi_options ) ) || ( $Listing->is_edit_page( 'new' ) && is_array( $default_options ) && in_array( $row[2], $default_options ) ) ? " checked='checked'" : "" ) . ">" . $row[2] . "</label></td>\n" : "");
							echo "</tr>";
						}

						echo "</table>";
					} else {
						echo "<table>";

						echo "</table>";
					} ?>

					<h4 style="margin-bottom: 5px;"><a href="#" class="hide-if-no-js add_new_name"
					                                   data-id="options">+ <?php _e( "Add New Option", "listings" ); ?></a>
					</h4>

					<div class='add_new_content options_sh' style="display: none;">
						<input class='options' type='text' style="width: 100%; margin-left: 0;"/>
						<button class='button submit_new_name' data-type='options'
						        data-exact="options"><?php _e( "Add New Option", "listings" ); ?></button>
						        </div>
					</div>
				</div>
			<?php } ?>

			<?php if ( ! empty( $third_tab ) ) { ?>
				<div id="tabs-3">
					<div class="tab_content">
					<?php $technical_specifications = get_post_meta( $post->ID, "technical_specifications", true );
					wp_editor( $technical_specifications, "technical_specifications", array(
						"media_buttons" => true,
						"textarea_rows" => 12
					) ); ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( ! empty( $fourth_tab ) ) { ?>
				<div id="tabs-4">
					<div class="tab_content">
					<i class='fa-info-circle auto_info_tooltip fa'
					   data-title="<?php _e( "Right click on the google map to store the coordinates of a location", "listings" ); ?>!"></i>
					<?php $location = get_post_meta( $post->ID, "location_map", true );

					if ( empty( $location ) ) {
						$location['latitude']  = ( isset( $lwp_options['default_value_lat'] ) && ! empty( $lwp_options['default_value_lat'] ) ? $lwp_options['default_value_lat'] : "" );
						$location['longitude'] = ( isset( $lwp_options['default_value_long'] ) && ! empty( $lwp_options['default_value_long'] ) ? $lwp_options['default_value_long'] : "" );
						$location['zoom']      = ( isset( $lwp_options['default_value_zoom'] ) && ! empty( $lwp_options['default_value_zoom'] ) ? $lwp_options['default_value_zoom'] : "" );
					}

					?>
					<table border='0'>
						<tr>
							<td><?php _e( "Latitude", "listings" ); ?>:</td>
							<td><input type='text' name='location_map[latitude]' class='location_value'
							           data-location='latitude'
							           value='<?php echo( isset( $location['latitude'] ) && ! empty( $location['latitude'] ) ? $location['latitude'] : "43.653226" ); ?>'/>
							</td>
						</tr>
						<tr>
							<td><?php _e( "Longitude", "listings" ); ?>:</td>
							<td><input type='text' name='location_map[longitude]' class='location_value'
							           data-location='longitude'
							           value='<?php echo( isset( $location['longitude'] ) && ! empty( $location['longitude'] ) ? $location['longitude'] : "-79.3831843" ); ?>'/>
							</td>
						</tr>
						<tr>
							<td><?php _e( "Zoom", "listings" ); ?>:</td>
							<td><span class='zoom_level_text'></span><input type='hidden' readonly="readonly"
							                                                class='zoom_level' name='location_map[zoom]'
							                                                value='<?php echo( isset( $location['zoom'] ) && ! empty( $location['zoom'] ) ? $location['zoom'] : 10 ); ?>'/>
							</td>
						</tr>
					</table>
					<br/>

					<div
						id='google-map'<?php echo " data-latitude='" . ( isset( $location['latitude'] ) && ! empty( $location['latitude'] ) ? $location['latitude'] : "43.653226" ) . "'";
					echo " data-longitude='" . ( isset( $location['longitude'] ) && ! empty( $location['longitude'] ) ? $location['longitude'] : "-79.3831843" ) . "'"; ?>></div>

					<div id="slider-vertical" style="height: 400px;"
					     data-value="<?php echo( isset( $location['zoom'] ) && ! empty( $location['zoom'] ) ? $location['zoom'] : 10 ); ?>"></div>
				</div>
				</div>
			<?php } ?>

			<?php if ( ! empty( $fifth_tab ) ) { ?>
				<div id="tabs-5">
					<div class="tab_content">
					<?php $other_comments = get_post_meta( $post->ID, "other_comments", true );
					wp_editor( $other_comments, "other_comments", array(
						"media_buttons" => true,
						"textarea_rows" => 12
					) ); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php
}

function gallery_images() {
	global $post, $lwp_options, $Listing;

	$saved_images = get_post_meta( $post->ID, 'gallery_images' );
	if ( isset( $saved_images[0] ) && ! empty( $saved_images[0] ) ) {
		$gallery_images = array_values( array_filter( $saved_images ) );
		$gallery_images = $gallery_images[0];
	}

	$post_options = get_post_meta( $post->ID, "listing_options" );
	$options      = @unserialize( $post_options[0] );

	// translations
	$image        = __( "Image", "listings" );
	$change_image = __( "Change image", "listings" );
	$set_default  = __( "Set default image", "listings" );
	$delete_image = __( "Delete image", "listings" );
	$move_image   = __( "Move Image", "listings" );
	$no_images    = __( "No gallery images", "listings" );
	$url_text     = __( "URL", "listings" ); ?>
	<div id="meta_tabs" class="automotive_meta_tabs loading_status_tabs">
		<div class="loading_tabs_overlay">
			<span class="spinner is-active"></span>
		</div>

		<div style="display:none;" class="hidden_content">
			<ul>
				<li><a href="#tab-images"><i class="fa fa-picture-o"></i> <?php _e( "Gallery Images", "listings" ); ?></a></li>
				<!--<li><a href="#tab-2"><i class="fa fa-briefcase"></i> <?php _e( "Details", "listings" ); ?></a></li>-->
				<li><a href="#tab-price"><i class="fa fa-usd"></i> <?php _e( "Pricing", "listings" ); ?></a></li>
				<li><a href="#tab-tax"><i class="fa fa-university"></i> <?php _e( "Tax Labels", "listings" ); ?></a></li>
				<?php echo ( isset( $lwp_options['woocommerce_listing_integration'] ) && $lwp_options['woocommerce_listing_integration'] == 1  ? '<li><a href="#tab-woo"><i class="fa fa-shopping-cart"></i> ' . __( "WooCommerce", "listings" ) . '</a></li>' : ""); ?>
				<li><a href="#tab-fuel"><i class="fa fa-car"></i> <?php _e( "Fuel Efficiency", "listings" ); ?></a></li>
				<li><a href="#tab-pdf"><i class="fa fa-file-pdf-o"></i> <?php _e( "PDF Replacement", "listings" ); ?></a></li>
				<li><a href="#tab-video"><i class="fa fa-video-camera"></i> <?php _e( "Video", "listings" ); ?></a></li>
				<li><a href="#tab-badge"><i class="fa fa-certificate"></i> <?php _e( "Listing Badge", "listings" ); ?></a></li>
				<li><a href="#tab-categories"><i class="fa fa-list"></i> <?php _e( "Listing Categories", "listings" ); ?></a></li>
				<li><a href="#tab-additional"><i class="fa fa-list-ul"></i> <?php _e( "Additional Categories", "listings" ); ?></a></li>
				<li><a href="#tab-others"><i class="fa fa-cog"></i> <?php _e( "Widget Settings", "listings" ); ?></a></li>
				<li><a href="#tab-status"><i class="fa fa-sliders"></i> <?php _e( "Status", "listings" ); ?></a></li>
			</ul>

			<div id="tab-images">
				<?php echo $Listing->automotive_admin_help_message(__("These are the images used in the slideshow when viewing the single listing. The first image (also known as the default image) is used throughout the site in the comparison table, recent vehicle scroller and on the inventory page.", "listings")); ?>

				<div class="tab_content">
					<?php if($Listing->is_hotlink()){ ?>
						<table id="gallery_images" class="hotlink" data-image="<?php echo $image; ?>"
						       data-change-image="<?php echo $change_image; ?>" data-set-default="<?php echo $set_default; ?>"
						       data-delete-image="<?php echo $delete_image; ?>" data-move-image="<?php echo $move_image; ?>"
						       data-no-images="<?php echo $no_images; ?>" data-url="<?php echo $url_text; ?>">
							<?php
							if ( isset( $gallery_images ) && ! empty( $gallery_images ) ) {
								$i = 1;
								echo "<tbody>";
								foreach ( $gallery_images as $gallery_image ) {
									echo "<tr><td data-id='" . $i . "'><div class='top_header'><input type='url' name='gallery_images[]' placeholder='" . $url_text . "' value='" . $gallery_image . "'></div>";
									echo "<div class='image_preview'>" . $Listing->auto_image( $gallery_image, "auto_thumb" ) . "</div>";
									echo "<div class='buttons'><span class='button make_default_image" . ( $i == 1 ? " active_image" : "" ) . "'>" . $set_default . "</span> ";
									echo "<span class='button delete_image'>" . $delete_image . "</span> ";
									echo "<span class='button move_image'>" . $move_image . "</span></div>";
									echo "</td></tr>";
									$i ++;
								}
								echo "</tbody>";
							} else {
								echo "<tbody><tr class='no_images'><td>" . $no_images . "</td></tr></tbody>";
							} ?>
						</table>
						<button class='add_image_hotlink button button-primary'><?php _e( "Add Image", "listings" ); ?></button>
					<?php } else { ?>
						<table id="gallery_images" data-image="<?php echo $image; ?>"
						       data-change-image="<?php echo $change_image; ?>" data-set-default="<?php echo $set_default; ?>"
						       data-delete-image="<?php echo $delete_image; ?>" data-move-image="<?php echo $move_image; ?>"
							   data-no-images="<?php echo $no_images; ?>">
							<?php
							if ( isset( $gallery_images ) && ! empty( $gallery_images ) ) {
								$i = 1;
								echo "<tbody>";
								foreach ( $gallery_images as $gallery_image ) {
									echo "<tr><td data-id='" . $i . "'><div class='top_header'>" . $image . " #{$i}</div>";
									echo "<div class='image_preview'>" . $Listing->auto_image( $gallery_image, "auto_thumb" ) . "</div>";
									echo "<div class='buttons'><span class='button add_image_gallery' data-id='" . $i . "'>" . $change_image . "</span> ";
									echo "<span class='button make_default_image" . ( $i == 1 ? " active_image" : "" ) . "'>" . $set_default . "</span> ";
									echo "<span class='button delete_image'>" . $delete_image . "</span> ";
									echo "<span class='button move_image'>" . $move_image . "</span></div>";
									echo "<input type='hidden' name='gallery_images[]' value='" . $gallery_image . "'>";
									echo "</td></tr>";
									$i ++;
								}
								echo "</tbody>";
							} else {
								echo "<tbody><tr class='no_images'><td>" . $no_images . "</td></tr></tbody>";
							} ?>
						</table>
						<button class='add_image button button-primary'><?php _e( "Add Image", "listings" ); ?></button>
					<?php } ?>

					<div class='clear'></div>
				</div>
			</div>

			<div id="tab-price">
				<?php echo $Listing->automotive_admin_help_message(__("This allows you to control the pricing of the vehicle. The original price can be used for when there are sales on a specific vehicle.", "listings")); ?>

				<div class="tab_content">
				<?php
				$currency_symbol    = ( isset( $lwp_options['currency_symbol'] ) && ! empty( $lwp_options['currency_symbol'] ) ? $lwp_options['currency_symbol'] : "" );
				$currency_placement = ( isset( $lwp_options['currency_placement'] ) && ! empty( $lwp_options['currency_placement'] ) ? $lwp_options['currency_placement'] : "" );

				$decimal_sep  = ( isset( $lwp_options['currency_separator_decimal'] ) && ! empty( $lwp_options['currency_separator_decimal'] ) ? $lwp_options['currency_separator_decimal'] : "." );
				$thousand_sep = ( isset( $lwp_options['currency_separator'] ) && ! empty( $lwp_options['currency_separator'] ) ? $lwp_options['currency_separator'] : "." );
				$taxrate      = ( isset( $lwp_options['tax_amount'] ) && ! empty( $lwp_options['tax_amount'] ) ? "1." . $lwp_options['tax_amount'] : "0" );
				$decimals     = ( isset( $lwp_options['currency_decimals'] ) && ! empty( $lwp_options['currency_decimals'] ) ? $lwp_options['currency_decimals'] : "2" ); ?>

					<table>
						<tbody>
						<tr>
							<td>
								<h2 class="detail_heading"><?php _e( "Current Price", "listings" ); ?></h2><br>
								<?php echo( ! empty( $currency_symbol ) && $currency_placement == 1 ? $currency_symbol : "" ); ?>
								<input type="text" name="options[price][value]"
								       data-decimal-char="<?php echo $decimal_sep; ?>"
								       data-thousand-char="<?php echo $thousand_sep; ?>"
								       value="<?php echo( isset( $options['price']['value'] ) && ! empty( $options['price']['value'] ) ? $options['price']['value'] : "" ); ?>"
								       class="info price current_price" data-placement="right" data-trigger="focus"
								       data-title="<img src='<?php echo LISTING_DIR; ?>/images/thumbnails/widget_slider/example-price.png' style='opacity: 1'>"
								       data-html="true" data-original-title=""
								       title=""><?php echo( ! empty( $currency_symbol ) && $currency_placement == 0 ? $currency_symbol : "" ); ?>

								<?php if ( $Listing->is_tax_active() ) { ?>
									<br><label>
										<?php _e( "Add Tax on Value", "listings" ); ?>
										<input type="checkbox" class="add_tax_value"
										       data-input="current_price" <?php checked( $lwp_options['default_tax'], 1 ); ?>
										       data-taxrate="<?php echo $taxrate; ?>"
										       data-decimals="<?php echo $decimals; ?>">
									</label>
								<?php } ?>
							</td>
							<td>
								<h2 class="detail_heading"><?php _e( "Original Price (Optional)", "listings" ); ?></h2>
								<br>
								<?php echo( ! empty( $currency_symbol ) && $currency_placement == 1 ? $currency_symbol : "" ); ?>
								<input type="text" name="options[price][original]"
								       data-decimal-char="<?php echo $decimal_sep; ?>"
								       data-thousand-char="<?php echo $thousand_sep; ?>"
								       value="<?php echo( isset( $options['price']['original'] ) && ! empty( $options['price']['original'] ) ? $options['price']['original'] : "" ); ?>"
								       class="info price original_price" data-placement="right" data-trigger="focus"
								       data-title="<img src='<?php echo LISTING_DIR; ?>/images/thumbnails/widget_slider/example-original.png' style='opacity: 1'>"
								       data-html="true" data-original-title=""
								       title=""><?php echo( ! empty( $currency_symbol ) && $currency_placement == 0 ? $currency_symbol : "" ); ?>

								<?php if ( $Listing->is_tax_active() ) { ?>
									<br><label>
										<?php _e( "Add Tax on Value", "listings" ); ?>
										<input type="checkbox" class="add_tax_value"
										       data-input="original_price" <?php checked( $lwp_options['default_tax'], 1 ); ?>
										       data-taxrate="<?php echo $taxrate; ?>"
										       data-decimals="<?php echo $decimals; ?>">
									</label>
								<?php } ?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<?php if( isset( $lwp_options['woocommerce_listing_integration'] ) && $lwp_options['woocommerce_listing_integration'] == 1){ ?>
			<div id="tab-woo">
				<?php echo $Listing->automotive_admin_help_message(__("This setting allows you to associate a WooCommerce product with a listing and show an Add to Cart section to allow users to checkout and purchase items.", "listings")); ?>

				<div class="tab_content">
					<table>
						<tr>
							<td>
								<?php
								$current_woo_id = get_post_meta($post->ID, "woocommerce_integration_id", true);

								$args = array(
									'post_type'      => 'product',
									'posts_per_page' => - 1
								);

								$loop = get_posts( $args );

								if ( ! empty( $loop ) ) {
									echo "<select name='woocommerce_integration_id'>";
									echo "<option value=''>" . __( "No product association", "listings" ) . "</option>";

									foreach ( $loop as $product ) {
										$current_id = $product->ID;


										echo "<option value='" . $current_id . "'" . selected( $current_id, $current_woo_id, false ) . ">" . $product->post_title . "</option>";
									}

									echo "</select>";
								} else {
									echo __( 'No products found', 'listings' );
								}
								wp_reset_query();
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<?php } ?>

			<div id="tab-pdf">
				<?php echo $Listing->automotive_admin_help_message(__("This setting allows you to upload a custom PDF for the user to download rather than the automatically generated one.", "listings")); ?>

				<div class="tab_content">
					<?php $pdf_brochure = get_post_meta( $post->ID, "pdf_brochure_input", true );
					$pdf_link           = wp_get_attachment_url( $pdf_brochure ); ?>

					<button class="pick_pdf_brochure button primary"
					        style="margin-top: 10px;"><?php _e( "Choose a PDF Brochure", "listings" ); ?></button>

					<?php if ( isset( $pdf_link ) && ! empty( $pdf_link ) ) {
						echo "<button class='remove_pdf_brochure button primary'>" . __( "Remove", "listings" ) . "</button>";
					} ?>

					<br><br> <?php _e( "Current File", "listings" ); ?>: <span class="pdf_brochure_label"><a
							href="<?php echo $pdf_link; ?>" target="_blank"><?php echo $pdf_link; ?></a></span>

					<input type="hidden" name="pdf_brochure_input" class="pdf_brochure_input"
					       value="<?php echo $pdf_brochure; ?>">
				</div>
			</div>

			<div id="tab-tax">
				<?php echo $Listing->automotive_admin_help_message(__("Here you can customize the tax labels used on this individual listing, if you click on a text box you can see exactly which area in a screenshot.", "listings")); ?>

				<div class="tab_content">
					<?php
					$custom_tax_inside = ( isset( $options['custom_tax_inside'] ) && ! empty( $options['custom_tax_inside'] ) ? $options['custom_tax_inside'] : "" );
					$custom_tax_page   = ( isset( $options['custom_tax_page'] ) && ! empty( $options['custom_tax_page'] ) ? $options['custom_tax_page'] : "" );
					?>
					<table>
						<tr>
							<td><?php _e( "Tax Label (below the price) on Listing Page", "listings" ); ?>:</td>
							<td><input type='text' name='options[custom_tax_inside]'
							           value='<?php echo $custom_tax_inside; ?>' class='info' data-placement='right'
							           data-trigger='focus'
							           data-title="<img src='<?php echo THUMBNAIL_URL; ?>widget_slider/example-tax-inside.png' style='opacity: 1'>"
							           data-html='true'/></td>
						</tr>
						<tr>
							<td><?php _e( "Tax Label (below the price) on Inventory Page", "listings" ); ?>:</td>
							<td><input type='text' name='options[custom_tax_page]'
							           value='<?php echo $custom_tax_page; ?>'
							           class='info' data-placement='right' data-trigger='focus'
							           data-title="<img src='<?php echo THUMBNAIL_URL; ?>widget_slider/example-tax-page.png' style='opacity: 1'>"
							           data-html='true'/></td>
						</tr>
					</table>
				</div>
			</div>

			<div id="tab-fuel">
				<?php echo $Listing->automotive_admin_help_message(__("Set the city and highway MPG for the current listing.", "listings")); ?>

				<div class="tab_content">
					<table>
						<tbody>
						<tr>
							<td>
								<h2 class="detail_heading"><?php _e( "City MPG", "listings" ); ?></h2><br>
								<input type="text" name="options[city_mpg][value]"
								       placeholder="<?php _e( "City MPG", "listings" ); ?>"
								       value="<?php echo( isset( $options['city_mpg']['value'] ) && ! empty( $options['city_mpg']['value'] ) ? $options['city_mpg']['value'] : "" ); ?>"
								       class="info city_mpg" data-placement="right" data-trigger="focus"
								       data-title="<img src='<?php echo LISTING_DIR; ?>/images/thumbnails/widget_slider/example-city_mpg.png' style='opacity: 1'>"
								       data-html="true" data-original-title="" title="">
							</td>
							<td>
								<h2 class="detail_heading"><?php _e( "Highway MPG", "listings" ); ?></h2><br>
								<input type="text" name="options[highway_mpg][value]"
								       placeholder="<?php _e( "Highway MPG", "listings" ); ?>"
								       value="<?php echo( isset( $options['highway_mpg']['value'] ) && ! empty( $options['highway_mpg']['value'] ) ? $options['highway_mpg']['value'] : "" ); ?>"
								       class="info highway_mpg" data-placement="right" data-trigger="focus"
								       data-title="<img src='<?php echo LISTING_DIR; ?>/images/thumbnails/widget_slider/example-highway_mpg.png' style='opacity: 1'>"
								       data-html="true" data-original-title="" title="">
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div id="tab-badge">
				<?php echo $Listing->automotive_admin_help_message(__("Here you can enable a listing badge on a listing and control the color. Creating custom badges can be done under Listing Options >> Automotive Settings >> Custom Badges.", "listings")); ?>

				<div class="tab_content">
					<?php $custom_badges = (isset($lwp_options['custom_badges']) && !empty($lwp_options['custom_badges']) ? $lwp_options['custom_badges'] : "");

					if( !empty($custom_badges) ){
						$custom_badges['name']   = array_values(array_filter($custom_badges['name']));
						$custom_badges['color']  = array_values(array_filter($custom_badges['color']));
						$custom_badges['font']   = array_values(array_filter($custom_badges['font']));

						$options['custom_badge'] = (isset($options['custom_badge']) && !empty($options['custom_badge']) ? $options['custom_badge'] : "");

						echo __("Custom Listing Badge", "listings") . ": <select name='options[custom_badge]'>";
						echo "<option value=''>" . __("None", "listings") . "</option>";
						foreach($custom_badges['name'] as $key => $badge_name){
							if(!empty($badge_name)){
								echo "<option value='" . $badge_name . "'" . selected($options['custom_badge'], $badge_name, false) . ">" . $badge_name . "</option>";
							}
						}
						echo "</select>";
					}

					echo "<hr>"; ?>

					<h4 style="margin-bottom: 5px;"><?php _e("Add a new badge", "listings"); ?></h4>
					<table>
						<tr><td><?php _e("Badge Name", "listings"); ?>:</td><td> <input type="text" name="new_badge_name" class="new_badge_name"></td></tr>
						<tr><td><?php _e("Badge Color", "listings"); ?>:</td><td> <input type="text" name="new_badge_color" class="new_badge_color auto_color_picker"></td></tr>
						<tr><td><?php _e("Font Color", "listings"); ?>:</td><td> <input type="text" name="new_badge_font" class="new_badge_font auto_color_picker"></td></tr>
						<tr><td colspan="2"> <button class="add_new_badge button-primary"><?php _e("Add New Badge", "listings"); ?></button> </td></tr>
					</table>
				</div>
			</div>

			<div id="tab-video">
				<?php echo $Listing->automotive_admin_help_message(__("Add a YouTube/Vimeo video to show off the listing in action.", "listings")); ?>

				<div class="tab_content">
					<?php _e( "YouTube/Vimeo link", "listings" ); ?>: <input type='text' name='options[video]'
					                                                         id='listing_video_input'
					                                                         style='width: 500px;'<?php echo( isset( $options['video'] ) && ! empty( $options['video'] ) ? " value='" . $options['video'] . "'" : "" ); ?> />

					<div id='listing_video'>
						<?php if ( isset( $options['video'] ) && ! empty( $options['video'] ) ) {

							$video_id = $Listing->get_video_id($options['video']);

							if($video_id){
								echo "<br><br>";

								if($video_id[0] == "youtube"){
									echo "<iframe width=\"644\" height=\"400\" src=\"http://www.youtube.com/embed/" . $video_id[1] . "\" frameborder=\"0\" allowfullscreen></iframe>";
								} elseif($video_id[0] == "vimeo"){
									echo "<iframe width=\"644\" height=\"400\" src=\"http://player.vimeo.com/video/" . $video_id[1] . "\" frameborder=\"0\" allowfullscreen></iframe>";
								}
							} else {
								echo __( "Not a valid YouTube/Vimeo link", "listings" ) . "...";
							}
						} ?>
					</div>
				</div>
			</div>

			<div id="tab-categories">
				<?php echo $Listing->automotive_admin_help_message(__("This allows you to the listing category values you have configured for your listings.", "listings")); ?>

				<div class="tab_content">
					<table style="width: 100%;">
						<?php
						$listing_categories = $Listing->get_listing_categories();

						if ( ! empty( $listing_categories ) ) {
							foreach ( $listing_categories as $category ) {
								$slug = $category['slug'];

								$category['link_value'] = ( isset( $category['link_value'] ) && ! empty( $category['link_value'] ) ? $category['link_value'] : "" );

								// link value
								if ( empty( $category['link_value'] ) || $category['link_value'] == "none" ) {
									echo "<tr><td>" . $category['singular'] . ": </td><td>";

									if ( ! isset( $category['compare_value'] ) || ( isset( $category['compare_value'] ) && $category['compare_value'] == "=" ) ) {
										echo "<select name='" . $slug . "' style='width: 100%;' id='" . $slug . "'>";
										echo "<option value='" . __( "None", "listings" ) . "'>" . __( "None", "listings" ) . "</option>";

										// sort
										if ( ! empty( $category['terms'] ) ) {
											if ( isset( $category['sort_terms'] ) && $category['sort_terms'] == "desc" ) {
												arsort( $category['terms'] );
											} else {
												asort( $category['terms'] );
											}
										}

										if ( ! empty( $category['terms'] ) ) {
											foreach ( $category['terms'] as $term_key => $term ) {
												$option_value = htmlentities( stripslashes( $term ), ENT_QUOTES );

												echo "<option value='" . htmlentities( stripslashes( $option_value ), ENT_QUOTES ) . "' " . selected( $option_value, htmlentities( stripslashes( get_post_meta( $post->ID, $slug, true ) ), ENT_QUOTES ), false ) . ">" . stripslashes( $term ) . "</option>";
											}
										}

										echo "</select>";
									} else {
										$text_value = get_post_meta( $post->ID, $slug, true );
										echo "<input type='text' name='" . $slug . "' value='" . htmlspecialchars( stripslashes( $text_value ), ENT_QUOTES ) . "'>";
									}

									echo "</td><td style='text-align: right; width: 350px; max-width: 350px;'> <a href='#' class='hide-if-no-js add_new_name' data-id='" . $slug . "'>+ " . __( "Add New Term", "listings" ) . "</a>";
									echo '<div class="add_new_content ' . $slug . '_sh" style="display: none;">
							        <input class="' . $slug . '" type="text" style="margin-left: 0;" />
							        <button class="button submit_new_name" data-type="' . $slug . '" data-exact="' . $slug . '">' . __( "Add New Term", "listings" ) . '</button>
							    </div>';
									echo "</td></tr>";
								}
							}
						}
						?>
					</table>
				</div>
			</div>

			<div id="tab-additional">
				<?php echo $Listing->automotive_admin_help_message(__("Here you can set the additional categories you have configured under Listing Options >> Automotive Settings >> Additional Categories.", "listings")); ?>

				<div class="tab_content">
					<table>
						<?php
						$checked = get_post_meta( $post->ID, "verified", true );
						echo "<tr><td><label for='verified'>" . __( "Show vehicle history report image", "listings" ) . ":</label></td><td><input type='checkbox' name='verified' value='yes' id='verified'" . ( ( isset( $checked ) && ! empty( $checked ) ) || $Listing->is_edit_page( 'new' ) && isset( $lwp_options['default_vehicle_history']['on'] ) && $lwp_options['default_vehicle_history']['on'] == "1" ? " checked='checked'" : "" ) . "></td></tr>";

						$additional_categories = 'additional_categories';

						if ( $Listing->is_wpml_active() ) {
							$additional_categories .= '_' . ICL_LANGUAGE_CODE;
						}

						if ( ! empty( $lwp_options[ $additional_categories ]['value'] ) ) {
							foreach ( $lwp_options[ $additional_categories ]['value'] as $key => $category ) {
								if ( ! empty( $category ) ) {
									$safe_handle = str_replace( " ", "_", strtolower( $category ) );
									$current_val = get_post_meta( $post->ID, $safe_handle, true );

									if ( $Listing->is_edit_page( 'new' ) && isset( $lwp_options[ $additional_categories ]['check'][ $key ] ) && $lwp_options[ $additional_categories ]['check'][ $key ] == "on" ) {
										$current_val = 1;
									}

									echo "<tr><td><label for='" . $safe_handle . "'>" . $category . ":</label></td><td><input type='checkbox' name='" . $additional_categories . "[value][" . $safe_handle . "]' id='" . $safe_handle . "' value='1'" . ( $current_val == 1 ? "checked='checked'" : "" ) . "></td></tr>";
								}
							}
						} ?>
					</table>
				</div>
			</div>

			<div id="tab-others">
				<?php echo $Listing->automotive_admin_help_message(__("This allows you to set the short description used on the recent vehicle slider.", "listings")); ?>

				<div class="tab_content">

					<?php _e( "Short Description For Vehicle Slider Widget", "listings" ); ?>:

					<input type='text'
					       name='options[short_desc]'<?php echo( isset( $options['short_desc'] ) && ! empty( $options['short_desc'] ) ? " value='" . $options['short_desc'] . "'" : "" ); ?>
					       class='info' data-placement='right' data-trigger="focus"
					       data-title="<img src='<?php echo THUMBNAIL_URL; ?>widget_slider/example.png' width='183' height='201' style='opacity: 1'>"
					       data-html='true' style="margin-top: 10px"/>
				</div>
			</div>

			<div id="tab-status">
				<?php echo $Listing->automotive_admin_help_message(__("Set the vehicle status using toggles.", "listings")); ?>

				<div class="tab_content">
					<?php $car_sold = get_post_meta( $post->ID, "car_sold", true ); ?>

					<table>
						<tr><td><label for='sold_check'><?php _e( "Sold", "listings" ); ?>:</label></td><td><div class="auto_toggle toggle-light" data-checkbox="sold_check"></div> <input type='checkbox' name='car_sold' id='sold_check' class="hide" value='1' <?php echo ( isset( $car_sold ) && $car_sold == 1 ? " checked='checked'" : "" ); ?>></td></tr>
						<?php if(isset($lwp_options['featured_vehicle_widget']) && $lwp_options['featured_vehicle_widget'] == 1){
							$car_featured = get_post_meta( $post->ID, "car_featured", true ); ?>
							<tr><td><label for='featured_check'><?php _e( "Featured", "listings" ); ?>:</label></td><td><div class="auto_toggle toggle-light" data-checkbox="featured_check"></div> <input type='checkbox' name='car_featured' id='featured_check' class="hide" value='1' <?php echo ( isset( $car_featured ) && $car_featured == 1 ? " checked='checked'" : "" ); ?>></td></tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'add_meta_boxes', 'plugin_add_custom_boxes' );

function plugin_add_after_editor() {
	global $post, $wp_meta_boxes;

	do_meta_boxes( get_current_screen(), 'advanced', $post );

	$post_types = get_post_types();

	foreach ( $post_types as $post_type ) {
		unset( $wp_meta_boxes[ $post_type ]['advanced'] );
	}
}

add_action( "edit_form_after_title", "plugin_add_after_editor" );

function plugin_secondary_title() {
	global $post;

	$secondary_title = get_post_meta( $post->ID, "secondary_title", true );
	echo "<input type='text' value='" . $secondary_title . "' name='secondary_title' style='width:100%;'/>";
}

//********************************************
//	Custom meta boxes for custom categories
//***********************************************************
function plugin_register_menu_pages() {
	global $Listing;

	add_submenu_page( 'edit.php?post_type=listings', __( "Options", "listings" ), __( "Options", "listings" ), 'manage_options', 'options', 'auto_listing_category_terms_page' );

	$listing_categories = $Listing->get_listing_categories();

	foreach ( $listing_categories as $key => $field ) {
		$plural = $field['plural'];
		$slug   = $field['slug'];

		if ( ! empty( $plural ) && ! empty( $slug ) ) {
			add_submenu_page( 'edit.php?post_type=listings', $plural, stripslashes( $plural ), 'manage_options', $slug, 'auto_listing_category_terms_page' );
		}
	}
}


function auto_listing_category_terms_page() {
	global $Listing;

	// refresh the var to see newly added terms
	$Listing->refresh_listing_categories();

	$is_options = false;
	$value      = $svalue = $_GET['page'];
	$category   = $Listing->get_single_listing_category( $svalue );

	if ( $value == "options" ) {
		$label      = __( "Options", "listings" );
		$is_options = true;

		$default = get_option( "options_default_auto" );
	} else {
		$label = stripslashes( $category['singular'] );//ucwords(str_replace("_", " ", $_GET['page']));
	}

	$options = $options_key_order = ( isset( $category['terms'] ) && ! empty( $category['terms'] ) ? $category['terms'] : "" );
	$i       = 0;

	if ( ! empty( $options ) ) {

		// alphabetically sort options (case insensitive)
		$options = array_filter( $options, 'is_not_null' );

		array_multisort( array_map( 'strtolower', $options ), $options );

		if(!$is_options) {
			$total_options = count( $options );
			$per_page      = 50;
			$paged_options = array_chunk( $options, $per_page, true );
			$current_page  = ( ( isset( $_GET['o_page'] ) && ! empty( $_GET['o_page'] ) ? preg_replace( '/\D/', '', $_GET['o_page'] ) : 1 ) - 1 );

			$pagination = ' <div class="tablenav">
                            <div class="tablenav-pages">
                                <span class="displaying-num">' . $total_options . ' item' . ( $total_options != 1 ? 's' : '' ) . '</span>
                                <span class="pagination-links">';

			foreach ( $paged_options as $key => $value ) {
				$pagination .= '<a class="next-page' . ( $key == $current_page ? " disabled" : "" ) . '" href="' . add_query_arg( "o_page", ( $key + 1 ) ) . '">' . ( $key + 1 ) . '</a>';
			}

			$pagination .= '</span>
                            </div>
                        </div>';
		}

	} else {
		$pagination = ' <div class="tablenav">
                            <div class="tablenav-pages">
                                <span class="displaying-num">0 items</span>
                                <span class="pagination-links"><a class="next-page disabled" href="#">1</a></span>
                            </div>
                        </div>';
	}

	?>
	<style type="text/css"> .delete_name {
			cursor: pointer
		} </style>
	<div class='wrap nosubsub'>
		<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
		<h2 style="margin-bottom:25px;"><?php echo ucwords( $label ); ?></h2>

		<div id='col-container'>
			<div id='col-left' style='display: inline-block; width: 20%; vertical-align: top;'>
				<strong
					style="display: block;"><?php echo __( "Add New", "listings" ) . " " . $label; ?></strong><br/>

				<form method="POST" action="">
					<table border='0'>
						<tr>
							<td><?php _e( "Value", "listings" ); ?>:</td>
							<td> <?php echo( isset( $category['compare_value'] ) && ! empty( $category['compare_value'] ) && $category['compare_value'] != "=" ? $category['compare_value'] : "" ); ?>
								<input type='text' name='new_name'/></td>
						</tr>
						<tr>
							<td colspan="2"><input type='submit' class='button-primary' name='add_new_name'
							                       value='<?php _e( "Add", "listings" ); ?>'/></td>
						</tr>
					</table>
				</form>
			</div>

			<div id='col-right' style='display: inline-block; float: none; width: 79%;'>

				<?php echo (!$is_options ? $pagination : ""); ?>

				<form method="POST" action="">
					<table border='0' class='wp-list-table widefat fixed tags listing_table'
					       data-save-text='<?php _e( "Save", "listings" ); ?>' data-slug="<?php echo $svalue; ?>">
						<thead>
						<tr>
							<th><?php _e( "Value", "listings" ); ?></th>
							<th><?php _e( "Slug", "listings" ); ?></th>
							<th><?php _e( "Posts", "listings" ); ?></th>
							<?php if ( isset( $category['location_email'] ) && ! empty( $category['location_email'] ) ) { ?>
								<th><?php _e( "Email Address", "listings" ); ?></th>

								<?php $location_email = get_option( "location_email" );
							} ?>
							<th><?php _e( "Actions", "listings" ); ?></th>
							<?php echo( $is_options ? "<th>" . __( "Default Selection", "listings" ) . "</th>" : "" ); ?>
						</tr>
						</thead>

						<tbody>
						<?php
						//********************************************
						//  Page Pagination
						//***********************************************************
						if ( empty( $options ) ) {
							echo "<tr><td colspan='3'>" . __( "No terms yet", "listings" ) . "</td></tr>";
						} else {
							$loop_options = (!$is_options ? $paged_options[ $current_page ] : $options);

							foreach ( $loop_options as $key => $option ) {
								$option_label        = stripslashes( $option );
								$option_array_search = $option;

								echo "<tr" . ( $i % 2 == 0 ? " class='alt'" : "" ) . " id='t_" . $i . "'>\n<td>" . $option_label . "</td>\n";

								echo "<td>" . $Listing->slugify( $option_label ) . "</td>\n";

								echo "<td>" . get_total_meta( $svalue, $option_label, ( $is_options ) ) . "</td>\n";

								if ( isset( $category['location_email'] ) && ! empty( $category['location_email'] ) ) {
									echo "<td><input type='email' placeholder='" . __( "Email", "listings" ) . "' value='" . ( isset( $location_email[ htmlspecialchars_decode( $option ) ] ) && ! empty( $location_email[ htmlspecialchars_decode( $option ) ] ) ? $location_email[ htmlspecialchars_decode( $option ) ] : "" ) . "' name='location_email[" . htmlspecialchars( $option, ENT_QUOTES ) . "]'></td>\n";
								}

								echo "<td><button class='delete_name button-primary' data-id='" . array_search( $option_array_search, $options_key_order ) . "' data-type='" . $svalue . "' data-row='" . $i . "'>" . __( "Delete", "listings" ) . "</button>&nbsp;&nbsp;<button class='edit_name_text button-primary' data-id='" . array_search( $option_array_search, $options_key_order ) . "' data-type='" . $svalue . "' data-row='" . $i . "'>" . __( "Edit", "listings" ) . "</button></td>\n";

								if ( $is_options ) {
									echo "<td><input type='checkbox' name='default[]' value='" . $option . "' " . ( ! empty( $default ) && is_array($default) && in_array( $option, $default ) ? " checked='checked'" : "" ) . "></td>\n";
								}

								echo "</tr>\n";
								$i ++;
							}
						}
						?>
						</tbody>
					</table>

					<?php echo (!$is_options ? $pagination : ""); ?>

					<input type="submit" name="submit" value="Save Default" class="button button-primary"
					       style="margin-top: 15px;">

				</form>
			</div>
		</div>
	</div>
	<?php
}

// deleting
function plugin_delete_name() {
	global $Listing;

	$id   = $_POST['id'];
	$type = $_POST['type'];

	$listing_categories = $Listing->get_listing_categories( true );
	$current_category   = ( isset( $listing_categories[ $type ] ) && ! empty( $listing_categories[ $type ] ) ? $listing_categories[ $type ] : "" );

	// update the var
	$listing_categories[ $type ] = $current_category;

	unset( $listing_categories[ $type ]['terms'][ $id ] );

	update_option( $Listing->get_listing_categories_option_name(), $listing_categories );

	die;
}

add_action( "wp_ajax_delete_name", "plugin_delete_name" );
add_action( "wp_ajax_nopriv_delete_name", "plugin_delete_name" );

// ajax save
function plugin_add_name() {
	global $Listing;

	$name  = $_POST['value'];
	$type  = $_POST['type'];
	$exact = $_POST['exact'];

	$listing_categories                                                  = $Listing->get_listing_categories( true );
	$listing_categories[ $exact ]['terms'][ $Listing->slugify( $name ) ] = htmlspecialchars( $name, ENT_QUOTES );

	update_option( $Listing->get_listing_categories_option_name(), $listing_categories );

	die;
}

add_action( "wp_ajax_add_name", "plugin_add_name" );
add_action( "wp_ajax_nopriv_add_name", "plugin_add_name" );

// saving
function plugin_save_new_custom_meta() {
	if ( isset( $_POST['add_new_name'] ) ) {
		global $Listing;

		$name = $type = $_POST['new_name'];
		$slug = $Listing->slugify($name);

		$current_page = ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ? $_GET['page'] : "" );

		$listing_categories = $Listing->get_listing_categories( true );
		$current_category   = ( isset( $listing_categories[ $current_page ] ) && ! empty( $listing_categories[ $current_page ] ) ? $listing_categories[ $current_page ] : "" );

		if ( ! empty( $current_category['terms'] ) ) {
			$current_category['terms'][ $slug ] = $name;
		} else {
			$current_category['terms'] = array( $slug => $name );
		}

		// update the var
		$listing_categories[ $current_page ] = $current_category;

		update_option( $Listing->get_listing_categories_option_name(), $listing_categories );
	}

	if ( isset( $_POST['location_email'] ) && ! empty( $_POST['location_email'] ) ) {
		update_option( "location_email", $_POST['location_email'] );
	}

	if ( isset( $_POST['default'] ) && ! empty( $_POST['default'] ) ) {

		update_option( "options_default_auto", $_POST['default'] );

	}
}

add_action( 'init', 'plugin_save_new_custom_meta', 15 );
add_action( 'admin_menu', 'plugin_register_menu_pages' );
?>