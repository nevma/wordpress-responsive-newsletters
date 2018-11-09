<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}



function mnltr_cpt_register() {

    $labels = array (
        
        'name' => 'Newsletters',
        'singular_name' => 'Newsletter',
        'menu_name' => 'Newsletters',
        'all_items' => 'All Newsletters',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Newsletter',
        'edit' => 'Edit',
        'edit_item' => 'Edit Newsletter',
        'new_item' => 'New Newsletter',
        'view' => 'View',
        'view_item' => 'View Newsletter',
        'search_items' => 'Search Newsletter',
        'not_found' => 'No Newsletters found',
        'not_found_in_trash' => 'No Newsletters found in Trash',
        'parent' => 'Parent Newsletter' 
    );
    
    $args = array (
        
        'labels' => $labels,
        'description' => '',
        'public' => true,
        'show_ui' => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'exclude_from_search' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array (
            'slug' => 'newsletter',
            'with_front' => true 
        ),
        'query_var' => true,
        'supports' => array (
            'title',
            'revisions',
            'custom-fields',
            'author' 
        ) 
    );
    
    if ( is_wp_error( register_post_type( mnltr_get_newsletter_cpt_name(), $args ) ) ) {

        mnltr_admin_notices( 'Failed to register Newsletter custom post type.', 'error' );

        return false;
    }

    return true;
}

function mnltr_cpt_unregister() {

    global $wp_post_types;
    
    $post_type = mnltr_get_newsletter_cpt_name();
    
    if ( isset( $wp_post_types[ $post_type ] ) ) {
        
        unset( $wp_post_types[ $post_type ] );
        
        return true;
    }
    return false;
}

function mnltr_cpt_registered() {

    return post_type_exists( mnltr_get_newsletter_cpt_name() );
}

function mnltr_cpt_flush_rewrite_rules() {

    mnltr_cpt_register();
    flush_rewrite_rules( false );
}




    /**
     * Makes the TinyMCE editor in the edit page of the newsletters custom post
     * type automatically grow or shrink to the size of its contents. This way
     * the editor produces no scrollbars, which is very handy when editing the
     * content of small -or not so small- newsletter columns. 
     * 
     * @return void
     */

    function mnltr_cpt_tinymce_autogrow () { 

        // Only run for the newsletters post type. 

        $current_screen = get_current_screen();

        if ( $current_screen->parent_base != 'edit' || 
             $current_screen->post_type   != mnltr_get_newsletter_cpt_name() ) {
            return;
        } ?>

        <script type = "text/javascript" src = "<?php echo mnltr_get_plugin_dir_uri() . 'js/mnltr_cpt_tinymce_autogrow.js'; ?>"></script> <?php

    };



    /**
     * Adds custom TinyMCE editor styles via a predefined editor.css file which
     * may be found inside the current skin's directory. 
     * 
     * @return void
     */

    function mnltr_cpt_add_editor_styles ( $stylesheets ) {

        // Only run for the newsletters post type. 

        $current_screen = get_current_screen();

        if ( $current_screen->parent_base != 'edit' || 
             $current_screen->post_type   != mnltr_get_newsletter_cpt_name() ) {
            return $stylesheets;
        }

        global $post;

        $newsletter_data = mnltr_templates_get_newsletter_data( $post->ID );
        $editor_file_css = trailingslashit( $newsletter_data['skin_path'] ) . mnltr_get_default_editor_css_filename();


        if ( file_exists( $editor_file_css ) ) {
            $stylesheets .= ',' . trailingslashit( $newsletter_data['skin_uri'] ) . mnltr_get_default_editor_css_filename();
        }

        return $stylesheets;

    }

?>