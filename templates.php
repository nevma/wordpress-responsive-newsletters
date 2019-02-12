<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

function mnltr_templates_filter_single_newsletter_template( $default_template ) {

	if ( is_singular( mnltr_get_newsletter_cpt_name() ) ) {

		return mnltr_get_templates_dir_path() . 'newsletter.php';
	}

	return $default_template;

}

function mnltr_templates_get_core_stylesheet_uri() {

	return mnltr_get_templates_dir_uri() . 'css/style.default.css';

}

function mnltr_templates_get_outlook_stylesheet_path() {

	return mnltr_get_templates_dir_path() . 'css/style.outlook.css';

}

function mnltr_templates_get_newsletter_data( $post_id = null ) {

	if ( $post_id === null ) {
		
		global $post;
		$post_id = $post->ID;
	}

	$skin_data = mnltr_cf_get_skin();

	$newsletter_data = array();

	$newsletter_data['name']      = $skin_data['name'];
	$newsletter_data['skin_path'] = $skin_data['skin_path'];
	$newsletter_data['skin_uri']  = $skin_data['skin_uri'];

	$newsletter_data['stylesheets'] = apply_filters( 'mnltr_newsletter_stylesheets',
		array(
			mnltr_templates_get_core_stylesheet_uri(),
			$skin_data['stylesheet_uri'],
		)
	);

	$newsletter_data['functions_path'] = $skin_data['functions_path'];

	return $newsletter_data;

}

function mnltr_templates_get_editor_stylesheet_uri() {

	return mnltr_get_templates_dir_uri() . 'css/editor.default.css';

}

function mnltr_templates_add_editor_stylesheet( $mce_css ){

    $mce_css .= ', ' . mnltr_templates_get_editor_stylesheet_uri();
    return $mce_css;

}

add_filter( 'mce_css', 'mnltr_templates_add_editor_stylesheet' );

function mnltr_templates_get_skin_path() {
	
	global $newsletter_data;
	return $newsletter_data['skin_path'];

}

function mnltr_templates_get_skin_uri() {
	
	global $newsletter_data;
	return $newsletter_data['skin_uri'];

}

function mnltr_templates_should_emogrify() {
	
	return ! ( isset( $_GET['emogrify'] ) && $_GET['emogrify'] === 'false' );

}

function mnltr_templates_should_debug() {
	
	return isset( $_GET['debug'] ) && $_GET['debug'] === 'true';

}

function mnltr_templates_body_open( $stylesheet_uris = array() ) {

	// No white space at start of document
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" 
			  xmlns:o="urn:schemas-microsoft-com:office:office"
			  xmlns:v="urn:schemas-microsoft-com:vml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset = UTF-8" />
				<meta name="viewport" content="width=device-width"/>
				<title><?php echo apply_filters( 'mnltr_newsletter_title', get_the_title() ); ?></title>
				<?php if ( ! mnltr_templates_should_emogrify() ) : ?>
					<?php foreach( $stylesheet_uris as $stylesheet ) : ?>
						<link rel="stylesheet" type="text/css" href="<?php echo $stylesheet; ?>"/>
					<?php endforeach; ?>
					<style type="text/css">
						<?php echo mnltr_templates_get_structural_css(); ?>
					</style>
				<?php endif; ?>
				<?php do_action( 'mnltr_head' ); ?>
			</head>
			<body class="<?php echo mnltr_templates_should_debug() ? 'debug' : '' ?>">
				<?php do_action( 'mnltr_after_body_open' ); ?>
				<center>
					<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="body-table">
						<tr>
							<td align="center" valign="top">
								<?php do_action( 'mnltr_before_email_table' ); ?>
								<table border="0" cellpadding="0" cellspacing="0" id="email-table" width="<?php echo MNLTR_NEWSLETTER_WIDTH; ?>">
	<?php

}

function mnltr_templates_body_close() { ?>

								</table><!-- #email-table -->
								<?php do_action( 'mnltr_after_email_table' ); ?>
							</td>
						</tr>
					</table><!-- #body-table -->
				</center>
				<?php do_action( 'mnltr_before_body_close' ); ?>
				<?php 
					if ( is_user_logged_in() && apply_filters( 'mnltr_allow_debug', true ) ) {
						include mnltr_get_templates_dir_path() . 'debug.php'; 
					}
				?>
			</body>
		</html><?php // No white space at end of document

}

