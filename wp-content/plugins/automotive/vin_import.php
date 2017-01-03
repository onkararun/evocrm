<?php
function register_vin_menu_item() {
    add_submenu_page( 'edit.php?post_type=listings', __('VIN Import', 'listings'), __('VIN Import', 'listings'), 'manage_options', 'vin-import', 'automotive_vin_import' ); 
}
add_action('admin_menu', 'register_vin_menu_item');

function get_vin_info($vin){
    global $lwp_options;

    $api_key     = (isset($lwp_options['edmunds_api_key']) && !empty($lwp_options['edmunds_api_key']) ? $lwp_options['edmunds_api_key'] : "");
    $api_secret  = (isset($lwp_options['edmunds_api_secret']) && !empty($lwp_options['edmunds_api_secret']) ? $lwp_options['edmunds_api_secret'] : "");

    $response    = wp_remote_get("https://api.edmunds.com/api/vehicle/v2/vins/" . $vin . "?fmt=json&api_key=" . $api_key);
    $body_encode = $response['body'];
    
    return json_decode($body_encode);
}

function auto_object_to_array($obj) {
    if(is_object($obj)) $obj = (array) $obj;
    if(is_array($obj)) {
        $new = array();
        foreach($obj as $key => $val) {
            $new[$key] = auto_object_to_array($val);
        }
    }
    else $new = $obj;
    return $new;       
}

/* errors
/* * * * */
function admin_errors_vin(){
    $vin = (isset($_GET['vin']) && !empty($_GET['vin']) ? $_GET['vin'] : "");

    if(!empty($vin) && isset($_GET['error'])){
        $body = get_vin_info($vin);

        if(isset($body->errorType) && !empty($body->errorType)){
            echo "<div class='error'><span class='error_text'>";

            echo "Error: " . $body->message . "<br>";

            echo "</span></div>";
        }
    }
}
add_action( 'admin_notices', 'admin_errors_vin' );

function vin_test(){
    if(isset($_GET['vin']) && !empty($_GET['vin'])){    
        $vin  = (isset($_GET['vin']) && !empty($_GET['vin']) ? $_GET['vin'] : "");
        $body = get_vin_info($vin);

        if(isset($body->errorType) && !empty($body->errorType) && !isset($_GET['error'])){
            header("Location: " . add_query_arg("error", ""));
        }
    }
}
add_action( 'init', 'vin_test' );

function get_vin_value($location, $body){
    $search_array = array();
    $return_value = "";   

    if(strpos($location, "|") !== false){
        $search_array = explode("|", $location);
    } else {
        $search_array = array($location);
    }

    if(!empty($search_array)){
        $return_value = $body;

        foreach($search_array as $value){
            if(isset($return_value[$value]) && !empty($return_value[$value])){
                $return_value = $return_value[$value];
            } else {
                $return_value = "";
                break;
            }
        }
    }

    return $return_value;
}

