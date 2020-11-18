<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

/**
 * Returns an array containing all built-in and custom skins, after they have been validated.
 *
 * @return array The array of skins. Each element is a skin data array.
 */
function mnltr_skins_get_skins() {

	return array_merge( mnltr_skins_get_built_in_skins(), 
		mnltr_skins_get_custom_skins() );
}

/**
 * Returns an array containing all built-in skins, after they have been validated.
 *
 * @return array The array of skins. Each element is a skin data array.
 */
function mnltr_skins_get_built_in_skins() {

	return mnltr_skins_get_skins_in_container( 
		mnltr_skins_get_built_in_skin_container_path() );
}

/**
 * Returns an array containing all custom skins (those defined within the theme), after they have been validated.
 *
 * @return array The array of skins. Each element is a skin data array.
 */
function mnltr_skins_get_custom_skins() {

	return mnltr_skins_get_skins_in_container( 
		mnltr_skins_get_custom_skin_container_path() );
}

/**
 * Returns the absolute path of the directory where the plugin will look for built-in skins.
 *
 * Note: Contains a trailing slash.
 *
 * @return string The directory path.
 */
function mnltr_skins_get_built_in_skin_container_path() {

	return trailingslashit( 
		mnltr_get_plugin_dir_path() .
			 mnltr_skins_get_built_in_skin_container_dir() );
}

/**
 * Returns the URI of the directory where the plugin will look for built-in skins.
 *
 * Note: Contains a trailing slash.
 *
 * @return string The directory URI.
 */
function mnltr_skins_get_built_in_skin_container_uri() {

	return trailingslashit( 
		mnltr_get_plugin_dir_uri() .
			 mnltr_skins_get_built_in_skin_container_dir() );
}

/**
 * Returns the absolute path of the directory where the plugin will look for custom skins, within the theme.
 *
 * Note: Contains a trailing slash.
 *
 * @return string The directory path.
 */
function mnltr_skins_get_custom_skin_container_path() {

	return trailingslashit( 
		get_stylesheet_directory() . '/' .
			 mnltr_skins_get_custom_skin_container_dir() );
}

/**
 * Returns the URI of the directory where the plugin will look for custom skins, within the theme.
 *
 * Note: Contains a trailing slash.
 *
 * @return string The directory URI.
 */
function mnltr_skins_get_custom_skin_container_uri() {

	return trailingslashit( 
		get_stylesheet_directory_uri() . '/' .
			 mnltr_skins_get_custom_skin_container_dir() );
}

/**
 * Returns the name of the directory where the plugin will look for built-in skins.
 *
 * @return string The directory name.
 */
function mnltr_skins_get_built_in_skin_container_dir() {

	return 'skins';
}

/**
 * Returns the name of the directory where the plugin will look for custom skins, within the theme.
 *
 * @return string The directory name.
 */
function mnltr_skins_get_custom_skin_container_dir() {

	return apply_filters( 'mntlr_skins_dir', 'mnltr-skins' );

}

/**
 * Returns an array containing all the skins found in a directory, after they have been validated.
 *
 * @todo handle invalid skins
 * @param string $skin_container
 *        	The absolute path of the container directory, in which to look for valid skins (trailing slash is optional).
 * @return array The array of skins. Each element is a skin data array.
 */
function mnltr_skins_get_skins_in_container( $skin_container ) {

	$skin_container = trailingslashit( $skin_container );
	
	$skin_directories = @glob( $skin_container . '*', GLOB_ONLYDIR | GLOB_MARK );
	
	if ( ! $skin_directories ) {
		
		$skin_directories = array ();
	}
	
	$skins = array ();
	
	foreach ( $skin_directories as $directory ) {
		
		$skin_data = mnltr_skins_get_skin_data( $directory );
		
		if ( is_wp_error( $skin_data ) ) {
		/**
		 *
		 * @todo Handle invalid skin data here.
		 */
		} else {
			$skins[] = $skin_data;
		}
	}

	usort( $skins, function( $skin1, $skin2 ) {
		return $skin1['name'] > $skin2['name'];
	});
	
	return $skins;
}