function mnltr_templates_row_open( $row_classes = '' ) {

	static $row_counter;

	$row_counter++;

	do_action( 'mnltr_before_row_open', $row_counter );

	if ( is_array( $row_classes ) ) {

		$row_classes = implode( ' ', $row_classes );
	}
	?>

	<!-- ROW  -->
	<tr>
		<td align="center" valign="top" class="row row-<?php echo $row_counter; ?> <?php echo $row_classes; ?>">
			<!-- CENTERING TABLE -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center" valign="top">
						<!-- FLEXIBLE CONTAINER -->
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align="center" valign="top" width="100%">

	<?php do_action( 'mnltr_after_row_open', $row_counter );

}

function mnltr_templates_row_close() {

	static $row_counter;

	$row_counter++;

	do_action( 'mnltr_before_row_close', $row_counter );

	?>

								</td>
							</tr>
						</table>
						<!-- .flexibleContainer -->
					</td>
				</tr>
			</table>
			<!-- // CENTERING TABLE -->
		</td>
	</tr>
	<!-- /ROW -->

	<?php

	do_action( 'mnltr_after_row_close', $row_counter );

}

function mnltr_templates_column( $content, $width_factor = '1/1', $column_order=null ) {

	switch ( $width_factor ) {
		case '1/1':
			$width_pixels     = MNLTR_NEWSLETTER_WIDTH;
			$width_percentage = 100;
		break;
		case '1/2':
			$width_pixels     = MNLTR_NEWSLETTER_WIDTH/2;
			$width_percentage = 50;
		break;
		case '1/3':
			$width_pixels     = MNLTR_NEWSLETTER_WIDTH/3;
			$width_percentage = 33;
		break;
		case '2/3':
			$width_pixels     = (MNLTR_NEWSLETTER_WIDTH*2)/3;
			$width_percentage = 66;
		break;
	}

	do_action( 'mnltr_before_column_open', $width_factor );

	?>
	<!-- COLUMN -->
	<table align="left" border="0" cellpadding="0" cellspacing="0" width="<?php echo $width_pixels . ''; ?>" class="column-container">
		<tr>
			<td class="column <?php echo isset( $column_order ) ? 'column-' . $column_order : '' ?> column-<?php echo floor( $width_percentage ); ?>" valign="top">
				<div class="column-content">
					<?php
						do_action( 'mnltr_after_column_open', $width_factor );
						echo apply_filters( 'mnltr_column_content', $content, $width_factor );
						do_action( 'mnltr_before_column_close', $width_factor );
					?>
				</div>
			</td>
		</tr>
	</table>
	<!-- /COLUMN -->
	<?php

	do_action( 'mnltr_after_column_close', $width_factor );

}

function mnltr_templates_get_structural_css() {

	$css = '';

	$css .= '#email-table { width: ' . MNLTR_NEWSLETTER_WIDTH . 'px; }' . "\n";
	$css .= '.column { padding-left: ' . MNLTR_NEWSLETTER_GUTTER . 'px; padding-right: ' . MNLTR_NEWSLETTER_GUTTER . 'px; }';

	return $css;
	
}



/**
 * Calculates the usable width of a newsletter column in pixels. Useful mostly
 * for IMG elements whose width attributes need to be set in order for Outlook
 * to render them as requested.
 * 
 * @param string  $width_factor A string that represents the ratio of the 
 * 								current newsletter column. For instance '1/1',
 * 								'1/2', '1/3', '2/3', etc.
 * 
 * @param boolean $compact      Whether the calculation will take into account
 * 								the newsletter gutters or not. If the gutters
 * 							    are not taken into account then the calculated
 * 								width will take up the whole width of a column. 
 */
function mnltr_templates_get_width_pixels( $width_factor, $compact=FALSE ) {

	switch ( $width_factor ) {
		case '1/1':
			return MNLTR_NEWSLETTER_WIDTH*1     - ( $compact ? 0 : 2*MNLTR_NEWSLETTER_GUTTER );
		break;
		case '1/2':
			return MNLTR_NEWSLETTER_WIDTH/2     - ( $compact ? 0 : 2*MNLTR_NEWSLETTER_GUTTER );
		break;
		case '1/3':
			return MNLTR_NEWSLETTER_WIDTH/3     - ( $compact ? 0 : 2*MNLTR_NEWSLETTER_GUTTER );
		break;
		case '2/3':
			return (MNLTR_NEWSLETTER_WIDTH*2)/3 - ( $compact ? 0 : 2*MNLTR_NEWSLETTER_GUTTER );
		break;
	}

}



