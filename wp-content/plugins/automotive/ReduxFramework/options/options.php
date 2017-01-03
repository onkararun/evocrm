<?php
if ( ! class_exists( "ReduxFramework" ) ) {
	return;
}

if ( ! class_exists( "Redux_Framework_automotive_wp" ) ) {
	class Redux_Framework_automotive_wp {

		public $args = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {
			// This is needed. Bah WordPress bugs.  ;)
			if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
				// $this->initSettings();
			} else {
				add_action( 'plugins_loaded', array( $this, 'loadConfig' ), 10 );
			}

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}

		public function loadConfig() {
			global $Listing;

			$sections = array(
				array(
					'title'  => __( 'Automotive Settings', 'listings' ),
					'icon'   => 'fa fa-car',
					'fields' => array(
						array(
							'id'       => 'section-start',
							'type'     => 'section',
							'title'    => __( '"Vehicle" term', 'listings' ),
							'subtitle' => 'Change all instances of the term "Vehicle"',
							'indent'   => true
						),
						array(
							'id'      => 'vehicle_singular_form',
							'type'    => 'text',
							'title'   => __( 'Singular Form', 'listings' ),
							'default' => 'Vehicle',
						),
						array(
							'id'      => 'vehicle_plural_form',
							'type'    => 'text',
							'title'   => __( 'Plural Form', 'listings' ),
							'default' => 'Vehicles',
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'title'   => __( "Hotlink Gallery Images", "listings" ),
							'desc'    => __( 'If enabled this will allow you to hotlink images in the backend and in file imports. Please note you will lose the gallery images on existing listings.<br><br>Using hotlinks requires the website you are hotlinking from to have the appropriate Cross-Origin Resource Sharing header for some elements to function properly.', 'listings' ),
							'id'      => 'hotlink',
							'type'    => 'switch',
							'default' => '0',
						),
						array(
							'desc'    => __( 'Name of service used for vehicle history reports', 'listings' ),
							'id'      => 'vehicle_history_label',
							'type'    => 'text',
							'title'   => __( 'Vehicle History Report', 'listings' ),
							'default' => 'Carfax',
						),
						array(
							'desc' => __( 'Logo used for vehicle history reports', 'listings' ),
							'id'   => 'vehicle_history',
							'type' => 'media',
							'url'  => true,
						),
						array(
							'desc'    => __( 'Enable this option to have new listings show vehicle history image by default.', 'listings' ),
							'id'      => 'default_vehicle_history',
							'type'    => 'checkbox',
							'options' => array( "on" => "Checked by default" )
						),
						array(
							'desc'    => __( 'Show or hide the Vehicle History Report on the single listing page', 'listings' ),
							'id'      => 'show_vehicle_history_inventory',
							'type'    => 'switch',
							'title'   => __( 'Vehicle History Report on Single Listing Page', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => false
						),
						array(
							'desc'       => __( 'Link the VIN to the carfax (or which ever service you use), where you want the variable to go use {vin}<br>e.g. http://www.carfax.com/VehicleHistory/p/Report.cfx?partner=ECL_0&vin={vin}', 'listings' ),
							'id'         => 'carfax_linker',
							'type'       => 'carfax_linker',
							'title'      => __( "Vehicle History Report Link", "listings" ),
							'categories' => $Listing->get_listing_categories()
						),
						array(
							'desc'  => __( 'The image used to display when a vehicle doesn\'t have an image', 'listings' ),
							'id'    => 'not_found_image',
							'type'  => 'media',
							'title' => __( 'No Image Found Image', 'listings' )
						),
						array(
							'id'    => 'comparison_page',
							'type'  => 'select',
							'title' => __( 'Comparison Page', 'listings' ),
							'desc'  => __( 'Select the comparison page that will be used', 'listings' ),
							'data'  => 'pages'
						),
						array(
							'id'      => 'car_comparison',
							'title'   => __( 'Comparison functionality', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'Enable or disable the comparison functionality', 'listings' ),
							'default' => true,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'title'   => __( "Delete associated images", "listings" ),
							'desc'    => __( 'When deleting a listing also delete the image associated with it', 'listings' ),
							'id'      => 'delete_associated',
							'type'    => 'switch',
							'default' => '0',
						),
						array(
							'id'      => 'additional_categories',
							'type'    => 'multi_text_auto',
							'title'   => __( 'Additional Categories', 'listings' ),
							'desc'    => __( 'These categories will show up under the search box widget and are on each listing edit page.<br><br> Check the box beside them to make it automatically checked when adding inventory listings.', 'listings' ),
							'default' => array()
						),
						array(
							'id'    => 'custom_badges',
							'type'  => 'custom_badges',
							'title' => __( 'Custom Badges', 'listings' ),
							'desc'  => __( 'Create custom badges with custom colors and easily select them while creating new listings or editing existing listings.', 'listings' ),
						),
						array(
							'id'      => 'sold_attach_badge',
							'title'   => __( 'Automatically Add Sold Badge', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'If enabled this will automatically add the sold badge to cars marked as sold.', 'listings' ),
							'default' => false,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'id'      => 'featured_vehicle_widget',
							'title'   => __( 'Featured Vehicle Scroller', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'If enabled this will add scroller to the bottom of each page, it will also allow you to mark a listing as featured to display it in the slider.', 'listings' ),
							'default' => false,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'id'      => 'keyword_search',
							'title'   => __( 'Keyword Search', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'Choose whether the keywords in the search box shortcode will search the title or the listing category values (useful for stock numbers or VINs).', 'listings' ),
							'default' => false,
							'on'      => __( "Listing Categories", "listings" ),
							'off'     => __( "Title", "listings" )
						),
						array(
							'id'      => 'show_image_column',
							'title'   => __( 'Image Column', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'Enable or disable an image column when viewing the listings in the backend, if enabled it will display the first gallery image of the listing.', 'listings' ),
							'default' => false
						),
						array(
							'title'   => __( "Hide Admin Hints", "listings" ),
							'desc'    => __( 'When turned off this will hide the admin hints that help with using the backend.', 'listings' ),
							'id'      => 'hide_hints',
							'type'    => 'switch',
							'default' => '0',
						),
					),
				),
				array(
					'title'  => __( 'Currency Settings', 'listings' ),
					'icon'   => 'fa fa-usd',
					'fields' => array(
						array(
							'desc'  => __( 'Enter in your symbol used for currency', 'listings' ),
							'type'  => 'text',
							'id'    => 'currency_symbol',
							'title' => __( 'Currency Symbol', 'listings' ),
						),
						array(
							'desc'    => __( 'Change the position of the currency symbol', 'listings' ),
							'type'    => 'switch',
							'id'      => 'currency_placement',
							'title'   => __( 'Currency Symbol Placement', 'listings' ),
							'off'     => __( 'After Value', 'listings' ),
							'on'      => __( 'Before Value', 'listings' ),
							'default' => true
						),
						array(
							'desc'    => __( 'Enter in a separator for large currency amounts', 'listings' ),
							'type'    => 'text',
							'id'      => 'currency_separator',
							'title'   => __( 'Thousand Separator', 'listings' ),
							'default' => ','
						),
						array(
							'desc'    => __( 'Enter in a separator for large currency amounts', 'listings' ),
							'type'    => 'text',
							'id'      => 'currency_separator_decimal',
							'title'   => __( 'Decimal Separator', 'listings' ),
							'default' => '.'
						),
						array(
							'desc'     => __( 'Number of decimals to be displayed after the price', 'listings' ),
							'type'     => 'text',
							'id'       => 'currency_decimals',
							'title'    => __( 'Number of Decimals', 'listings' ),
							'default'  => '0',
							'validate' => 'numeric',
							'msg'      => __( "Value must be a number", "listings" )
						),

						array(
							'id'     => 'section-start',
							'type'   => 'section',
							'title'  => __( 'Listing Taxes', 'listings' ),
							'indent' => true
						),
						array(
							'id'      => 'tax_functionality',
							'type'    => 'switch',
							'title'   => __( 'Tax Functionality', 'listings' ),
							'default' => false
						),
						array(
							'id'       => 'tax_amount',
							'type'     => 'text',
							'title'    => __( 'Tax Rate', 'listings' ),
							'desc'     => __( 'The percentage of tax to be added to a price.', 'listings' ),
							'validate' => 'numeric',
							'value'    => '3',
							'msg'      => __( 'Value must be a number', 'listings' ),
							'required' => array( 'tax_functionality', 'equals', 1 )
						),
						array(
							'id'       => 'default_tax',
							'type'     => 'switch',
							'title'    => __( 'Default Prices Entered', 'listings' ),
							'desc'     => __( 'Choose if the prices include tax or exclude tax', 'listings' ),
							'on'       => __( "Include Tax", "listings" ),
							'off'      => __( "Exclude Tax", "listings" ),
							'default'  => false,
							'required' => array( 'tax_functionality', 'equals', 1 )
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),

						array(
							'id'    => 'sale_value',
							'title' => __( 'Sale Price Prefix', 'listings' ),
							'type'  => 'text',
							'desc'  => __( 'This text gets prefixed to the "Price:" if listing is on sale (i.e. Reduced Price, Sale Price)', 'listings' ),
						),
						array(
							'id'      => 'price_text_replacement',
							'title'   => __( 'Replace Price Text', 'listings' ),
							'type'    => 'text',
							'desc'    => __( 'Replace the price text on each listing with custom text. Leave empty to disable.', 'listings' ),
							'default' => ""
						),
						array(
							'id'      => 'price_text_all_listings',
							'type'    => 'switch',
							'desc'    => __( 'If enabled the "Replace Price Text" option will only show only on listings with an empty price field, otherwise it will appear on every single listing (if all of the listings price fields are populated).', 'listings' ),
							'default' => true,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'id'      => 'tax_label_box',
							'title'   => __( 'Default Tax Label (below the price) on Inventory Page', 'listings' ),
							'type'    => 'text',
							'desc'    => __( 'The text inside the inventory listing box.', 'listings' ),
							'default' => __( 'Plus Sales Tax', 'listings' )
						),
						array(
							'id'      => 'tax_label_page',
							'title'   => __( 'Default Tax Label (below the price) on Single Listing Page', 'listings' ),
							'type'    => 'text',
							'desc'    => __( 'The text on the listing page under the price.', 'listings' ),
							'default' => __( 'Plus Taxes & Licensing', 'listings' )
						),
					),
				),
				array(
					'title'  => __( 'Email Templates', 'listings' ),
					'icon'   => 'fa fa-envelope-o',
					'fields' => array(
						array(
							'desc'    => __( 'Change the default name WordPress uses to send all emails.', 'listings' ),
							'id'      => 'default_email_name',
							'type'    => 'text',
							'title'   => __( 'Name used on sent emails', 'listings' ),
							'default' => 'WordPress',
						),
						array(
							'desc'    => __( 'Change the default email address WordPress uses to send all emails.', 'listings' ),
							'id'      => 'default_email_address',
							'type'    => 'text',
							'title'   => __( 'Email address used on sent emails', 'listings' ),
							'default' => '',
						),

						array(
							'desc'    => __( 'Display this message when an email is successfully sent.', 'listings' ),
							'id'      => 'email_success',
							'type'    => 'text',
							'title'   => __( 'Email was sent', 'listings' ),
							'default' => __( 'The email was sent.', 'listings' ),
						),
						array(
							'desc'    => __( 'Display this message when an email isn\'t successfully sent.', 'listings' ),
							'id'      => 'email_failure',
							'type'    => 'text',
							'title'   => __( 'Email failed to send', 'listings' ),
							'default' => __( 'The email was not sent.', 'listings' ),
						),
						array(
							'desc'    => __( 'Display this message if the email is being marked by', 'listings' ) . ' <a href=\'http://akismet.com/\' target=\'_blank\'>Akismet.com</a>.',
							'id'      => 'email_spam',
							'type'    => 'text',
							'title'   => __( 'Email is spam', 'listings' ),
							'default' => __( 'The email you are trying to send is considered spam.', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the subject of the email that is used to tell friends about vehicles. You can use the variable', 'listings' ) . ' {name}',
							'id'      => 'friend_subject',
							'type'    => 'text',
							'title'   => __( 'Email to a Friend', 'listings' ),
							'default' => '{name} ' . __( 'wants you to check this vehicle out', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the layout of the email. HTML is allowed and some variables you can use are', 'listings' ) . ': <br><br> {name}, {table} ' . __( 'and', 'listings' ) . ' {message}',
							'id'      => 'friend_layout',
							'type'    => 'textarea',
							'default' => __( 'I want you check this vehicle out', 'listings' ) . ' {table} Message: {message}',
						),
						array(
							'desc'  => __( 'Change the email that receives the emails', 'listings' ),
							'id'    => 'drive_to',
							'type'  => 'text',
							'title' => __( 'Schedule a Test Drive', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the subject of the email that is used to schedule test drives.', 'listings' ),
							'id'      => 'drive_subject',
							'type'    => 'text',
							'default' => __( 'Scheduled Test Drive Request', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the layout of the email. HTML is allowed and some variables you can use are', 'listings' ) . ': <br><br> {name}, {contact_method}, {email}, {phone}, {best_day}, {best_time}, {table} ' . __( 'and', 'listings' ) . ' {link}',
							'id'      => 'drive_layout',
							'type'    => 'textarea',
							'default' => __( 'Information', 'listings' ) . '
	
	{table}
	
	Vehicle: {link}',
						),
						array(
							'desc'  => __( 'Change the email that receives the emails', 'listings' ),
							'id'    => 'info_to',
							'type'  => 'text',
							'title' => __( 'Request More Info', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the subject of the email that is used to request more info.', 'listings' ),
							'id'      => 'info_subject',
							'type'    => 'text',
							'default' => __( 'Information Request', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the layout of the email. HTML is allowed and some variables you can use are', 'listings' ) . ': <br><br> {name}, {contact_method}, {email}, {phone}, {table} ' . __( 'and', 'listings' ) . ' {link}',
							'id'      => 'info_layout',
							'type'    => 'textarea',
							'default' => __( 'Request Information', 'listings' ) . '
	
	{table}
	
	Vehicle: {link}',
						),
						array(
							'desc'  => __( 'Change the email that receives the emails', 'listings' ),
							'id'    => 'trade_to',
							'type'  => 'text',
							'title' => __( 'Trade-In Appraisal', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the subject of the email that is used.', 'listings' ),
							'id'      => 'trade_subject',
							'type'    => 'text',
							'default' => __( 'Trade-In Appraisal', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the layout of the email. HTML is allowed and some variables you can use are', 'listings' ) . ': <br><br> {table} ' . __( 'and', 'listings' ) . ' {link}',
							'id'      => 'trade_layout',
							'type'    => 'textarea',
							'default' => '{table}
	
	Vehicle: {link}',
						),
						array(
							'desc'  => __( 'Change the email that receives the emails', 'listings' ),
							'id'    => 'offer_to',
							'type'  => 'text',
							'title' => 'Make an Offer',
						),
						array(
							'desc'    => __( 'Edit the subject of the email that is used.', 'listings' ),
							'id'      => 'offer_subject',
							'type'    => 'text',
							'default' => __( 'Offer', 'listings' ),
						),
						array(
							'desc'    => __( 'Edit the layout of the email. HTML is allowed and some variables you can use are', 'listings' ) . ': <br><br> {name}, {contact_method}, {email}, {phone}, {offered_price}, {financing_required}, {other_comments}, {table} ' . __( 'and', 'listings' ) . ' {link}',
							'id'      => 'offer_layout',
							'type'    => 'textarea',
							'default' => '{table}
	
	Vehicle: {link}',
						),
					),
				),
				array(
					'title'  => __( 'Custom Forms', 'listings' ),
					'icon'   => 'fa fa-bars',
					'fields' => array(
						array(
							'desc'  => 'Using <a href=\'https://wordpress.org/plugins/contact-form-7/\' target=\'_blank\'>Contact Form 7</a> you can replace any of the following forms by pasting a shortcode of the form in the corresponding text box. If left blank the default form will show.<br><br>You can use the tag [_listing_details] in a contact form to retrieve which listing the form was sent from.',
							'id'    => 'request_info_form_shortcode',
							'type'  => 'text',
							'title' => __( 'Request More Info Form', 'listings' ),
						),
						array(
							'id'    => 'schedule_test_drive_form_shortcode',
							'type'  => 'text',
							'title' => __( 'Schedule Test Drive Form', 'listings' ),
						),
						array(
							'id'    => 'make_offer_form_shortcode',
							'type'  => 'text',
							'title' => __( 'Make an Offer Form', 'listings' ),
						),
						array(
							'id'    => 'tradein_form_shortcode',
							'type'  => 'text',
							'title' => __( 'Trade-In Appraisal Form', 'listings' ),
						),
						array(
							'id'    => 'email_friend_form_shortcode',
							'type'  => 'text',
							'title' => __( 'Email to a Friend Form', 'listings' ),
						),
					),
				),
				array(
					'title'  => __( "Inventory Page", "listings" ),
					'desc'   => __( 'These settings control elements and functionality on the page that displays all of your inventory.', 'listings' ),
					'icon'   => 'fa fa-file',
					'fields' => array(
						array(
							'id'    => 'inventory_page',
							'type'  => 'select',
							'title' => __( 'Inventory Page', 'listings' ),
							'desc'  => __( 'Select the inventory page that will be highlighted in the menu and displayed in the breadcrumbs', 'listings' ),
							'data'  => 'pages'
						),
						array(
							'id'    => 'inventory_no_sold',
							'type'  => 'switch',
							'title' => __( 'Sold vehicles', 'listings' ),
							'desc'  => __( 'This will hide the vehicles that are sold, sold vehicles can still be shown by adding &show_sold to the end of the URL', 'listings' ),
							'on'    => __( "Show", "listings" ),
							'off'   => __( "Hide", "listings" )
						),
						// array (
						// 	'id' => 'inventory_sold_to_bottom',
						// 	'type' => 'switch',
						// 	'title' => __('Sold vehicles to bottom', 'listings'),
						// 	'desc' => __('This will force your sold listings to the bottom of your inventory.', 'listings'),
						// 	'on' => __("Enabled", "listings"),
						// 	'off' => __("Disabled", "listings"),
						// 	'required' => array('inventory_no_sold', 'equals', '1')
						// ),
						array(
							'desc'    => __( 'The amount of listings being displayed on the inventory page', 'listings' ),
							'id'      => 'listings_amount',
							'step'    => '1',
							'max'     => '100',
							'default' => '10',
							'type'    => 'slider',
							'title'   => __( 'Number of listings', 'listings' ),
						),
						array(
							'id'      => 'sortby',
							'title'   => __( 'Sort By functionality', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'Enable or disable the sort by functionality', 'listings' ),
							'default' => true,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'id'       => 'sortby_categories',
							'title'    => __( 'Sort By Categories', 'listings' ),
							'type'     => 'sorter',
							'class'    => 'sort_by_option_redux',
							'options'  => $Listing->sort_by_options(),
							'required' => array( 'sortby', 'equals', '1' )
						),
						array(
							'id'       => 'sortby_default',
							'title'    => __( 'Default Sort By', 'listings' ),
							'type'     => 'switch',
							'desc'     => __( 'Adjust how the sort by defaults to', 'listings' ),
							'default'  => true,
							'on'       => __( "Ascending", "listings" ),
							'off'      => __( "Descending", "listings" ),
							'required' => array( 'sortby', 'equals', '1' )
						),
						array(
							'title'   => __( "Listing Views", "listings" ),
							'desc'    => __( 'Toggle the functionality of the listing views', 'listings' ),
							'id'      => 'inventory_listing_toggle',
							'type'    => 'switch',
							'default' => '1',
						),
						array(
							'title'   => __( "Top Pagination", "listings" ),
							'desc'    => __( 'Toggle the functionality of the top pagination, useful for single paged inventories.', 'listings' ),
							'id'      => 'top_pagination',
							'type'    => 'switch',
							'default' => '1',
						),
						array(
							'title'   => __( "Bottom Pagination", "listings" ),
							'desc'    => __( 'Toggle the functionality of the bottom pagination, useful for single paged inventories.', 'listings' ),
							'id'      => 'bottom_pagination',
							'type'    => 'switch',
							'default' => '1',
						),
						array(
							'title'   => __( "Vehicle Overview on Listings", "listings" ),
							'desc'    => __( 'If enabled this will show the vehicle overview on listings instead of the listing categories.', 'listings' ),
							'id'      => 'vehicle_overview_listings',
							'type'    => 'switch',
							'default' => '0',
						),
						array(
							'title'    => __( 'Vehicle Overview Character Limit', 'listings' ),
							'desc'     => __( "Control the amount of characters shown on the exceprt", "listings" ),
							'id'       => 'vehicle_overview_listings_limit',
							'type'     => 'text',
							'validate' => 'numeric',
							'required' => array( 'vehicle_overview_listings', 'equals', 1 ),
							'default'  => 250
						),
						array(
							'title'    => __( 'Vehicle Overview Ellipsis', 'listings' ),
							'desc'     => __( "Customize the text used when a vehicle overview is longer than what is displayed", "listings" ),
							'id'       => 'vehicle_overview_ellipsis',
							'type'     => 'text',
							'required' => array( 'vehicle_overview_listings', 'equals', 1 ),
							'default'  => "[...]"
						),
						array(
							'title'   => __( "Thumbnail Slideshow", "listings" ),
							'desc'    => __( 'Display a slideshow if the user clicks the listing thumbnail', 'listings' ),
							'id'      => 'thumbnail_slideshow',
							'type'    => 'switch',
							'default' => '1',
						),
					),
				),
				array(
					'title'  => __( 'Single Listing Page', 'listings' ),
					'desc'   => __( 'These settings control elements and functionality on the page that displays a single listing.', 'listings' ),
					'icon'   => 'fa fa-file-o',
					'fields' => array(
						array(
							'desc'    => __( 'Customize the slug used for single listings. <br><br>You will need to regenerate the permalink settings after you change this value by going to "Settings" >> "Permalinks" and re-saving the options.' ),
							'id'      => 'listing_slug',
							'type'    => 'text',
							'title'   => __( 'Listing Slug', 'listings' ),
							'default' => 'listings'
						),
						array(
							'id'      => 'inventory_primary_title',
							'type'    => 'text',
							'desc'    => __( 'This title shows up in the header section of all listings posted.', 'listings' ),
							'title'   => __( 'Inventory Listing Titles', 'listings' ),
							'default' => __( 'Inventory Listing', 'listings' ),
						),
						array(
							'id'      => 'inventory_secondary_title',
							'type'    => 'text',
							'desc'    => __( 'This secondary title displays under previous title in the header', 'listings' ),
							'default' => __( 'Powerful Inventory Marketing, Fully Integrated', 'listings' ),
						),
						array(
							'id'      => 'woocommerce_listing_integration',
							'title'   => __( "WooCommerce Listing Integration", "listings" ),
							'type'    => 'switch',
							'desc'    => __( 'If enabled this will allow you to associate WooCommerce products with your listings allowing users to add items to their carts while browsing listings.', 'listings' ),
							'default' => false,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'id'       => 'woocommerce_integration_border',
							'title'    => __( "WooCommerce Integration Border", "listings" ),
							'type'     => 'border',
							'desc'     => __( "Control the border around the WooCommerce Add to Cart element", "listings" ),
							'all'      => true,
							'default'  => array(
								'border-color'  => '#ccc',
								'border-style'  => 'solid',
								'border-top'    => '4px',
								'border-right'  => '4px',
								'border-bottom' => '4px',
								'border-left'   => '4px'
							),
							'required' => array( 'woocommerce_listing_integration', 'equals', 1 )
						),
						array(
							'id'       => 'woocommerce_integration_padding',
							'title'    => __( "WooCommerce Integration Padding", "listings" ),
							'type'     => 'spacing',
							'desc'     => __( "Control the padding inside the WooCommerce Add to Cart element", "listings" ),
							'mode'     => 'padding',
							'units'    => 'px',
							'default'  => array(
								'padding-left'   => '12px',
								'padding-top'    => '12px',
								'padding-bottom' => '12px',
								'padding-right'  => '12px',
							),
							'required' => array( 'woocommerce_listing_integration', 'equals', 1 )
						),
						array(
							'desc'  => __( 'The image used to display a vehicles fuel efficiency rating', 'listings' ),
							'id'    => 'fuel_efficiency_image',
							'type'  => 'media',
							'title' => __( 'Fuel Efficiency Rating Image', 'listings' )
						),
						array(
							'desc'    => __( 'Show or hide the fuel efficiency box on the inventory page', 'listings' ),
							'id'      => 'fuel_efficiency_show',
							'type'    => 'switch',
							'title'   => __( 'Fuel Efficiency', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'desc'     => __( 'The text displayed in the fuel efficiency box', 'listings' ),
							'id'       => 'fuel_efficiency_text',
							'type'     => 'textarea',
							'default'  => 'Actual rating will vary with options, driving conditions, driving habits and vehicle condition.',
							'required' => array( 'fuel_efficiency_show', 'equals', 1 )
						),
						array(
							'desc'    => __( 'Show or hide the social icons', 'listings' ),
							'id'      => 'social_icons_show',
							'type'    => 'switch',
							'title'   => __( 'Social Icons', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'desc'    => __( 'Show or hide the listing video in the right sidebar', 'listings' ),
							'id'      => 'display_vehicle_video',
							'type'    => 'switch',
							'title'   => __( 'Listing Video', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'id'      => 'youtube_video_options',
							'type'    => 'checkbox',
							'title'   => __( 'YouTube Video Options', 'listings' ),
							'desc'    => __( 'Add additional options to the videos used in the listings', 'listings' ),
							'options' => array(
								'rel'             => __( 'Show related videos when the video finishes', 'listings' ),
								'player_controls' => __( 'Show player controls', 'listings' ),
								'title_actions'   => __( 'Show video title and player actions', 'listings' ),
								'privacy'         => __( 'Enable privacy-enhanced mode', 'listings' ),
							)
						),
						array(
							'id'      => 'listing_badge_slider',
							'title'   => __( 'Listing Badge on Slider', 'listings' ),
							'type'    => 'switch',
							'desc'    => __( 'Enable or disable the listing badge on the listing slider', 'listings' ),
							'default' => false,
							'on'      => __( "Enabled", "listings" ),
							'off'     => __( "Disabled", "listings" )
						),
						array(
							'id'       => 'section-start',
							'type'     => 'section',
							'title'    => __( 'Financing Calculator', 'listings' ),
							'subtitle' => __( 'Control the financing calculator found on the inventory listing page.', 'listings' ),
							'indent'   => true
						),
						array(
							'desc'    => __( 'Show or hide the financing calculator', 'listings' ),
							'id'      => 'calculator_show',
							'type'    => 'switch',
							'title'   => __( 'Financing Calculator', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'desc'     => __( 'Control the down payment for the finance calculator', 'listings' ),
							'id'       => 'calculator_down_payment',
							'type'     => 'text',
							'validate' => 'numeric',
							'title'    => __( 'Financing Calculator Down Payment', 'listings' ),
							'default'  => 1000,
							'required' => array( 'calculator_show', '=', true )
						),
						array(
							'desc'     => __( 'Control the annual interest rate for the finance calculator', 'listings' ),
							'id'       => 'calculator_rate',
							'type'     => 'text',
							'validate' => 'numeric',
							'title'    => __( 'Financing Calculator Rate', 'listings' ),
							'default'  => 7,
							'required' => array( 'calculator_show', '=', true )
						),
						array(
							'title'   => __( 'Default Loan Calculator Frequency', 'listings' ),
							'desc'    => __( 'Choose which option is selected', 'listings' ),
							'type'    => 'button_set',
							'id'      => 'default_frequency',
							'options' => array(
								'1' => __( 'Bi-Weekly', 'listings' ),
								'2' => __( 'Weekly', 'listings' ),
								'3' => __( 'Monthly', 'listings' )
							),
							'default' => 1
						),
						array(
							'desc'     => __( 'Control the text displayed below the finance calculator', 'listings' ),
							'id'       => 'calculator_below_text',
							'type'     => 'text',
							'title'    => __( 'Financing Calculator Text', 'listings' ),
							'required' => array( 'calculator_show', '=', true )
						),
						array(
							'desc'     => __( 'Control the term of loan for the finance calculator', 'listings' ),
							'id'       => 'calculator_loan',
							'type'     => 'text',
							'validate' => 'numeric',
							'title'    => __( 'Financing Calculator Loan', 'listings' ),
							'default'  => 5,
							'required' => array( 'calculator_show', '=', true )
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'desc'    => __( 'Show or hide the recent vehicles', 'listings' ),
							'id'      => 'recent_vehicles_show',
							'type'    => 'switch',
							'title'   => __( 'Recent Vehicles', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'desc'     => __( 'Change the slider to show either recent or related vehicles', 'listings' ),
							'id'       => 'recent_related_vehicles',
							'type'     => 'switch',
							'title'    => __( 'Recent or Related Vehicles', 'listings' ),
							'on'       => __( 'Recent', 'listings' ),
							'off'      => __( 'Related', 'listings' ),
							'required' => array( 'recent_vehicles_show', 'equals', 1 ),
							'default'  => true
						),
						array(
							'title'    => __( 'Related Vehicle Category', 'listings' ),
							'desc'     => __( 'Use this category to select related vehicles.', 'listings' ),
							'type'     => 'select',
							'id'       => 'related_category',
							'required' => array( 'recent_related_vehicles', '!=', 1 ),
							'options'  => get_listing_categories_to_redux_select(),
						),
						array(
							'desc'    => __( 'Edit the recent vehicles slider title', 'listings' ),
							'id'      => 'recent_vehicles_title',
							'type'    => 'text',
							'title'   => __( 'Recent Vehicles Title', 'listings' ),
							'default' => __( "Recent Vehicles", "listings" )
						),
						array(
							'desc'    => __( 'Edit the recent vehicles slider description', 'listings' ),
							'id'      => 'recent_vehicles_desc',
							'type'    => 'text',
							'title'   => __( 'Recent Vehicles Description', 'listings' ),
							'default' => __( "Browse through the vast selection of vehicles that have recently been added to our inventory.", "listings" )
						),
						array(
							'desc'    => __( 'Adjust the amount of vehicles shown in the recent vehicles (-1 to display all).', 'listings' ),
							'id'      => 'recent_vehicles_limit',
							'type'    => 'text',
							'title'   => __( 'Number of Recent Vehicles', 'listings' ),
							'default' => "10"
						),
						array(
							'id'       => 'section-start',
							'type'     => 'section',
							'title'    => __( 'Top buttons', 'listings' ),
							'subtitle' => __( 'Show or hide the top buttons found on the inventory listing page.', 'listings' ),
							'indent'   => true
						),
						array(
							'title'   => __( 'Previous Vehicle', 'listings' ),
							'id'      => 'previous_vehicle_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Previous Vehicle Label', 'listings' ),
							'id'      => 'previous_vehicle_label',
							'type'    => 'text',
							'default' => "Prev Vehicle"
						),
						array(
							'title'   => __( 'Request More Info', 'listings' ),
							'id'      => 'request_more_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Request More Info Label', 'listings' ),
							'id'      => 'request_more_label',
							'type'    => 'text',
							'default' => "Request More Info"
						),
						array(
							'title'   => __( 'Schedule Test Drive', 'listings' ),
							'id'      => 'schedule_test_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Schedule Test Drive Label', 'listings' ),
							'id'      => 'schedule_test_label',
							'type'    => 'text',
							'default' => "Schedule Test Drive"
						),
						array(
							'title'   => __( 'Make an Offer', 'listings' ),
							'id'      => 'make_offer_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Make an Offer Label', 'listings' ),
							'id'      => 'make_offer_label',
							'type'    => 'text',
							'default' => "Make an Offer"
						),
						array(
							'title'   => __( 'Trade-In Appraisal', 'listings' ),
							'id'      => 'tradein_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Trade-In Appraisal Label', 'listings' ),
							'id'      => 'tradein_label',
							'type'    => 'text',
							'default' => "Trade-In Appraisal"
						),
						array(
							'title'   => __( 'PDF Brochure', 'listings' ),
							'id'      => 'pdf_brochure_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'PDF Brochure Label', 'listings' ),
							'id'      => 'pdf_brochure_label',
							'type'    => 'text',
							'default' => "PDF Brochure"
						),
						array(
							'title'   => __( 'Print this Vehicle', 'listings' ),
							'id'      => 'print_vehicle_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Print this Vehicle Label', 'listings' ),
							'id'      => 'print_vehicle_label',
							'type'    => 'text',
							'default' => "Print this Vehicle"
						),
						array(
							'title'   => __( 'Email to a Friend', 'listings' ),
							'id'      => 'email_friend_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Email to a Friend Label', 'listings' ),
							'id'      => 'email_friend_label',
							'type'    => 'text',
							'default' => "Email to a Friend"
						),
						array(
							'title'   => __( 'Next Vehicle', 'listings' ),
							'id'      => 'next_vehicle_show',
							'type'    => 'switch',
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
						array(
							'title'   => __( 'Next Vehicle Label', 'listings' ),
							'id'      => 'next_vehicle_label',
							'type'    => 'text',
							'default' => "Next Vehicle"
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'id'       => 'section-start',
							'type'     => 'section',
							'title'    => __( 'Content Tabs', 'listings' ),
							'subtitle' => __( 'Edit the names of the tabs displayed under the slideshow. Leave blank remove the tab from the admin and frontend.', 'listings' ),
							'indent'   => true
						),
						array(
							'title'   => __( 'First Tab', 'listings' ),
							'desc'    => __( 'Default text: Vehicle Overview', 'listings' ),
							'id'      => 'first_tab',
							'type'    => 'text',
							'default' => 'Vehicle Overview'
						),
						array(
							'title'   => __( 'Second Tab', 'listings' ),
							'desc'    => __( 'Default text: Features & Options', 'listings' ),
							'id'      => 'second_tab',
							'type'    => 'text',
							'default' => 'Features & Options'
						),
						array(
							'title'   => __( 'Third Tab', 'listings' ),
							'desc'    => __( 'Default text: Technical Specifications', 'listings' ),
							'id'      => 'third_tab',
							'type'    => 'text',
							'default' => 'Technical Specifications'
						),
						array(
							'title'   => __( 'Fourth Tab', 'listings' ),
							'desc'    => __( 'Default text: Vehicle Location', 'listings' ),
							'id'      => 'fourth_tab',
							'type'    => 'text',
							'default' => 'Vehicle Location'
						),
						array(
							'title'   => __( 'Fifth Tab', 'listings' ),
							'desc'    => __( 'Default text: Other Comments', 'listings' ),
							'id'      => 'fifth_tab',
							'type'    => 'text',
							'default' => 'Other Comments'
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'desc'  => __( 'Logo used for the generated PDF\'s. Must be using header logo image.', 'listings' ),
							'id'    => 'pdf_logo',
							'type'  => 'media',
							'url'   => true,
							'title' => __( 'PDF Logo', 'listings' )
						),
						array(
							'desc'    => __( 'Enable or disable comments on the single listing page.', 'listings' ),
							'id'      => 'listing_comments',
							'type'    => 'switch',
							'title'   => __( 'Comments on listing pages', 'listings' ),
							'on'      => 'Enable',
							'off'     => 'Disable',
							'default' => false
						),
						array(
							'desc'  => __( 'Add a message under each single listing page.', 'listings' ),
							'id'    => 'listing_comment_footer',
							'type'  => 'editor',
							'title' => __( 'Message under each listing', 'listings' )
						),
						array(
							'desc'  => __( 'Display a comment under the vehicle overview for sold vehicles.', 'listings' ),
							'id'    => 'sold_listing_comment',
							'type'  => 'textarea',
							'title' => __( 'Message under each sold listing', 'listings' )
						),
					),
				),
				array(
					'title'  => __( "Portfolio Page", "listings" ),
					'fields' => array(
						array(
							'desc'    => __( 'Customize the slug used for portfolio items. (Don\'t create a page with the same slug)<br><br>You will need to regenerate the permalink settings after you change this value by going to "Settings" >> "Permalinks" and re-saving the options.' ),
							'id'      => 'portfolio_slug',
							'type'    => 'text',
							'title'   => __( 'Portfolio Slug', 'listings' ),
							'default' => 'listings_portfolio'
						),
						array(
							'desc'    => __( 'Change the portfolio image to link to the page rather than to fancybox.', 'listings' ),
							'id'      => 'portfolio_image_link',
							'type'    => 'switch',
							'title'   => __( 'Portfolio Image Link', 'listings' ),
							'on'      => 'FancyBox',
							'off'     => 'Portfolio Item',
							'default' => true
						),
						array(
							'title'   => __( 'Job Description Title', 'listings' ),
							'id'      => 'job_description_title',
							'type'    => 'text',
							'default' => 'Job Description'
						),
						array(
							'title'   => __( 'Project Details Title', 'listings' ),
							'id'      => 'project_details_title',
							'type'    => 'text',
							'default' => 'Projects Details'
						),
						array(
							'title'   => __( 'Related Projects Title', 'listings' ),
							'id'      => 'related_projects_title',
							'type'    => 'text',
							'default' => 'Related Projects'
						),
						array(
							'desc'    => __( 'Show or hide the related projects on the portfolio page.', 'listings' ),
							'id'      => 'show_related_projects',
							'type'    => 'switch',
							'title'   => __( 'Show Related Projects', 'listings' ),
							'on'      => 'Show',
							'off'     => 'Hide',
							'default' => true
						),
					),
					'icon'   => 'fa fa-folder-o'
				),
				array(
					'title'  => __( 'Default Values', 'listings' ),
					'fields' => array(
						array(
							'id'       => 'section-start',
							'type'     => 'section',
							'title'    => __( 'Default location', 'listings' ),
							'subtitle' => __( 'This location will be the default used while creating new listings', 'listings' ),
							'indent'   => true
						),
						array(
							'title'   => __( 'Latitude', 'listings' ),
							'desc'    => __( 'The default latitude.', 'listings' ),
							'id'      => 'default_value_lat',
							'type'    => 'text',
							'default' => '43.653226'
						),
						array(
							'title'   => __( 'Longitude', 'listings' ),
							'desc'    => __( 'The default longitde.', 'listings' ),
							'id'      => 'default_value_long',
							'type'    => 'text',
							'default' => '-79.3831843'
						),
						array(
							'title'         => __( 'Zoom', 'listings' ),
							'desc'          => __( 'The default zoom level.', 'listings' ),
							'id'            => 'default_value_zoom',
							'type'          => 'slider',
							'default'       => '10',
							'min'           => 0,
							'max'           => 19,
							'step'          => 1,
							'display_value' => 'text'
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'id'       => 'section-start',
							'type'     => 'section',
							'title'    => __( 'Default Details', 'listings' ),
							'subtitle' => __( 'Control the default values for the details of new listings.', 'listings' ),
							'indent'   => true
						),
						array(
							'title'   => __( 'Price Label', 'listings' ),
							'desc'    => __( 'The default label for price.', 'listings' ),
							'id'      => 'default_value_price',
							'type'    => 'text',
							'default' => 'Price'
						),
						array(
							'title'   => __( 'City MPG Label', 'listings' ),
							'desc'    => __( 'The default label for city MPG.', 'listings' ),
							'id'      => 'default_value_city',
							'type'    => 'text',
							'default' => 'City'
						),
						array(
							'title'   => __( 'Highway MPG Label', 'listings' ),
							'desc'    => __( 'The default label for highway MPG.', 'listings' ),
							'id'      => 'default_value_hwy',
							'type'    => 'text',
							'default' => 'Highway'
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
					),
					'icon'   => 'fa fa-pencil-square-o'
				),
				array(
					'title'  => __( "API Keys", "listings" ),
					'class'  => 'api_keys',
					'fields' => array(
						array(
							'id'     => 'section-start',
							'type'   => 'section',
							'title'  => __( 'Edmunds VIN Import', 'listings' ),
							'indent' => true
						),
						array(
							'title'   => __( 'API Key', 'listings' ),
							'id'      => 'edmunds_api_key',
							'type'    => 'text',
							'default' => ''
						),
						array(
							'title'   => __( 'API Secret', 'listings' ),
							'id'      => 'edmunds_api_secret',
							'type'    => 'text',
							'default' => '',
							'desc'    => __( 'Enter your <a href=\'http://developer.edmunds.com/\' target=\'_blank\'>Edmunds</a> API Keys to import vehicle information with a VIN.', 'listings' ),
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'id'     => 'section-start',
							'type'   => 'section',
							'title'  => __( 'MailChimp API', 'listings' ),
							'indent' => true
						),
						array(
							'id'    => 'mailchimp_api_key',
							'type'  => 'text',
							'title' => __( 'MailChimp API', 'listings' ),
							'desc'  => __( 'Enter your <a href="https://apidocs.mailchimp.com/" target="_blank">MailChimp</a> API Key to use the MailChimp widget.', 'listings' ),
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'id'     => 'section-start',
							'type'   => 'section',
							'title'  => __( 'Google reCAPTCHA API', 'listings' ),
							'indent' => true
						),
						array(
							'id'      => 'recaptcha_enabled',
							'type'    => 'switch',
							'title'   => __( 'ReCAPTCHA enabled', 'listings' ),
							'default' => true,
						),
						array(
							'id'    => 'recaptcha_public_key',
							'type'  => 'text',
							'title' => __( 'Public Key', 'listings' ),
						),
						array(
							'id'    => 'recaptcha_private_key',
							'type'  => 'text',
							'title' => __( 'Private Key', 'listings' ),
							'desc'  => __( 'You can get a Google reCAPTCHA API from <a href="http://www.google.com/recaptcha/intro/" target="_blank">here</a>', 'listings' ),
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
						array(
							'id'     => 'section-start',
							'type'   => 'section',
							'title'  => __( 'Twitter API', 'listings' ),
							'indent' => true
						),
						array(
							'desc'  => __( 'Turn on to enable widget twitter feeds', 'listings' ) . '.<br><br><a href=\'http://dev.twitter.com/apps\' target=\'_blank\'>' . __( 'Create a Twitter application here', 'listings' ) . '</a>',
							'id'    => 'twitter_switch',
							'type'  => 'switch',
							'title' => __( 'Twitter Widget Feed', 'listings' ),
						),
						array(
							'id'       => 'consumer_key',
							'type'     => 'text',
							'title'    => __( 'API Key', 'listings' ),
							'required' => array(
								0 => 'twitter_switch',
								1 => '=',
								2 => 1,
							),
						),
						array(
							'id'       => 'secret_consumer_key',
							'type'     => 'text',
							'title'    => __( 'API Secret Key', 'listings' ),
							'required' => array(
								0 => 'twitter_switch',
								1 => '=',
								2 => 1,
							),
						),
						array(
							'id'       => 'access_token',
							'type'     => 'text',
							'title'    => __( 'Access Token', 'listings' ),
							'required' => array(
								0 => 'twitter_switch',
								1 => '=',
								2 => 1,
							),
						),
						array(
							'id'       => 'secret_access_token',
							'type'     => 'text',
							'title'    => __( 'Access Token Secret', 'listings' ),
							'required' => array(
								0 => 'twitter_switch',
								1 => '=',
								2 => 1,
							),
							'desc'     => __( 'Enter your <a href="https://apps.twitter.com/" target="_blank">Twitter</a> application API Keys to use the Twitter widget.', 'listings' ),
						),
						array(
							'id'     => 'section-end',
							'type'   => 'section',
							'indent' => false,
						),
					),
					'icon'   => 'fa fa-key'
				),
				array(
					'title'  => __( "Import / Export", "listings" ),
					'class'  => 'custom_import',
					'fields' => array(
						array(
							'id'         => 'opt-import-export',
							'type'       => 'import_export',
							'title'      => __( 'Import Export', 'listings' ),
							'subtitle'   => __( 'Save and restore your Redux options', 'listings' ),
							'full_width' => true,
						),
					),
					'icon'   => 'el-icon-refresh'
				)
			);

			// import listing categories
			$show_listing_categories = ( get_option( 'show_listing_categories' ) ? get_option( 'show_listing_categories' ) : "show" );

			if ( $show_listing_categories != "hide" ) {
				$sections[9]['fields'][] = array(
					'id'      => 'import-demo-listing-categories',
					'type'    => 'custom_import',
					'title'   => __( 'Import Demo Listing Categories', 'listings' ),
					'desc'    => "<span class='remove_option_categories'>" . __( 'Click here to permanently remove this option from view.', 'listings' ) . "</span>",
					'options' => array(
						'1' => __( 'Import', 'listings' )
					),
					'class'   => 'import_listing_categories',
					'default' => 1
				);
			}

			/* WPML Sort By Options */
			if ( $Listing->is_wpml_active() ) {
				$all_languages = apply_filters( "wpml_active_languages", "", array(
					"skip_missing" => 0,
					"orderby"      => "id"
				) );

				if ( ! empty( $all_languages ) ) {
					// additional categories
					unset( $sections[0]['fields'][14] );

					foreach ( $all_languages as $lang_code => $lang ) {
						array_splice( $sections[0]['fields'], 14, 0, array(
							array(
								'id'      => 'additional_categories_' . $lang_code,
								'type'    => 'multi_text_auto',
								'title'   => __( 'Additional Categories', 'listings' ) . ' ' . $lang['display_name'],
								'desc'    => __( 'These categories will show up under the search box widget and are on each listing edit page.<br><br> Check the box beside them to make it automatically checked when adding inventory listings.', 'listings' ),
								'default' => array()
							)
						) );
					}

					// carfax link
					unset( $sections[0]['fields'][8] );

					foreach ( $all_languages as $lang_code => $lang ) {
						array_splice( $sections[0]['fields'], 8, 0, array(
							array(
								'desc'       => __( 'Link the VIN to the carfax (or which ever service you use), where you want the variable to go use {vin}<br>e.g. http://www.carfax.com/VehicleHistory/p/Report.cfx?partner=ECL_0&vin={vin}', 'listings' ),
								'id'         => 'carfax_linker_' . $lang_code,
								'type'       => 'carfax_linker',
								'title'      => __( 'Vehicle History Report Link', "listings" ) . ' ' . $lang['display_name'],
								'categories' => $Listing->get_listing_categories( false, $lang_code )
							),
						) );
					}

					// unset original sort by option
					unset( $sections[4]['fields'][4] );

					foreach ( $all_languages as $lang_code => $lang ) {
						array_splice( $sections[4]['fields'], 4, 0, array(
							array(
								'id'       => 'sortby_categories_' . $lang_code,
								'type'     => 'sorter',
								'class'    => 'sort_by_option_redux',
								'options'  => $Listing->sort_by_options( $lang_code ),
								'title'    => __( 'Sort By Categories', 'listings' ) . ' ' . $lang['display_name'],
								'required' => array( 'sortby', 'equals', '1' )
							)
						) );
					}
				}
			}

			// Change your opt_name to match where you want the data saved.
			$args = array(
				"opt_name"        => "listing_wp",
				"menu_title"      => __( "Listing Options", 'listings' ),
				"page_slug"       => "listing_wp",
				"global_variable" => "listing_wp",
				"dev_mode"        => false,
				"display_name"    => __( "Automotive Listings Plugin", "listings" ),
				"display_version" => AUTOMOTIVE_VERSION,
				"footer_credit"   => "Automotive by Theme Suite",
				"share_icons"     => array(
					array(
						'url'   => 'https://www.facebook.com/ThemeSuite.Themes',
						'title' => 'Like us on Facebook',
						'icon'  => 'fa fa-facebook-official'
					),
					array(
						'url'   => 'https://twitter.com/themesuite',
						'title' => 'Follow us on Twitter',
						'icon'  => 'fa fa-twitter'
					)
				)
			);

			// Use this section if this is for a theme. Replace with plugin specific data if it is for a plugin.
			$ReduxFramework = new ReduxFramework( $sections, $args );
		}

	}

	global $automotive_plugin_redux;

	$automotive_plugin_redux = new Redux_Framework_automotive_wp();
}