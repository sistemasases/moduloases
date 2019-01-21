/**
 * @package     block_ases
 * @author      Jeison Cardona Gómez
 * @copyright   Jeison Cardona Gómez - <jeison.cardona@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @module      block_ases/general_modal_manager
 */


define([
    'jquery',
    'block_ases/mustache',
    'block_ases/sweetalert'
], function ($, mustache, sweetalert) {

    // General Modal Template
    /**
     * @see _general_nmodal_manager.mustache
     */

    let template_location = "../templates/_general_modal_manager.mustache";
    let css_location = "../style/_general_modal_manager.css";
    
    $('head').append('<link rel="stylesheet" href="' + css_location + '" type="text/css" />');


    let show_modal = function ( modal_selector, animation_time = 300, animation = "fadeIn" ){

        let available_animations =  [ "fadein", "show" ];
        let default_animation =     "fadein";
        let input_animation =       animation.toString().toLowerCase();
        let animation_selected =    null;

        if( available_animations.indexOf( input_animation ) === -1 ){
            animation_selected = default_animation;
        };

        switch ( animation_selected ) {
            case "fadein":
                $( modal_selector ).fadeIn( animation_time );
                break;
            case "show":
                $( modal_selector ).show( animation_time );
                break;
        };        
        
    };
    

    let generate_modal = function( selector_class_name, m_title, m_body, m_class = "", callback = function(){} ){
        
        $.ajax({
            url: template_location,
            data: null,
            dataType: "text",
            success: function( template ){

                let modal_data = {
                    general_modal_title: m_title,
                    general_modal_body: m_body,
                    general_modal_class: selector_class_name + " " + m_class
                };
                
                let html_modal = mustache.render(  template, modal_data );

                $("body").append( html_modal );

                $(".general_modal_close").unbind();

                callback();
            },
            error: function(){
                console.log( template_location + " cannot be reached. " );
            }
          });

    };

    $( document ).on( "click", ".general_modal_close", function(){
        let general_modal = $(this)
                                    .parent() // general_modal_header
                                    .parent() // general_modal_content
                                    .parent(); // general_modal

        general_modal.fadeOut(300, function(){
                    general_modal.remove();
                });

    } );

    $( document ).on( "click", ".general_modal_outside", function(){
        let outside = $(this);
        swal(
            {
                title: 'Confirmación de salida',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Salir'
            }, function(isConfirm) {
                if ( isConfirm ) {
                    outside.parent(".general_modal").fadeOut(300);
                }
            }
        );
    } );

    console.log( "General Modal Manager initialised" );

    return {
        generate_modal: generate_modal,
        show_modal: show_modal
    };
});