/**
 * Returns the class used in image wrapping elements, where the wrapped IMG
 * element is required to take up its natural size, as set by the author inside
 * TinyMCE, and not be automatically resized by the framework. 
 */
function mnltr_templates_get_natural_width_image_class () {

	return 'mnltr-natural-width-image';

}



/**
 * Returns the class used in image wrapping elements, where the wrapped IMG
 * element is required to take up the whole width of its column automatically. 
 */
function mnltr_templates_get_compact_width_image_class () {

	return 'mnltr-compact-width-image';

}



/**
 * Returns the class used for the general text vertical spacer element.
 */
function mnltr_templates_get_general_spacer_class () {

	return 'mnltr-spacer';

}

/**
 * Returns the class used specifically for the spacing tables inside text
 * because Outlook has trouble setting vertical margins in tables.
 */
function mnltr_templates_get_table_spacer_class () {

	return 'mnltr-spacer-table';

}



function mnltr_templates_get_general_spacer_html () {

	return '
		<table class="' . mnltr_templates_get_general_spacer_class() . '" border="0" cellpadding="0" cellspacing="0" width="100%" style="width: 100% !important; margin: 0 !important; padding: 0 !important;"> 
			<tr>
				<td width="100%">&nbsp;</td>
			</tr>
		</table>
	';

}



function mnltr_templates_get_table_spacer_html () {

	return '
		<table class="' . mnltr_templates_get_table_spacer_class() . '" border="0" cellpadding="0" cellspacing="0" width="100%" style="width: 100% !important; margin: 0 !important; padding: 0 !important;"> 
			<tr>
				<td width="100%">&nbsp;</td>
			</tr>
		</table>
	';

}



function mnltr_templates_get_wysiwyg_content( $field_name ) {

	return '<div class="' . apply_filters( 'mnltr_wysiwyg_content_classes', 'text' ) . '">' . get_sub_field( $field_name ) . '</div>';

}



function mnltr_templates_fix_img_attributes( $html, $width_factor ) {

	// If an image is marked as naturally sized then do not set its size attribute.

	if ( preg_match( '/<[^>]*\s*class\s*=\s*\"[^\"]*' . mnltr_templates_get_natural_width_image_class() . '[^\"]*\"/', $html ) ) {
		return $html;
	}

	// If an image is marked as compact sized then do not take gutters into account.

	$compact = preg_match( '/<[^>]*\s*class\s*=\s*\"[^\"]*' . mnltr_templates_get_compact_width_image_class() . '[^\"]*\"/', $html );

	$new_width = mnltr_templates_get_width_pixels( $width_factor, $compact );

	$fixed_width_html = preg_replace( '/(<img[^>]*\s*width\s*=\s*\")[^\"]*(\")/', '${1}' . $new_width . '${2}', $html );
	$stripped_height_html = preg_replace( '/(<img[^>]*\s*)height\s*=\s*\"[^\"]*\"/', '$1', $fixed_width_html );

	return $stripped_height_html;

}

add_filter( 'mnltr_column_content', 'mnltr_templates_fix_img_attributes', 10, 2 );



function mnltr_templates_fix_general_spacer( $html ) {

	// Replaces a spacer hr with a table spacer html.

	$spacer = mnltr_templates_get_general_spacer_html();

	$fixed_html = preg_replace( '/(<hr[^>]*\s*class\s*=\s*\"' . mnltr_templates_get_general_spacer_class() . '(\")[^>]*\s*\/>)/', $spacer, $html );

	return $fixed_html;

}

add_filter( 'mnltr_column_content', 'mnltr_templates_fix_general_spacer', 11, 2 );



function mnltr_templates_fix_tables_spacer( $html ) {

	// Insert a spacer in front of each table inside text.

	$spacer = mnltr_templates_get_table_spacer_html();

	$fixed_html = preg_replace( '/(<table)/', $spacer . '${1}', $html );

	return $fixed_html;

}

// add_filter( 'mnltr_column_content', 'mnltr_templates_fix_tables_spacer', 10, 2 );

?>