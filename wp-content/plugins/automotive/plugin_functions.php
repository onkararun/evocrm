<?php
function my_filter_head() {
    remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'my_filter_head');

// orderby WPML get_auto_listing_categories_option
if(!function_exists("get_auto_orderby_option")){
	function get_auto_orderby_option(){
		$option = "listing_orderby";

		if(defined("ICL_LANGUAGE_CODE") && ICL_LANGUAGE_CODE != "en"){
			$option .= "_" . ICL_LANGUAGE_CODE;
		}

		return $option;
	}
}

if(!function_exists("get_auto_orderby")){
	function get_auto_orderby(){
		$option = get_auto_orderby_option();

		return get_option($option);
	}
}

if(!function_exists("get_listing_categories_to_redux_select")){
	function get_listing_categories_to_redux_select(){
		global $Listing;

		$return             = array();
		$listing_categories = $Listing->get_listing_categories();

		if(!empty($listing_categories)){
			foreach($Listing->get_listing_categories() as $key => $category){
				if(is_array($category)){
					$return[$key] = $category['singular'];
				}
			}
		}

		return $return;
	}
}

function add_sold_view( $views ) {
	global $wpdb;

	$query ="SELECT *
    	FROM " . $wpdb->prefix . "posts as p, " . $wpdb->prefix . "postmeta as m
    	WHERE p.ID = m.post_id AND p.post_status = 'publish' AND p.post_type = 'listings' AND m.meta_key = 'car_sold' AND m.meta_value = '1'";

    $total = count($wpdb->get_results($query));

	$views  = array_slice($views, 0, 1, true) +
	          array('sold'     => "<a href='" . add_query_arg(array("post_type" => "listings", "sold_listings" => 1), admin_url('edit.php')) . "'" . (isset($_GET['sold_listings']) && $_GET['sold_listings'] == 1 ? " class='current'" : "") . ">" . __("Sold", "listings") . " <span class=\"count\">(" . $total . ")</span></a>") +
			  array_slice($views, 1, NULL, true);

    return $views;
}

add_action( 'views_edit-listings',  'add_sold_view' );


function add_for_sale_view( $views ) {
	global $wpdb;

	$query ="SELECT *
    	FROM " . $wpdb->prefix . "posts as p, " . $wpdb->prefix . "postmeta as m
    	WHERE p.ID = m.post_id AND p.post_status = 'publish' AND p.post_type = 'listings' AND m.meta_key = 'car_sold' AND m.meta_value = '2'";

    $total = count($wpdb->get_results($query));

	$views  = array_slice($views, 0, 1, true) +
   	          array('for_sale' => "<a href='" . add_query_arg(array("post_type" => "listings", "sold_listings" => 2), admin_url('edit.php')) . "'" . (isset($_GET['sold_listings']) && $_GET['sold_listings'] == 2 ? " class='current'" : "") . ">" . __("For Sale", "listings") . " <span class=\"count\">(" . $total . ")</span></a>") +
	          array_slice($views, 1, NULL, true);

    return $views;
}

add_action( 'views_edit-listings',  'add_for_sale_view' );

function add_sold_view_query( $query ){
	if(isset($_GET['sold_listings']) && $_GET['sold_listings'] == 1){
		$query->set('meta_key', 'car_sold');
		$query->set('meta_value', '1');
	} elseif(isset($_GET['sold_listings']) && $_GET['sold_listings'] == 2){
		$query->set('meta_key', 'car_sold');
		$query->set('meta_value', '2');
	}

	return $query;
}

add_action("pre_get_posts", "add_sold_view_query");

function automotive_plugin_editor_styles() {
    add_editor_style( CSS_DIR . 'wp.css' );
    //add_editor_style( CSS_DIR . 'bootstrap.css' );
    add_editor_style( CSS_DIR . 'bootstrap.min.css' );
}
add_action( 'init', 'automotive_plugin_editor_styles' );

//********************************************
//	Register Sidebar
//***********************************************************
$args = array(
	'name'          => __( 'Listings Sidebar', 'listings' ),
	'id'            => 'listing_sidebar',
	'description'   => '',
    'class'         => '',
	'before_widget' => '<div class="side-widget padding-bottom-50">',
	'after_widget' => '</div>',
	'before_title' => '<h3 class="side-widget-title margin-bottom-25">',
	'after_title' => '</h3>' );

register_sidebar( $args );

//********************************************
//  Delete associated images
//***********************************************************
function automotive_delete_init() {
	global $lwp_options;

    if ( current_user_can( 'delete_posts' ) && isset($lwp_options['delete_associated']) && $lwp_options['delete_associated'] == 1 ){
        add_action( 'before_delete_post', 'delete_auto_images' );
    }
}
add_action( 'admin_init', 'automotive_delete_init' );

function delete_auto_images( $pid ) {
	$post_type = get_post_type($pid);

	if(isset($post_type) && $post_type == "listings"){
		$gallery_images = get_post_meta( $pid, "gallery_images", true );

		if(!empty($gallery_images)){
			foreach($gallery_images as $gid){
				wp_delete_attachment( $gid );
			}
		}
	}
}

function car_listing_container($layout){
	$return = array();
	
	if($layout == "boxed_fullwidth"){
		$return['start'] = '<div class="inventory_box car_listings boxed boxed_full">';
		$return['end']   = '</div>';
	} elseif($layout == "wide_fullwidth"){
		$return['start'] = '<div class="content-wrap car_listings row">';
		$return['end']   = '</div>';
	} elseif($layout == "boxed_left"){		
		$return['start'] = '<div class="car_listings boxed boxed_left col-md-9 col-lg-push-3 col-md-push-3">';
		$return['end']   = '</div>';
	} elseif($layout == "boxed_right"){
		$return['start'] = '<div class="car_listings boxed boxed_right col-md-9">';
		$return['end']   = '</div>';
	} elseif($layout == "wide_left"){
		$return['start'] = '<div class="inventory-wide-sidebar-left col-md-9  col-lg-push-3 col-md-push-3 car_listings"><div class="sidebar">';
		$return['end']   = '</div></div>';
	} elseif($layout == "wide_right"){
		$return['start'] = '<div class="inventory-wide-sidebar-right car_listings col-md-9 padding-right-15"><div class="sidebar">';
		$return['end']   = '</div></div>';
	} else {		
		$return['start'] = '<div class="inventory_box car_listings">';
		$return['end']   = '</div>';
	}
	
	return $return;
}

if(!function_exists("listing_youtube_video")){
	function listing_youtube_video(){
		return '<div id="youtube_video">
			<iframe width="560" height="315" src="about:blank" allowfullscreen style="width: 560px; height: 315px; border: 0;"></iframe>
		</div>';
	}
}

if(!function_exists("listing_template")){
	function listing_template($layout, $is_ajax = false, $ajax_array = false){
		global $Listing, $Listing_Template;

		if($is_ajax == false) { ?>
			<div class="inner-page row">
				<?php
		}

				add_filter('posts_orderby', 'auto_sold_to_bottom');
				$args     = ($is_ajax == false ? $Listing->listing_args($_GET) : $Listing->listing_args($_GET, false, $ajax_array));
		        $listings = get_posts($args[0]);
				remove_filter('posts_orderby', 'auto_sold_to_bottom');

				if($is_ajax == false){
					echo $Listing_Template->locate_template("listing_view", array("layout" => $layout));
		        	echo $Listing_Template->locate_template("listing_filter_sort");
				}
				
		        $container = car_listing_container($layout);
		        
		        echo (!$is_ajax ? "<div class='row generate_new'>" : "") . $container['start'];

		        if(!empty($listings)){
			        foreach($listings as $listing){
			            echo $Listing_Template->locate_template("inventory_listing", array("id" => $listing->ID, "layout" => $layout));
			        }
			    } else {
			    	echo do_shortcode('[alert type="2" close="No"]' . __("No listings found", "listings") . '[/alert]') . "<div class='clearfix'></div>";
			    }

				echo "<div class=\"clearfix\"></div>";
		        echo $container['end'];
				
				if($layout == "boxed_left"){
					echo "<div class=\" col-md-3 col-sm-12 col-lg-pull-9 col-md-pull-9 left-sidebar side-content listing-sidebar\">";
					dynamic_sidebar("listing_sidebar");
					echo "</div>";
				} elseif($layout == "boxed_right"){
					echo "<div class=\"inventory-sidebar col-md-3 side-content listing-sidebar\">";
					dynamic_sidebar("listing_sidebar");
					echo "</div>";
				} elseif($layout == "wide_left"){
					echo "<div class=\" col-md-3 col-lg-pull-9 col-md-pull-9 left-sidebar side-content listing-sidebar\">";
					dynamic_sidebar("listing_sidebar");
					echo "</div>";
				} elseif($layout == "wide_right"){
					echo "<div class=\"inventory-sidebar col-md-3 side-content listing-sidebar\">";
					dynamic_sidebar("listing_sidebar");
					echo "</div>";
				}
				
				if($is_ajax == false){
					echo bottom_page_box($layout);
					echo "</div>"; 
				}

				echo "<div id='preview_slideshow'></div>";
				
				echo (!$is_ajax ? "</div>" : ""); 
	    		echo listing_youtube_video();
	}
}

function preview_slideshow_ajax(){
	global $Listing;

	$id             = sanitize_text_field( $_POST['id'] );
	$gallery_images = get_post_meta($id, "gallery_images", true);


    if(!empty($gallery_images)){	
		$full_images  = "";
		$thumb_images = "";
		
		foreach($gallery_images as $gallery_image){
			$gallery_thumb  = $Listing->auto_image($gallery_image, "auto_thumb", true);
			$gallery_slider = $Listing->auto_image($gallery_image, "auto_slider", true);
			$full 			= wp_get_attachment_image_src($gallery_image, "full");
			$full 			= $full[0];

			$full_images  .= "<li data-thumb=\"" . $gallery_thumb . "\"> <img src=\"" . $gallery_slider . "\" alt=\"\" data-full-image=\"" . $full . "\" /> </li>\n";
			$thumb_images .= "<li data-thumb=\"" . $gallery_thumb . "\"> <img src=\"" . $gallery_thumb . "\" alt=\"\" /> </li>\n";
		}
    } ?>

    <div class="listing-slider">
        <section class="slider home-banner">
			<a title="Close" class="fancybox-item fancybox-close" href="javascript:;" id="close_preview_area"></a>

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
    <?php

	die;
}
add_action("wp_ajax_preview_slideshow_ajax", "preview_slideshow_ajax");
add_action("wp_ajax_nopriv_preview_slideshow_ajax", "preview_slideshow_ajax");

