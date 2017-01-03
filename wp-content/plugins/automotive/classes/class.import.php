<?php


if(!class_exists("Auto_Import")){
    class Auto_Import {

        /**
         * Gets the file content for import
         *
         * @param $type
         * @param $file
         *
         * @return array
         */
        public function get_file_contents($type, $file){
            $return_messages = array();

            if($type == "url"){
                // if valid URL
                if(filter_var($file, FILTER_VALIDATE_URL)){

                    // wp_remote_get
                    $wp_get = wp_remote_get( $file );

                    // test for error
                    if(!is_wp_error($wp_get)){

                        // generate temp file to process
                        $file_content = $wp_get['body'];

                        $return_messages = array("success", $file_content);

                        $_SESSION['auto_csv']['file_content'] = $file_content;
                    } else {
                       $return_messages[] = array("error", $wp_get->get_error_message());
                    }
                } else {
                    $return_messages[] = array("error", __("Not a valid URL", "listings"));
                }

            // if uploaded file
            } elseif($type == "file") {
                $empty_file_string = __("There was no file uploaded", "listings");

                if(empty($file)){
                    $return_messages[] = array("error", $empty_file_string);
                }

                // check file for upload error codes
                switch($file['error']){
                    case 0:
                        $file_content = file_get_contents($file['tmp_name']);

                        $return_messages = array("success", $file_content);
                        $_SESSION['auto_csv']['file_content'] = $file_content;
                        break;
                    case 1:
                        $return_messages[] = array("error", __("Your file exceeded your servers maximum upload size", "listings"));
                        break;
                    case 2:
                        $return_messages[] = array("error", __("Your file exceeded the form maximum upload size", "listings"));
                        break;
                    case 3:
                        $return_messages[] = array("error", __("The file was only partially uploaded", "listings"));
                        break;
                    case 4:
                        $return_messages[] = array("error", $empty_file_string);
                        break;
                    case 6:
                        $return_messages[] = array("error", __("Your server is missing a temporary folder", "listings"));
                        break;
                    case 7:
                        $return_messages[] = array("error", __("Your server cannot write the file to the disk", "listings"));
                        break;
                    case 8:
                        $return_messages[] = array("error", __("A PHP extension installed on your server has stopped the upload", "listings"));
                        break;
                }
            }

            // if not success then add error to sesh
            if($return_messages[0] != "success"){
                $_SESSION['auto_csv']['error'] = $return_messages;
            }

            return $return_messages;

        }


        /**
         * Get file type based on content, if not valid XML then its assumed CSV
         *
         * @param $file_content
         * @return string
         */
        public function get_file_type($file_content){
            // test if XML, if not assume it is CSV
            libxml_use_internal_errors(true);

            $doc = simplexml_load_string($file_content);

            if($doc) {
                $file_type = "xml";
            } else {
                $file_type = "csv";
            }

            $_SESSION['auto_csv']['file_type'] = $file_type;

            return $file_type;
        }


        /**
         * Validates the array doesn't contain any integer column names which can derp the import process
         *
         * @param $array
         * @return bool
         */
        private function validate_array($array){
            $valid_array = true;

            if(!empty($array)){
                foreach($array[0] as $key => $value){
                    if(is_int($key)){
                        $valid_array = false;
                        break;
                    }
                }
            } else {
                $valid_array = false;
            }

            return $valid_array;
        }

        /**
         * @param array $array
         * @return array
         */
        public function array_keys_multi(array $array) {
            $keys = array();

            foreach ($array as $key => $value) {
                $keys[] = $key;

                if (is_array($array[$key])) {
                    $keys = array_merge($keys, $this->array_keys_multi($array[$key]));
                }
            }

            return $keys;
        }

        /**
         * Converts XML/CSV file content into a usable array
         *
         * @param $file_type
         * @param $file_content
         * @param string $xml_parent
         * @return array
         */
        public function convert_file_content_to_array($file_type, $file_content, $xml_parent = ""){
            $return = array();

            if($file_type == "csv"){
                // load CSV parser
                if(!class_exists("parseCSV")){
                    include(LISTING_HOME . "/classes/" . "parsecsv.lib.php");
                }

                $csv  = new parseCSV($file_content);
                // $csv->delimiter = apply_filters("file_import_delimiter", ",");

                $rows   = array_values($this->array_remove_empty($csv->data));
                $titles = $csv->titles;

                if($this->validate_array($rows)) {
                    $_SESSION['auto_csv']['file_row'] = $rows;
                    $_SESSION['auto_csv']['titles']   = $titles;

                    $return = array($rows, $titles);
                } else {
                    $return = array("error", __("The file must not contain numbers as the column title", "listings"));
                }

            } elseif($file_type == "xml"){
                $xml    = simplexml_load_string($file_content);
                $json   = json_encode($xml);
                $rows   = json_decode($json, TRUE);

                // $xml_parent

                $_SESSION['auto_csv']['file_row'] = $rows;

                $return = array($rows);

                if(!empty($xml_parent)) {
                    update_option("file_import_xml_key", $xml_parent);
	                if(isset($rows[$xml_parent][0]) && !empty($rows[$xml_parent][0])) {
		                $titles = $this->array_keys_multi( $rows[ $xml_parent ][0] );
	                } else {
		                $titles = $this->array_keys_multi( $rows[ $xml_parent ] ); // single xml row
	                }

                    $_SESSION['auto_csv']['file_row'] = $rows[$xml_parent];
                    $_SESSION['auto_csv']['titles']   = $titles;

                    $return[] = $titles;
                }
            }

            return $return;
        }

        /**
         * @param $subject
         * @param $array
         * @return array|null
         */
        public function key_get_parents($subject, $array, $exact_val = ""){

            if(!empty($array)) {
                foreach ($array as $key => $value) {

                    if (is_array($value)) {

                        if (in_array($subject, array_keys($value), true) && (isset($exact_val) && in_array($exact_val, array_values($value)))) {
                            return array($key);
                        } else {
                            $chain = $this->key_get_parents($subject, $value, $exact_val);

                            if (!is_null($chain)) {
                                return array_merge(array($key), $chain);
                            }
                        }
                    }
                }
            }

            return null;
        }

        public function multiarray_keys($ar) {

            foreach($ar as $k => $v) {
                if(is_array($v)){
                    $keys[] = $k;
                }

                if (is_array($ar[$k]) && is_array($this->multiarray_keys($ar[$k]))) {
                    $keys = array_merge($keys, $this->multiarray_keys($ar[$k]));
                }
            }
            return (isset($keys) && !empty($keys) ? $keys : "");
        }

        public function auto_add_http($url) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }
            return $url;
        }

        public function get_upload_image($image_url){
            $image = $image_use = str_replace("\"", "", $image_url);
            $get   = wp_remote_get( $image );
            $type  = wp_remote_retrieve_header( $get, 'content-type' );

            $allowed_images = array("image/jpg", "image/jpeg", "image/png", "image/gif");
            $extension = pathinfo($image, PATHINFO_EXTENSION);

            // try to determine type if not set
            if(empty($type)){
                if($extension == "jpg" || $extension == "jpeg"){
                    $type = "image/jpg";
                } elseif($extension == "png"){
                    $type == "image/png";
                } elseif($extension == "gif"){
                    $type = "image/gif";
                }
            }

            if (!$type && in_array($type, $allowed_images)){
                return false;
            }

            if(empty($extension)){
                $content_type = $type;

                // check if content type is even set...
                if(strstr($content_type, "image/jpg") || strstr($content_type, "image/jpeg")){
                    $image_use = $image . ".jpg";
	                $type      = "image/jpg";
                } elseif(strstr($content_type, "image/png")){
                    $image_use = $image . ".png";
	                $type      = "image/png";
                } elseif(strstr($content_type, "image/gif")){
                    $image_use = $image . ".gif";
	                $type      = "image/gif";
                }
            }

            $mirror = wp_upload_bits(  basename( $image_use ), '', wp_remote_retrieve_body( $get ) );

            $attachment = array(
                'post_title'=> basename( $image ),
                'post_mime_type' => $type
            );

            if(isset($mirror) && !empty($mirror) ){
                $attach_id = wp_insert_attachment( $attachment, $mirror['file'] );

                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );

                wp_update_attachment_metadata( $attach_id, $attach_data );
            } else {
                $attach_id = "";
            }

            return $attach_id;
        }

        public function search_array_keys($array, $term, $references, $title_val = ""){
            $count  = array_count_values($references);
            $return = "";

            // if variable has more than a single value
            if(isset($count[$term]) && $count[$term] >= 2){
                $keys = array_keys($references, $term);

                foreach($keys as $key){
                    if(strpos($key, "|") !== false){
                        $paths = explode("|", $key);
                        $items = $array;

                        foreach($paths as $ndx){
                            $items = $items[$ndx];
                        }

                        $return .= $items . "<br>";
                    } else {
                        $return .= (isset($array[$key]) && !empty($array[$key]) ? $array[$key] : "") . "<br>";
                    }
                }
            } else {
                $key = array_search($term, $references);

                if(strpos($key, "|") !== false){
                    $paths = explode("|", $key);
                    $items = $array;

                    foreach($paths as $ndx){
                        $items = $items[$ndx];
                    }

                    $return .= $items;
                } else {
                    if(!empty($title_val)){
	                    if(strpos($term, "|") !== false) {
		                    $paths = explode( "|", $term );
		                    $items = $array;

		                    foreach ( $paths as $ndx ) {
			                    $items = $items[ $ndx ];
		                    }

		                    $value = (isset($items) && !empty($items) ? $items : "");
	                    } else {
		                    $value = (isset($array[$term]) && !empty($array[$term]) ? $array[$term] : "");
	                    }

                        $return .= $value;
                    } else {
                        $return .= (isset($array[array_search($term, $references)]) && !empty($array[array_search($term, $references)]) ? $array[array_search($term, $references)] : "");
                    }
                }
            }

            return $return;
        }

        function array_remove_empty($haystack) {
            foreach ($haystack as $key => $value) {
                if (is_array($value)) {
                    $haystack[$key] = $this->array_remove_empty($haystack[$key]);
                }

                if (empty($haystack[$key])) {
                    unset($haystack[$key]);
                }
            }

            return $haystack;
        }

        public function recursive_sortable($loop, $rows, $associations, $return = array()){
            foreach($loop as $key => $row){
                if(!is_array($row)){
                    $parents = $this->key_get_parents($key, $rows, $row);
                    $label   = (is_null($parents) ? $key : end($parents) . " " . $key);

                    //echo "<li class='ui-state-default'><input type='hidden' name='csv[" . (is_null($parents) ? $key : implode("|", $parents) . "|" . $key ) . "]' > " . $label . "</li>";
                    $return[(is_null($parents) ? $key : implode("|", $parents) . "|" . $key )] = $label;
                } else {
                    $return = $this->recursive_sortable($row, $rows, $associations, $return);
                }

            }

            return $return;
        }

	    public function title_recursive_sortable($loop, $all_keys = array(), $original_loop = array()){
		    /*$main = $loop[0];

		    unset($loop[0]);

		    if(!empty($loop)){
			    foreach($loop as $key => $loop_element){
				    if(is_array($loop_element)){
					    $main = array_merge( $main, $this->title_merge($loop_element) );
				    } else {
					    $main = array_merge( $main, $loop_element );
				    }
			    }
		    }*/

		    if(empty($original_loop)){
			    $original_loop = $loop;
		    }

		    foreach($loop as $key => $loop_element){
			    if(!is_array($loop_element)){
				    $parents = $this->key_get_parents($key, $original_loop, $loop_element);
				    $label   = (is_null($parents) ? $key : end($parents) . " " . $key);

				    $all_keys[(is_null($parents) ? $key : implode("|", $parents) . "|" . $key )] = $label;
			    } else {
					$all_keys = $this->title_recursive_sortable($loop_element, $all_keys, $loop);
			    }
		    }

		    return $all_keys;
	    }

	    public function recursive_all_keys($input){
			$main = array();

		    foreach($input as $key => $row){
			    $main = $this->merge_arrays($row, $main);
		    }

		    return $main;
	    }

	    public function merge_arrays ( array &$array1, array &$array2 )
	    {
		    $merged = $array1;

		    foreach ( $array2 as $key => &$value )
		    {
			    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
			    {
				    $merged [$key] = $this->merge_arrays( $merged [$key], $value );
			    }
			    else
			    {
				    $merged [$key] = $value;
			    }
		    }

		    return $merged;
	    }


	    public function has_arrays($element){
		    $return = false;

		    if(isset($element) && is_array($element) && !empty($elemet)){
			    foreach($element as $element_row){
				    if(is_array($element_row)){
					    $return = true;
					    break;
				    }
			    }
		    }

		    return $return;
	    }


	    public function is_wpml_active(){
		    return (defined("ICL_LANGUAGE_CODE") ? true : false);
	    }

	    public function encode_fix($string){
		    return $string;
	    }

        public function insert_listings($rows, $additional_options = array()){
            global $Listing, $lwp_options;

            // additional options
            $output = (isset($additional_options['output']) && !empty($additional_options['output']) ? $additional_options['output'] : "");
	        $overwrite_listing_images  = (isset($additional_options['overwrite_existing_listing_images']) && !empty($additional_options['overwrite_existing_listing_images']) ? $additional_options['overwrite_existing_listing_images'] : "");
	        $remove_listings_not_found = (isset($additional_options['remove_listings_not_found']) && !empty($additional_options['remove_listings_not_found']) ? $additional_options['remove_listings_not_found'] : "");

            // if form submitted
            if(isset($_POST['csv']) && !empty($_POST['csv'])){

                $csv                     = (isset($_POST['csv']) && !empty($_POST['csv']) ? $_POST['csv'] : "");
                $duplicate_check         = (isset($_POST['duplicate_check']) && !empty($_POST['duplicate_check']) ? $_POST['duplicate_check'] : "");

	            if($Listing->is_wpml_active()){
		            $listing_categories_safe = $listing_categories = $Listing->get_listing_categories(true, ICL_LANGUAGE_CODE);
	            } else {
		            $listing_categories_safe = $listing_categories = $Listing->get_listing_categories(true);
	            }

	            /* Default Latitude & Longitude */
	            $default_latitude  = (isset($lwp_options['default_value_lat']) && !empty($lwp_options['default_value_lat']) ? $lwp_options['default_value_lat'] : "43.653226");
	            $default_longitude = (isset($lwp_options['default_value_long']) && !empty($lwp_options['default_value_long']) ? $lwp_options['default_value_long'] : "-79.3831843");

                $imported_listings       = array();

                if(!empty($csv)){
                    // duplicate check outside listings
                    $current_listings = get_posts( array( "post_type" => "listings", "posts_per_page" => -1 ) );

	                $all_listings  = array();
                    $current_check = array();
                    $i = 0;

                    foreach($current_listings as $listing){
                        $post_meta          = get_metadata("post", $listing->ID);
						$post_meta['title'] = $listing->post_title;

                        if(isset($post_meta[$duplicate_check]) && is_array($post_meta[$duplicate_check]) && !empty($post_meta[$duplicate_check])){
                            $check_label = (isset($post_meta[$duplicate_check][0]) && !empty($post_meta[$duplicate_check][0]) ? $post_meta[$duplicate_check][0] : "");
                            $i++;
                        } elseif(isset($post_meta[$duplicate_check]) && !is_array($post_meta[$duplicate_check]) && !empty($post_meta[$duplicate_check])) {
                            $check_label = (isset($post_meta[$duplicate_check]) && !empty($post_meta[$duplicate_check]) ? $post_meta[$duplicate_check] : "");
                        }

	                    if(isset($check_label)) {
		                    $current_check[ $listing->ID ] = $check_label;
		                    $all_listings[ $listing->ID ]  = $check_label;
	                    }
                    }

	                // fix for single XML
	                if(!isset($rows[0]) && isset($_SESSION['auto_csv']['file_type']) && $_SESSION['auto_csv']['file_type'] == "xml"){
		                $temp_rows = $rows;
		                $rows = array();

		                $rows[0] = $temp_rows;
	                }

                    foreach($rows as $key => $row){
                        // generate post_title
                        $post_title = "";
                        if(!empty($_POST['title_from_values'])){
                            foreach($_POST['title_from_values'] as $title_value){
                                $post_title .= $this->search_array_keys($row, $title_value, $csv, true) . " ";
                            }

                            $post_title = rtrim($post_title, " ");
                        }

	                    // needs a title...
	                    if(empty($post_title)){
		                    $post_title = "N/A";
	                    }

                        // $post_title     = $this->search_array_keys($row, "title", $csv);
                        $post_content   = $this->search_array_keys($row, "vehicle_overview", $csv);

                        // update dependancies
                        $dependancy_categories = array();

                        $insert_info    = array(
                            'post_type'     => "listings",
                            'post_title'    => ($post_title),
                            'post_content'  => ($post_content),
                            'post_status'   => "publish"
                        );

                        if($duplicate_check == "none"){
                            $no_check = true;
                        } elseif($duplicate_check != "title"){
                            $search_value  = $this->search_array_keys($row, $duplicate_check, $csv);
                        } else {
                            $search_value  = $post_title;
                        }

                        if( (isset($current_check) && isset($search_value) && !in_array($search_value, $current_check) ) || (isset($no_check) && $no_check)){
                            $insert_id      = wp_insert_post( $insert_info );

                            /* Record inserted posts */
                            $imported_listings[$insert_id] = ($post_title);

                            // listing categories
                            $listing_categories['Technical Specifications'] = array("multiple" => true);
                            $listing_categories['Other Comments']           = array("multiple" => true);

                            foreach($listing_categories as $key => $option){
                                if(isset($option['multiple'])){
                                    // contains multiple values, concatenate them
                                    $key   = (isset($option['slug']) && !empty($option['slug']) ? $option['slug'] : str_replace(" ", "_", strtolower($key)));
                                    $value = $this->encode_fix($this->search_array_keys($row, $key, $csv));
                                } else {
                                    $value = $this->encode_fix($this->search_array_keys($row, $key, $csv));

	                                //link_value
	                                if(isset($option['link_value']) && !empty($option['link_value'])){
		                                if($option['link_value'] == "price"){
			                                $value = $this->search_array_keys($row, "price", $csv);

			                                $linked_price_value = $value;

			                                update_post_meta($insert_id, $key, $linked_price_value);
		                                }
	                                }

	                                if(empty($value)){
		                                $value = __("None", "listings");
	                                }

                                    // add value if not already added
                                    $terms = (isset($listing_categories_safe[$key]['terms']) && !empty($listing_categories_safe[$key]['terms']) ? $listing_categories_safe[$key]['terms'] : array());
                                    //compare_value
                                    if(is_array($terms) && !in_array($value, $terms) && !empty($value) && isset($option['compare_value']) && $option['compare_value'] == "="){
                                        $listing_categories_safe[$key]['terms'][$Listing->slugify($value)] = $value;
                                    }
                                }

                                if(!empty($value) && $value != "n-a") {
                                    update_post_meta($insert_id, $key, $value);
	                                if($value != __("None", "listings")) {
		                                $dependancy_categories[ $key ] = array( $Listing->slugify( $value ) => $value );
	                                }
                                }
                            }

                            // gallery images
	                        $this->import_gallery_images($row, $csv, $insert_id);

                            // Features & Options
                            $values = $this->search_array_keys($row, "features_and_options", $csv);
                            $features_and_options = array();
                            $dynamite = "";

                            if(!empty($values)){
                                if(strstr($values, ",")){
                                    $dynamite = ",";
                                } elseif(strstr($values, "<br>")){
                                    $dynamite = "<br>";
                                } elseif(strstr($values, "|")){
                                    $dynamite = "|";
                                } elseif(strstr($values, ";")){
                                    $dynamite = ";";
                                }

                                if(isset($dynamite) && !empty($dynamite)){
                                    $values   = explode($dynamite, $values);

                                    foreach($values as $val){
                                        $features_and_options[$Listing->slugify($val)] = $this->encode_fix($val);
                                    }
                                } else {
                                    $features_and_options[$Listing->slugify($values)] = $this->encode_fix($values);
                                }
                            }

                            if(!empty($features_and_options)){
                                update_post_meta($insert_id, "multi_options", $features_and_options);

                                $options = $listing_categories_safe['options']['terms'];

                                foreach($features_and_options as $option){
                                    $option = $this->encode_fix(trim($option));
                                    $option = preg_replace('/\x{EF}\x{BF}\x{BD}/u', '', @iconv(mb_detect_encoding($option), 'UTF-8', $option));

                                    if(is_array($options) && !in_array($option, $options)){
                                        $listing_categories_safe['options']['terms'][] = $option;
                                    }
                                }
                            }

                            global $lwp_options;

                            // additional detail
                            if(!empty($lwp_options['additional_categories']['value'])){
                                foreach($lwp_options['additional_categories']['value'] as $key => $additional_category){
                                    if(isset($lwp_options['additional_categories']['check'][$key]) && $lwp_options['additional_categories']['check'][$key] == "on"){
                                        $safe_category = str_replace(" ", "_", strtolower($additional_category));

                                        update_post_meta($insert_id, $safe_category, 1);
                                    }
                                }
                            }

	                        $latitude  = $this->search_array_keys($row, "latitude", $csv);
	                        $longitude = $this->search_array_keys($row, "longitude", $csv);

	                        $latitude  = (empty($latitude) ? $default_latitude : $latitude);
	                        $longitude = (empty($longitude) ? $default_longitude : $longitude);

                            // map location
                            $location = array(
                                "latitude"  => $latitude,
                                "longitude" => $longitude,
                                "zoom"      => (isset($lwp_options['default_value_zoom']) && !empty($lwp_options['default_value_zoom']) ? $lwp_options['default_value_zoom'] : "10")
                            );

                            update_post_meta((int)$insert_id, "location_map", $location);

                            // post options (city, hwy, video)
                            $post_options = array(
                                "video" => $this->search_array_keys($row, "video", $csv),
                                "price" => array(
                                    "text"  => (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")),
                                    "value" => (isset($linked_price_value) ? $linked_price_value : $this->search_array_keys($row, "price", $csv))
                                ),
                                "city_mpg" => array(
                                    "text"  => (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : __("City MPG", "listings")),
                                    "value" => $this->search_array_keys($row, "city_mpg", $csv)
                                ),
                                "highway_mpg" => array(
                                    "text"  => (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : __("Highway MPG", "listings")),
                                    "value" => $this->search_array_keys($row, "highway_mpg", $csv)
                                )
                            );

	                        $original_price = $this->search_array_keys($row, "original_price", $csv);

	                        if(!empty($original_price)) {
		                        $post_options['price']['original'] = $original_price;
	                        }

	                        update_post_meta($insert_id, "listing_options", serialize($post_options));

                            // default history image
                            if(isset($lwp_options['default_vehicle_history']['on']) && $lwp_options['default_vehicle_history']['on'] == "1"){
                                update_post_meta( $insert_id, "verified", "yes" );
                            }

                            // update car_sold
                            update_post_meta( $insert_id, "car_sold", 2 );

	                        // secondary title
	                        $secondary_title = $this->search_array_keys($row, "secondary_title", $csv);
	                        update_post_meta($insert_id, "secondary_title", (string)esc_html($secondary_title));

	                        // WPML
	                        if($this->is_wpml_active()){
		                        do_action("wpml_admin_make_post_duplicates", $insert_id);
	                        }
                        } else {

                            $duplicate_ids = array_keys($current_check, $search_value);

                            if(!empty($duplicate_ids)){

                                foreach($duplicate_ids as $duplicate_id){
                                    $update_post = get_post($duplicate_id, ARRAY_A);

	                                $imported_listings['duplicate'][$duplicate_id] = $update_post['post_title'];

	                                if(isset($all_listings[$duplicate_id])){
		                                unset($all_listings[$duplicate_id]);
	                                }

                                    if(!empty($update_post) && isset($_POST['overwrite_existing']) && $_POST['overwrite_existing'] == "on"){
                                        // generate post_title
                                        $post_title = "";
                                        if(!empty($_POST['title_from_values'])){
                                            foreach($_POST['title_from_values'] as $title_value){
                                                $post_title .= $this->search_array_keys($row, $title_value, $csv, true) . " ";
                                            }

                                            $post_title = rtrim($post_title, " ");
                                        }
                                        // $post_title     = $this->search_array_keys($row, "title", $csv);
                                        $post_content   = $this->search_array_keys($row, "vehicle_overview", $csv);

                                        $dependancy_categories = array();

                                        // update post title and content
                                        $update_post['post_title']   = ($post_title);
                                        $update_post['post_content'] = ($post_content);

                                        $insert_id = $update_post['ID'];

                                        wp_update_post( $update_post );

                                        // update old information
                                        $listing_categories['Technical Specifications'] = array("multiple" => true);
                                        $listing_categories['Other Comments']           = array("multiple" => true);

                                        foreach($listing_categories as $key => $option){
                                            $key   = (isset($option['slug']) && !empty($option['slug']) ? $option['slug'] : str_replace(" ", "_", strtolower($key)));

                                            if(isset($option['multiple'])){
                                                // contains multiple values, concatenate them
                                                $value = $this->encode_fix($this->search_array_keys($row, $key, $csv));
                                            } else {
                                                $value = $this->encode_fix($this->search_array_keys($row, $key, $csv));

	                                            //link_value
	                                            if(isset($option['link_value']) && !empty($option['link_value'])){
		                                            if($option['link_value'] == "price"){
			                                            $value = $this->search_array_keys($row, "price", $csv);

			                                            $linked_price_value = $value;
		                                            }
	                                            }

                                                // numbers
                                                if(isset($option['compare_value']) && $option['compare_value'] != "="){
                                                    $value = preg_replace('/\D/', '', $value);
                                                }

                                                // add value if not already added
                                                $terms = (isset($listing_categories_safe[$key]['terms']) && !empty($listing_categories_safe[$key]['terms']) ? $listing_categories_safe[$key]['terms'] : array());
                                                //compare_value
                                                if(is_array($terms) && !in_array($value, $terms) && !empty($value) && isset($option['compare_value']) && $option['compare_value'] == "="){
                                                    $listing_categories_safe[$key]['terms'][] = $value;
                                                }
                                            }

                                            update_post_meta( $insert_id, $key, $value );
                                            $dependancy_categories[$key] = array($Listing->slugify($value) => $value);
                                        }

                                        // gallery images
//                                        if(!isset($overwrite_listing_images) || (isset($overwrite_listing_images) && $overwrite_listing_images == false)){
	                                    if(isset($overwrite_listing_images) && ($overwrite_listing_images == "true" || $overwrite_listing_images == "on")){
	                                        $this->import_gallery_images($row, $csv, $insert_id);
                                        }

                                        // Features & Options
                                        $values = $this->search_array_keys($row, "features_and_options", $csv);
                                        $features_and_options = array();
                                        $dynamite = "";

                                        if(!empty($values)){
                                            if(strstr($values, ",")){
                                                $dynamite = ",";
                                            } elseif(strstr($values, "<br>")){
                                                $dynamite = "<br>";
                                            } elseif(strstr($values, "|")){
                                                $dynamite = "|";
                                            }

                                            if(isset($dynamite) && !empty($dynamite)){
                                                $values   = explode($dynamite, $values);

                                                foreach($values as $val){
                                                    $features_and_options[] = $this->encode_fix($val);
                                                }
                                            } else {
                                                $features_and_options[] = $this->encode_fix($values);
                                            }
                                        }

                                        if(!empty($features_and_options)){
                                            update_post_meta($insert_id, "multi_options", $features_and_options);

                                            $options = $listing_categories_safe['options']['terms'];

                                            foreach($features_and_options as $option){
                                                if(is_array($options) && !in_array($option, $options)){
                                                    $listing_categories_safe['options']['terms'][] = $option;
                                                }
                                            }
                                        }

                                        global $lwp_options;

                                        // additional detail
                                        if(!empty($lwp_options['additional_categories']['value'])){
                                            foreach($lwp_options['additional_categories']['value'] as $key => $additional_category){
                                                if(isset($lwp_options['additional_categories']['check'][$key]) && $lwp_options['additional_categories']['check'][$key] == "on"){
                                                    $safe_category = str_replace(" ", "_", strtolower($additional_category));

                                                    update_post_meta($insert_id, $safe_category, 1);
                                                }
                                            }
                                        }

	                                    $latitude  = $this->search_array_keys($row, "latitude", $csv);
	                                    $longitude = $this->search_array_keys($row, "longitude", $csv);

	                                    $latitude  = (empty($latitude) ? $default_latitude : $latitude);
	                                    $longitude = (empty($longitude) ? $default_longitude : $longitude);

                                        // map location
                                        $location = array(
                                            "latitude"  => $latitude,
                                            "longitude" => $longitude,
                                            "zoom"      => (isset($lwp_options['default_value_zoom']) && !empty($lwp_options['default_value_zoom']) ? $lwp_options['default_value_zoom'] : "10")
                                        );

                                        update_post_meta((int)$insert_id, "location_map", $location);

                                        // post options (city, hwy, video)
                                        $post_options = array(
                                            "video" => $this->search_array_keys($row, "video", $csv),
                                            "price" => array(
                                                "text"  => (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")),
                                                "value" => (isset($linked_price_value) ? $linked_price_value : preg_replace('/\D/', '', $this->search_array_keys($row, "price", $csv)))
                                            ),
                                            "city_mpg" => array(
                                                "text"  => (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : __("City MPG", "listings")),
                                                "value" => $this->search_array_keys($row, "city_mpg", $csv)
                                            ),
                                            "highway_mpg" => array(
                                                "text"  => (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : __("Highway MPG", "listings")),
                                                "value" => $this->search_array_keys($row, "highway_mpg", $csv)
                                            )
                                        );

	                                    // existing badge color and tax labels
	                                    $get_post_options = get_post_meta( $insert_id, "listing_options" );
	                                    $get_options      = @unserialize( $get_post_options[0] );

	                                    if(!empty($get_options['badge_color'])){
		                                    $post_options['badge_color'] = $get_options['badge_color'];
	                                    }

	                                    if(!empty($get_options['badge_text'])){
		                                    $post_options['badge_text'] = $get_options['badge_text'];
	                                    }

	                                    if(!empty($get_options['custom_tax_inside'])){
		                                    $post_options['custom_tax_inside'] = $get_options['custom_tax_inside'];
	                                    }

	                                    if(!empty($get_options['custom_tax_page'])){
		                                    $post_options['custom_tax_page'] = $get_options['custom_tax_page'];
	                                    }

	                                    $original_price = $this->search_array_keys($row, "original_price", $csv);

	                                    if(!empty($original_price)) {
		                                    $post_options['price']['original'] = $original_price;
	                                    }

                                        update_post_meta($insert_id, "listing_options", serialize($post_options));

	                                    // secondary title
	                                    $secondary_title = $this->search_array_keys($row, "secondary_title", $csv);
	                                    update_post_meta($insert_id, "secondary_title", (string)esc_html($secondary_title));
                                    }
                                }
                            }
                        }

	                    if($Listing->is_wpml_active()){
		                    update_option( $Listing->get_listing_categories_option_name(ICL_LANGUAGE_CODE), $listing_categories_safe );
	                    } else {
		                    update_option( $Listing->get_listing_categories_option_name(), $listing_categories_safe );
	                    }
                    }

	                $Listing->generate_dependancy_option(true);

                    $return = (!empty($output) && $output == "array" ? array() : "");

                    $duplicates = (isset($imported_listings['duplicate']) && !empty($imported_listings['duplicate']) ? $imported_listings['duplicate'] : "");
                    unset($imported_listings['duplicate']);

                    $no_title_available = __("No Title Available", "listings");
                    $edit_label         = __("Edit", "listings");

                    if(!empty($imported_listings)){

                        if(!empty($output) && $output == "array"){
                            $return['import'] = $imported_listings;
                        } else {
                            $return .= __("Successfully imported these listings", "listings") . ":<br>";

                            $return .= "<ul>";
                            foreach($imported_listings as $key => $listing){
                                if($key != "duplicate"){
                                    $return .= "<li><a href='" . get_permalink($key) . "'>" . (!empty($listing) ? $listing : $no_title_available) . "</a> (<a href='" . get_edit_post_link($key) . "'>" . $edit_label . "</a>)</li>";
                                }
                            }
                            $return .= "</ul>";
                        }
                    }

                    if(!empty($duplicates)){
                        if(!empty($output) && $output == "array"){
                            $return['duplicates'] = $duplicates;
                        } else {
                            if(isset($_POST['overwrite_existing']) && $_POST['overwrite_existing'] == "on"){
                                $return .= __("These listings were updated with new information from the imported file", "listings") . ":<br>";
                            } else {
                                $return .= __("These listings weren't imported because a duplicate listing was detected", "listings") . ":<br>";
                            }

                            $return .= "<ul>";
                            foreach($duplicates as $key => $listing){
                                $return .= "<li>" . (!empty($listing) ? $listing : $no_title_available) . " (<a href='" . get_edit_post_link($key) . "'>" . $edit_label . "</a>)</li>";
                            }
                            $return .= "</ul>";
                        }
                    }

	                if($remove_listings_not_found){
		                $deleted_not_found  = array();
		                $not_found_listings = array_diff($all_listings, $duplicate_ids);

		                if(!empty($not_found_listings)){
			                foreach($not_found_listings as $not_found_id => $not_found_title){
				                wp_delete_post($not_found_id);
				                $deleted_not_found[$not_found_id] = $not_found_title;
			                }
		                }

		                if(!empty($output) && $output == "array"){
			                $return['deleted'] = $deleted_not_found;
		                } else {
			                $return .= __("These listings were deleted since they were not found in the import file", "listings") . ":<br>";

			                $return .= "<ul>";
			                foreach($deleted_not_found as $key => $listing){
				                $return .= "<li>" . (!empty($listing) ? $listing : $no_title_available) . " </li>";
			                }
			                $return .= "</ul>";
		                }
	                }

	                if(empty($output)) {
		                $return .= "<a href='" . admin_url( "edit.php?post_type=listings&page=file-import" ) . "'><button class='button button-primary'>" . __( "Import more listings", "listings" ) . "</button></a>";
	                }

                    return $return;
                }
            }
        }

	    public function import_gallery_images($row, $csv, $insert_id){
		    global $Listing;

		    // gallery images
		    $values         = $this->search_array_keys($row, "gallery_images", $csv);
		    $gallery_images = array();

		    if(!empty($values)){
			    if(strstr($values, ",")){
				    $dynamite = ",";
			    } elseif(strstr($values, "<br>")){
				    $dynamite = "<br>";
			    } elseif(strstr($values, "|")){
				    $dynamite = "|";
			    } elseif(strstr($values, ";")){
				    $dynamite = ";";
			    }

			    if(isset($dynamite) && !empty($dynamite)){
				    $values   = explode($dynamite, $values);

				    foreach($values as $val){
					    if(!empty($val)){
						    $val = $this->auto_add_http(trim(urldecode($val)));

						    if(filter_var($val, FILTER_VALIDATE_URL)){
							    $val = preg_replace('/\?.*/', '', $val);
							    $gallery_images[] = ($Listing->is_hotlink() ? $val : $this->get_upload_image($val));
						    }
					    }
				    }
			    } else {
				    $values = $this->auto_add_http(trim($values));
				    if(filter_var($values, FILTER_VALIDATE_URL)){
					    $values = preg_replace('/\?.*/', '', $values);
					    $gallery_images[] = ($Listing->is_hotlink() ? $values : $this->get_upload_image($values));
				    }
			    }
		    }

		    if(!empty($gallery_images)){
			    update_post_meta($insert_id, "gallery_images", $gallery_images);
		    }
	    }

    }
}

?>