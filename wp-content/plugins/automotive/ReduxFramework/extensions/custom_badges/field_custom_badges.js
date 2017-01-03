/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.custom_badges  = redux.field_objects.custom_badges  || {};

    $( document ).ready(
        function() {
            redux.field_objects.custom_badges .init();
        }
    );

    redux.field_objects.custom_badges .init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.redux-container-custom_badges:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }
                el.find( '.redux-custom-badges-remove' ).live(
                    'click', function() {
                        redux_change( $( this ) );
                        $( this ).prev( 'input[type="text"]' ).val( '' );
                        $( this ).parent().slideUp(
                            'medium', function() {
                                $( this ).remove();           
                    
                                // update checkbox numbers
                                var i=0;
                                $("#custom_badges-ul > li").filter(":visible").each( function(index, element){
                                    $(this).find("input[type='checkbox']").attr("name", "listing_wp[additional_categories][check][" + i + "]");
                                    i++;
                                });
                            }
                        );
                    }
                );

                el.find( '.redux-custom-badges-add' ).click(
                    function() {
                        var number = parseInt( $( this ).attr( 'data-add_number' ) );
                        var id = $( this ).attr( 'data-id' );
                        var name = $( this ).attr( 'data-name' );
                        for ( var i = 0; i < number; i++ ) {
                            var new_input = $( '#' + id + ' li:hidden' ).clone();
                            console.log(new_input);
                            el.find( '#' + id ).append( new_input );
                            el.find( '#' + id + ' li:last-child' ).removeAttr( 'style' );
                            el.find( '#' + id + ' li:last-child input[type="text"]' ).val( '' );
                            el.find( '#' + id + ' li:last-child input[type="text"]' ).attr( 'name', name );

                            el.find( '#' + id + ' li:last-child input[type="text"]' ).attr("name", "listing_wp[custom_badges][name][" + ($("#custom_badges-ul > li").length - 2) + "]");

                            el.find( '#' + id + ' li:last-child .custom_badge_color').attr("name", "listing_wp[custom_badges][color][" + ($("#custom_badges-ul > li").length - 2) + "]").addClass('redux-color-init ');
                            el.find( '#' + id + ' li:last-child .custom_badge_font').attr("name", "listing_wp[custom_badges][font][" + ($("#custom_badges-ul > li").length - 2) + "]").addClass('redux-color-init ');
                            //"listing_wp[additional_categories][check][" + $(".additional_categories-ul > li").length + "]"

                        }
                        redux.field_objects.badge_color .init();
                    }
                );
            }
        );
    };
})( jQuery );