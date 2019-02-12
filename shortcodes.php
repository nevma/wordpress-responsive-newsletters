<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

function mnltr_shortcodes_init() {

	$shortcodes = mnltr_shortcodes_get_skin_shortcodes();

	if ( ! $shortcodes ) {
		return;
	}

	if ( mnltr_is_newsletter_edit_screen_get_id() ) {

		add_action( 'admin_init', 'mnltr_shortcodes_add_tinymce_plugin' );		
	
	} elseif ( mnltr_is_single_newsletter_get_id() ) {

		// And add our handler for each one.
		foreach ( $shortcodes as $name => $callback ) {
			add_shortcode( $name, $callback ? $callback : 'mnltr_shortcodes_do_wrapper_shortcode' );
		}
	}

}
add_action( 'init', 'mnltr_shortcodes_init' );

function mnltr_shortcodes_add_tinymce_plugin() {

	// Add the TinyMCE plugin that handles the shortcodes
	add_filter( 'mce_external_plugins', 'mnltr_shortcodes_add_tinymce_plugin_js' );
	add_filter( 'mce_buttons_3', 'mnltr_shortcodes_add_tinymce_buttons' );

	// Print them to JS var so that they're available to the plugin
	add_action( 'admin_head-post.php', 'mnltr_shortcodes_admin_head' );

}

function mnltr_shortcodes_add_tinymce_plugin_js( $plugin_array ) {

	$plugin_array['mnltrskinshortcodes'] = mnltr_get_plugin_dir_uri() . 'js/mnltr_tinymce_skin_shortcodes.js';

	return $plugin_array;

}

function mnltr_shortcodes_add_tinymce_buttons( $buttons ) {

	$shortcodes = mnltr_shortcodes_get_skin_shortcodes();

	$buttons = array_merge( $buttons, array_keys( $shortcodes ) );

	return $buttons;

}

function mnltr_shortcodes_admin_head() {

	$shortcodes = mnltr_shortcodes_get_skin_shortcodes();

	?><script type = "text/javascript">
		var mnltr_skin_shortcodes = <?php echo json_encode( array_keys( $shortcodes ) ); ?>;
	</script><?php

}

function mnltr_shortcodes_get_skin_shortcodes() {

	static $shortcodes;

	if ( $shortcodes === null ) {

		// Get the filtered shortcodes from the newsletter skin
		$shortcodes = apply_filters( 'mnltr_shortcodes', array() );

		if ( ! is_array( $shortcodes ) ) {

			$shortcodes = array( $shortcodes );
		}

		$shortcodes = mnltr_array_mixed_to_assoc( $shortcodes );

		// Sanitize shortcode names
		$shortcodes = mnltr_shortcodes_sanitize( $shortcodes );
	}

	return $shortcodes;

}

function mnltr_shortcodes_do_wrapper_shortcode( $atts, $content, $shortcode ) {

	return '<div class = "' . $shortcode . '">' . mnltr_shortcodes_wpautopbr( do_shortcode( $content ) ) . '</div>';

}

/**
 * Takes a string which is meant to have been produced by TinyMCE and 
 * possibly contains shortcodes and removes any extraneous opening and 
 * closing paragraphs or breaking lines from its beginning or end.
 * 
 * @param string $content The input string to clean.
 * 
 * @return string The input string cleaned.
 */
function mnltr_shortcodes_wpautopbr ( $content ) {

	$regexp = 
		'/'.
			 '^\s*<p>\s*<\/p>\s*'  . '|' .
			 '^\s*<p>\s*'          . '|' . 
			 '^\s*<\/p>\s*'        . '|' . 
			 '^\s*<br\s*\/>\s*'    . '|' . 
			  '\s*<br\s*\/>\s*$'   . '|' . 
			  '\s*<p>\s*$'         . '|' . 
			  '\s*<\/p>\s*$'       . '|' . 
			  '\s*<p>\s*<\/p>\s*$' . 
		'/';

	return preg_replace( $regexp, '', $content );

}

/**
 * Shortcode names must never contain the following characters:
 * Square braces: [ ]
 * Angle braces: < >
 * Ampersand: &
 * Forward slash: /
 * Whitespace: space linefeed tab
 * Non-printing characters: \x00 - \x20
 * 
 * It is recommended to also avoid quotes ('") in the names of shortcodes.
 * 
 * @param  [type] $shortcode_name [description]
 * @return [type]            [description]
 */
function mnltr_shortcodes_sanitize_shortcode_name( $shortcode_name ) {

	if ( ! is_string( $shortcode_name ) ) {
		return '';
	}

	// Remove not allowed printable characters
	$shortcode_name = str_replace( array( '[', ']', '<', '>', '&', '/', '\'', '"' ), '', $shortcode_name );

	// Replace (multiple) occurences of whitespace with underscores
	$shortcode_name = preg_replace( '/\s+/', '_', $shortcode_name );

	// Remove non-printable characters
	$shortcode_name = preg_replace('/[\x00-\x20]/', '', $shortcode_name );


	return $shortcode_name;

}

function mnltr_shortcodes_sanitize( $shortcodes ) {

	$_shortcodes = array();

	foreach ( $shortcodes as $name => $callback ) {

		$name = mnltr_shortcodes_sanitize_shortcode_name( $name );

		if ( $name && ( is_string( $callback ) || is_null( $callback ) ) ) {
			$_shortcodes[ $name ] = $callback;
		}
	}

	return $_shortcodes;

}

?>