<?php 
/* Template Name: View Vehicle */
	get_header(); ?>
		
		<?php if (have_posts()): while (have_posts()) : the_post(); 
		
		$sidebar       = get_post_meta(get_current_id(), "sidebar", true); 
		
		$classes       = content_classes($sidebar);

		$content_class = $classes[0];
		$sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>
			<div class="section-wrap">
				<div class="inner-page row wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">
					<!-- <div class="<?php echo $content_class; ?> page-content"> -->
					<div id="post-<?php the_ID(); ?>" <?php echo post_class($content_class . " page-content post-entry"); ?>>
						
						<?php the_content(); ?>

				<?php wp_link_pages( array('before' => '<p class="margin-top-20">' . __( 'Pages:' ), 'after' => '</p>') ); ?>          

					<?php 

						wp_reset_postdata();
						$post_id = $_GET['pid'];
						if(isset($post_id)) {
							$vehicleDetails = update_vehicle_form($post_id, 'view'); ?>
							<div class="view-page-wrapper">
								<div class="section-header">
									<h2 class="stock-info">Stock Information</h2>
									<div class="content-nav margin-bottom-30">
										<ul>
											<?php if(isset($lwp_options['print_vehicle_show']) && !empty($lwp_options['print_vehicle_show']) && $lwp_options['print_vehicle_show'] == 1){ ?>
												<li class="print gradient_button"><a class="print_page"><?php echo $lwp_options['print_vehicle_label']; ?></a></li>
											<?php } ?>

											<?php if(isset($lwp_options['email_friend_show']) && !empty($lwp_options['email_friend_show']) && $lwp_options['email_friend_show'] == 1){ ?>
												<li class="email gradient_button"><a href="#email_fancybox_form" class="fancybox_div"><?php echo $lwp_options['email_friend_label']; ?></a></li>
											<?php } ?>
											<?php if(isset($lwp_options['pdf_brochure_show']) && !empty($lwp_options['pdf_brochure_show']) && $lwp_options['pdf_brochure_show'] == 1){
												$pdf_brochure = get_post_meta($post->ID, "pdf_brochure_input", true);
												$pdf_link		= wp_get_attachment_url( $pdf_brochure ); ?>
												<li class="pdf gradient_button"><a href="<?php echo (isset($pdf_link) && !empty($pdf_link) ? $pdf_link : ''); ?>" class="<?php echo (isset($pdf_link) && !empty($pdf_link) ? '' : 'generate_pdf'); ?>" <?php echo (isset($pdf_link) && !empty($pdf_link) ? "target='_blank'" : ''); ?>><?php echo $lwp_options['pdf_brochure_label']; ?></a></li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<div class="row view-details">
									<div class="col-sm-3 view-colom">  
										<label class="view-label">Stock</label>
										<p class="view-value">New - Will be assigned</p>
										<label class="view-label">Priv Plate</label>
										<p class="view-value"><?php echo $vehicleDetails['priv-plate']?:'no data available'; ?></p>
										<label class="view-label">Make</label>
										<p class="view-value"><?php echo $vehicleDetails['make']?:'no data available'; ?></p>
										<label class="view-label">Import</label>
										<p class="view-value"><?php echo $vehicleDetails['import']?:'no data available'; ?></p>
										<label class="view-label">Advert Mileage</label>
										<p class="view-value"><?php echo $vehicleDetails['advert-mileage']?:'no data available'; ?></p>
										<label class="view-label">Former Oweners(view)</label>
										<p class="view-value"><?php echo $vehicleDetails['former-owner']?:'no data available'; ?></p>
										<label class="view-label">Location</label>
										<p class="view-value"><?php echo $vehicleDetails['location']?:'no data available'; ?></p>
										<label class="view-label">Motability</label>
										<p class="view-value"><?php echo $vehicleDetails['motability']?:'no data available'; ?></p>
										<label class="view-label">Door Key Number</label>
										<p class="view-value"><?php echo $vehicleDetails['door-key']?:'no data available'; ?></p>
										<label class="view-label">Tax</label>
										<p class="view-value"><?php echo $vehicleDetails['vehicle-tax']?:'no data available'; ?></p>
										<label class="view-label">Purchase Date</label>
										<p class="view-value"><?php echo $vehicleDetails['purchase-date']?:'no data available'; ?></p>
										<label class="view-label">Supplier Invoice Number</label>
										<p class="view-value"><?php echo $vehicleDetails['supplier-inv-no']?:'no data available'; ?></p>
										<label class="view-label">Hide on Web</label>
										<p class="view-value"><?php echo $vehicleDetails['hide-web']?:'no data available'; ?></p>
										<label class="view-label">History</label>
										<p class="view-value"><?php echo $vehicleDetails['history']?:'no data available'; ?></p>
										<label class="view-label">Warrenty</label>
										<p class="view-value"><?php echo $vehicleDetails['warrenty']?:'no data available'; ?></p>
										<label class="view-label">Transmission</label>
										<p class="view-value"><?php echo $vehicleDetails['transmission']?:'no data available'; ?></p>
										<label class="view-label">Number of Doors</label>
										<p class="view-value"><?php echo $vehicleDetails['Doors']?:'no data available'; ?></p>
									</div>
									<div class="col-sm-3 view-colom">  
										<label class="view-label">Reg Number</label>
										<p class="view-value"><?php echo $vehicleDetails['reg-no']?:'no data available'; ?></p>
										<label class="view-label">Sale Type</label>
										<p class="view-value"><?php echo $vehicleDetails['sale-type']?:'no data available'; ?></p>
										<label class="view-label">Model</label>
										<p class="view-value"><?php echo $vehicleDetails['model']?:'no data available'; ?></p>
										<label class="view-label">Hide Finance</label>
										<p class="view-value"><?php echo $vehicleDetails['hide-finance']?:'no data available'; ?></p>
										<label class="view-label">Actual Mileage</label>
										<p class="view-value"><?php echo $vehicleDetails['mileage']?:'no data available'; ?></p>
										<label class="view-label">Status</label>
										<p class="view-value"><?php echo $vehicleDetails['status']?:'no data available'; ?></p>
										<label class="view-label">Current MOT Date</label>
										<p class="view-value"><?php echo $vehicleDetails['mot-date1']?:'no data available'; ?></p>
										<label class="view-label">Radio Code</label>
										<p class="view-value"><?php echo $vehicleDetails['radio-code']?:'no data available'; ?></p>
										<label class="view-label">Spare Keys</label>
										<p class="view-value"><?php echo $vehicleDetails['spare-key']?:'no data available'; ?></p>
										<label class="view-label">HPI</label>
										<p class="view-value"><?php echo $vehicleDetails['hpi']?:'no data available'; ?></p>
										<label class="view-label">Dealer PI Ref</label>
										<p class="view-value"><?php echo $vehicleDetails['reg-date']?:'no data available'; ?></p>
										<label class="view-label">Source</label>
										<p class="view-value"><?php echo $vehicleDetails['source']?:'no data available'; ?></p>
										<label class="view-label">Sale or Return</label>
										<p class="view-value"><?php echo $vehicleDetails['sale-return']?:'no data available'; ?></p>
										<label class="view-label">FSH</label>
										<p class="view-value"><?php echo $vehicleDetails['fsh']?:'no data available'; ?></p>
										<label class="view-label">Engine Size</label>
										<p class="view-value"><?php echo $vehicleDetails['engine-size']?:'no data available'; ?></p>
										<label class="view-label">Gears</label>
										<p class="view-value"><?php echo $vehicleDetails['gears']?:'no data available'; ?></p>
										<label class="view-label">Group</label>
										<p class="view-value"><?php echo $vehicleDetails['group']?:'no data available'; ?></p>
									</div>
									<div class="col-sm-3 view-colom">  
										<label class="view-label">Reg Letter</label>
										<p class="view-value"><?php echo $vehicleDetails['reg-letter']?:'no data available'; ?></p>
										<label class="view-label">VAT Type</label>
										<p class="view-value"><?php echo $vehicleDetails['vat-type']?:'no data available'; ?></p>
										<label class="view-label">Year</label>
										<p class="view-value"><?php echo $vehicleDetails['year']?:'no data available'; ?></p>
										<label class="view-label">VIN/Chassis Number</label>
										<p class="view-value"><?php echo $vehicleDetails['vin-number']?:'no data available'; ?></p>
										<label class="view-label">Ins Group</label>
										<p class="view-value"><?php echo $vehicleDetails['ins-group']?:'no data available'; ?></p>
										<label class="view-label">Grading</label>
										<p class="view-value"><?php echo $vehicleDetails['grading']?:'no data available'; ?></p>
										<label class="view-label">MOT Number</label>
										<p class="view-value"><?php echo $vehicleDetails['mot-number']?:'no data available'; ?></p>
										<label class="view-label">Alarm Code</label>
										<p class="view-value"><?php echo $vehicleDetails['alarm-code']?:'no data available'; ?></p>
										<label class="view-label">V5</label>
										<p class="view-value"><?php echo $vehicleDetails['v5']?:'no data available'; ?></p>
										<label class="view-label">Mileage Check</label>
										<p class="view-value"><?php echo $vehicleDetails['mileage-check']?:'no data available'; ?></p>
										<label class="view-label">Part-Ex Ref</label>
										<p class="view-value"><?php echo $vehicleDetails['make']?:'no data available'; ?></p>
										<label class="view-label">Supplier</label>
										<p class="view-value"><?php echo $vehicleDetails['supplier']?:'no data available'; ?></p>
										<label class="view-label">Supplier Details</label>
										<p class="view-value"><?php echo $vehicleDetails['make']?:'no data available'; ?></p>
										<label class="view-label">VAN</label>
										<p class="view-value"><?php echo $vehicleDetails['VAN']?:'no data available'; ?></p>
										<label class="view-label">Fuel Type</label>
										<p class="view-value"><?php echo $vehicleDetails['fuel-type']?:'no data available'; ?></p>
										<label class="view-label">Drive</label>
										<p class="view-value"><?php echo $vehicleDetails['drive-train']?:'no data available'; ?></p>
										<label class="view-label">Trim type</label>
										<p class="view-value"><?php echo $vehicleDetails['trim-type']?:'no data available'; ?></p>
									</div>
									<div class="col-sm-3 view-colom">  
										<label class="view-label">Reg Date</label>
										<p class="view-value"><?php echo $vehicleDetails['reg-date']?:'no data available'; ?></p>
										<label class="view-label">Retail Price</label>
										<p class="view-value"><?php echo $vehicleDetails['price']?:'no data available'; ?></p>
										<label class="view-label">Description</label>
										<p class="view-value"><?php echo $vehicleDetails['secondary_title']?:'no data available'; ?></p>
										<label class="view-label">Engine Number</label>
										<p class="view-value"><?php echo $vehicleDetails['engine-number']?:'no data available'; ?></p>
										<label class="view-label">Colour</label>
										<p class="view-value"><?php echo $vehicleDetails['exterior-color']?:'no data available'; ?></p>
										<label class="view-label">Key Tag</label>
										<p class="view-value"><?php echo $vehicleDetails['key-tag']?:'no data available'; ?></p>
										<label class="view-label">Next MOT Date</label>
										<p class="view-value"><?php echo $vehicleDetails['mot-date2']?:'no data available'; ?></p>
										<label class="view-label">IG Key Number</label>
										<p class="view-value"><?php echo $vehicleDetails['ig-key']?:'no data available'; ?></p>
										<label class="view-label">V5 Number</label>
										<p class="view-value"><?php echo $vehicleDetails['v5-no']?:'no data available'; ?></p>
										<label class="view-label">Service Check</label>
										<p class="view-value"><?php echo $vehicleDetails['service-check']?:'no data available'; ?></p>
										<label class="view-label">Purchased Price</label>
										<p class="view-value"><?php echo $vehicleDetails['purchase-price']?:'no data available'; ?></p>
										<label class="view-label">Buyer</label>
										<p class="view-value"><?php echo $vehicleDetails['buyer']?:'no data available'; ?></p>
										<label class="view-label">Stock Comments</label>
										<p class="view-value"><?php echo $vehicleDetails['stock-comments']?:'no data available'; ?></p>
										<label class="view-label">BHP</label>
										<p class="view-value"><?php echo $vehicleDetails['bhp']?:'no data available'; ?></p>
										<label class="view-label">Engine Type</label>
										<p class="view-value"><?php echo $vehicleDetails['engine-type']?:'no data available'; ?></p>
										<label class="view-label">Body Type</label>
										<p class="view-value"><?php echo $vehicleDetails['body']?:'no data available'; ?></p>
										<label class="view-label">Trim Colour</label>
										<p class="view-value"><?php echo $vehicleDetails['trim-colour']?:'no data available'; ?></p>
									</div>
								</div>
							</div>
						</div>
						<?php }else{ ?>
							<article>
							<h1><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h1>
							</article>
						<?php } ?>
					</div>
					<?php // sidebar 
						$default_sidebar = get_post_meta( get_current_id(), "sidebar_area", true );
					if(isset($sidebar) && !empty($sidebar) && $sidebar != "none" && isset($default_sidebar) && !empty($default_sidebar)){
						echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
						dynamic_sidebar($default_sidebar);
						echo "</div>";
					}					
				?>
				</div>
			</div>
		
		<?php endwhile; ?>

	<?php else: ?>

		<!-- article -->
		<article>

			<h1><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h1>

		</article>
		<!-- /article -->

	<?php endif; ?>

<?php get_footer(); ?>
