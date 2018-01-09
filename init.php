<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

require_once 'custom-fields.php';
require_once 'custom-post-types.php';
require_once 'helper.php';
require_once 'skins.php';
require_once 'templates.php';
require_once 'shortcodes.php';
require_once 'classes/Emogrifier/Emogrifier.php';

// Low priority to make sure the CPT is declared before acf/init
add_action( 'init', 'mnltr_do_init', 0 );
add_action( 'acf/init', 'mnltr_do_acf_init' );

function mnltr_do_init() {

	mnltr_cpt_register();
}

function mnltr_do_acf_init() {

	if ( ! mnltr_cpt_registered() ) {

		mnltr_admin_notices( 'A plugin initialization error occured. Please contact support.', 'error' );

		return;
	}

	if ( mnltr_is_newsletter_edit_screen_get_id() ) {

		mnltr_cf_maybe_update_acf_keys();
	}

	mnltr_maybe_include_skin_functions_file();

	// Register the required Advanced Custom Fields
	mnltr_cf_register_custom_fields();

	// At this point the ACFs and the CPT have been successfully registered, and we can enable template filtering on the front-end
	add_filter( 'template_include', 'mnltr_templates_filter_single_newsletter_template' );
}

?>