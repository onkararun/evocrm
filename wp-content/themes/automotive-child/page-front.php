<?php 
/* Template Name: Home Page */
	get_header(); ?>
    
    <?php if (have_posts()): while (have_posts()) : the_post(); 
		
		$sidebar       = get_post_meta(get_current_id(), "sidebar", true); 
		
		$classes       = content_classes($sidebar);

		$content_class = $classes[0];
		$sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>
    
        <div class="inner-page row wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">
        	<!-- <div class="<?php echo $content_class; ?> page-content"> -->
        	<div id="post-<?php the_ID(); ?>" <?php echo post_class($content_class . " page-content post-entry"); ?>>
            
        		<?php the_content(); ?>

				<?php wp_link_pages( array('before' => '<p class="margin-top-20">' . __( 'Pages:' ), 'after' => '</p>') ); ?>          

                <?php 
                wp_reset_postdata();
                echo "<div class='wp-clock-date'><div class='wp-time'>";
                echo date("h:i <\s\p\a\\n>a<\/\s\p\a\\n>");
                echo "</div><div class='wp-date'>";
                echo date('l, jS F Y');
                echo "</div></div>";
                echo "<div class='wp-evo-logo'>";
                echo "<img src='http://www.evocrm.co.uk/wp-content/uploads/2016/12/logo.png' alt='evo-logo' class='evo-logo'>";
                echo "</div>";
                $count_vehicles = wp_count_posts('listings');
                $count_customers = wp_count_posts('vehicle-customer');
                echo "<div class='wp-total-customers wp-home-total'>";
                echo '<span class="customers-count">'.$count_customers->publish.'</span>
                <span class="customers-text">Total Customers</span>';
                echo "</div>";
                echo "<div class='wp-total-sales wp-home-total'>";
                $query = new WP_Query( array( 'post_type' => 'listings', 'meta_key' => 'car_sold', 'meta_value' => "1" ) );
                echo "<span class='sale-count'>".$query->found_posts."</span>
                <span class='sale-text'>Today's Sales</span>";
                echo "</div>";
                echo "<div class='wp-total-vehicles wp-home-total'>";
                echo "<span class='vehicles-count'>".$count_vehicles->publish."</span>
                <span class='vehicles-text'>Total Vehicles in stock</span>";
                echo "</div>";
                echo "<div class='wp-home-sticky-notes'>";
                $user_id = get_current_user_id();
                $stickyNotes = get_user_meta($user_id, 'sticky-notes', 'true');
                $stickyNotes2 = get_user_meta($user_id, 'sticky-notes-2', 'true');
                $stickyNotes3 = get_user_meta($user_id, 'sticky-notes-3', 'true');
                $sticky = '<form  id="stick-notes-form" class="col-md-4 pull-right" method="post">';
				$sticky.= '<textarea name="stickynotes" placeholder="Sticky Notes" cols="10" rows="1">' .$stickyNotes. '</textarea>';
				$sticky.= '<input type="submit" id="sticky-submit" name="sticky-submit" value="Stick it"/></form>';
                $sticky.= '<form  id="stick-notes-form" class="col-md-4 pull-right" method="post">';
                $sticky.= '<textarea name="stickynotes-2" placeholder="Sticky Notes" cols="10" rows="1">' .$stickyNotes2. '</textarea>';
                $sticky.= '<input type="submit" id="sticky-submit-2" name="sticky-submit-2" value="Stick it"/></form>';
                $sticky.= '<form  id="stick-notes-form" class="col-md-4 pull-right" method="post">';
                $sticky.= '<textarea name="stickynotes-3" placeholder="Sticky Notes" cols="10" rows="1">' .$stickyNotes3. '</textarea>';
                $sticky.= '<input type="submit" id="sticky-submit-2" name="sticky-submit-3" value="Stick it"/></form></div>';
				echo $sticky;
				if(isset($_POST['sticky-submit'])) {
					$notes = $_POST['stickynotes'];
					update_user_meta($user_id, 'sticky-notes', $notes);
					wp_redirect(home_url());
				}
                if(isset($_POST['sticky-submit-2'])) {
                    $notes = $_POST['stickynotes-2'];
                    update_user_meta($user_id, 'sticky-notes-2', $notes);
                    wp_redirect(home_url());
                }
                if(isset($_POST['sticky-submit-3'])) {
                    $notes = $_POST['stickynotes-3'];
                    update_user_meta($user_id, 'sticky-notes-3', $notes);
                    wp_redirect(home_url());
                }
                echo "</div>";?>
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
    
    <?php endwhile; ?>

	<?php else: ?>

		<!-- article -->
		<article>

			<h1><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h1>

		</article>
		<!-- /article -->

	<?php endif; ?>

<?php get_footer(); ?>
