<?php

///// register page
function register_csv_menu_item() {
    add_submenu_page( 'edit.php?post_type=listings', __('Automotive Listings Import', 'listings'), __('File Import', 'listings'), 'manage_options', 'file-import', 'automotive_file_import' ); 
}
add_action('admin_menu', 'register_csv_menu_item');


/* errors
/* * * * */
function admin_errors_auto(){
    $error = (isset($_GET['error']) && !empty($_GET['error']) ? sanitize_text_field($_GET['error']) : "");

    if(!empty($error) || (isset($_SESSION['auto_csv']['error']) && !empty($_SESSION['auto_csv']['error']))){
        echo "<div class='error'><span class='error_text'>";

        if(isset($_SESSION['auto_csv']['error']) && !empty($_SESSION['auto_csv']['error'])){
            $return = "";
            foreach($_SESSION['auto_csv']['error'] as $key => $message){
                $return .= $message[1] . "<br>";
            }

            echo rtrim($return, "<br>");

            unset($_SESSION['auto_csv']['error']);
        } elseif($error == "file"){
            _e("The file uploaded wasn't a valid XML or CSV file, please try again.", "listings");
        } elseif($error == "url"){
            _e("The URL submitted wasn't a valid URL, please try again.", "listings");
        } elseif($error == "int_file"){
            _e("The file must not contain numbers as the column title.", "listings");
        }

        echo "</span></div>";
    }
}
add_action( 'admin_notices', 'admin_errors_auto' );

function file_type_check_auto() {
    include_once(WP_PLUGIN_DIR . "/automotive/classes/class.import.php");
    $Auto_Import = new Auto_Import();

    if(isset($_POST['csv']) && !empty($_POST['csv'])){
        $rows = $_SESSION['auto_csv']['file_row'];
		$additional_options = array();

	    $additional_options['overwrite_existing_listing_images'] = (isset($_POST['overwrite_existing_listing_images']) && !empty($_POST['overwrite_existing_listing_images']) ? "true" : "false");

        $_SESSION['auto_csv']['import_results'] = $Auto_Import->insert_listings($rows, $additional_options);

        header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import' ));
        die;
    }

    if(isset($_POST['url_automotive']) || isset($_POST['import_submit_auto'])){
        if(isset($_POST['url_automotive'])) {
            $file_content = $Auto_Import->get_file_contents("url", $_POST['url_automotive']);
        } else {
            $file_content = $Auto_Import->get_file_contents("file", $_FILES['import_upload']);
        }

        if($file_content[0] == "success"){
            $file_type  = $Auto_Import->get_file_type($file_content[1]);

            $file_array = $Auto_Import->convert_file_content_to_array($file_type, $file_content[1]);

            // forward to import screen if passes
            if($file_type == "csv"){

                if($file_array[0] == "error"){
                    header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=file' ));
                } else {
                    header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&import_file' ));
                }
            } else {
                header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&xml' ));
            }


        }

    } elseif(isset($_SESSION['auto_csv']['file_row']) && !empty($_SESSION['auto_csv']['file_row']) && isset($_GET['xml']) && !empty($_GET['xml']) && !isset($_GET['import_file'])){
        $file_array = $Auto_Import->convert_file_content_to_array($_SESSION['auto_csv']['file_type'], $_SESSION['auto_csv']['file_content'], $_GET['xml']);

        if($file_array[0] == "error"){
            header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=file' ));
        } else {
            header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&xml=' . sanitize_text_field($_GET['xml']) . '&import_file' ));
        }
    }
}
add_action( 'init', 'file_type_check_auto' );