// create new inventory listings for select view buttons
function generate_new_view(){
	$layout = $_POST['layout'];
	$page   = sanitize_text_field((isset($_POST['page']) && !empty($_POST['page']) ? $_POST['page'] : 1));
	$params = json_decode(stripslashes($_POST['params']), true);

	// paged fix
	if(isset($page) && !empty($page)){
		$params['paged'] = $page;
	}

	ob_start();
	listing_template($layout, true, $params);
	$html = ob_get_clean();

	echo json_encode(array(
		"html"        => $html,
	    "top_page"    => page_of_box($page),
	    "bottom_page" => bottom_page_box(false, $page),
	));

	die;
}
add_action("wp_ajax_generate_new_view", "generate_new_view");
add_action("wp_ajax_nopriv_generate_new_view", "generate_new_view");

if(!function_exists("automotive_forms_footer")){
	function automotive_forms_footer(){
		global $Listing_Template;

		echo $Listing_Template->locate_template("footer_forms");
	}
}
add_action("wp_footer", "automotive_forms_footer");

function auto_sold_to_bottom( $orderby ){
	// global $wpdb, $lwp_options;

	// $sold_bottom_orderby = preg_replace("/" . $wpdb->prefix . "postmeta.meta_value (ASC|DESC)/", "mt1.meta_value DESC, mt2.meta_value $1", $orderby);

	// $orderby = (isset($lwp_options['inventory_sold_to_bottom']) && isset($lwp_options['inventory_no_sold']) && $lwp_options['inventory_sold_to_bottom'] == 1 && $lwp_options['inventory_no_sold'] == 1 ? $sold_bottom_orderby : $orderby);

	return $orderby;
}

