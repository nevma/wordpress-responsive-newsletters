<?php

    // Exit if accessed directly
    
    if( ! defined( 'ABSPATH' ) ) { 
        exit; 
    }



    // Obligatory newsletter constants.

    define( 'MNLTR_NEWSLETTER_WIDTH', 600 );
    define( 'MNLTR_NEWSLETTER_GUTTER', 20 );



    /**********************************************************************************************

        ██████╗  ██████╗ ██╗    ██╗     ██████╗██╗      █████╗ ███████╗███████╗███████╗███████╗
        ██╔══██╗██╔═══██╗██║    ██║    ██╔════╝██║     ██╔══██╗██╔════╝██╔════╝██╔════╝██╔════╝
        ██████╔╝██║   ██║██║ █╗ ██║    ██║     ██║     ███████║███████╗███████╗█████╗  ███████╗
        ██╔══██╗██║   ██║██║███╗██║    ██║     ██║     ██╔══██║╚════██║╚════██║██╔══╝  ╚════██║
        ██║  ██║╚██████╔╝╚███╔███╔╝    ╚██████╗███████╗██║  ██║███████║███████║███████╗███████║
        ╚═╝  ╚═╝ ╚═════╝  ╚══╝╚══╝      ╚═════╝╚══════╝╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝╚══════╝

     **********************************************************************************************/



    // Declare available layout rows classes.

    function nvm_mnltr_layout_classes ( $classes ) {

        $classes['narrow']    = 'Narrow row';
        $classes['narrow-x']  = 'Very narrow row';
        $classes['narrow-xx'] = 'Very-very narrow row';
        $classes['colour']    = 'Coloured row';

        return $classes;

    }

    add_filter( 'mnltr_layout_classes', 'nvm_mnltr_layout_classes' );



    /********************************************************************

        ███████╗ ██████╗ ██████╗ ███╗   ███╗ █████╗ ████████╗███████╗
        ██╔════╝██╔═══██╗██╔══██╗████╗ ████║██╔══██╗╚══██╔══╝██╔════╝
        █████╗  ██║   ██║██████╔╝██╔████╔██║███████║   ██║   ███████╗
        ██╔══╝  ██║   ██║██╔══██╗██║╚██╔╝██║██╔══██║   ██║   ╚════██║
        ██║     ╚██████╔╝██║  ██║██║ ╚═╝ ██║██║  ██║   ██║   ███████║
        ╚═╝      ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝╚═╝  ╚═╝   ╚═╝   ╚══════╝

     ********************************************************************/



    // Declare your custom TinyMCE formats here.

    function nvm_mnltr_formats ( $formats ) {


        // $formats []= array(
        //     'selector' => 'element',
        //     'title'    => 'Title', 
        //     'inline'   => 'span', 
        //     'block'    => 'div', 
        //     'classes'  => 'class',
        //     'wrapper'  => false
        // );
        
        return $formats; 

    }

    add_filter( 'mnltr_formats_array', 'nvm_mnltr_formats' );



    /*********************************************************************

        ██████╗ ██╗   ██╗████████╗████████╗ ██████╗ ███╗   ██╗███████╗
        ██╔══██╗██║   ██║╚══██╔══╝╚══██╔══╝██╔═══██╗████╗  ██║██╔════╝
        ██████╔╝██║   ██║   ██║      ██║   ██║   ██║██╔██╗ ██║███████╗
        ██╔══██╗██║   ██║   ██║      ██║   ██║   ██║██║╚██╗██║╚════██║
        ██████╔╝╚██████╔╝   ██║      ██║   ╚██████╔╝██║ ╚████║███████║
        ╚═════╝  ╚═════╝    ╚═╝      ╚═╝    ╚═════╝ ╚═╝  ╚═══╝╚══════╝

     *********************************************************************/



    // Button shortcode function.

    function nvm_mnltr_button_shortcode( $atts, $content = '' ) {

        preg_match( '/<a.*>(.*)<.*/i', $content, $matches );
        $text = $matches[1];

        preg_match( '/<a.*href="([^"]*)"/i', $content, $matches );
        $url = $matches[1];

        $width         = mb_strlen( $text ) * 12;
        if ( $atts['type'] == 'wide' ) {
            $width = $width*1.5;
        }
        $width_outlook = $width*1.2;
        $height        = 50;
        $color         = '#ffffff';
        $background    = '#666666';

        return '
            <span style="display: inline-block;">
                <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $url . '" style="height:' . $height . 'px;v-text-anchor:middle;width:' . $width_outlook . 'px;" arcsize="8%" stroke="f" fillcolor="' . $background . '">
                    <w:anchorlock/>
                    <center>
                <![endif]-->
                <a href="' . $url . '"
                   title = "' . $text . '"
                   style="background-color:' . $background . ';color:' . $color . ';line-height:' . $height . 'px;width:' . $width . 'px;border-radius:3px;display:inline-block;text-align:center;text-decoration:none;-webkit-text-size-adjust:none;">' . 
                    $text . 
                '</a>
                <!--[if mso]>
                    </center>
                </v:roundrect>
                <![endif]-->
            </span>
        ';
            
    }



    // Register button shortcode.

    function nvm_mnltr_button_shortcode_register( $shortcodes ) {
        
        return array_merge( $shortcodes, array(
            'mnltr-button' => 'nvm_mnltr_button_shortcode'
        ));

    }

    add_filter( 'mnltr_shortcodes', 'nvm_mnltr_button_shortcode_register' );



    /****************************************************************************************************

        ██████╗  █████╗  ██████╗██╗  ██╗ ██████╗ ██████╗  ██████╗ ██╗   ██╗███╗   ██╗██████╗ ███████╗
        ██╔══██╗██╔══██╗██╔════╝██║ ██╔╝██╔════╝ ██╔══██╗██╔═══██╗██║   ██║████╗  ██║██╔══██╗██╔════╝
        ██████╔╝███████║██║     █████╔╝ ██║  ███╗██████╔╝██║   ██║██║   ██║██╔██╗ ██║██║  ██║███████╗
        ██╔══██╗██╔══██║██║     ██╔═██╗ ██║   ██║██╔══██╗██║   ██║██║   ██║██║╚██╗██║██║  ██║╚════██║
        ██████╔╝██║  ██║╚██████╗██║  ██╗╚██████╔╝██║  ██║╚██████╔╝╚██████╔╝██║ ╚████║██████╔╝███████║
        ╚═════╝ ╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝  ╚═╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═══╝╚═════╝ ╚══════╝

     ****************************************************************************************************/



    // Open a row with a background colour.

    function nvm_mnltr_row_background_color_open ( $css_args=array() ) {

        $css = array_merge( array(
            'color'   => '#ffffff',
            'padding' => '30px'
        ), $css_args );

        return '
            <table class="mnltr-spacer-exception" border="0" cellpadding="' . $css['padding'] . '" cellspacing="0" width="100%"> 
                <tr>
                    <td width="100%" bgcolor="' . $css['color'] . '" style="width: 100%; padding: ' . $css['padding'] . '; background-color: ' . $css['color'] . ';">
        ';
        
    }



    // Close a row with a background colour.

    function nvm_mnltr_row_background_color_close () {

        return '
                    </td>
                </tr>
            </table>
        ';
        
    }



    /*******************************************************

        ██╗  ██╗███████╗ █████╗ ██████╗ ███████╗██████╗
        ██║  ██║██╔════╝██╔══██╗██╔══██╗██╔════╝██╔══██╗
        ███████║█████╗  ███████║██║  ██║█████╗  ██████╔╝
        ██╔══██║██╔══╝  ██╔══██║██║  ██║██╔══╝  ██╔══██╗
        ██║  ██║███████╗██║  ██║██████╔╝███████╗██║  ██║
        ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚═════╝ ╚══════╝╚═╝  ╚═╝

     *******************************************************/



    // Newsletter header.

    function nvm_mnltr_header () {

        $html = '
            <div class="' . mnltr_templates_get_natural_width_image_class() . '">
                <img src = "' . vanilla_theme_get_image_src( get_post_thumbnail_id(), 'large' ) . '" alt="Featured image: ' . get_the_title() . '" width = "' . MNLTR_NEWSLETTER_WIDTH . '" />
            </div>
        ';

        mnltr_templates_row_open( array( 'header', 'header-image', 'nexus', 'compact' ) );
        mnltr_templates_column( $html );
        mnltr_templates_row_close();
        
        $html = 
            nvm_mnltr_row_background_color_open( array( 'color' => '#d3d3d3' ) ) .
                '<div class="text" style="text-align: center;">
                    <h1>' . get_the_title() . '</h1>
                </div>' . 
            nvm_mnltr_row_background_color_close();

        mnltr_templates_row_open( array( 'header', 'header-title', 'nexus', 'compact' ) );
        mnltr_templates_column( $html );
        mnltr_templates_row_close();

    }

    add_action( 'mnltr_before_all_layouts', 'nvm_mnltr_header', 10 );



    /**********************************************************

        ███████╗ ██████╗  ██████╗ ████████╗███████╗██████╗
        ██╔════╝██╔═══██╗██╔═══██╗╚══██╔══╝██╔════╝██╔══██╗
        █████╗  ██║   ██║██║   ██║   ██║   █████╗  ██████╔╝
        ██╔══╝  ██║   ██║██║   ██║   ██║   ██╔══╝  ██╔══██╗
        ██║     ╚██████╔╝╚██████╔╝   ██║   ███████╗██║  ██║
        ╚═╝      ╚═════╝  ╚═════╝    ╚═╝   ╚══════╝╚═╝  ╚═╝

     **********************************************************/


    // Newsletter footer.

    function nvm_mnltr_footer () {

        $html = 
            nvm_mnltr_row_background_color_open( array( 'color' => '#d3d3d3' ) ) . '
                <div class="text">
                    <div class="disclaimer">
                        <p style="text-align: center;">
                            Δεν εµφανίζεται όπως θα περιμένατε το newsletter στην οθόνη σας; <a href="http://#trackingDomain#/show_campaign/#campaign:key#/#recipient:key#/#ab#">Δείτε το στο browser σας</a>. Για να διαγραφείτε από τη λίστα παραληπτών πατήστε <a href = "#unsubscribeLink#"> εδώ.</a>
                        </p>
                    </div>
                    <div class="social">
                        <p style="text-align: center;">
                            <a href = "https://www.facebook.com" title = "Facebook"><img src = "' . mnltr_get_plugin_dir_uri() . 'skins/default/img/facebook.png" alt = "Facebook"></a>
                            <a href = "https://www.twitter.com" title = "Twitter"><img src = "' . mnltr_get_plugin_dir_uri() . 'skins/default/img/twitter.png" alt = "Twitter"></a>
                            <a href = "https://www.instagram.com" title = "Instagram"><img src = ' . mnltr_get_plugin_dir_uri() . 'skins/default/img/instagram.png alt = "Instagram"></a>
                            <a href = "https://www.linkedin.com" title = "Linkedin"><img src = ' . mnltr_get_plugin_dir_uri() . 'skins/default/img/linkedin.png alt = "Linkedin"></a>
                            <a href = "https://www.youtube.com" title = "Youtube"><img src = ' . mnltr_get_plugin_dir_uri() . 'skins/default/img/youtube.png alt = "Youtube"></a>
                        </p>
                    </div>
                </div>' . 
            nvm_mnltr_row_background_color_close();

        mnltr_templates_row_open( array( 'footer', 'nexus', 'compact' ) );
        mnltr_templates_column( $html );
        mnltr_templates_row_close();

    }

    add_action( 'mnltr_after_all_layouts', 'nvm_mnltr_footer', 10 );

?>