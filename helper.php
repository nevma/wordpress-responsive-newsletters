<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) { 
	exit; 
} 



function mnltr_get_options() {

	return get_option( 'mnltr_options' );
}

function mnltr_save_options( $options ) {

	update_option( 'mnltr_options', $options, false );
}

function mnltr_get_option( $option ) {

	$options = mnltr_get_options();

	return isset( $options[ $option ] ) ? $options[ $option ] : false;
}

function mnltr_save_option( $option, $value ) {

	if ( ! is_string( $option ) ) {

		return false;
	}

	$options = mnltr_get_options();

	if ( ! is_array( $options ) ) {

		$options = array();
	}

	$options[ $option ] = $value;

	mnltr_save_options( $options );
}

function mnltr_get_plugin_name() {

	return 'Modular Newsletters';
}

function mnltr_get_newsletter_cpt_name() {

	return 'mnltr_newsletter';
}

function mnltr_get_default_editor_css_filename() {

	return 'editor.css';
}

function mnltr_get_plugin_dir_path() {

	return plugin_dir_path( __FILE__ );
}

function mnltr_get_plugin_dir_uri() {

	// plugin_dir_uri's returned value isn't trailing slashed
	return trailingslashit( plugin_dir_url( __FILE__ ) );
}

function mnltr_get_templates_dir_path() {

	return mnltr_get_plugin_dir_path() . 'templates/';
}

function mnltr_get_templates_dir_uri() {
	return mnltr_path_to_uri( mnltr_get_templates_dir_path() );
}

function mnltr_plugin_file() {

	return mnltr_get_plugin_dir_path() . 'modular-newsletters.php';
}

function mnltr_log_file() {

	return mnltr_get_plugin_dir_path() . 'mnltr_log.txt';
}