function automotive_file_import() {
	global $Listing;

	if($Listing->is_wpml_active()){
		$listing_categories = $Listing->get_listing_categories(false, ICL_LANGUAGE_CODE);
	} else {
		$listing_categories = $Listing->get_listing_categories();
	}

    include_once(WP_PLUGIN_DIR . "/automotive/classes/class.import.php");
    $Auto_Import = new Auto_Import();    ?>
    <div class="wrap auto_import">
        <h2 style="display: inline-block;"><?php _e("File Import", "listings"); ?></h2> 

        <?php if(isset($_GET['import_file'])){ ?>
            <button class="submit_csv button button-primary" style="vertical-align: super"><?php _e("Import Listings", "listings"); ?></button>
        <?php } ?>

        <br>

        <?php
        if(isset($_SESSION['auto_csv']['import_results']) && !empty($_SESSION['auto_csv']['import_results'])){
            echo "<br>";
            echo $_SESSION['auto_csv']['import_results'];

            unset($_SESSION['auto_csv']);
        } elseif(isset($_GET['xml']) && empty($_GET['xml'])){

            $xml_options = $Auto_Import->multiarray_keys($_SESSION['auto_csv']['file_row']); ?>
            
            <div class="upload-plugin">

                <p class="install-help"><?php _e("Choose which XML node contains each listings information.", "listings"); ?></p>

                <form method="get" class="wp-upload-form" action="">
                    <input type="hidden" name="post_type" value="listings">
                    <input type="hidden" name="page" value="file-import">

                    <select name='xml'>
                    <?php
					$default_xml_option = get_option("file_import_xml_key");

                    if(!empty($xml_options)) {
	                    foreach ( $xml_options as $option ) {
		                    echo "<option value='" . $option . "'" . selected( $option, $default_xml_option, false ) . ">" . $option . "</option>";
	                    }
                    } ?>
                    </select>

                    <button onclick="jQuery(this).closest('form').submit()" class="button"><?php _e("Import Now", "listings"); ?></button>  
                </form>

            </div>

        <?php } elseif(isset($_GET['import_file'])){ ?>

            <?php if(isset($rows) && count($rows) > 100){ ?>
            <div class="error">
                <span class="error_text"><?php _e("Please consider breaking the import file into multiple files, large imports may not import fully depending on your server's settings.", "listings"); ?></span>
            </div>
            <?php } ?>

            <p><?php _e("To import your listings simply drag and drop the left column items from your import file into the listing category boxes on the right hand side then click the above \"Import Listings\" button. For more information please refer to our Automotive Plugin Documentation.", "listings"); ?></p>

            <br>

            <?php
            $assoc_val              = get_option($Listing->is_wpml_active() ? "file_import_associations_" . ICL_LANGUAGE_CODE : "file_import_associations");
            $associations           = ($assoc_val ? $assoc_val : array());
            $duplicate_check_val    = (isset($associations['duplicate_check']) && !empty($associations['duplicate_check']) ? $associations['duplicate_check'] : "");
            $overwrite_existing_val = (isset($associations['overwrite_existing']) && !empty($associations['overwrite_existing']) ? $associations['overwrite_existing'] : "");

	        $overwrite_existing_listing_images = (isset($associations['overwrite_existing_listing_images']) && !empty($associations['overwrite_existing_listing_images']) ? $associations['overwrite_existing_listing_images'] : "" ); ?>

            <ul id="csv_items" class="connectedSortable">
                <?php
                if(isset($_SESSION['auto_csv']['file_type']) && $_SESSION['auto_csv']['file_type'] == "xml"){
                    $rows   = $_SESSION['auto_csv']['file_row'];

	                // single row fix
	                if(!isset($rows[0])){
		                $temp_rows = $rows;
		                $rows = array();

		                $rows[0] = $temp_rows;
	                }

	                $title_sort = $Auto_Import->recursive_all_keys( $rows );
	                $titles     = $Auto_Import->recursive_sortable( $rows, $title_sort, $associations, array() );
                } else {
                    if(isset($_SESSION['auto_csv']['titles']) && !empty($_SESSION['auto_csv']['titles'])){
                        $titles = $_SESSION['auto_csv']['titles'];
                    }
                }

                if(!empty($titles)){
                    foreach($titles as $key => $value){
                        $key = (is_int($key) ? $value : $key); // xml/csv name detection
                        echo (!isset($associations['csv'][$key]) ? "<li class='ui-state-default'><input type='hidden' name='csv[" . $key .  "]' > " . $value . "</li>" : "");
                    }
                }?>
            </ul>

            <form method="post" id="csv_import" data-one-per="<?php _e("Only one per list!", "listings"); ?>">
                <?php foreach($listing_categories as $key => $option) {
	                $needle         = $option['slug'];
	                $is_association = ( isset( $associations['csv'] ) && is_array( $associations['csv'] ) && array_search( $needle, $associations['csv'] ) ? true : false );

	                if(!isset($option['link_value']) || (isset($option['link_value']) && $option['link_value'] == "none")){ ?>
		                <fieldset class="category">
			                <legend><?php echo $option['singular']; ?></legend>

			                <ul class="listing_category connectedSortable" data-limit="1"
			                    data-name="<?php echo $key; ?>">
				                <?php if ( $is_association ) {
					                $values = array_keys( $associations['csv'], $needle );

					                if ( ( isset( $rows[0][ $values[0] ] ) || array_search( $values[0], $titles ) !== false || isset($titles[$values[0]]) ) ) {
						                $label = (isset($_SESSION['auto_csv']['file_type']) && $_SESSION['auto_csv']['file_type'] == "xml" && isset($titles[$values[0]]) ? $titles[$values[0]] : $values[0]);

						                echo '<li class="ui-state-default ui-sortable-handle"><input type="hidden" name="csv[' . $values[0] . ']" value="' . $needle . '"> ' . $label . '</li>';
					                }
				                } ?>
			                </ul>
		                </fieldset>
	                <?php }
                }

                // extra spots
                $extra_spots = array(
	                "vehicle_overview"          => array(__("Vehicle Overview", "listings"), 0),
	                "technical_specifications"  => array(__("Technical Specifications", "listings"), 0),
	                "other_comments"            => array(__("Other Comments", "listings"), 0),
	                "gallery_images"            => array(__("Gallery Images", "listings"), 0),
	                "price"                     => array(__("Price", "listings"), 1),
	                "original_price"            => array(__("Original Price", "listings"), 1),
	                "city_mpg"                  => array(__("City MPG", "listings"), 1),
	                "highway_mpg"               => array(__("Highway MPG", "listings"), 1),
	                "video"                     => array(__("Video", "listings"), 1),
	                "features_and_options"      => array(__("Features and Options", "listings"), 0),
	                "secondary_title"           => array(__("Secondary Title", "listings"), 1),
	                "latitude"                  => array(__("Latitude", "listings"), 1),
	                "longitude"                 => array(__("Longitude", "listings"), 1)
                );

                foreach($extra_spots as $key => $option){ 
                    $needle         = $key;
                    $is_association = (isset($associations['csv']) && is_array($associations['csv']) && array_search($needle, $associations['csv']) ? true : false); ?>
                    <fieldset class="category">
                        <legend><?php echo $option[0] . ($option[1] == 0 ? " <i class='fa fa-bars'></i>" : ""); ?></legend>

                        <ul class="listing_category connectedSortable" data-limit="<?php echo $option[1]; ?>" data-name="<?php echo str_replace(" ", "_", strtolower($key)); ?>">
                            <?php if($is_association){
                                $safe_val = $needle;
                                $values   = array_keys($associations['csv'], $safe_val);

                                foreach($values as $val_key => $val_val){
                                    if( (isset($rows[0][$val_val])  || array_search($val_val, $titles) !== false  || isset($titles[$val_val])) ){
	                                    $label = (isset($_SESSION['auto_csv']['file_type']) && $_SESSION['auto_csv']['file_type'] == "xml" && isset($titles[$val_val]) ? $titles[$val_val] : $val_val);

                                        echo '<li class="ui-state-default ui-sortable-handle"><input type="hidden" name="csv[' . $val_val . ']" value="' . $safe_val . '"> ' . $label . '</li>';
                                    }
                                }
                            } ?>
                        </ul>
                    </fieldset>
                <?php } ?>

                <br><br>
                
                <?php _e("Title Values", "listings"); ?>:
                <select class="multiselect" multiple="multiple" name="title_from_values[]">
                <?php
                if(isset($_SESSION['auto_csv']['file_type']) && $_SESSION['auto_csv']['file_type'] == "xml"){
                    $rows   = $_SESSION['auto_csv']['file_row'];

	                // single row fix
	                if(!isset($rows[0])){
		                $temp_rows = $rows;
		                $rows = array();

		                $rows[0] = $temp_rows;
	                }

                    $titles = $Auto_Import->recursive_sortable($rows[0], $rows[0], $associations);
                } else {
                    if(isset($_SESSION['auto_csv']['titles']) && !empty($_SESSION['auto_csv']['titles'])){
                        $titles = $_SESSION['auto_csv']['titles'];
                    }
                }

                if(!empty($titles)){
                    if(!isset($associations['title_from_values']) || empty($associations['title_from_values'])){
                        $associations['title_from_values'] = array();
                    }

                    foreach($titles as $key => $value){
	                    $option_value = (isset($_SESSION['auto_csv']['file_type']) && $_SESSION['auto_csv']['file_type'] == "xml" ? $key : $value);

                        echo "<option value='" . $option_value .  "'" . (in_array($option_value, $associations['title_from_values']) ? " selected='selected'" : "") . "> " . $value . "</option>\n";
                    }
                }
                ?>
                </select>

                <p style="font-style: italic;"><?php _e("Generate the listing title from multiple values in the import file", "listings"); ?>.</p>

                <br>

                <?php _e("Check for duplicate listings using", "listings"); ?>: 
                <select name="duplicate_check">
                    <option value="none"><?php _e("None", "listings"); ?></option>
                    <option value="title" <?php selected( "title", $duplicate_check_val, true ) ?>><?php _e("Title", "listings"); ?></option>
                    <?php
                    foreach($listing_categories as $key => $option){
                        $val = $option['slug'];
                        echo "<option value='" . $val . "' " . selected( $val, $duplicate_check_val, false ) . ">" . $option['singular'] . "</option>";
                    } ?>
                </select>&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="overwrite_existing" value="on" <?php echo (!empty($overwrite_existing_val) ? "checked='checked'" : ""); ?>> <?php _e("Overwrite duplicate listings with new data", "listings"); ?>

	            <br><br>

	            <div class="overwrite_existing_listing_images" style="display: <?php echo ((isset($duplicate_check_val) && $duplicate_check_val != "none") ? "block" : "none"); ?>;">
					<label><input type="checkbox" name="overwrite_existing_listing_images" value="on" <?php echo ((isset($overwrite_existing_listing_images) && $overwrite_existing_listing_images == "on") ? "checked='checked'" : ""); ?>> <?php _e("Overwrite images on existing listings", "listings"); ?></label>

		            <br><br>
	            </div>

                * <i class="fa fa-bars"></i> <?php _e("Categories with this symbol can contain multiple values", "listings"); ?>

                <br><br>

                <button class="save_import_categories button button-primary"><?php _e("Save the above associations", "listings"); ?></button>

            </form>
        <?php } else { ?>
            <div class="upload-plugin">
                <p class="install-help"><?php _e("If you have a listing data in a .csv or .xml file format, you may import it by uploading it here.", "listings"); ?></p>

                <form method="post" enctype="multipart/form-data" class="wp-upload-form" action="<?php echo remove_query_arg( "error" ); ?>" name="import_upload">
                    <input type="hidden" name="post_type" value="listings">
                    <input type="hidden" name="page" value="file-import">
                    <input type="hidden" name="file" value="uploaded">

                    <label class="screen-reader-text" for="import_upload"><?php _e("Listing file", "listings"); ?></label>
                    <input type="file" id="import_upload" name="import_upload">
                    <input type="submit" name="import_submit_auto" id="install-plugin-submit" class="button" value="<?php _e("Import Now", "listings"); ?>" disabled="">
                </form>
            </div>


            <div class="upload-plugin">
                <p class="install-help"><?php _e("If you have a link to a .csv or .xml listing file, you may import it by pasting the URL it here.", "listings"); ?></p>

                <form method="post" class="wp-upload-form" action="<?php echo remove_query_arg( "error" ); ?>" name="import_url">
                    <input type="hidden" name="post_type" value="listings">
                    <input type="hidden" name="page" value="file-import">
                    <input type="hidden" name="file" value="uploaded">

                    <label class="screen-reader-text" for="pluginzip"><?php _e("Listing file", "listings"); ?></label>
                    <input type="text" name="url_automotive" placeholder="<?php _e("URL to xml or csv", "listings"); ?>" style="width: 70%;">
                    <button onclick="jQuery(this).closest('form').submit()" class="button"><?php _e("Import Now", "listings"); ?></button>
                </form>
            </div>
        <?php } ?>

    </div>
<?php }

function save_import_categories(){
    if(isset($_POST['form']) && !empty($_POST['form'])){
	    global $Listing;

        parse_str($_POST['form'], $form);

	    $option = ($Listing->is_wpml_active() ? "file_import_associations_" . ICL_LANGUAGE_CODE : "file_import_associations");

        update_option($option, $form);

        echo "Saved";
    }

    die;
}
add_action("wp_ajax_save_import_categories", "save_import_categories");
add_action("wp_ajax_nopriv_save_import_categories", "save_import_categories");

function automotive_import_scripts() {
    wp_enqueue_script( 'jquery-ui' );
    wp_enqueue_script( 'jquery-ui-sortable' );
}

add_action( 'wp_enqueue_scripts', 'automotive_import_scripts' ); ?>