if(!function_exists("D")){
	function D($var){
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
}

//********************************************
//	Get Font Awesome Icons
//***********************************************************
if(!function_exists("get_fontawesome_icons")){
	function get_fontawesome_icons(){
		$pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
		$subject = @file_get_contents(LISTING_DIR . 'css/font-awesome.css');

		if($subject){
			preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

			$icons = array();

			foreach($matches as $match){
				$icons[$match[1]] = $match[2];
			}

			return $icons;
		} else {
			return "cant find file: " . LISTING_DIR . 'css/font-awesome.css';
		}
	}
}

//********************************************
//	Pagination Boxes
//***********************************************************
if(!function_exists("page_of_box")){
	function page_of_box($load = false, $fake_get = null, $load_posts = false){
		global $lwp_options, $Listing;

		$return = "";

		if((isset($lwp_options['top_pagination']) && $lwp_options['top_pagination'] != 0) || !isset($lwp_options['top_pagination'])){
			$get_holder = (!is_null($fake_get) && !empty($fake_get) ? $fake_get : $_REQUEST);

			if($load != false && !empty($load)){
				$paged = $load;

				if($load_posts == false){
					if(isset($_REQUEST['action']) && $_REQUEST['action'] == "generate_new_view"){
						$params = json_decode(stripslashes($_POST['params']), true);

						$listing_args = $Listing->listing_args($params);
					} else {
						$listing_args = $Listing->listing_args($_POST);
					}

					$args		  = $listing_args[0];

					$args['posts_per_page'] = -1;
					$matches       = get_posts( $args );
				} else {
					$matches = $load_posts;
				}
				$load_number   = count($matches);
			} else {
				$paged_var 	  = (isset($get_holder['paged']) && !empty($get_holder['paged']) ? $get_holder['paged'] : "");
				$paged     	  = (isset($paged_var) && !empty($paged_var) ? $paged_var : (get_query_var("paged") ? get_query_var("paged") : 1));

				if($load_posts == false){
					$listing_args = $Listing->listing_args($get_holder);
					$args		  = $listing_args[0];

					$args['posts_per_page'] = -1;
					$matches       = get_posts( $args );
				} else {
					$matches = $load_posts;
				}
				$load_number   = count($matches);
			}

			$number = $load_number;
			$total  = ceil($number / (isset($lwp_options['listings_amount']) && !empty($lwp_options['listings_amount']) ? $lwp_options['listings_amount'] : 1));

	        $return .= '<div class="controls full page_of" data-page="' . ($paged ? $paged : 1) . '">
	            <a href="#" class="left-arrow' . ($paged == 1 ? " disabled" : "") . '"><i class="fa fa-angle-left"></i></a>
	            <span>' . __("Page", "listings") . ' <span class="current_page">' . ($paged ? $paged : 1) . '</span> ' . __('of', 'listings') . ' <span class="total_pages">' . ($total == 0 || empty($lwp_options['listings_amount']) ? 1 : $total) . '</span></span>
	            <a href="#" class="right-arrow'. ($paged == $total || empty($lwp_options['listings_amount']) ? " disabled" : "") . '"><i class="fa fa-angle-right"></i></a>
	        </div>';
        }

        return $return;

		if(isset($_POST['action']) && !empty($_POST['action'])){
			die;
		}
	}
}

add_action('wp_ajax_load_page_of_box', 'page_of_box');
add_action('wp_ajax_nopriv_load_page_of_box', 'page_of_box');

if(!function_exists("bottom_page_box")){
	function bottom_page_box($layout = false, $load = false, $fake_get = null, $additional_vars = array(), $load_posts = false){
		global $lwp_options, $Listing;

		$return = "";

		if((isset($lwp_options['bottom_pagination']) && $lwp_options['bottom_pagination'] != 0) || !isset($lwp_options['bottom_pagination'])){
			$get_holder = (!is_null($fake_get) && !empty($fake_get) ? $fake_get : $_REQUEST);

			if($load != false && !empty($load)){
				$paged = $load;
				$load_number = $load;

				$paged_var = get_query_var('paged');
				if(!isset($_REQUEST['action']) && $_REQUEST['action'] != "generate_new_view"){
					$paged   = (isset($paged_var) && !empty($paged_var) ? $paged_var : 1);
				}

				if($load_posts == false){
					if(isset($_REQUEST['action']) && $_REQUEST['action'] == "generate_new_view"){
						$params = json_decode(stripslashes($_POST['params']), true);

						$listing_args = $Listing->listing_args($params);
					} else {
						$listing_args = $Listing->listing_args($_POST);
					}

					$args		  = $listing_args[0];

					$args['posts_per_page'] = -1;
					$matches       = get_posts( $args );
				} else {
					$matches = $load_posts;
				}
				$load_number   = count($matches);
			} else {
				$paged_var = (isset($get_holder['paged']) && !empty($get_holder['paged']) ? $get_holder['paged'] : "");
				$paged     = (isset($paged_var) && !empty($paged_var) ? $paged_var : (get_query_var("paged") ? get_query_var("paged") : 1));

				if($load_posts == false){
					$listing_args = $Listing->listing_args($get_holder);
					$args		  = $listing_args[0];

					$args['posts_per_page'] = -1;
					$matches       = get_posts( $args );
				} else {
					$matches = $load_posts;
				}
				$load_number   = count($matches);

				// if any special layouts
				if($layout == "wide_left" || $layout == "boxed_left"){
					$additional_classes = "col-lg-offset-3";
					$cols = 9;
				} else {
					$cols = 12;
				}

				$return .= '<div class="col-lg-' . $cols . ' col-md-' . $cols . ' col-sm-12 col-xs-12 pagination_container' . (isset($additional_classes) && !empty($additional_classes) ? " " . $additional_classes : "") . '">';
			}

			$number = $load_number;
			$total = ceil($number / (isset($lwp_options['listings_amount']) && !empty($lwp_options['listings_amount']) ? $lwp_options['listings_amount'] : 1));

			$return .= '<ul class="pagination margin-bottom-none margin-top-25 md-margin-bottom-none bottom_pagination">';

			$return .= "<li data-page='previous' class='" . ($paged > 1 ? "" : "disabled") . " previous' style='margin-right:2px;'><a href='#'><i class='fa fa-angle-left'></i></a></li>";

			if($total == 0 || empty($lwp_options['listings_amount'])){
				$return .= "<li data-page='1' class='disabled number'><a href='#'>1</a></li>";
			} else {
				$each_side = 3;

				if($total > (($each_side * 2) + 1)){

					// additional options
					if(isset($additional_vars['number']) && !empty($additional_vars['number'])){
						$number = $additional_vars['number'];
					}

					// before numbers
					if($paged > ($each_side)){
						$before_start = ($paged - $each_side);
						$before_pages = (($before_start + $each_side) - 1);
						// echo "3 after";
					} else {
						$before_start = 1;
						$before_pages = (($paged - $each_side) + 2);
						// echo "less than 3 after";
					}

					// after numbers
					if($total < ($each_side + $paged)){
						$after_start = ($paged + 1);
						$after_pages = $total;
						// echo "less than 3 after";
					} else {
						$after_start = ($paged + 1);
						$after_pages = (($after_start + $each_side) - 1);
						// echo "3 after";
					}

					for($i = $before_start; $i <= $before_pages; $i++){
						$return .= "<li data-page='" . $i . "' class='number'><a href='#'>" . $i . "</a></li>";
					}

					$return .= "<li data-page='" . $paged . "' class='disabled number'><a href='#'>" . $paged . "</a></li>";

					for($i = $after_start; $i <= $after_pages; $i++){
						$return .= "<li data-page='" . $i . "' class='number'><a href='#'>" . $i . "</a></li>";
					}
				} else {
					for($i = 1; $i <= $total; $i++){
						$return .= "<li data-page='" . $i . "' class='" . ($paged != $i ? "" : "disabled") . " number'><a href='#'>" . $i . "</a></li>";
					}
				}
			}

			$return .= "<li data-page='next' class='" . ($paged < $total && !empty($lwp_options['listings_amount']) ? "" : "disabled") . " next'><a href='#'><i class='fa fa-angle-right'></i></a></li>";

			$return .= "</ul></div>";
		}

		return $return;

		// wp_reset_postdata();
		// wp_reset_query();
	}
}



add_action('wp_ajax_load_bottom_page_box', 'bottom_page_box');
add_action('wp_ajax_nopriv_load_bottom_page_box', 'bottom_page_box');

if(!function_exists("get_total_meta")){
	function get_total_meta($meta_key, $meta_value, $is_options = false, $show_sold = false){
		global $wpdb, $Listing;

		if(!$is_options){
			if($Listing->is_wpml_active()){
				$lang_var = ICL_LANGUAGE_CODE;
				$sql = $wpdb->prepare("SELECT count(DISTINCT pm.post_id)
					FROM $wpdb->postmeta pm
					JOIN $wpdb->posts p ON (p.ID = pm.post_id)
					JOIN {$wpdb->prefix}icl_translations wicl_translations
					WHERE pm.meta_key = %s
					AND wicl_translations.element_id = p.ID
					AND wicl_translations.language_code = %s
					AND pm.meta_value = %s
					AND p.post_type = 'listings'
					AND p.post_status = 'publish'
	        		" . ($show_sold == false ? "AND (SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'car_sold' AND post_id = pm.post_id LIMIT 1) = 2" : "") . "					
				", $meta_key, $lang_var, $meta_value);
			} else {
				$sql = $wpdb->prepare("SELECT count(DISTINCT pm.post_id)
					FROM $wpdb->postmeta pm
					JOIN $wpdb->posts p ON (p.ID = pm.post_id)
					WHERE pm.meta_key = %s
					AND pm.meta_value = %s
					AND p.post_type = 'listings'
					AND p.post_status = 'publish'
	        		" . ($show_sold == false ? "AND (SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'car_sold' AND post_id = pm.post_id LIMIT 1) = 2" : "") . "
				", $meta_key, $meta_value);
			}
		} else {
			$sql = $wpdb->prepare("SELECT count(DISTINCT pm.post_id)
				FROM $wpdb->postmeta pm
				JOIN $wpdb->posts p ON (p.ID = pm.post_id)
				WHERE pm.meta_key = 'multi_options'
				AND pm.meta_value LIKE '%%%s%%'
				AND p.post_type = 'listings'
				AND p.post_status = 'publish'
			", $meta_value);
		}

		$count = $wpdb->get_var($sql);

		return $count;
	}
}

if(!function_exists("get_all_meta_values")){
	function get_all_meta_values( $key = '', $type = 'post', $status = 'publish', $show_sold = false ) {
	    global $wpdb;

	    if( empty( $key ) )
	        return;

	    $r = $wpdb->get_col( $wpdb->prepare( "
	        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	        WHERE pm.meta_key = '%s' 
	        AND p.post_status = '%s' 
	        AND p.post_type = '%s'
	        " . ($show_sold == false ? "AND (SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'car_sold' AND post_id = pm.post_id LIMIT 1) = 2" : "") . "
	    ", $key, $status, $type ) );

	    return $r;
	}
}

if(!function_exists("comp_thumb")){
	function comp_thumb($number, $dimension){
		switch($number){
			case 2:
				$return = ($dimension == "width" ? 562 : 292);
				break;

			case 3:
				$return = ($dimension == "width" ? 362 : 188);
				break;

			case 4:
				$return = ($dimension == "width" ? 262 : 136);
				break;
		}

		return $return;
	}
}

function remove_shortcode_extras($code){
	$return = preg_replace( '%<p>&nbsp;\s*</p>%', '', $code );
	$return = preg_replace( '%<p>\s*</p>%', '', $code );
	$old    = array( '<br />', '<br>' );
	$new    = array( '', '' );
	$return = str_replace( $old, $new, $return );

	return $return;
}

//********************************************
//	Plugin Modifications
//***********************************************************

if(!function_exists("ksort_deep")){
	function ksort_deep(&$array){
		ksort($array);
		foreach($array as $value)
			{if(is_array($value))
				{ksort_deep($value);}}
	}
}

//********************************************
//	Get All Post Meta
//***********************************************************
if( !function_exists("get_post_meta_all") ){
	function get_post_meta_all($post_id){
		global $wpdb;
		$data = array();
		$wpdb->query( "
			SELECT `meta_key`, `meta_value`
			FROM $wpdb->postmeta
			WHERE `post_id` = $post_id");

		foreach($wpdb->last_result as $k => $v){
			$data[$v->meta_key] =   $v->meta_value;
		};
		return $data;
	}
}

//********************************************
//	Listing Video
//***********************************************************
if( !function_exists("listing_video") ){
	function listing_video($url){
		if (strpos($url, 'youtube') > 0) {
			parse_str( parse_url( $url, PHP_URL_QUERY ), $query_string );

			$return['id']      = $query_string['v'];
			$return['service'] = 'youtube';
		} elseif (strpos($url, 'vimeo') > 0) {
			$return['id']      = (int)substr(parse_url($url, PHP_URL_PATH), 1);
			$return['service'] = 'vimeo';
		} else {
			$return['service'] = 'unknown';
		}

		return $return;
	}
}


//********************************************
//	Shortcode / Widget Functions
//***********************************************************
if(!function_exists("testimonial_slider")){
	function testimonial_slider($slide, $speed, $pager, $content, $widget = false){
		// remove br
		$content = str_replace("<br />", "", $content);

		$return  = "<!--Testimonials Start-->";
		$return .= "<div class='testimonial'>";
		$return .= "<ul class=\"testimonial_slider\">";
			if($widget === false){
				$return .= do_shortcode($content);
			} else {

				foreach($widget as $fields){
					$return .= testimonial_slider_quote($fields['name'], $fields['content']);
				}
			}
		$return .= "</ul>";
		$return .= "</div>";
		$return .= "<!--Testimonials End-->";

		$return = remove_shortcode_extras($return);

		return $return;
	}
}

if(!function_exists("testimonial_slider_quote")){
	function testimonial_slider_quote($name, $content){
		$return  = "<li><blockquote class='style1'><span>" . $content;
		$return .= "</span><strong>" . $name . "</strong> ";
		$return .= "</blockquote></li>";

	    return $return;
	}
}

if(!function_exists("random_string")){
	function random_string($length = 10) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}

if(!function_exists("vehicle_scroller")){
	function vehicle_scroller($title = "Recent Vehicles", $description = "Browse through the vast selection of vehicles that have been added to our inventory", $limit = -1, $sort = null, $listings = null, $other_options = array()){
		global $lwp_options, $Listing;

		switch($sort){
			case "newest":
				$args  = array("post_type"      => "listings",
							   "posts_per_page" => $limit,
							   "orderby"        => "date",
							   "order"          => "DESC"
						);
				break;

			case "oldest":
				$args  = array("post_type"      => "listings",
							   "posts_per_page" => $limit,
							   "orderby"        => "date",
							   "order"          => "ASC"
						);
				break;

			case "related":
				//
				$args  = array("post_type"      => "listings",
							   "posts_per_page" => $limit,
							   "order"          => "DESC"
						);
				break;

			default:
				$args = array("post_type"		=> "listings",
							  "posts_per_page" 	=> $limit);
				break;
		}

		$data = array();

		// related
		if($sort == "related" && isset($lwp_options['related_category']) && !empty($lwp_options['related_category'])){
			$data[] = array(
				array(
					"key" 	=> $lwp_options['related_category'],
					"value" => $other_options['related_val'],
				)
			);

			unset($other_options['related_val']);
		}


		if($sort == "related" && isset($other_options['current_id']) && !empty($other_options['current_id'])){
			$args['post__not_in'] = array( $other_options['current_id'] );
		}

		if(empty($lwp_options['inventory_no_sold'])){
			$data[] = array("key"     => "car_sold",
							"value" => "2");
		}

		if(!empty($data)){
			$args['meta_query'] = $data;
		}

		$args['post_status'] = 'publish';

		if(isset($listings) && !empty($listings)){
			$listing_ids = explode(",", $listings);
			$args['post__in'] = $listing_ids;
		}

		$query = new WP_Query( $args );

		ob_start(); ?>

	    <div class="recent-vehicles-wrap">
			<div class="row">
	            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12 recent-vehicles padding-left-none xs-padding-bottom-20">
	    			<div class="scroller_title margin-top-none"><?php echo $title; ?></div>
	                <p><?php echo $description; ?></p>

	                <div class="arrow3 clearfix" id="slideControls3"><span class="prev-btn"></span><span class="next-btn"></span></div>
	    		</div>
	   			<div class="col-md-10 col-sm-8 padding-right-none sm-padding-left-none xs-padding-left-none">
	   				<?php
	   				$additional_attr = "";
	   				if(!empty($other_options)){
	   					foreach($other_options as $key => $option){
	   						$additional_attr .= "data-" . $key . "='" . $option . "' ";
	   					}
	   				}

	   				?>
					<div class="carasouel-slider3" <?php echo (!empty($additional_attr) ? $additional_attr : ""); ?>>
						<?php
	                    while ( $query->have_posts() ) : $query->the_post();
	                        $post_meta       = $Listing->get_listing_meta(get_the_ID());
		                    $listing_options = (isset($post_meta['listing_options']) && !empty($post_meta['listing_options']) ? $post_meta['listing_options'] : "");
		                    $gallery_images  = (isset($post_meta['gallery_images']) && !empty($post_meta['gallery_images']) ? $post_meta['gallery_images'] : "");

	                        if(isset($gallery_images) && !empty($gallery_images) && !empty($gallery_images[0])){
		                        $thumbnail 		 = $Listing->auto_image($gallery_images[0], "auto_thumb", true);
		                    } elseif(empty($gallery_images[0]) && isset($lwp_options['not_found_image']['url']) && !empty($lwp_options['not_found_image']['url'])){
		                    	$thumbnail 		 = $lwp_options['not_found_image']['url'];
		                    } else {
		                    	$thumbnail 		 = LISTING_DIR . "images/pixel.gif";
		                    }

							echo "<div class=\"slide\">";
	                            echo "<a href=\"" . get_permalink(get_the_ID()) . "\"><div class=\"car-block\">";
	                                echo "<div class=\"img-flex\">";
	                                if(isset($post_meta['car_sold']) && $post_meta['car_sold'] == 1){
	                                	echo '<span class="sold_text">' . __('Sold', 'listings') . '</span>';
	                                }
									echo "<span class=\"align-center\"><i class=\"fa fa-3x fa-plus-square-o\"></i></span> <img src=\"" . $thumbnail . "\" alt=\"\" class=\"img-responsive no_border\"> </div>";
	                                echo "<div class=\"car-block-bottom\">";
	                                    echo "<div class='scroller_text'><strong>" . get_the_title() . "</strong></div>";
	                                    echo "<div class='scroller_text'>" . (isset($listing_options['short_desc']) && !empty($listing_options['short_desc']) ? $listing_options['short_desc'] : "") . "</div>";

	                                    if(isset($listing_options['price']['value']) && !empty($listing_options['price']['value'])){
					                		if(isset($lwp_options['price_text_replacement']) && !empty($lwp_options['price_text_replacement']) && $lwp_options['price_text_all_listings'] == 0){
						            			echo do_shortcode($lwp_options['price_text_replacement']);
						            		} else {
						                        echo '<span class="scroller_price">' . $Listing->format_currency($listing_options['price']['value']) . '</span>';
							                }
					                    } elseif( (empty($listing_options['price']['value']) && isset($lwp_options['price_text_all_listings']) && $lwp_options['price_text_all_listings'] == 1 ) || (isset($lwp_options['price_text_replacement']) && !empty($lwp_options['price_text_replacement']) && $lwp_options['price_text_all_listings'] == 0) ){
					                    	echo do_shortcode($lwp_options['price_text_replacement']);
					                    }

	                                echo "</div>";
	                            echo "</div></a>";
	                        echo "</div>";
	                    endwhile; ?>
	                </div>
	    		</div>

	            <div class="clear"></div>
			</div>
	    </div>
	<?php

	wp_reset_query();

	return ob_get_clean();

	}
}

//********************************************
//	Filter Listings
//***********************************************************
function filter_listing_results($var) {
	global $lwp_options, $Listing, $Listing_Template;

	add_filter('posts_orderby', 'auto_sold_to_bottom');
	$listing_args = $Listing->listing_args($_POST);
	$args		  = $args2 = $listing_args[0];

	// meta query with dashes
	if(!empty($args['meta_query'])){
		foreach($args['meta_query'] as $key => $meta){
			if(isset($args['meta_query'][$key]['value']) && !empty($args['meta_query'][$key]['value'])){
				$args['meta_query'][$key]['value'] = str_replace("%2D", "-", (isset($meta['value']) && !empty($meta['value']) ? $meta['value'] : ""));
			}
		}
	}

	$posts = get_posts($args);
	remove_filter('posts_orderby', 'auto_sold_to_bottom');

	$return = '';
	foreach($posts as $post){
		$return .= (isset($_POST['layout']) && !empty($_POST['layout']) ? $Listing_Template->locate_template("inventory_listing", array("id" => $post->ID, "layout" => $_POST['layout'])) : $Listing_Template->locate_template("inventory_listing", array("id" => $post->ID)));
	}

	$args['posts_per_page'] = -1;

	$total_posts   = get_posts($args);
	$total_matches = count($total_posts);
	$return = ($total_matches == 0 ? do_shortcode('[alert type="2" close="No"]' . __("No listings found", "listings") . '[/alert]') . "<div class='clearfix'></div>" : $return);

	$paged = (get_query_var('paged') ? get_query_var('paged') : false);

	$return_array = array( "content"        => $return,
						   "number"         => $total_matches,
						   "top_page"       => page_of_box($paged, false, $total_posts),
						   "bottom_page"    => bottom_page_box(false, $paged, null, array("number" => $total_matches), $total_posts),
						   "dependancies"   => $Listing->process_dependancies($_POST),
						   "args"		    => $args2
				   );

	// filter
	if(isset($filter) && !empty($filter)){
		$return_array['filter'] = $filter;
	}

	if($var === true){
		return json_encode( $return_array );
	} else {
		echo json_encode( $return_array );
	}

   	die();
}

add_action("wp_ajax_filter_listing", "filter_listing_results");
add_action("wp_ajax_nopriv_filter_listing", "filter_listing_results");

//********************************************
//	Show Social Icons
//***********************************************************
if( !function_exists("show_social_icons") ) {
	function show_social_icons() {
		if ( has_post_thumbnail() ) {
		 	$image = wp_get_attachment_image_src(get_the_post_thumbnail());
		} elseif(is_singular('listings')) {
			$saved_images   = get_post_meta(get_queried_object_id(), "gallery_images");
			$gallery_images = unserialize($saved_images[0]);

			$image = (isset($gallery_images[0]) && !empty($gallery_images[0]) ? $gallery_images[0] : "");
		} else {
			$image = '';
		}
		?>
		<ul class="social-likes" data-url="<?php echo get_permalink(); ?>" data-title="<?php echo get_the_title(); ?>">
            <li class="facebook" title="<?php _e("Share link on Facebook", "listings"); ?>"></li>
            <li class="plusone" title="<?php _e("Share link on Google+", "listings"); ?>"></li>
            <li class="pinterest" title="<?php _e("Share image on Pinterest", "listings"); ?>" data-media="<?php echo $image; ?>"></li>
            <li class="twitter" title="<?php _e("Share link on Twitter", "listings"); ?>"></li>
        </ul>
	<?php
	}
}


if(!function_exists("is_ajax_request")){
	function is_ajax_request(){
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}
}

function column_maker(){ ?>
	<div id='full_column' class='column_display_container' data-number='0'>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
        <div class='empty one'></div>
    </div>

    <br />

    <div class='generate_columns button'><?php _e("Generate Columns", "listings"); ?></div>

    <?php
	$i     = 1;
	$width = 31;

	while($i <= 12){
		echo "<div class='column_display_container insert' data-number='" . $i . "'><span class='label'>" . $i . ($i != 1 ? " / 12" : "") . "</span> <div class='full twelve' style='width: " . ($i * $width) . "px;'></div></div><br />";
		$i++;
	}

	die;
}
add_action('wp_ajax_column_maker', 'column_maker');
add_action('wp_ajax_nopriv_column_maker', 'column_maker');

if(!function_exists("remove_editor")){
	function remove_editor() {
        // Visual Composer Frontend Editor Fix...
        if(!isset($_GET['vc_action'])){
            remove_post_type_support('listings', 'editor');
        }
	}
}
add_action('admin_init', 'remove_editor');


if(!function_exists("youtube_video_id")){
	function youtube_video_id($url) {
		if(is_youtube($url)) {
			$pattern = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/';
			preg_match($pattern, $url, $matches);
			if (count($matches) && strlen($matches[7]) == 11) {
				return $matches[7];
			}
		}

		return '';
	}
}

if(!function_exists("is_youtube")){
	function is_youtube($url) {
		return (preg_match('/youtu\.be/i', $url) || preg_match('/youtube\.com\/watch/i', $url));
	}
}


if(!function_exists("get_all_media_images")){
	function get_all_media_images(){
		$query_images_args = array(
			'post_type' => 'attachment', 'post_mime_type' =>'image', 'post_status' => 'inherit', 'posts_per_page' => -1,
		);

		$query_images = new WP_Query( $query_images_args );
		$images = array();

		foreach ( $query_images->posts as $image) {
			$images[]= wp_get_attachment_url( $image->ID );
		}

		return $images;
	}
}

//********************************************
//	Single Listing Template
//***********************************************************
add_filter( 'template_include', 'my_plugin_templates' );
function my_plugin_templates( $template ) {
    $post_types = array(  );

    if ( is_singular( 'listings' ) && ! file_exists( get_stylesheet_directory() . '/single-listings.php' ) ){
        $template = LISTING_HOME . 'single-listings.php';
	} elseif( is_singular( 'listings_portfolio' ) ){
		if(file_exists( get_stylesheet_directory() . '/single-portfolio.php' )){
			$template = get_stylesheet_directory() . '/single-portfolio.php';
		} else {
			$template = LISTING_HOME . 'single-portfolio.php';
		}
	}

    return $template;
}

/* Form */
if(!function_exists("listing_form")){
	function listing_form(){
		global $lwp_options, $Listing;

		$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
		$form   = $_POST['form'];
		$errors = array();

		// email headers
		$headers  = "From: " . $_POST['email'] . "\r\n";
		$headers .= "Reply-To: ". $_POST['email'] . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

		$subject  = ucwords(str_replace("_", " ", $_POST['form']));

		if($form == "email_friend"){

			// validate email
			if(!filter_var($_POST['friends_email'], FILTER_VALIDATE_EMAIL) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
				$errors[] = __("Not a valid email", "listings");
			} else {
				$post_meta = $Listing->get_listing_meta($_POST['id']);

				$listing_options = (isset($post_meta['listing_options']) && !empty($post_meta['listing_options']) ? $post_meta['listing_options'] : array());
				$gallery_images  = (isset($post_meta['gallery_images']) && !empty($post_meta['gallery_images']) ? $post_meta['gallery_images'] : array());

				$name    = (isset($_POST['name']) && !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : "");
				$friend  = (isset($_POST['friends_email']) && !empty($_POST['friends_email']) ? sanitize_text_field($_POST['friends_email']) : "");
				$message = (isset($_POST['message']) && !empty($_POST['message']) ? sanitize_text_field($_POST['message']) : "");

				$thumbnail  = $Listing->auto_image($gallery_images[0], "auto_thumb", true);//$gallery_images[0]['thumb']['url'];

				$categories = $Listing->get_listing_categories();

				$table   = "<table width='100%' border='0' cellspacing='0' cellpadding='2'><tbody>";

				$table  .= "<tr>
					<td><img src='" . $thumbnail . "'></td>
					<td style='font-weight:bold;color:#000;'>" . get_the_title($_POST['id']) . "</td>
					<td></td>
					<td>" . (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")) . ": " . $Listing->format_currency($listing_options['price']['value']) . "</td>
				</tr>";

				foreach($categories as $category){
					$slug   = $category['slug'];
					$table .= "<tr><td>" . $category['singular'] . ": </td><td> " . (isset($post_meta[$slug]) && !empty($post_meta[$slug]) ? $post_meta[$slug] : __("N/A", "listings")) . "</td></tr>";
				}

				$table  .= "<tr>
								<td>&nbsp;</td>
								<td align='center' style='background-color:#000;font-weight:bold'><a href='" . get_permalink($_POST['id']) . "' style='color:#fff;text-decoration:none' target='_blank'>" . __('Click for more details', 'listings') . "</a></td>
							</tr>";

				$table  .= "</tbody></table>";

				$search  = array('{table}', '{message}', '{name}');
				$replace = array($table, $message, $name);

				$subject      = str_replace("{name}", $name, $lwp_options['friend_subject']);
				$send_message = str_replace($search, $replace, $lwp_options['friend_layout']);

				$mail         = @wp_mail($friend, $subject, $send_message, $headers);
			}
		} else {

			// validate email
			if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
				$errors[] = __("Not a valid email", "listings");
			} else {

				switch ($form) {
					case 'request_info':
						$to      = ($lwp_options['info_to'] ? $lwp_options['info_to'] : get_bloginfo('admin_email'));
						$subject = $lwp_options['info_subject'];

						$name           = (isset($_POST['name']) && !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : "");
						$contact_method = (isset($_POST['contact_method']) && !empty($_POST['contact_method']) ? sanitize_text_field($_POST['contact_method']) : "");
						$email          = (isset($_POST['email']) && !empty($_POST['email']) ? sanitize_text_field($_POST['email']) : "");
						$phone          = (isset($_POST['phone']) && !empty($_POST['phone']) ? sanitize_text_field($_POST['phone']) : "");
						$comment        = (isset($_POST['comments']) && !empty($_POST['comments']) ? sanitize_text_field($_POST['comments']) : "");

						$table   = "<table border='0'>";
						$table  .= "<tr><td>" . __("First Name", "listings") . ": </td><td> " . $name . "</td></tr>";
						$table  .= "<tr><td>" . __("Contact Method", "listings") . ": </td><td> " . $contact_method . "</td></tr>";
						$table  .= "<tr><td>" . __("Phone", "listings") . ": </td><td> " . $phone . "</td></tr>";
						$table  .= "<tr><td>" . __("Email", "listings") . ": </td><td> " . $email . "</td></tr>";
						$table  .= "<tr><td>" . __("Question/Comment", "listings") . ": </td><td> " . $comment . "</td></tr>";
						$table  .= "</table>";

						$link    = get_permalink($_POST['id']);

						$search  = array("{name}", "{contact_method}", "{email}", "{phone}", "{table}", "{link}");
						$replace = array($name, $contact_method, $email, $phone, $table, $link);

						$message = str_replace($search, $replace, $lwp_options['info_layout']);
					break;

					case 'schedule':
						$to      = ($lwp_options['drive_to'] ? $lwp_options['drive_to'] : get_bloginfo('admin_email'));
						$subject = $lwp_options['drive_subject'];

						$name           = (isset($_POST['name']) && !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : "");
						$contact_method = (isset($_POST['contact_method']) && !empty($_POST['contact_method']) ? sanitize_text_field($_POST['contact_method']) : "");
						$email          = (isset($_POST['email']) && !empty($_POST['email']) ? sanitize_text_field($_POST['email']) : "");
						$phone          = (isset($_POST['phone']) && !empty($_POST['phone']) ? sanitize_text_field($_POST['phone']) : "");
						$best_day       = (isset($_POST['best_day']) && !empty($_POST['best_day']) ? sanitize_text_field($_POST['best_day']) : "");
						$best_time      = (isset($_POST['best_time']) && !empty($_POST['best_time']) ? sanitize_text_field($_POST['best_time']) : "");

						$table   = "<table border='0'>";
						$table  .= "<tr><td>" . __("Name", "listings") . ": </td><td> " . $name . "</td></tr>";
						$table  .= "<tr><td>" . __("Contact Method", "listings") . ": </td><td> " . $contact_method . "</td></tr>";
						$table  .= "<tr><td>" . __("Phone", "listings") . ": </td><td> " . $phone . "</td></tr>";
						$table  .= "<tr><td>" . __("Email", "listings") . ": </td><td> " . $email . "</td></tr>";
						$table  .= "<tr><td>" . __("Best Date", "listings") . ": </td><td> " . $best_day . " " . $best_time . "</td></tr>";
						$table  .= "</table>";

						$link    = get_permalink($_POST['id']);

						$search  = array("{name}", "{contact_method}", "{email}", "{phone}", "{best_day}", "{best_time}", "{table}", "{link}");
						$replace = array($name, $contact_method, $email, $phone, $best_day, $best_time, $table, $link);

						$message = str_replace($search, $replace, $lwp_options['drive_layout']);
					break;

					case 'make_offer':
						$to      = ($lwp_options['offer_to'] ? $lwp_options['offer_to'] : get_bloginfo('admin_email'));
						$subject = $lwp_options['offer_subject'];


						$name 				= (isset($_POST['name']) && !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : "");
						$contact_method 	= (isset($_POST['contact_method']) && !empty($_POST['contact_method']) ? sanitize_text_field($_POST['contact_method']) : "");
						$email 				= (isset($_POST['email']) && !empty($_POST['email']) ? sanitize_text_field($_POST['email']) : "");
						$phone 				= (isset($_POST['phone']) && !empty($_POST['phone']) ? sanitize_text_field($_POST['phone']) : "");
						$offered_price 		= (isset($_POST['offered_price']) && !empty($_POST['offered_price']) ? sanitize_text_field($_POST['offered_price']) : "");
						$financing_required = (isset($_POST['financing_required']) && !empty($_POST['financing_required']) ? sanitize_text_field($_POST['financing_required']) : "");
						$other_comments 	= (isset($_POST['other_comments']) && !empty($_POST['other_comments']) ? sanitize_text_field($_POST['other_comments']) : "");


						$table   = "<table border='0'>";
						$table  .= "<tr><td>" . __("Name", "listings") . ": </td><td> " . $name . "</td></tr>";
						$table  .= "<tr><td>" . __("Contact Method", "listings") . ": </td><td> " . $contact_method . "</td></tr>";
						$table  .= "<tr><td>" . __("Phone", "listings") . ": </td><td> " . $phone . "</td></tr>";
						$table  .= "<tr><td>" . __("Email", "listings") . ": </td><td> " . $email . "</td></tr>";
						$table  .= "<tr><td>" . __("Offered Price", "listings") . ": </td><td> " . $offered_price . "</td></tr>";
						$table  .= "<tr><td>" . __("Financing Required", "listings") . ": </td><td> " . $financing_required . "</td></tr>";
						$table  .= "<tr><td>" . __("Other Comments", "listings") . ": </td><td> " . $other_comments . "</td></tr>";
						$table  .= "</table>";

						$link   = get_permalink($_POST['id']);

						$search  = array("{name}", "{contact_method}", "{email}", "{phone}", "{offered_price}", "{financing_required}", "{other_comments}", "{table}", "{link}");
						$replace = array($name, $contact_method, $email, $phone, $offered_price, $financing_required, $other_comments, $table, $link);

						$message = str_replace($search, $replace, $lwp_options['offer_layout']);
					break;

					case 'trade_in':
						$to      = ($lwp_options['trade_to'] ? $lwp_options['trade_to'] : get_bloginfo('admin_email'));
						$subject = $lwp_options['trade_subject'];

						$form_items = array(
							__("First Name", "listings") => "first_name", 
							__("Last Name", "listings") => "last_name", 
							__("Work Phone", "listings") => "work_phone", 
							__("Phone", "listings") => "phone", 
							__("Email", "listings") => "email", 
							__("Contact Method", "listings") => "contact_method", 
							__("Comments", "listings") => "comments", 
							__("Options", "listings") => "options", 
							__("Year", "listings") => "year", 
							__("Make", "listings") => "make", 
							__("Model", "listings") => "model", 
							__("Exterior Colour", "listings") => "exterior_colour", 
							__("VIN", "listings") => "vin", 
							__("Kilometres", "listings") => "kilometres", 
							__("Engine", "listings") => "engine", 
							__("Doors", "listings") => "doors", 
							__("Transmission", "listings") => "transmission", 
							__("Drivetrain", "listings") => "drivetrain", 
							__("Body Rating", "listings") => "body_rating", 
							__("Tire Rating", "listings") => "tire_rating", 
							__("Engine Rating", "listings") => "engine_rating", 
							__("Transmission Rating", "listings") => "transmission_rating", 
							__("Glass Rating", "listings") => "glass_rating", 
							__("Interior Rating", "listings") => "interior_rating", 
							__("Exhaust Rating", "listings") => "exhaust_rating", 
							__("Rental Rating", "listings") => "rental_rating", 
							__("Odometer Accurate", "listings") => "odometer_accurate", 
							__("Service Records", "listings") => "service_records", 
							__("Lienholder", "listings") => "lienholder", 
							__("Titleholder", "listings") => "titleholder", 
							__("Equipment", "listings") => "equipment", 
							__("Vehiclenew", "listings") => "vehiclenew", 
							__("Accidents", "listings") => "accidents", 
							__("Damage", "listings") => "damage", 
							__("Paint", "listings") => "paint", 
							__("Salvage", "listings") => "salvage"
						);

						$table  = "<table border='0'>";
						foreach($form_items as $key => $single){
							$table .= "<tr><td>" . $key . ": </td><td> ";
							if($single == "options" && is_array($_POST[$single]) && isset($_POST[$single]) && !empty($_POST[$single])){
								$table .= rtrim(implode(", ", $_POST[$single]), ", ");
							} else {
								$table .= (isset($_POST[$single]) && !empty($_POST[$single]) ? $_POST[$single] : "");
							}

							$table .= "</td></tr>";
						}
						$table .= "</table>";

						$link   = get_permalink($_POST['id']);

						$search   = array("{table}", "{link}");
						$replace  = array($table, $link);

						$message  = str_replace($search, $replace, $lwp_options['trade_layout']);
					break;
				}

				// if location email
				$location_email    = get_option("location_email");
				$location_category = $Listing->get_location_email_category();

				// var_dump($location_email);

				// var_dump($location_category);

				if(isset($location_email) && !empty($location_email) && isset($location_category) && !empty($location_category)){
					$to = (isset($location_email[get_post_meta( (int)$_POST['id'], $location_category, true )]) && !empty($location_email[get_post_meta( (int)$_POST['id'], $location_category, true )]) ? $location_email[get_post_meta( (int)$_POST['id'], $location_category, true )] : $to);
				}


				$mail = @wp_mail($to, $subject, $message, $headers);
			}
		}

		if($mail && empty($errors)){
			echo json_encode(
				array(
					"message" => __("Sent Successfully", "listings"),
					"status"  => "success"
				)
			);
		} else {
			$return_message = "<ul class='error_list'>";
			foreach($errors as $error){
				$return_message .= "<li>" . $error . "</li>";
			}
			$return_message .= "</ul>";

			echo json_encode(
				array(
					"message" => $return_message,
					"status"  => "error"
				)
			);
		}

		die;
	}
}
add_action("wp_ajax_listing_form", "listing_form");
add_action("wp_ajax_nopriv_listing_form", "listing_form");

function get_first_post_image($post) {
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = (isset($matches[1][0]) && !empty($matches[1][0]) ? $matches[1][0] : false);

	return $first_img;
}



function url_origin($s, $use_forwarded_host=false){
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url($s, $use_forwarded_host=false){
    return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

function recaptcha_check(){
	global $lwp_options;

	require_once(LISTING_HOME . 'recaptchalib.php');

	$resp = recaptcha_check_answer($lwp_options['recaptcha_private_key'],
								  $_SERVER["REMOTE_ADDR"],
								  $_POST["recaptcha_challenge_field"],
								  $_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) {
	  	echo __("The reCAPTCHA wasn't entered correctly. Go back and try it again.", "listings");// ."(reCAPTCHA said: " . $resp->error . ")");
	} else {
		echo __("success", "listings");
	}

	die;
}
add_action("wp_ajax_recaptcha_check", "recaptcha_check");
add_action("wp_ajax_nopriv_recaptcha_check", "recaptcha_check");

function get_menu_parent_ID($menu_name, $post_id){
    if(!isset($menu_name)){
      return 0;
    }

    $menu_slug = $menu_name;
    $locations = get_nav_menu_locations();
    $menu_id   = $locations[$menu_slug];
    $menu_items     = wp_get_nav_menu_items($menu_id);
    $parent_item_id = wp_filter_object_list($menu_items,array('object_id'=>$post_id),'and','menu_item_parent');
    $parent_item_id = array_shift( $parent_item_id );

	if(!function_exists("automotive_check_parent_item")){
    function automotive_check_parent_item($parent_item_id,$menu_items){
      $parent_post_id = wp_filter_object_list( $menu_items, array( 'ID' => $parent_item_id ), 'and', 'object_id' );
      $parent_item_id = wp_filter_object_list($menu_items,array('ID'=>$parent_item_id),'and','menu_item_parent');
      $parent_item_id = array_shift( $parent_item_id );
      if($parent_item_id=="0"){
        $parent_post_id = array_shift($parent_post_id);
        return $parent_post_id;
      }else{
        return automotive_check_parent_item($parent_item_id,$menu_items);
      }
    }
    }
    if(!empty($parent_item_id)){
      return automotive_check_parent_item($parent_item_id,$menu_items);
    }else{
      return $post_id;
    }
}

add_filter('nav_menu_css_class', 'add_active_class', 10, 2 );

function add_active_class($classes, $item) {
  if($item->object_id == get_option("inventory_parent_page") && is_singular("listings")){
    $classes[] = "active";
  }

  if($item->object_id == get_option( 'page_for_posts' ) && ($key = array_search("current_page_parent", $classes)) !== false){
    unset($classes[$key]);
  }

  return $classes;
}


function get_inventory_page_parent_id($options){
	if(isset($options['inventory_page']) && !empty($options['inventory_page'])){
		$parent_item = get_menu_parent_ID("header-menu", $options['inventory_page']);

	    update_option("inventory_parent_page", $parent_item);
	}
}
add_action("redux/options/listing_wp/saved", "get_inventory_page_parent_id");

// listing categories import
function import_listing_categories(){
    $demo_content = unserialize('a:18:{s:4:"year";a:7:{s:8:"singular";s:4:"Year";s:6:"plural";s:5:"Years";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:6:{i:2014;s:4:"2014";i:2013;s:4:"2013";i:2012;s:4:"2012";i:2010;s:4:"2010";i:2009;s:4:"2009";i:2015;s:4:"2015";}s:4:"slug";s:4:"year";}s:4:"make";a:7:{s:8:"singular";s:4:"Make";s:6:"plural";s:5:"Makes";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:1:{s:7:"porsche";s:7:"Porsche";}s:4:"slug";s:4:"make";}s:5:"model";a:7:{s:8:"singular";s:5:"Model";s:6:"plural";s:6:"Models";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:5:{s:7:"carrera";s:7:"Carrera";s:3:"gts";s:3:"GTS";s:7:"cayenne";s:7:"Cayenne";s:7:"boxster";s:7:"Boxster";s:5:"macan";s:5:"Macan";}s:4:"slug";s:5:"model";}s:10:"body-style";a:7:{s:8:"singular";s:10:"Body Style";s:6:"plural";s:11:"Body Styles";s:10:"filterable";s:1:"1";s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:3:{s:11:"convertible";s:11:"Convertible";s:5:"sedan";s:5:"Sedan";s:22:"sports-utility-vehicle";s:22:"Sports Utility Vehicle";}s:4:"slug";s:10:"body-style";}s:7:"mileage";a:7:{s:8:"singular";s:7:"Mileage";s:6:"plural";s:8:"Mileages";s:10:"filterable";s:1:"1";s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:4:"&lt;";s:5:"terms";a:10:{i:10000;s:5:"10000";i:20000;s:5:"20000";i:30000;s:5:"30000";i:40000;s:5:"40000";i:50000;s:5:"50000";i:60000;s:5:"60000";i:70000;s:5:"70000";i:80000;s:5:"80000";i:90000;s:5:"90000";i:100000;s:6:"100000";}s:4:"slug";s:7:"mileage";}s:12:"transmission";a:7:{s:8:"singular";s:12:"Transmission";s:6:"plural";s:13:"Transmissions";s:10:"filterable";s:1:"1";s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:8:{s:14:"6-speed-manual";s:14:"6-Speed Manual";s:17:"5-speed-automatic";s:17:"5-Speed Automatic";s:17:"8-speed-automatic";s:17:"8-Speed Automatic";s:17:"6-speed-semi-auto";s:17:"6-Speed Semi-Auto";s:17:"6-speed-automatic";s:17:"6-Speed Automatic";s:14:"5-speed-manual";s:14:"5-Speed Manual";s:17:"8-speed-tiptronic";s:17:"8-Speed Tiptronic";s:11:"7-speed-pdk";s:11:"7-Speed PDK";}s:4:"slug";s:12:"transmission";}s:12:"fuel-economy";a:7:{s:8:"singular";s:12:"Fuel Economy";s:6:"plural";s:14:"Fuel Economies";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:4:"&lt;";s:5:"terms";a:5:{i:10;s:2:"10";i:20;s:2:"20";i:30;s:2:"30";i:40;s:2:"40";i:50;s:2:"50";}s:4:"slug";s:12:"fuel-economy";}s:9:"condition";a:7:{s:8:"singular";s:9:"Condition";s:6:"plural";s:10:"Conditions";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:3:{s:9:"brand-new";s:9:"Brand New";s:13:"slightly-used";s:13:"Slightly Used";s:4:"used";s:4:"Used";}s:4:"slug";s:9:"condition";}s:8:"location";a:7:{s:8:"singular";s:8:"Location";s:6:"plural";s:9:"Locations";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:1:{s:7:"toronto";s:7:"Toronto";}s:4:"slug";s:8:"location";}s:5:"price";a:9:{s:8:"singular";s:5:"Price";s:6:"plural";s:6:"Prices";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:4:"&lt;";s:8:"currency";s:1:"1";s:10:"link_value";s:5:"price";s:5:"terms";a:10:{i:10000;s:5:"10000";i:20000;s:5:"20000";i:30000;s:5:"30000";i:40000;s:5:"40000";i:50000;s:5:"50000";i:60000;s:5:"60000";i:70000;s:5:"70000";i:80000;s:5:"80000";i:90000;s:5:"90000";i:100000;s:6:"100000";}s:4:"slug";s:5:"price";}s:10:"drivetrain";a:7:{s:8:"singular";s:10:"Drivetrain";s:6:"plural";s:11:"Drivetrains";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:4:{s:3:"awd";s:3:"AWD";s:3:"rwd";s:3:"RWD";s:3:"4wd";s:3:"4WD";s:14:"drivetrain-rwd";s:14:"Drivetrain RWD";}s:4:"slug";s:10:"drivetrain";}s:6:"engine";a:7:{s:8:"singular";s:6:"Engine";s:6:"plural";s:7:"Engines";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:9:{s:7:"3-6l-v6";s:7:"3.6L V6";s:17:"4-8l-v8-automatic";s:17:"4.8L V8 Automatic";s:13:"4-8l-v8-turbo";s:13:"4.8L V8 Turbo";s:7:"4-8l-v8";s:7:"4.8L V8";s:7:"3-8l-v6";s:7:"3.8L V6";s:18:"2-9l-mid-engine-v6";s:18:"2.9L Mid-Engine V6";s:18:"3-4l-mid-engine-v6";s:18:"3.4L Mid-Engine V6";s:14:"3-0l-v6-diesel";s:14:"3.0L V6 Diesel";s:13:"3-0l-v6-turbo";s:13:"3.0L V6 Turbo";}s:4:"slug";s:6:"engine";}s:14:"exterior-color";a:7:{s:8:"singular";s:14:"Exterior Color";s:6:"plural";s:15:"Exterior Colors";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:10:{s:13:"racing-yellow";s:13:"Racing Yellow";s:23:"rhodium-silver-metallic";s:23:"Rhodium Silver Metallic";s:16:"peridot-metallic";s:16:"Peridot Metallic";s:17:"ruby-red-metallic";s:17:"Ruby Red Metallic";s:5:"white";s:5:"White";s:18:"aqua-blue-metallic";s:18:"Aqua Blue Metallic";s:23:"chestnut-brown-metallic";s:23:"Chestnut Brown Metallic";s:10:"guards-red";s:10:"Guards Red";s:18:"dark-blue-metallic";s:18:"Dark Blue Metallic";s:18:"lime-gold-metallic";s:18:"Lime Gold Metallic";}s:4:"slug";s:14:"exterior-color";}s:14:"interior-color";a:7:{s:8:"singular";s:14:"Interior Color";s:6:"plural";s:15:"Interior Colors";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:8:{s:14:"interior-color";s:14:"Interior Color";s:10:"agate-grey";s:10:"Agate Grey";s:15:"alcantara-black";s:15:"Alcantara Black";s:11:"marsala-red";s:11:"Marsala Red";s:5:"black";s:5:"Black";s:13:"platinum-grey";s:13:"Platinum Grey";s:11:"luxor-beige";s:11:"Luxor Beige";s:19:"black-titanium-blue";s:21:"Black / Titanium Blue";}s:4:"slug";s:14:"interior-color";}s:3:"mpg";a:8:{s:8:"singular";s:3:"MPG";s:6:"plural";s:3:"MPG";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:10:"link_value";s:3:"mpg";s:5:"terms";a:7:{s:14:"19-city-27-hwy";s:16:"19 city / 27 hwy";s:14:"16-city-24-hwy";s:16:"16 city / 24 hwy";s:14:"15-city-21-hwy";s:16:"15 city / 21 hwy";s:14:"18-city-26-hwy";s:16:"18 city / 26 hwy";s:14:"20-city-30-hwy";s:16:"20 city / 30 hwy";s:14:"20-city-28-hwy";s:16:"20 City / 28 Hwy";s:14:"19-city-29-hwy";s:16:"19 city / 29 hwy";}s:4:"slug";s:3:"mpg";}s:12:"stock-number";a:7:{s:8:"singular";s:12:"Stock Number";s:6:"plural";s:13:"Stock Numbers";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:12:{i:590388;s:6:"590388";i:590524;s:6:"590524";i:590512;s:6:"590512";i:590499;s:6:"590499";i:590435;s:6:"590435";i:590421;s:6:"590421";i:590476;s:6:"590476";i:590271;s:6:"590271";i:590497;s:6:"590497";i:16115;s:5:"16115";i:590124;s:6:"590124";i:590562;s:6:"590562";}s:4:"slug";s:12:"stock-number";}s:10:"vin-number";a:7:{s:8:"singular";s:10:"VIN Number";s:6:"plural";s:11:"VIN Numbers";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:11:{s:17:"wp0cb2a92cs376450";s:17:"WP0CB2A92CS376450";s:17:"wp0ab2a74al092462";s:17:"WP0AB2A74AL092462";s:17:"wp1ad29p09la73659";s:17:"WP1AD29P09LA73659";s:17:"wp0ab2a74al079264";s:17:"WP0AB2A74AL079264";s:17:"wp0cb2a92cs754706";s:17:"WP0CB2A92CS754706";s:17:"wp0ca2a96as740274";s:17:"WP0CA2A96AS740274";s:17:"wp0ab2a74al060306";s:17:"WP0AB2A74AL060306";s:17:"wp1ad29p09la65818";s:17:"WP1AD29P09LA65818";s:17:"wp0ab2e81ek190171";s:17:"WP0AB2E81EK190171";s:17:"wp0cb2a92cs377324";s:17:"WP0CB2A92CS377324";s:17:"wp0ct2a92cs326491";s:17:"WP0CT2A92CS326491";}s:4:"slug";s:10:"vin-number";}s:7:"options";a:1:{s:5:"terms";a:40:{s:23:"adaptive-cruise-control";s:23:"Adaptive Cruise Control";s:7:"airbags";s:7:"Airbags";s:16:"air-conditioning";s:16:"Air Conditioning";s:12:"alarm-system";s:12:"Alarm System";s:21:"anti-theft-protection";s:21:"Anti-theft Protection";s:15:"audio-interface";s:15:"Audio Interface";s:25:"automatic-climate-control";s:25:"Automatic Climate Control";s:20:"automatic-headlights";s:20:"Automatic Headlights";s:15:"auto-start-stop";s:15:"Auto Start/Stop";s:19:"bi-xenon-headlights";s:19:"Bi-Xenon Headlights";s:18:"bluetoothr-handset";s:19:"Bluetooth® Handset";s:20:"boser-surround-sound";s:21:"BOSE® Surround Sound";s:25:"burmesterr-surround-sound";s:26:"Burmester® Surround Sound";s:18:"cd-dvd-autochanger";s:18:"CD/DVD Autochanger";s:9:"cdr-audio";s:9:"CDR Audio";s:14:"cruise-control";s:14:"Cruise Control";s:21:"direct-fuel-injection";s:21:"Direct Fuel Injection";s:22:"electric-parking-brake";s:22:"Electric Parking Brake";s:10:"floor-mats";s:10:"Floor Mats";s:18:"garage-door-opener";s:18:"Garage Door Opener";s:15:"leather-package";s:15:"Leather Package";s:25:"locking-rear-differential";s:25:"Locking Rear Differential";s:20:"luggage-compartments";s:20:"Luggage Compartments";s:19:"manual-transmission";s:19:"Manual Transmission";s:17:"navigation-module";s:17:"Navigation Module";s:15:"online-services";s:15:"Online Services";s:10:"parkassist";s:10:"ParkAssist";s:21:"porsche-communication";s:21:"Porsche Communication";s:14:"power-steering";s:14:"Power Steering";s:16:"reversing-camera";s:16:"Reversing Camera";s:20:"roll-over-protection";s:20:"Roll-over Protection";s:12:"seat-heating";s:12:"Seat Heating";s:16:"seat-ventilation";s:16:"Seat Ventilation";s:18:"sound-package-plus";s:18:"Sound Package Plus";s:20:"sport-chrono-package";s:20:"Sport Chrono Package";s:22:"steering-wheel-heating";s:22:"Steering Wheel Heating";s:24:"tire-pressure-monitoring";s:24:"Tire Pressure Monitoring";s:25:"universal-audio-interface";s:25:"Universal Audio Interface";s:20:"voice-control-system";s:20:"Voice Control System";s:14:"wind-deflector";s:14:"Wind Deflector";}}}');
	$update = update_option("listing_categories", $demo_content);

	if($update){
		update_option("show_listing_categories", "hide");
		_e("The listing categories have been imported.", "listings");
	} else {
		_e("There was an error importing the listing categories, please try again later.", "listings");
	}

	die;
}

add_action("wp_ajax_import_listing_categories", "import_listing_categories");
add_action("wp_ajax_noprive_import_listing_categories", "import_listing_categories");


function convert_seo_string($string){
	global $post, $Listing;

	$categories = $Listing->get_listing_categories();
	$post_meta  = $Listing->get_listing_meta($post->ID);

	foreach($categories as $category){
	    $safe   = str_replace(" ", "_", strtolower($category['singular']));
	    $string = str_replace("%" . $safe . "%", (isset($post_meta[$safe]) && !empty($post_meta[$safe]) ? $post_meta[$safe] : ""), $string);
	}

	return $string;
}

function hide_import_listing_categories(){
	update_option("show_listing_categories", "hide");

	die;
}

add_action("wp_ajax_hide_import_listing_categories", "hide_import_listing_categories");
add_action("wp_ajax_noprive_hide_import_listing_categories", "hide_import_listing_categories");


function remove_parent_classes($class) {
	return ($class == 'current_page_item' || $class == 'current_page_parent' || $class == 'current_page_ancestor'  || $class == 'current-menu-item') ? false : true;
}

function add_class_to_wp_nav_menu($classes){
     switch (get_post_type()){
     	case 'listings_portfolio':
     		// we're viewing a custom post type, so remove the 'current_page_xxx and current-menu-item' from all menu items.
     		$classes = array_filter($classes, "remove_parent_classes");

     		break;
    }
	return $classes;
}
add_filter('nav_menu_css_class', 'add_class_to_wp_nav_menu');



// gget_child_categories
function get_child_categories(){
	//echo $_POST['name'] . "<br>" . $_POST['value'];
	global $lwp_options, $Listing;

	$return = array();

	$category        = $Listing->get_single_listing_category($_POST['name']);
	$load_options    = ($category['dependancies'][$_POST['key']]);
	$second_category = $Listing->get_single_listing_category($_POST['name']);

	if(isset($second_category['dependancies'][$_POST['key']]) && !empty($second_category['dependancies'][$_POST['key']])){
		// foreach option
		foreach($second_category['dependancies'][$_POST['key']] as $key){
			if(!is_null($category['terms'][$key])){
				$return[] = array("key" => $key, "term" => $category['terms'][$key]);
			}
		}
	}

	if(isset($lwp_options['sort_terms']) && $lwp_options['sort_terms'] == "desc"){
    	arsort($return);
    } else {
        asort($return);
    }

    $return = array_filter($return, 'is_not_null');
    $return = array_values($return);

    // array_multisort(array_map('strtolower', $return), $return);

	echo json_encode($return);

	die;
}

function is_not_null ($var) { return !is_null($var); }

add_action("wp_ajax_get_child_categories", "get_child_categories");
add_action("wp_ajax_nopriv_get_child_categories", "get_child_categories");

//********************************************
//	Add subscriber to mail chimp (WP-AJAX)
//***********************************************************
function add_mailchimp(){
	$email = wp_filter_nohtml_kses( $_POST['email'] );
	
	if(isset($email)){
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo __("Not a valid email!", "listings");
			die;
		}
		
		require_once("classes/mailchimp/MCAPI.class.php");
	
		global $lwp_options;
		
		$api_key = $lwp_options['mailchimp_api_key'];
		$api     = new MCAPI($api_key);
		$list    = $api->lists();
		$retval  = $api->listSubscribe( $_POST['list'], $email );
		
		if ($api->errorCode){
			if($api->errorCode == 214){
				echo __("Already subscribed.", "listings");	
			} else {
				echo __("Unable to load listSubscribe()!\n", "listings");
				echo "\t<!--Code=".$api->errorCode."-->\n";
				echo "\t<!--Msg=".$api->errorMessage."-->\n";
			}
		} else {
			echo __("Subscribed - look for the confirmation email!\n", "listings");
		}
	} else {
		echo __("Enter an email!", "listings");
	}
	
	die;
}

add_action('wp_ajax_add_mailchimp', 'add_mailchimp');
add_action('wp_ajax_nopriv_add_mailchimp', 'add_mailchimp');


if(!function_exists("get_page_by_slug")){
	function get_page_by_slug($page_slug, $output = OBJECT ) { 
	  	global $wpdb; 

	  	$post_type = 'listings';

   		$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $page_slug, $post_type ) ); 

	    if ( $page ) 
		    {return get_post($page, $output);}

	    return null;
	}
}

/* Post URLs to IDs function, supports custom post types - borrowed and modified from url_to_postid() in wp-includes/rewrite.php */
function auto_url_to_postid($url)
{
	global $wp_rewrite;

	$url = apply_filters('url_to_postid', $url);

	// First, check to see if there is a 'p=N' or 'page_id=N' to match against
	if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )	{
		$id = absint($values[2]);
		if ( $id )
			{return $id;}
	}

	// Check to see if we are using rewrite rules
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
	if ( empty($rewrite) )
		{return 0;}

	// Get rid of the #anchor
	$url_split = explode('#', $url);
	$url = $url_split[0];

	// Get rid of URL ?query=string
	$url_split = explode('?', $url);
	$url = $url_split[0];

	// Add 'www.' if it is absent and should be there
	if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
		{$url = str_replace('://', '://www.', $url);}

	// Strip 'www.' if it is present and shouldn't be
	if ( false === strpos(home_url(), '://www.') )
		{$url = str_replace('://www.', '://', $url);}

	// Strip 'index.php/' if we're not using path info permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		{$url = str_replace('index.php/', '', $url);}

	if ( false !== strpos($url, home_url()) ) {
		// Chop off http://domain.com
		$url = str_replace(home_url(), '', $url);
	} else {
		// Chop off /path/to/blog
		$home_path = parse_url(home_url());
		$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '' ;
		$url = str_replace($home_path, '', $url);
	}

	// Trim leading and lagging slashes
	$url = trim($url, '/');

	$request = $url;
	// Look for matches.
	$request_match = $request;
	foreach ( (array)$rewrite as $match => $query) {
		// If the requesting file is the anchor of the match, prepend it
		// to the path info.
		if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
			{$request_match = $url . '/' . $request;}

		if ( preg_match("!^$match!", $request_match, $matches) ) {
			// Got a match.
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			// Filter out non-public query vars
			global $wp;
			parse_str($query, $query_vars);
			$query = array();
			foreach ( (array) $query_vars as $key => $value ) {
				if ( in_array($key, $wp->public_query_vars) )
					{$query[$key] = $value;}
			}

		// Taken from class-wp.php
		foreach ( $GLOBALS['wp_post_types'] as $post_type => $t )
			{if ( $t->query_var )
				{$post_type_query_vars[$t->query_var] = $post_type;}}

		foreach ( $wp->public_query_vars as $wpvar ) {
			if ( isset( $wp->extra_query_vars[$wpvar] ) )
				{$query[$wpvar] = $wp->extra_query_vars[$wpvar];}
			elseif ( isset( $_POST[$wpvar] ) )
				{$query[$wpvar] = $_POST[$wpvar];}
			elseif ( isset( $_GET[$wpvar] ) )
				{$query[$wpvar] = $_GET[$wpvar];}
			elseif ( isset( $query_vars[$wpvar] ) )
				{$query[$wpvar] = $query_vars[$wpvar];}

			if ( !empty( $query[$wpvar] ) ) {
				if ( ! is_array( $query[$wpvar] ) ) {
					$query[$wpvar] = (string) $query[$wpvar];
				} else {
					foreach ( $query[$wpvar] as $vkey => $v ) {
						if ( !is_object( $v ) ) {
							$query[$wpvar][$vkey] = (string) $v;
						}
					}
				}

				if ( isset($post_type_query_vars[$wpvar] ) ) {
					$query['post_type'] = $post_type_query_vars[$wpvar];
					$query['name'] = $query[$wpvar];
				}
			}
		}

			// Do the query
			$query = new WP_Query($query);
			if ( !empty($query->posts) && $query->is_singular )
				{return $query->post->ID;}
			else
				{return 0;}
		}
	}
	return 0;
}

add_action('wpcf7_before_send_mail', 'wpcf7_update_email_body');

function wpcf7_update_email_body($contact_form) {
  $submission = WPCF7_Submission::get_instance();

  if ( $submission ) {
    $mail = $contact_form->prop('mail');    
    $additional_settings = $contact_form->prop('additional_settings');

    $data = $submission->get_posted_data();

    $listing_id     = $data['_listing_id'];
    $listing_object = get_post($listing_id);

    if(isset($listing_object) && !empty($listing_object)){
    	$listing_details  = "Listing URL: " . get_permalink($listing_object->ID);
    	$listing_details .= "\nListing Title: " . $listing_object->post_title;

	    $mail['body'] = str_replace("[_listing_details]", $listing_details, $mail['body']);

	    $additional_settings = 'on_sent_ok: "setTimeout(function(){ $.fancybox.close(); }, 2000);"';
	}

    $contact_form->set_properties(array('mail' => $mail, 'additional_settings' => $additional_settings));
  }
}


if(!function_exists("key_val_list_pluck")){
	function key_val_list_pluck($list, $key_field, $val_field){
		$return = array();
		if(!empty($list)){
			foreach($list as $key => $value){
				$return[$value->$key_field] = $value->$val_field;
			}
		}

		return $return;
	}
}

function inventory_auto_message(){
	global $Listing;

	echo $Listing->automotive_message(__("Some options have changed!", "listings"), __("We've moved some options around in the Listing Options. Firstly we created a new tab called <b>Single Listing Page</b> and moved the previous <b>Inventory Page</b> options under here as most of the options were for the single listing page. We've also relocated some of the options under <b>Automotive Settings</b> to either <b>Inventory Page</b> or <b>Single Listing Page</b> depending where they belong.", "listings"), "info", "listing_options_moved_options");
}
add_action("redux/listing_wp/panel/before", "inventory_auto_message");


function featured_vehicle_widget(){
	global $Listing;

	$get_listings = get_posts(
		array(
			"post_type"      => "listings",
			"posts_per_page" => -1,
			"meta_key"       => "car_featured",
			"meta_value"     => "1"
		)
	);
	?>
	<div id='featured_vehicles_widget'>
		<?php
		if(!empty($get_listings)){
			echo "<ul class='listings'>";
			foreach($get_listings as $listing){
				$post_meta       = $Listing->get_listing_meta($listing->ID);

				$gallery_images  = (isset($post_meta['gallery_images']) && !empty($post_meta['gallery_images']) ? $post_meta['gallery_images'] : "");
				$price           = (isset($post_meta['listing_options']['price']['value']) && !empty($post_meta['listing_options']['price']['value']) ? $post_meta['listing_options']['price']['value'] : "");

				if(isset($gallery_images) && !empty($gallery_images) && !empty($gallery_images[0])){
	                $thumbnail 		 = $Listing->auto_image($gallery_images[0], "auto_thumb", true);
	            } elseif(empty($gallery_images[0]) && isset($lwp_options['not_found_image']['url']) && !empty($lwp_options['not_found_image']['url'])){
	                $thumbnail 		 = $lwp_options['not_found_image']['url'];
	            } else {
	                $thumbnail 		 = LISTING_DIR . "images/pixel.gif";
	            }

				echo "<li><img src='" . $thumbnail . "' class='listing_thumb'> ";
				echo "<div class='listing_title'>" . $listing->post_title . "</div>";
				echo "<div class='listing_price'>" . $Listing->format_currency($price) . "</div>";

				echo "<a href='" . get_permalink($listing->ID) . "'><button class='listing_button'>" . __("Check it out", "listings") . "</button></a>";

				echo "</li>";
			}
			echo "</ul>";
		} else {
			_e("No Listings", "listings");
		}

		?>

		<div class="next" data-next-text="<?php _e("More Listings", "listings"); ?>"></div>
		<div class="hover_hint"><?php _e("Hover here for more", "listings"); ?></div>
	</div>
<?php
}

if(isset($lwp_options['featured_vehicle_widget']) && $lwp_options['featured_vehicle_widget'] == 1){
	add_action("wp_footer", "featured_vehicle_widget");
}

function selective_page_disable_canonical_redirect( $query ) {
    if( 'car-listings' == $query->query_vars['pagename'] ){
        remove_filter( 'template_redirect', 'redirect_canonical' );
        }
}

/*add_filter( 'parse_query', 'ba_admin_posts_filter' );
add_action( 'restrict_manage_posts', 'ba_admin_posts_filter_restrict_manage_posts' );

function ba_admin_posts_filter( $query )
{
    global $pagenow;
    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_NAME']) && $_GET['ADMIN_FILTER_FIELD_NAME'] != '') {
        $query->query_vars['meta_key'] = $_GET['ADMIN_FILTER_FIELD_NAME'];
    if (isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '')
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}

function ba_admin_posts_filter_restrict_manage_posts()
{
    global $wpdb;
    $sql = 'SELECT DISTINCT "make" FROM '.$wpdb->postmeta.' ORDER BY 1';
    $fields = $wpdb->get_results($sql, ARRAY_N);
?>
<select name="ADMIN_FILTER_FIELD_NAME">
<option value=""><?php _e('Filter By Custom Fields', 'baapf'); ?></option>
<?php
    $current = isset($_GET['ADMIN_FILTER_FIELD_NAME'])? $_GET['ADMIN_FILTER_FIELD_NAME']:'';
    $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? $_GET['ADMIN_FILTER_FIELD_VALUE']:'';
    foreach ($fields as $field) {
        if (substr($field[0],0,1) != "_"){
        printf
            (
                '<option value="%s"%s>%s</option>',
                $field[0],
                selected( $field[0] == $current, true, false ),
                $field[0]
            );
        }
    }
?>
</select> <?php _e('Value:', 'baapf'); ?><input type="TEXT" name="ADMIN_FILTER_FIELD_VALUE" value="<?php echo $current_v; ?>" />
<?php
}*/

function articlesmakesRequestAdmin($request) {
    if( isset($_GET['make']) && !empty($_GET['make']) ) {
        $request['meta_key'] = 'make';
        $request['meta_value'] = $_GET['make'];
    }
    return $request;
}

function articlesmakesRestrictManagePosts() {
    global $wpdb;
    $makes = $wpdb->get_col("
        SELECT DISTINCT meta_value
        FROM ". $wpdb->postmeta ."
        WHERE meta_key = 'make'
        ORDER BY meta_value
    ");
    $output = array_slice($makes, 2); 
    ?>
    <select name="make" id="make">
        <option value="">Make</option>
        <?php foreach ($output as $make) { ?>
        <option value="<?php echo esc_attr( $make ); ?>" <?php if(isset($_GET['make']) && !empty($_GET['make']) ) selected($_GET['make'], $make); ?>><?php echo esc_attr($make); ?></option>
        <?php } ?>
    </select>
    <?php
}

if( is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'listings' ) {
    add_filter('request', 'articlesmakesRequestAdmin');
    add_filter('restrict_manage_posts', 'articlesmakesRestrictManagePosts');
}