/**
 * Checks if a given directory contains a valid skin, parses its data, and returns it in the form of an associative array.
 * The array's structure is this:
 * <code>
 * <?php
 * $skin_data = array(
 * 'name' => The skin's name,
 * 'skin_path' => The absolute path to the skin's root directory, including a trailing slash.
 * 'skin_uri' => The URI to the skin's root directory, including a trailing slash.
 * 'type' => Either 'builtin', 'custom' or 'unknown', depending on whether the given directory is a subdirectory of the built-in or custom skin containers, or an unknown one.
 * );
 * ?>
 * </code>
 *
 * @todo decide if absolute skin directories should be returned in errors, instead of relative.
 * @param unknown $skin_directory        	
 * @return WP_Error|mixed
 */
function mnltr_skins_get_skin_data( $skin_directory ) {
	
	// Bail if no functions file exists.
	if ( ! file_exists( 
		mnltr_skins_get_skin_functions_file_path( $skin_directory ) ) ) {
		return new WP_Error( 'mnltr_skins_no_functions_file', 
			'No functions file found in skin.', $skin_directory );
	}
	
	// Bail if no stylesheet file exists.
	if ( ! file_exists( 
		mnltr_skins_get_skin_stylesheet_file_path( $skin_directory ) ) ) {
		return new WP_Error( 'mnltr_skins_no_stylesheet_file', 
			'No stylesheet file found in skin.', $skin_directory );
	}
	
	// Parse data from skin's stylesheet file
	$parsed_skin_data = get_file_data( 
		mnltr_skins_get_skin_stylesheet_file_path( $skin_directory ), 
		array (
			'name' => 'Skin Name',
		) );
	
	// Bail if no name is defined.
	if ( empty( $parsed_skin_data[ 'name' ] ) ) {
		return new WP_Error( 'mnltr_skins_no_skin_name', 
			'No name defined for skin.', $skin_directory );
	}
	
	$parsed_skin_data['skin_path'] = trailingslashit( $skin_directory );
	$parsed_skin_data['skin_uri'] = mnltr_path_to_uri( $skin_directory );
	$parsed_skin_data['stylesheet_uri'] = mnltr_skins_get_skin_stylesheet_uri( $parsed_skin_data['skin_path'] );
	$parsed_skin_data['functions_path'] = mnltr_skins_get_skin_functions_file_path( $skin_directory );
	
	if ( mnltr_str_startswith( $parsed_skin_data[ 'skin_path' ], 
		mnltr_skins_get_built_in_skin_container_path() ) ) {
		
		$parsed_skin_data[ 'type' ] = 'builtin';

	} elseif ( mnltr_str_startswith( $parsed_skin_data[ 'skin_path' ], 

		mnltr_skins_get_custom_skin_container_path() ) ) {
		
		$parsed_skin_data[ 'type' ] = 'custom';
		
	} else {
		
		$parsed_skin_data[ 'type' ] = 'unknown';
	}
	
	// Return valid data
	return $parsed_skin_data;
}


/**
 * Returns the absolute path of a skin's functions PHP file, for a given skin-container directory.
 *
 * Always use this function to retrieve the appropriate file name, as it may change between versions.
 *
 * Note: Does not check if the file actually exists.
 *
 * Note: Contains a trailing slash.
 *
 * @param string $skin_directory
 *        	The absolute path of the skin's container directory
 *        	
 * @return string The absolute path to the functions file.
 */
function mnltr_skins_get_skin_functions_file_path( $skin_directory ) {

	return trailingslashit( $skin_directory ) . 'functions.php';
}

/**
 * Returns the absolute path of a skin's stylesheet, for a given skin-container directory.
 *
 * Always use this function to retrieve the appropriate file name, as it may change between versions.
 *
 * Note: Does not check if the file actually exists.
 *
 * Note: Contains a trailing slash.
 *
 * @param string $skin_directory
 *        	The absolute path of the skin's container directory
 *        	
 * @return string The absolute path to the stylesheet file.
 */
