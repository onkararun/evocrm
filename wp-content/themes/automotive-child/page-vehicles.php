<?php 
/* Template Name: Vahicles */
	ob_start();
	session_start();
	get_header(); ?>
    
    <?php if (have_posts()): while (have_posts()) : the_post(); 
		
		$sidebar       = get_post_meta(get_current_id(), "sidebar", true); 
		
		$classes       = content_classes($sidebar);

		$content_class = $classes[0];
		$sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>
    	<div class="section-wrap">
	        <div class="inner-page row wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">
	        	<!-- <div class="<?php echo $content_class; ?> page-content"> -->
	        	<div class="right-section">
	        		<div class="right-logo"><img src="http://www.evocrm.co.uk/wp-content/uploads/2016/12/Vehicles-Blue.png"></div>
	        		<div class="right-page-title"><?php the_page_title(); ?></div>
	        	</div>
	        	<div id="post-<?php the_ID(); ?>" <?php echo post_class($content_class . " page-content post-entry"); ?>>
	            
	        		<?php the_content(); ?>

					<?php wp_link_pages( array('before' => '<p class="margin-top-20">' . __( 'Pages:' ), 'after' => '</p>') ); ?>          
	                <?php
		                wp_reset_postdata();
		                dvla_api_form(); 
		                dvla_api_errors();
		                dvla_data_save();
		                get_vahicles();
	                ?>
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
        </div
    
    <?php endwhile; ?>

	<?php else: ?>

		<!-- article -->
		<article>

			<h1><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h1>

		</article>
		<!-- /article -->

	<?php endif; ?>

<?php get_footer(); ?>
