<?php

    // Exit if accessed directly

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }



    function mnltr_formats_init ( $formats ) {

        // Only run for the newsletters post type. 

        if ( ! mnltr_cpt_is_newsletter_edit_screen() ) {
            return;
        }

        // Initialise formats array if necessary.

        if ( isset( $formats['style_formats'] ) ) {
            $formats_array = json_decode( $formats['style_formats'] );
        } else {
            $formats_array = array();
        }

        // Intended to mark an image as a natural width image, which means that
        // the special which corrects image dimensions to fit the contents of
        // their table cells will leave them in their natural dimensions as
        // entered in the WordPress editor.

        $formats_array []= array(
            'selector' => 'img',
            'title'    => '(mnltr) Natural width image', 
            'block'    => 'p', 
            'classes'  => mnltr_templates_get_natural_width_image_class(),
            'wrapper'  => false
        );

        $formats_array []= array(
            'selector' => 'img',
            'title'    => '(mnltr) Compact width image', 
            'block'    => 'p', 
            'classes'  => mnltr_templates_get_compact_width_image_class(),
            'wrapper'  => false
        );

        $formats_array []= array(
            'selector' => 'hr',
            'title'    => '(mnltr) Spacer', 
            'classes'  => mnltr_templates_get_general_spacer_class(),
            'wrapper'  => false
        );

        $formats_array = apply_filters( 'mnltr_formats_array', $formats_array );

        $formats['style_formats'] = json_encode( $formats_array );  
        
        return $formats; 

    }

    add_action( 'tiny_mce_before_init', 'mnltr_formats_init' );

?>