function mnltr_skins_get_skin_stylesheet_file_path( $skin_directory ) {

	return trailingslashit( $skin_directory ) . 'style.css';
}

function mnltr_skins_get_skin_stylesheet_uri( $skin_directory ) {

	return mnltr_path_to_uri( mnltr_skins_get_skin_stylesheet_file_path( $skin_directory) );
}

/**
 * Converts the absolute path of a skin root directory, or any its subdirectories or files, to a shrinked version, convenient to use in HTML forms, for example.
 *
 * The path up to (but not including) the skin's root is replaced with a placeholder, if it matches either the built-in or custom theme container directories.
 *
 * @see mnltr_skins_get_builtin_dir_placeholder() and mnltr_skins_get_custom_dir_placeholder() for the actual placeholder values.
 *     
 * @param string $full_path
 *        	The absolute path to be shrinked.
 *        	
 * @return string The shrinked version of the path, if it pointed under the built-in or custom skin containers, or the original value untouched, otherwise.
 */
function mnltr_skins_shrink_skin_path( $full_path ) {

	$builtin_skin_container = mnltr_skins_get_built_in_skin_container_path();
	$custom_skin_container = mnltr_skins_get_custom_skin_container_path();
	
	if ( mnltr_str_startswith( $full_path, $builtin_skin_container ) ) {
		
		// Replace builtin skin path with an internal tag
		return untrailingslashit( 
			mnltr_str_replace_at_beginning( $builtin_skin_container, 
				mnltr_skins_get_builtin_dir_placeholder(), $full_path ) );
	} elseif ( mnltr_str_startswith( $full_path, $custom_skin_container ) ) {
		
		// Replace custom skin path with an internal tag
		return untrailingslashit( 
			mnltr_str_replace_at_beginning( $custom_skin_container, 
				mnltr_skins_get_custom_dir_placeholder(), $full_path ) );
	} else {
		
		// Touch nothing
		return $full_path;
	}
}

/**
 * Converts a shrinked path of a skin root directory, assuming it was shrunk using mnltr_skins_shrink_skin_path(), back to the original absolute-path version.
 *
 * If the shrinked path starts with a valid placeholder, then the placeholder is replaced by the absolute path of the appropriate directory.
 *
 * @see mnltr_skins_get_builtin_dir_placeholder() and mnltr_skins_get_custom_dir_placeholder() for the actual placeholder values.
 *     
 * @param string $shrinked_path
 *        	The shrinked path to be expanded.
 *        	
 * @return string The expanded version of the path if a valid placeholder was found, or the original value untouched, otherwise.
 */
function mnltr_skins_expand_skin_path( $shrinked_path ) {

	$builtin_dir_placeholder = mnltr_skins_get_builtin_dir_placeholder();
	$custom_dir_placeholder = mnltr_skins_get_custom_dir_placeholder();
	
	if ( mnltr_str_startswith( $shrinked_path, $builtin_dir_placeholder ) ) {
		
		return trailingslashit( 
			mnltr_str_replace_at_beginning( $builtin_dir_placeholder, 
				mnltr_skins_get_built_in_skin_container_path(), $shrinked_path ) );
	} elseif ( mnltr_str_startswith( $shrinked_path, $custom_dir_placeholder ) ) {
		
		return trailingslashit( 
			mnltr_str_replace_at_beginning( $custom_dir_placeholder, 
				mnltr_skins_get_custom_skin_container_path(), $shrinked_path ) );
	} else {
		
		return $shrinked_path;
	}
}

/**
 * Returns the placeholder which represents the built-in skin container directory.
 *
 * @return string The built-in directory placeholder.
 */
function mnltr_skins_get_builtin_dir_placeholder() {

	return 'mnltrbuiltindir_';
}

/**
 * Returns the placeholder which represents the custom skin container directory.
 *
 * @return string The custom directory placeholder.
 */
function mnltr_skins_get_custom_dir_placeholder() {

	return 'mnltrcustomdir_';
}

?>