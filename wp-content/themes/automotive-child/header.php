<?php global $awp_options, $post;

$header_image = get_post_meta(get_current_id(), "header_image", true); 
$header_image = (!empty($header_image) ? wp_get_attachment_image_src($header_image, "full") : "");
$header_image = (!empty($header_image) ? $header_image[0] : "");  

$no_header    = get_post_meta(get_current_id(), "no_header", true); 

$handle = "";
// determine handle
if(is_search()){
    $handle = "search";
} elseif(is_tag()){
    $handle = "tag";
} elseif(is_category()){
    $handle = "category";
} elseif(is_404()){
    $handle = "fourohfour";
} elseif(function_exists("is_product_category") && is_product_category()){
    $handle = "woo_category";
} elseif(function_exists("is_product_tag") && is_product_tag()){
    $handle = "woo_tag";
} elseif(function_exists("is_shop") && is_shop()){
    $handle = "woo_shop";
} elseif((get_option('show_on_front') == "posts" && is_home())){
    $handle = "homepage_blog";
} ?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js"><head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <title><?php automotive_head_title(); ?></title>
        
        <?php if(!empty($awp_options['favicon']['url'])){ ?>
        <link href="<?php echo $awp_options['favicon']['url']; ?>" rel="shortcut icon">
        <?php } ?>
        <link rel="stylesheet" type="text/css" media="screen, print" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php automotive_meta_tags(); ?>

        <?php // facebook image tag
        if(get_post_type() == "listings"){
            $gallery_images = get_post_meta(get_current_id(), "gallery_images", true);

            if(isset($gallery_images[0]) && !empty($gallery_images[0])){
                $image = wp_get_attachment_image_src($gallery_images[0], 'thumb');
                echo '<meta property="og:image" content="' . $image[0] .  '" />';
            }
        } ?>

        <?php wp_head(); ?>
        <?php automotive_google_analytics_code("head"); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php
        if(isset($awp_options['body_layout']) && !empty($awp_options['body_layout']) && $awp_options['body_layout'] != 1){
            echo "<div class='boxed_layout" . ($awp_options['body_layout'] == 3 ? " margin" : "") . "'>";
        } ?>

        <!--Header Start-->
        <header class="header">
            <?php if(isset($awp_options['header_top']) && $awp_options['header_top'] == 1){ ?>
            <section class="toolbar">
                <!-- <div class="container"> -->
                    <div class="row">
                        <div class="col-lg-6 left_bar">
                            <p>Evo Commercials - CRM System</p>
                        </div>
                        <div class="col-lg-6 pull-right">
                                <p>Powered by - <a href="http://proton6.com/">Proton6 | Intelligent Solutions </a></p>
                        </div>
                    </div>
                <!-- </div> -->
                
                <?php if(isset($awp_options['toolbar_shadow']) && $awp_options['toolbar_shadow'] == 1){ ?>
                    <div class="toolbar_shadow"></div>
                <?php } ?>
            </section>
            <?php } ?>
            
            <?php global $lwp_options; ?>
            <div class="bottom-header" >
                <!-- <div class="container"> -->
                    <nav class="navbar navbar-default" role="navigation">
                        <!-- <div class="container-fluid">  -->
                            <!-- Brand and toggle get grouped for better mobile display -->
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                                <?php echo (isset($awp_options['logo_link']) && $awp_options['logo_link'] == 1 ? '<a class="navbar-brand" href="' . home_url() . '">' : '<a class="navbar-brand">'); ?>
                                    <div class="home-icon">    
                                        <span class="logo">
                                            <?php
                                            //wpml multiple logos
                                            if(isset($awp_options['wpml_language_logos']) && $awp_options['wpml_language_logos'] && defined("ICL_LANGUAGE_CODE")) { ?>
                                                <img src='<?php echo $awp_options['logo_image_'.ICL_LANGUAGE_CODE]['url']; ?>' class='main_logo' alt='logo'>
                                                <img src="<?php echo (!empty($lwp_options['pdf_logo']) ? $lwp_options['pdf_logo']['url'] : $awp_options['logo_image_'.ICL_LANGUAGE_CODE]['url']); ?>" class="pdf_print_logo">
                                            <?php } elseif(isset($awp_options['logo_image']['url']) && !empty($awp_options['logo_image']['url'])){ ?>
                                                <img src='<?php echo $awp_options['logo_image']['url']; ?>' class='main_logo' alt='logo'>
                                                <img src="<?php echo (!empty($lwp_options['pdf_logo']) ? $lwp_options['pdf_logo']['url'] : $awp_options['logo_image']['url']); ?>" class="pdf_print_logo">
                                            <?php } else { ?>
                                                <span class="primary_text"><?php echo (isset($awp_options['logo_text']) && !empty($awp_options['logo_text']) ? $awp_options['logo_text'] : ""); ?></span>
                                                <span class="secondary_text"><?php echo (isset($awp_options['logo_text_secondary']) && !empty($awp_options['logo_text_secondary']) ? $awp_options['logo_text_secondary'] : ""); ?></span>
                                            <?php } ?>
                                        </span>
                                    </div>  
                                <?php echo (isset($awp_options['logo_link']) && $awp_options['logo_link'] == 1 ? '</a>' : '</a>'); ?>
                            
                                <!-- Collect the nav links, forms, and other content for toggling -->
                                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">       
                                    <?php 
                                    // bootstrap 3 menu
                                    if( has_nav_menu( "header-menu" )) {                                                               
                                        wp_nav_menu( 
                                            array('theme_location' => 'header-menu',        
                                                  'fallback_cb'    => 'wp_bootstrap_navwalker::fallback',
                                                  'walker'         => new wp_bootstrap_navwalker(),
                                                  'menu_class'     => 'nav navbar-nav pull-right fullsize_menu'
                                                 )  
                                        );     

                                        // mobile menu                                                          
                                        wp_nav_menu( 
                                            array('theme_location' => 'mobile-menu',        
                                                  'fallback_cb'    => 'wp_bootstrap_navwalker_mobile::fallback',
                                                  'walker'         => new wp_bootstrap_navwalker_mobile(),
                                                  'menu_class'     => 'nav navbar-nav pull-right mobile_dropdown_menu'
                                                 )  
                                        );
                                    } else {
                                        echo "<ul class=\"nav navbar-nav pull-right\"><li class=\"active\"><a href=\"index.html\">" . __("Home", "automotive") . "</a></li></ul>";
                                    }  ?>
                                </div>
                                <!-- /.navbar-collapse --> 
                                <div class="logout-link">
                                <?php if (is_user_logged_in()) : ?>
                                        <a href="<?php echo wp_logout_url(get_permalink()); ?>">Logout</a>
                                <?php endif;?>
                                </div>
                            </div>
                       <!--  </div> -->
                        <!-- /.container-fluid --> 
                    </nav>
                </div>

                <?php if(isset($awp_options['header_shadow']) && $awp_options['header_shadow'] == 1){ ?>
                    <div class="header_shadow"></div>
                <?php } ?>
            </div>
        </header>
        <!--Header End-->

        <div class="clearfix"></div>
        
        <?php 
        // if slideshow on homepage
        $action         = (is_404() || (function_exists("is_shop") && is_shop() || is_search()) ? "" : get_post_meta(get_current_id(), "action_toggle", true));
        $page_slideshow = (is_404() || (function_exists("is_shop") && is_shop() || is_search()) ? "" : get_post_meta(get_current_id(), "page_slideshow", true));

        if(isset($page_slideshow) && !empty($page_slideshow) && $page_slideshow != "none" && function_exists("putRevSlider")){
            echo "<div class='header_rev_slider_container'>";
            putRevSlider($page_slideshow);
            echo "</div>";
        } else { 
            // if is search page
            if(is_search() || is_category() || is_tag() || is_404() || (function_exists("is_product_category") && is_product_category()) || function_exists("is_product_tag") && is_product_tag() || function_exists("is_shop") && is_shop() || (get_option('show_on_front') == "posts" && is_home())){
                $header_image = (isset($awp_options[$handle . '_page_image']) && !empty($awp_options[$handle . '_page_image']) ? $awp_options[$handle . '_page_image']['url'] : "");
            }
            
            // if no header image grab the default
            if(empty($header_image) && isset($awp_options['default_header_image']) && !empty($awp_options['default_header_image'])){
                $header_image = $awp_options['default_header_image']['url'];
            }
        } 
        
        if(isset($action) && $action != "on"){
            echo '<div class="message-shadow"></div>';
        }
        
        action_area($action);
        
        ?>
        
        <section class="content<?php echo (isset($no_header) && $no_header == "no_header" ? " push_down" : ""); ?>">
            
            <!-- <div class="container"> -->