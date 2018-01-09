<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

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

?>