function mnltr_get_requested_url() {

	$protocol = ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? "https://" : "http://";

	return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function mnltr_admin_notices( $message = null, $notice_class = 'info', $is_dismissible = true ) {

	static $notices;

	// Initialize notices array on first call
	if ( is_null( $notices ) ) {

		$notices = array();
	}

	// Don't attempt to add a notice if not in admin
	if ( ! is_admin() ) {

		return;
	}

	// If no message is added, return the stored notices
	if ( is_null( $message ) ) {

		return $notices;
	}

	// Proceed and add the notice to the array
	$message = mnltr_get_plugin_name() . ': ' . $message;

	$notice_class = in_array( $notice_class, array( 'error', 'warning', 'success', 'info' ) ) ? 'notice-' . $notice_class : '';

	$notice_class .= $is_dismissible ? ' is-dismissible' : '';


	$notices[] = '<div class = "notice ' . $notice_class . '"</p>' . $message . '</p></div>';
}

function mnltr_admin_notices_echo() {

	$notices = mnltr_admin_notices();

	foreach ( $notices as $notice ) {
		echo $notice;
	}
}

add_action( 'admin_notices', 'mnltr_admin_notices_echo' );

function mnltr_uri_to_path( $uri ) {

	// URI points somewhere under themes directory
	$theme_root_path = get_theme_root();
	$theme_root_uri = get_theme_root_uri();

	if ( 0 === strpos( $uri, $theme_root_uri ) ) {

		$path = $theme_root_path . substr( $uri, strlen( $theme_root_uri ) );
		return $path;
	}

	// URI points somewhere under plugins directory
	$plugin_dir_path = mnltr_get_plugin_dir_path();
	$plugin_dir_uri = mnltr_get_plugin_dir_uri();

	if ( 0 === strpos( $uri, $plugin_dir_uri ) ) {

		$path = $plugin_dir_path . substr( $uri, strlen( $plugin_dir_uri ) );

		return $path;
	}
}

function mnltr_path_to_uri( $path ) {

	// Path points somewhere under themes directory
	$theme_root_path = get_theme_root();
	$theme_root_uri = get_theme_root_uri();

	if ( 0 === strpos( $path, $theme_root_path ) ) {

		$uri = $theme_root_uri . substr( $path, strlen( $theme_root_path ) );
		return $uri;
	}

	// Path points somewhere under plugins directory
	$plugin_dir_path = mnltr_get_plugin_dir_path();
	$plugin_dir_uri = mnltr_get_plugin_dir_uri();

	if ( 0 === strpos( $path, $plugin_dir_path ) ) {

		$uri = $plugin_dir_uri . substr( $path, strlen( $plugin_dir_path ) );

		return $uri;
	}
}



/**
 * Checks whether a string starts with another string
 *
 * @param string $haystack
 *        	The string to search in
 * @param string $needle
 *        	The string to look for
 * @return boolean Returns TRUE if the haystack starts with the needle, or FALSE otherwise
 */
function mnltr_str_startswith( $haystack, $needle ) {

	return substr( $haystack, 0, strlen( $needle ) ) === $needle;
}



/**
 * Replaces exactly one occurence of a string, if and only if it is found at the beginning of another string
 *
 * @param string $search
 *        	The string to search for, otherwise known as the needle.
 * @param string $replace
 *        	The string that replaces $search.
 * @param string $subject
 *        	The string being searched and replaced on, otherwise known as the haystack.
 * @return bool|string Returns a string with the replaced value, or FALSE if any of the arguments is not a string.
 */
function mnltr_str_replace_at_beginning( $search, $replace, $subject ) {

	if ( ! is_string( $search ) || ! is_string( $replace ) ||
		 ! is_string( $subject ) ) {
		return false;
	}

	if ( mnltr_str_startswith( $subject, $search ) ) {
		$subject = $replace . substr( $subject, strlen( $search ) );
	}

	return $subject;
}



/**
 * Converts an array which contains both indexed and keyed values to an associative one.
 * More specifically, if a key is an integer (which is the indexed case), the
 * corresponding value is transformed to a key.
 *
 * For example, consider this source array definition:
 * array(
 *     'str1',
 *     'str2' => 'str3',
 *     'str4',
 *     'str5'
 * );
 *
 * This is represented as follows, in [key] => [value] pairs:
 * [0] => 'str1'
 * ['str2'] => 'str3'
 * [1] => 'str4'
 * [2] => 'str5'
 *
 * This function will convert this array to this structure:
 * ['str1'] => null
 * ['str2'] => 'str3'
 * ['str4'] => null
 * ['str5'] => null
 * 
 * @param  array $arr The array to be processed
 * @return array The processed array
 */
function mnltr_array_mixed_to_assoc( $arr ) {

	$_arr = array();

	foreach( $arr as $key => $val ) {

		if ( is_int( $key ) ) {

			$_arr[ $val ] = null;

		} else {

			$_arr[ $key ] = $val;
		}
	}

	return $_arr;
}



function mnltr_debug( $thing ) {

	if ( ! isset( $_GET[ 'mnltr_debug' ] ) || $_GET[ 'mnltr_debug' ] !== 'true' ) {
		return;
	}

	$debug_mode = 'echo';

	if ( is_string( $thing ) ) {

		$data = $thing;

	} elseif ( is_array( $thing ) ) {

		$data = print_r( $thing, true );

	} else {

		ob_start();
		var_dump( $thing );
		$data = ob_get_clean();
	}

	if ( 'file' === $debug_mode ) {

		file_put_contents( mnltr_log_file(), $data, FILE_APPEND );

	} elseif ( 'echo' === $debug_mode ) {

		echo '<pre>';
		echo "$data\n";
		echo '</pre>';
	}
}



function mnltr_is_newsletter_edit_screen_get_id() {


	// Keep the result within the request.
	static $post_id;

	if ( $post_id === null ) {

		if ( is_admin() ) {

			global $pagenow;

			if ( $pagenow == 'post-new.php'
				&& isset( $_GET['post_type'] ) && $_GET['post_type'] === mnltr_get_newsletter_cpt_name() ) {

				$post_id = true;

			} elseif ( $pagenow == 'post.php'
				&& isset( $_GET['action'] ) && $_GET['action'] == 'edit'
				&& isset( $_GET['post'] )
				&& ( get_post_type( $_GET['post'] ) == mnltr_get_newsletter_cpt_name() ) ) {

				$post_id = (int) $_GET['post'];

			}
		}

		if ( ! $post_id ) {

			$post_id = false;
		}
	}
	
	return $post_id;
}

function mnltr_is_single_newsletter_get_id() {

	if ( is_admin() ) {

		return false;
	}

	$post_id = (int) url_to_postid( mnltr_get_requested_url() );

	if ( $post_id
		&& get_post_type( $post_id ) == mnltr_get_newsletter_cpt_name() ) {

		return $post_id;
	}

	return false;	
}

function mnltr_maybe_include_skin_functions_file() {


	$post_id = mnltr_is_newsletter_edit_screen_get_id();

	if ( ! is_int( $post_id ) ) {

		$post_id = mnltr_is_single_newsletter_get_id();
	}

	if ( $post_id ) {

		$skin_data = mnltr_cf_get_skin( $post_id );

		if ( isset( $skin_data['functions_path'] ) && file_exists( $skin_data['functions_path'] ) ) {

			include_once $skin_data['functions_path'];
		}
	}
}



/**
 * Prepares a set of CSS files for emogrification, by converting the relative
 * URLs that they contain to absolute ones, and concatenating them to a single
 * string.
 * @param  array|string $file_uris The URLs of the files to prepare.
 * @return string The processed CSS string.
 */
function mnltr_prepare_css_for_emogrification( $file_uris ) {

	// Always handle an array
	if ( ! is_array( $file_uris ) ) {
		$file_uris = array( $file_uris );
	}

	$output = '';

	foreach ( $file_uris as $file_uri ) {

		// Get the file contents
		$file_contents = @file_get_contents( $file_uri );

		// Skip file on error
		if ( false === $file_contents ) {
			continue;
		}

		// Convert any relative URLs to absolute within the file
		$output .= mnltr_css_urls_relative_to_absolute( $file_contents, dirname( $file_uri) );
	}

	return $output;
}



/**
 * Replaces relative urls in CSS properties with absolute ones, so that the rules
 * containing them can be safely converted to inline CSS (emogrified).
 *
 * All property values containing relative URLs will be processed, wether they are
 * enclosed in single/double quotes or not.
 * Property values containing URLs that start with the http(s) protocol, or are in
 * protocol-relative format (//www.example.com) will be ignored.
 *
 * @param string $css The css code to be processed.
 * @param string $css_file_url The root URL of the css file being processed.
 * @return string The processed CSS code.
 */
function mnltr_css_urls_relative_to_absolute( $css, $css_root_url ) {

	$css_root_url = trailingslashit( $css_root_url );

	return preg_replace( '/([:\s]url\([\'"]?)(?![\'"]?(https?:)?\/\/)/', '${1}' . $css_root_url, $css );
}

?>