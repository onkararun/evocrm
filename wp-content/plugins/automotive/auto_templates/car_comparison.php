<?php
global $Listing, $lwp_options;

//********************************************
//	Language Variables
//***********************************************************
$listing_image_alt = __( "Listing Image", "listings" );
$none_text         = __( "None", "listings" );
$options_text      = __( "OPTIONS", "listings" );
$view_listing_text = __( "View Listing", "listings" );

if ( $Listing->is_wpml_active() ) {
	$car = apply_filters( "wpml_object_id", $car, "listings", true );
}

$all_post_meta   = $Listing->get_listing_meta( $car );
$listing_options = (isset($all_post_meta['listing_options']) && !empty($all_post_meta['listing_options']) ? $all_post_meta['listing_options'] : array());
$gallery_images  = (isset($all_post_meta['gallery_images']) && !empty($all_post_meta['gallery_images']) ? $all_post_meta['gallery_images'] : array());

$price           = (isset($listing_options['price']['value']) && !empty($listing_options['price']['value']) ? $listing_options['price']['value'] : "");
?>
<div class='col-lg-<?php echo $class; ?>'>
	<div class="porche margin-bottom-25">
		<div class="porche-header"><span><?php echo get_the_title( $car ); ?></span>
			<strong><?php echo $Listing->format_currency( $price ); ?></strong></div>
		<?php
		if ( ! empty( $gallery_images ) && !empty( $gallery_images[0] ) ) {
			$img = $Listing->auto_image( $gallery_images[0], "auto_slider", true );
			$alt = get_post_meta( $gallery_images[0], '_wp_attachment_image_alt', true );
		} elseif ( (empty( $gallery_images[0] ) || empty($gallery_images) ) && isset( $lwp_options['not_found_image']['url'] ) && ! empty( $lwp_options['not_found_image']['url'] ) ) {
			$img = $lwp_options['not_found_image']['url'];
			$alt = $listing_image_alt;
		} else {
			$img = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
			$alt = $listing_image_alt;
		}
		?>
		<div class="porche-img"><img src="<?php echo $img; ?>" alt="<?php echo $alt; ?>" class="no_border"></div>
		<div class="car-detail clearfix">
			<div class="table-responsive">
				<table class="table comparison">
					<tbody>
					<?php
					$listing_categories = $Listing->get_listing_categories();

					foreach ( $listing_categories as $category ) {
						$slug  = $category['slug'];
						$value = ( isset( $all_post_meta[ $slug ] ) && ! empty( $all_post_meta[ $slug ] ) ? $all_post_meta[ $slug ] : "" );

						if ( isset( $category['currency'] ) && $category['currency'] == 1 ) {
							$value = $Listing->format_currency( $value );
						}

						$value = ( empty( $value ) ? $none_text : $value );

						if(!isset($category['hide_category']) || $category['hide_category'] == 0) {
							echo "<tr><td>" . $category['singular'] . ": </td><td>" . html_entity_decode( $value ) . "</td></tr>";
						}
					} ?>
					<tr>
						<td><?php echo $options_text; ?>:</td>
						<td></td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="option-tick-list clearfix">
				<div class="row">
					<div class="col-lg-12">
						<?php
						$multi_options = ( isset( $all_post_meta['multi_options'] ) && ! empty( $all_post_meta['multi_options'] ) ? $all_post_meta['multi_options'] : "" );

						if ( isset( $multi_options ) && ! empty( $multi_options ) ) {

							switch ( $class ) {
								case 6:
									$columns      = 3;
									$column_class = 4;
									break;

								case 4:
									$columns      = 2;
									$column_class = 6;
									break;

								case 3:
									$columns      = 1;
									$column_class = 12;
									break;
							}

							$amount = ceil( count( $multi_options ) / $columns );
							$new    = array_chunk( $multi_options, $amount );

							echo "<div class='row'>";
							foreach ( $new as $section ) {
								echo "<ul class='options col-lg-" . $column_class . "'>";
								foreach ( $section as $option ) {
									echo "<li>" . $option . "</li>";
								}
								echo "</ul>";
							}
							echo "</div>";
						} else {
							echo "<ul class='empty'><li>" . __( "No options yet", "listings" ) . "</li></ul>";
						} ?>
					</div>
				</div>
			</div>
			<div class="porche-footer margin-top-25 padding-top-20 padding-bottom-15">
				<form method="post" action="<?php echo get_permalink( $car ); ?>">
					<input type="submit" value="<?php echo $view_listing_text; ?>">
				</form>
			</div>
		</div>
	</div>
</div>