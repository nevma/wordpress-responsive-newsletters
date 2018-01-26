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
            'editor' 
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
     */

    function mnltr_cpt_tinymce_autogrow () { 

        // Only run for the newsletters post type. 

        $current_screen = get_current_screen();

        if ( $current_screen->parent_base == 'edit.php' && 
             $current_screen->post_type   == 'mnltr_newsletter' ) {
            return;
        } ?>

        <script type = "text/javascript">
            jQuery( function ( $ ) {

                // Sets the outer editor height to be same as the editor contents height.

                function mnltr_cpt_fix_editor_height ( editor ) {

                    var $iframe = $( editor.iframeElement );
                    var $body = $( $iframe.get( 0 ).contentDocument.documentElement ).find( 'BODY' );
                    var height = $body.outerHeight( true );
                    
                    $iframe.height( height );

                }

                // Fix editor height when it is initialised and when its contents change.

                tinymce.on( 'SetupEditor', function ( editor ) {

                    console.log( editor.id );

                    if ( editor.id.indexOf( 'acf-editor-' ) < 0 ) {
                        return;
                    }

                    editor.on( 'init', function ( event ) {
                        mnltr_cpt_fix_editor_height( editor );
                    });

                    editor.on( 'change', function ( event ) {
                        mnltr_cpt_fix_editor_height( editor );
                    });

                });
                
            });
        </script> <?php

    };

?>