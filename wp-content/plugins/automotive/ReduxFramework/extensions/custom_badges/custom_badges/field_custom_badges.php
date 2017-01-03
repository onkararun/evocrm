<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_custom_badges' ) ) {

    /**
     * Main ReduxFramework_custom_badges class
     *
     * @since       1.0.0
     */
    class ReduxFramework_custom_badges extends ReduxFramework {
    
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
        
            
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
            }    

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options'           => array(),
                'stylesheet'        => '',
                'output'            => true,
                'enqueue'           => true,
                'enqueue_frontend'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );            
        
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {

            $this->add_text   = ( isset( $this->field['add_text'] ) ) ? $this->field['add_text'] : __( 'Add More', 'redux-framework' );
            $this->show_empty = ( isset( $this->field['show_empty'] ) ) ? $this->field['show_empty'] : true;

	        //D($this->value);

            echo '<ul id="' . $this->field['id'] . '-ul" class="redux-custom-badges">';

	        $sold_value = (isset($this->value['name']['sold']) && !empty($this->value['name']['sold']) ? $this->value['name']['sold'] : "");
	        $sold_color = (isset($this->value['color']['sold']) && !empty($this->value['color']['sold']) ? $this->value['color']['sold'] : "");
	        $sold_font  = (isset($this->value['font']['sold']) && !empty($this->value['font']['sold']) ? $this->value['font']['sold'] : "");

	        /* Sold Badge */
	        //echo "<li><div style='font-weight: bold; margin-bottom: 3px;'>" . __("Sold Badge", "redux-framework") . "</div></li>";
	        echo '<li><div class="color_picker_container"><span style="text-align: left; margin-bottom: 3px;">' . __("Sold Badge", "redux-framework") . '</span><input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[name][sold]' . '" placeholder="' . __("Sold Badge Name", "redux-framework") . '" value="' . $sold_value . '"></div>';

	        echo '<div class="color_picker_container"><span>' . __('Badge Color', 'redux-framework') . '</span><input data-id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[color][sold]' . '" id="' . $this->field['id'] . '-color" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $sold_color . '" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
	        echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value=""></div> ';

	        echo '<div class="color_picker_container"><span>' . __('Badge Font', 'redux-framework') . '</span><input data-id="' . $this->field['id'] . '-font" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font][sold]' . '" id="' . $this->field['id'] . '-color" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $sold_font . '" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
	        echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value=""></div>';

	        echo '</li>';


	        /* Custom Badges */
	        echo "<li><div style='font-weight: bold; margin-top: 10px; margin-bottom: 3px;'>" . __("Custom Badges", "redux-framework") . "</div></li>";

	        if(isset($this->value['name']['sold'])) {
		        unset( $this->value['name']['sold'] );
	        }
	        if(isset($this->value['color']['sold'])) {
		        unset( $this->value['color']['sold'] );
	        }
	        if(isset($this->value['font']['sold'])) {
		        unset( $this->value['font']['sold'] );
	        }

            if ( isset( $this->value['name'] ) && is_array( $this->value['name'] ) ) {
	            $this->value['name']  = array_values(array_filter($this->value['name']));
	            $this->value['color'] = array_values(array_filter($this->value['color']));
	            $this->value['font']  = array_values(array_filter($this->value['font']));

                foreach ( $this->value['name'] as $k => $value ) {
                    if ( !empty($value) ) {
	                    $badge_color = (isset($this->value['color'][$k]) && !empty($this->value['color'][$k]) ? $this->value['color'][$k] : "");
	                    $badge_font  = (isset($this->value['font'][$k]) && !empty($this->value['font'][$k]) ? $this->value['font'][$k] : "");

                        echo '<li><input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[name][' . $k . ']' . '" placeholder="' . __("Badge Name", "redux-framework") . '" value="' . $value . '">';

	                    echo '<input data-id="' . $this->field['id'] . '-' . $k . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[color][' . $k . ']' . '" id="' . $this->field['id'] . '-' . $k . '-color" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $badge_color . '" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
	                    echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-' . $k . '-saved-color' . '" value="">  ';

	                    echo '<input data-id="' . $this->field['id'] . '-' . $k . '-font" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font][' . $k . ']' . '" id="' . $this->field['id'] . '-' . $k . '-color" class="redux-color redux-color-init ' . $this->field['class'] . '"  type="text" value="' . $badge_font . '" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
	                    echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-' . $k . '-saved-color' . '" value="">  <a href="javascript:void(0);" class="deletion redux-custom-badges-remove">' . __( 'Remove', 'redux-framework' ) . '</a>';

	                    echo '</li>';
                    }
                }
            }

	        /* Hidden Custom Badge */
            echo '<li style="display:none;"><input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[name][]' . '" placeholder="' . __("Badge Name", "redux-framework") . '">';

	        echo '<input data-id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[color][]' . '" id="' . $this->field['id'] . '" class="redux-color custom_badge_color ' . $this->field['class'] . '"  type="text" value="" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
	        echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value=""> ';

	        echo '<input data-id="' . $this->field['id'] . '-font" name="' . $this->field['name'] . $this->field['name_suffix'] . '[font][]' . '" id="' . $this->field['id'] . '" class="redux-color custom_badge_font ' . $this->field['class'] . '"  type="text" value="" data-oldcolor=""  data-default-color="' . ( isset( $this->field['default'] ) ? $this->field['default'] : "" ) . '" />';
	        echo '<input type="hidden" class="redux-saved-color" id="' . $this->field['id'] . '-saved-color' . '" value="">  <a href="javascript:void(0);" class="deletion redux-custom-badges-remove">' . __( 'Remove', 'redux-framework' ) . '</a>';

	        echo '</li>';

            echo '</ul>';
            $this->field['add_number'] = ( isset( $this->field['add_number'] ) && is_numeric( $this->field['add_number'] ) ) ? $this->field['add_number'] : 1;
            echo '<a href="javascript:void(0);" class="button button-primary redux-custom-badges-add" data-add_number="' . $this->field['add_number'] . '" data-id="' . $this->field['id'] . '-ul" data-name="' . $this->field['name'] . $this->field['name_suffix'] . '[value][]' . '">' . $this->add_text . '</a><br/>';
        }

    
        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public static function enqueue() {

            $extension = ReduxFramework_extension_custom_badges::getInstance();

	        wp_enqueue_style( 'wp-color-picker' );

            wp_enqueue_script(
                'redux-field-custom-badges-js',
                LISTING_DIR . "/ReduxFramework/extensions/custom_badges/field_custom_badges.js",
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );

	        wp_enqueue_script(
		        'redux-badge-field-color-js',
		        LISTING_DIR . "/ReduxFramework/extensions/custom_badges/field_color.js",
		        array( 'jquery', 'wp-color-picker', 'redux-js' ),
		        time(),
		        true
	        );
        
        }
        
        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */        
        public static function output() {

            //if ( $this->field['enqueue_frontend'] ) {

            //}
            
        }        
        
    }
}