function automotive_vin_import() { 
    global $lwp_options, $Listing;

    $vin  = (isset($_GET['vin']) && !empty($_GET['vin']) ? $_GET['vin'] : ""); 

    $body = get_vin_info($vin);
    $body = auto_object_to_array($body); ?>

    <div class="wrap auto_import">
        <h2 style="display: inline-block;"><?php _e("VIN Import", "listings"); ?></h2> 
        <?php if(!empty($vin) && !isset($_POST['import']) && !isset($_GET['error'])){ ?>
            <button class='button button-primary' onclick="jQuery('form[name=vin_import_form]').submit()" style="vertical-align: super;"><?php _e("Import Vehicle", "listings"); ?></button>
        <?php } ?>

        <br>

        <?php 

        if(isset($_POST['import']) && !empty($_POST['import'])){
            $post_title = "";

            $import = $_POST['import'];

            if(isset($import['title']) && !empty($import['title'])){
                foreach($import['title'] as $title){
                    $title = get_vin_value($title, $body);
                    $post_title .= sanitize_text_field($title) . " ";
                }
            }

            $post_content = "";
            if(isset($import['vehicle_overview']) && !empty($import['vehicle_overview'])){
                foreach($import['vehicle_overview'] as $overview){
                    $overview = get_vin_value($overview, $body);
                    $post_content .= sanitize_text_field($overview) . " ";
                }
            }

            $post_title   = trim($post_title);
            $post_content = trim($post_content);

            // insert post, get id
            $insert_info    = array(
                                'post_type'     => "listings",
                                'post_title'    => $post_title,
                                'post_content'  => $post_content,
                                'post_status'   => "publish"
                            );

            $dependancy_categories = array();

            $insert_id      = wp_insert_post( $insert_info );

            // if success :)
            if($insert_id){

                $listing_categories_safe = $listing_categories = $Listing->get_listing_categories(true);

                // add
                $listing_categories['Technical Specifications'] = array();
                $listing_categories['Other Comments']           = array();

                foreach($listing_categories as $key => $category){
                    $value      = "";
                    $safe_key   = (isset($category['slug']) && !empty($category['slug']) ? $category['slug'] : str_replace(" ", "_", strtolower($key)));//str_replace(" ", "_", strtolower($key));

                    if(isset($import[$safe_key]) && !empty($import[$safe_key]) && is_array($import[$safe_key])){
                        foreach ($import[$safe_key] as $key => $import_value) {
                            $value .= get_vin_value($import_value, $body) . " ";
                        }
                    } elseif(isset($import[$safe_key]) && !empty($import[$safe_key])) {
                        $value = get_vin_value($import[$safe_key], $body);
                    }

                    if(!empty($value)){
                        update_post_meta($insert_id, $safe_key, $value);
                        $dependancy_categories[$safe_key] = array($Listing->slugify($value) => $value);
                    }

                    $terms = (isset($listing_categories_safe[$key]['terms']) && !empty($listing_categories_safe[$key]['terms']) ? $listing_categories_safe[$key]['terms'] : array());
                    //compare_value
                    if(!in_array($value, $terms) && !empty($value) && isset($category['compare_value']) && $category['compare_value'] == "="){
                        $listing_categories_safe[$key]['terms'][$Listing->slugify($value)] = $value;
                    }
                }

                // gallery images
                $gallery_values = (isset($import['gallery_image']) && !empty($import['gallery_image']) ? $import['gallery_image'] : "");
                $gallery_images = array();

                if(!empty($gallery_values)){
                    foreach($gallery_values as $val){
                        $val = get_vin_value($val, $body);

                        if(filter_var($val, FILTER_VALIDATE_URL)){
                            $gallery_images[] = get_upload_image($val);
                        }
                    }
                }
                
                if(!empty($gallery_images)){
                    update_post_meta($insert_id, "gallery_images", $gallery_images);
                }

                // Features & Options
                $features_and_options = (isset($import['features_and_options']) && !empty($import['features_and_options']) ? $import['features_and_options'] : "");
                
                if(!empty($features_and_options)){
                    $options = $listing_categories_safe['options']['terms'];
                    $listing_feature_options = array();

                    foreach($features_and_options as $option){
                        $option = get_vin_value($option, $body);
                        $option = trim($option);
                        $option = preg_replace('/\x{EF}\x{BF}\x{BD}/u', '', @iconv(mb_detect_encoding($option), 'UTF-8', $option));

                        $listing_feature_options[$Listing->slugify($option)] = $option;

                        if(!in_array($option, $options)){
                            $listing_categories_safe['options']['terms'][$Listing->slugify($option)] = $option;
                        }
                    }

                    update_post_meta($insert_id, "multi_options", $listing_feature_options);
                }

                $video       = (isset($import['video']) && !empty($import['video']) ? get_vin_value($import['video'], $body) : "");
                $price       = (isset($import['price']) && !empty($import['price']) ? get_vin_value($import['price'], $body) : "");
                $city_mpg    = (isset($import['city_mpg']) && !empty($import['city_mpg']) ? get_vin_value($import['city_mpg'], $body) : "");
                $highway_mpg = (isset($import['highway_mpg']) && !empty($import['highway_mpg']) ? get_vin_value($import['highway_mpg'], $body) : "");

                // other categories
                $post_options = array(
                    "video" => $video,
                    "price" => array(
                        "text"  => (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")),
                        "value" => $price
                    ),
                    "city_mpg" => array(
                        "text"  => (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : __("City MPG", "listings")),
                        "value" => $city_mpg
                    ),
                    "highway_mpg" => array(
                        "text"  => (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : __("Highway MPG", "listings")),
                        "value" => $highway_mpg
                    )
                );
                
                update_post_meta($insert_id, "listing_options", serialize($post_options));

                // default history image
                if(isset($lwp_options['default_vehicle_history']['on']) && $lwp_options['default_vehicle_history']['on'] == "1"){
                    update_post_meta( $insert_id, "verified", "yes" );
                }

                update_option( $Listing->get_listing_categories_option_name(), $listing_categories_safe );

                // update car_sold
                update_post_meta( $insert_id, "car_sold", 2 );

                $Listing->update_dependancy_option($insert_id, $dependancy_categories);

                _e("Congratulations, you successfully imported this listing: ", "listings");
                echo "<a href='" . get_permalink($insert_id) . "'>" . (!empty($post_title) ? $post_title : __("Untitled", "listings")) . "</a>";
            } else {
                _e("Error importing your listing", "listings");
            }
        } else { ?>

            <?php if(!empty($vin) && !isset($_GET['error'])){
                    echo "<p>" . __("To import your listings simply drag and drop the left column items returned from the API into the listing category boxes on the right hand side then click the above \"Import Vehicle\" button. For more information please refer to our Automotive Plugin Documentation.", "listings") . "</p><br>";

                    echo '<ul id="items" class="form_value ui-sortable">';
                    // edmunds
                    if(!empty($body)){

                        $normal_options_display = array("make", "model", "engine", "transmission", "categories", "MPG", "price");
                        $single_options_display = array("drivenWheels", "numOfDoors", "manufacturer", "vin", "squishVin", "matchingType", "manufacturerCode");
                        $deep_options_display   = array("options", "colors", "years");

                        foreach($body as $key => $info){

                            if(!empty($info)){
                                echo "<h2>" . ucwords($key) . "</h2>\n";

                                // normal option display
                                if(in_array($key, $normal_options_display)){
                                    if(!empty($info)){
                                        foreach($info as $info_key => $info_value){

                                            if(is_array($info_value) && !empty($info_value)){
                                                foreach ($info_value as $info_deep_key => $info_deep_value) {
                                                    echo "<li class='ui-state-default'>" . $info_deep_key . ": " . $info_deep_value . " <input type='hidden' name='' value='" . $key . "|" . $info_key . "|" . $info_deep_key. "' /></li>\n";
                                                }
                                            } else {
                                                echo "<li class='ui-state-default'>" . $info_key . ": " . $info_value . " <input type='hidden' name='' value='" . $key . "|" . $info_key . "' /></li>\n";
                                            }
                                        }
                                    }
                                
                                // single option display
                                } elseif(in_array($key, $single_options_display)){
                                    echo "<li class='ui-state-default'>" . $key . ": " . $info . " <input type='hidden' name='' value='" . $key . "' /></li>\n";
                                
                                // deep options display
                                } elseif(in_array($key, $deep_options_display)){
                                    if(!empty($info)){
                                        foreach($info[0] as $info_key => $info_value){
                                            // if not array
                                            if(!is_array($info_value)){
                                                echo "<li class='ui-state-default'>" . $info_key . ": " . $info_value . " <input type='hidden' name='' value='" . $key . "|0|" . $info_key . "' /></li>\n";                                            
                                            
                                            // if is array
                                            } else {
                                                $i = 0;
                                                foreach($info_value as $deep_key => $deep_value){
                                                    if($key == "options" || $key == "colors"){
                                                        // D($deep_value);
                                                        if(!empty($deep_value)){
                                                            echo "<h5>" . ucwords($key) . " option set " . $i . "</h5>\n";

                                                            if(!empty($deep_value)){
                                                                foreach($deep_value as $deepest_key => $deepest_value){
                                                                    echo "<li class='ui-state-default'>" . $deepest_key . ": " . $deepest_value . " <input type='hidden' name='' value='" . $key . "|" . $deep_key . "|" . $info_key . "|" . $i . "|" . $deepest_key . "' /></li>\n";
                                                                }
                                                            }
                                                            
                                                            echo "<hr>";
                                                        }
                                                        // echo "<li class='ui-state-default'>" . $deep_key . ": " . $deep_value . " <input type='hidden' name='' value='" /*. $info*/ . "' /></li>";
                                                    } elseif($key == "years"){

                                                        // move submodels to the bottom
                                                        $submodels = $deep_value['submodel'];
                                                        unset($deep_value['submodel']);

                                                        $deep_value['submodel'] = $submodels;

                                                        echo "<h5>" . ucwords($key) . " submodel " . $i . "</h5>\n";

                                                        if(!empty($deep_value)){
                                                            foreach($deep_value as $deeper_key => $deeper_value){
                                                                echo (!is_array($deeper_value) && !is_object($deeper_value) ? "<li class='ui-state-default'>" . $deeper_key . ": " . $deeper_value . " <input type='hidden' name='' value='" . $key . "|" . $info_key . "|" . $i . "|" . $deeper_key . "' /></li>\n" : "");
                                                            }
                                                        }

                                                        if(!empty($deep_value['submodel'])){
                                                            foreach($deep_value['submodel'] as $submodel_key => $submodel_value){
                                                                echo "<li class='ui-state-default'>" . $submodel_key . ": " . $submodel_value . " <input type='hidden' name='' value='" . $key . "|" . $info_key . "|" . $i . "|submodel|" . $submodel_key . "' /></li>\n";
                                                            }
                                                        }
                                                        echo "<hr>\n";
                                                    } else {
                                                        //D($deep_value);
                                                    }

                                                    $i++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // recursive_get_all_values($body);
                    echo '</ul>';

                    $vin_import_var          = get_option("vin_import_associations");

                    if(!empty($vin_import_var)){
                        $vin_import_associations = (!empty($vin_import_var) ? $vin_import_var : "");
                        $vin_import_associations = $vin_import_associations['import'];
                    } else {
                        $vin_import_associations = array();
                    } ?>

                    <form method="post" action="" name="vin_import_form" id="vin_import_form">
                        <?php
                        $categories = $Listing->get_listing_categories();

                        foreach($categories as $key => $value){
                            $safe_name = $value['slug'];

                            echo "<fieldset class='category'>";
                            echo "<legend>" . $value['singular'] . "</legend>";

                            echo "<ul class='listing_category form_value' data-name='" . $safe_name . "' data-limit='1'>";

                            vin_import_list_item($safe_name, $vin_import_associations, $body);

                            echo "</ul>";

                            echo "</fieldset>";
                        }

                        // extra spots
                        $extra_spots = array(__("Title", "listings") => 0, __("Vehicle Overview", "listings") => 0, __("Technical Specifications", "listings") => 0, __("Other Comments", "listings") => 0, __("Gallery Images", "listings") => 0, __("Price", "listings") => 1, __("Original Price", "listings") => 1, __("City MPG", "listings") => 1, __("Highway MPG", "listings") => 1, __("Video", "listings") => 1,__("Features and Options", "listings") => 0);
                        foreach($extra_spots as $key => $option){ 
                            $safe_name = str_replace(" ", "_", strtolower($key)); ?>
                            
                            <fieldset class="category">
                                <legend><?php echo $key . ($option == 0 ? " <i class='fa fa-bars'></i>" : ""); ?></legend>

                                <ul class="listing_category form_value" data-limit="<?php echo $option; ?>" data-name="<?php echo $safe_name; ?>">
                                    <?php vin_import_list_item($safe_name, $vin_import_associations, $body, $option); ?>
                                </ul>
                            </fieldset>
                        <?php } ?>

                        <br><br>

                        * <i class="fa fa-bars"></i> <?php _e("Categories with this symbol can contain multiple values", "listings"); ?>

                        <br><br>

                        <button class="save_vin_import_categories button button-primary"><?php _e("Save the above associations", "listings"); ?></button>
                    </form>

                <?php } else { ?>

                    <?php                    
                    $api_key     = (isset($lwp_options['edmunds_api_key']) && !empty($lwp_options['edmunds_api_key']) ? $lwp_options['edmunds_api_key'] : "");
                    $api_secret  = (isset($lwp_options['edmunds_api_secret']) && !empty($lwp_options['edmunds_api_secret']) ? $lwp_options['edmunds_api_secret'] : "");

                    if(!empty($api_key) && !empty($api_secret)){ ?>

                        <div class="upload-plugin">
                            <form method="GET" class="wp-upload-form" action="" name="import_url">
                                <input type="hidden" name="post_type" value="listings">
                                <input type="hidden" name="page" value="vin-import">

                                <label class="screen-reader-text" for="pluginzip"><?php _e("Listing file", "listings"); ?></label>
                                <input type="text" name="vin" placeholder="<?php _e("VIN #", "listings"); ?>" style="width: 60%;">
                                <button onclick="jQuery(this).closest('form').submit()" class="button"><?php _e("Get vehicle details", "listings"); ?></button>                
                            </form>
                        </div>

                    <?php } else { ?>

                        <a href="<?php echo admin_url("admin.php?page=listing_wp&tab=8"); ?>"><?php _e("Please set both your edmunds API keys in the API Keys panel.", "listings"); ?></a>

                    <?php } ?>


                <?php } ?>

            <?php } ?>

    </div>

    <script>
    jQuery(document).ready( function($){
        var list_html;

        $( "#items, .listing_category" ).sortable({
            items: ':not(h2):not(h5):not(hr)',
            connectWith: ".form_value",
            placeholder: "ui-state-highlight",
            forcePlaceholderSize: false,
            create: function(e, ui){
                list_html = $("#items").html();
            },
            start: function(e, ui){
                ui.placeholder.height(ui.item.height());
            },
            receive: function(event, ui) {
                var $this = $(this);

                if ($this.data("limit") == 1 && $this.children('li').length > 1 && $this.attr('id') != "items") {
                    alert('<?php _e("Only one per list!", "listings"); ?>');
                    $(ui.sender).sortable('cancel');
                }

                // set val
                var name      = $this.data('name');

                var name_attr = ($this.data("limit") == 1 ? "import[" + name + "]" : "import[" + name + "][]");

                ui.item.find('input[type="hidden"]').attr("name", name_attr);
            },
            stop: function (event, ui){
                var $this = $(this);
                $("#items").html(list_html);
            }
        }).disableSelection();

        $(document).on("click", ".remove_element", function(){
            $(this).closest("li").remove();

            list_html = $("#items").html();
        });

        $("form[name='vin_import_form']").width(($(".auto_import").width() - $("#items").width()) + "px").show();

        $(window).resize( function(){
            $("form[name='vin_import_form']").width(($(".auto_import").width() - $("#items").width()) + "px").show();
        });

        $(document).on("click", ".save_vin_import_categories", function(e){

            e.preventDefault();

            jQuery.ajax({
                url: myAjax.ajaxurl,
                type: 'POST',
                data: { action: 'save_vin_import_categories', form: $("#vin_import_form").serialize() },
                success: function(response){
                    alert(response);
                }
            });
        });
    });
    </script>

    <style>
    fieldset.category {
        border: 1px solid #CCC;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    #items, ul.listing_category {
        width: 150px;
        min-height: 20px;
        list-style-type: none;
        margin: 0;
        padding: 5px 0 10px 0;
        float: left;
        margin-right: 10px;
    }

    #items {
        max-width: 30%;
        min-width: 300px;
    }

    .ui-state-highlight { margin-bottom: 0; }

    .ui-state-default .title {
        display: block;
        text-align: center;
        border-bottom: 1px solid #2D2D2D;
        margin: 0 4px;
        color: #2D2D2D;
        margin-bottom: 6px;
        font-size: 16px;
    }

    .ui-state-default .inside_value {
        padding: 4px;
    }

    .ui-state-default .remove_element {
        position: absolute;
        right: 2px;
        top: 2px;
        cursor: pointer;
    }

    .ui-state-default .remove_element:hover {
        color: #000;
    }

    #items li, ul.listing_category li {
        margin: 0 5px 5px 5px;
        padding: 5px;
        width: 125px;
        cursor: move;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        vertical-align: top;
    }

    #items li {
        display: inline-block;
    }

    .error_text {
        padding: 10px 0;
        display: block;
    }

    form[name='vin_import_form']{
        position: fixed;
        display: none;
        right: 0;
    }
    </style>
<?php } 

function display_vin_category_value($vin_ass, $safe_name, $body, $option){
    $value_exists  = true;
    $current_value = $body;
    $navigate      = explode("|", $vin_ass);

    foreach ($navigate as $nav_value) {
        if(isset($current_value[$nav_value])){
            $current_value = $current_value[$nav_value];
        } else {
            $value_exists = false;
            break;
        }
    }

    echo ($value_exists ? "<li class='ui-state-default'>" . end($navigate) . ": " . $current_value . " <input type='hidden' name='import[" . $safe_name . "]" . ($option == 0 ? "[]" : "") . "' value='" . $vin_ass . "' /></li>" : "");   
}

function vin_import_list_item($safe_name, $vin_import_associations, $body, $option = 1){
    // vin associations
    if(!empty($vin_import_associations) && isset($vin_import_associations[$safe_name]) && !empty($vin_import_associations[$safe_name])){
        $vin_ass = $vin_import_associations[$safe_name];

        if(is_array($vin_ass)){
            foreach ($vin_ass as $single_vin_ass) {
                display_vin_category_value($single_vin_ass, $safe_name, $body, $option);     
            }

        // if single level
        } elseif(strpos($vin_ass, "|") === false){
            echo "<li class='ui-state-default'>" . $vin_ass . ": " . $body[$vin_ass] . " <input type='hidden' name='import[" . $safe_name . "]" . ($option == 0 ? "[]" : "") . "' value='" . $vin_ass . "' /></li>";
        } else {
            display_vin_category_value($vin_ass, $safe_name, $body, $option);                                 
        }
    }
}

function save_vin_import_categories(){

    if(isset($_POST['form']) && !empty($_POST['form'])){
        parse_str($_POST['form'], $form);

        update_option("vin_import_associations", $form);

        echo "Saved";
    }

    die;
}
add_action("wp_ajax_save_vin_import_categories", "save_vin_import_categories");
add_action("wp_ajax_nopriv_save_vin_import_categories", "save_vin_import_categories"); ?>