<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

function mnltr_templates_filter_single_newsletter_template( $default_template ) {

	if ( is_singular( mnltr_get_newsletter_cpt_name() ) ) {

		return mnltr_get_templates_dir_path() . 'newsletter.php';
	}

	return $default_template;
}

function mnltr_templates_get_core_stylesheet_uri() {

	return mnltr_get_templates_dir_uri() . 'css/front-core.css';
}

function mnltr_templates_get_newsletter_data( $post_id = null ) {

	if ( $post_id === null ) {
		
		global $post;
		$post_id = $post->ID;
	}

	$skin_data = mnltr_cf_get_skin();

	$newsletter_data = array();

	$newsletter_data['skin_path'] = $skin_data['skin_path'];
	$newsletter_data['skin_uri'] = $skin_data['skin_uri'];

	$newsletter_data['stylesheets'] = apply_filters( 'mnltr_newsletter_stylesheets',
		array(
			mnltr_templates_get_core_stylesheet_uri(),
			$skin_data['stylesheet_uri'],
		)
	);

	$newsletter_data['functions_path'] = $skin_data['functions_path'];

	return $newsletter_data;
}

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

function mnltr_templates_body_open( $stylesheet_uris = array() ) {

	// No white space at start of document
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

		<html xmlns = "http://www.w3.org/1999/xhtml">

			<head>

				<meta http-equiv = "Content-Type" content = "text/html; charset = UTF-8" />

				<meta name = "viewport" content = "width=device-width"/>

				<title><?php echo apply_filters( 'mnltr_newsletter_title', get_the_title() ); ?></title>

				<?php if ( ! mnltr_templates_should_emogrify() ) : ?>

					<?php foreach( $stylesheet_uris as $stylesheet ) : ?>

						<link rel = "stylesheet" type = "text/css" href = "<?php echo $stylesheet; ?>"/>

					<?php endforeach; ?>

					<style type = "text/css">

						<?php echo mnltr_templates_get_structural_css(); ?>

					</style>

				<?php endif; ?>

				<?php do_action( 'mnltr_head' ); ?>

			</head>

			<body>
				<center>
					<table border = "0" cellpadding = "0" cellspacing = "0" height = "100%" width = "100%" id = "body-table">
						<tr>
							<td align = "center" valign = "top">
								
								<table border = "0" cellpadding = "0" cellspacing = "0" id = "email-table" width = "<?php echo MNLTR_NEWSLETTER_WIDTH; ?>">
<?php }

function mnltr_templates_body_close() { ?>

							</table><!-- #email-table -->
						</td>
					</tr>
				</table><!-- #body-table -->
			</center>
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
		<td align = "center" valign = "top" class = "row <?php echo $row_classes; ?>">
			<!-- CENTERING TABLE -->
			<table border = "0" cellpadding = "0" cellspacing = "0" width = "100%">
				<tr>
					<td align = "center" valign = "top">
						<!-- FLEXIBLE CONTAINER -->
						<table border = "0" cellpadding = "0" cellspacing = "0" width = "100%">
							<tr>
								<td align = "center" valign = "top" width = "100%">

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

function mnltr_templates_column( $content, $width_factor = 1 ) {

	$width_percentage = mnltr_templates_get_width_percentage( $width_factor );

	do_action( 'mnltr_before_column_open', $width_factor );

	?>
	<!-- COLUMN -->
	<table align = "left" border = "0" cellpadding = "0" cellspacing = "0" width = "<?php echo $width_percentage . '%'; ?>" class = "column-container">
		<tr>
			<td class = "column column-<?php echo floor( $width_percentage ); ?>" valign = "top">
				<div class = "column-content">
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

function mnltr_templates_get_width_pixels( $width_factor ) {

	return floor( MNLTR_NEWSLETTER_WIDTH * $width_factor ) - 2 * MNLTR_NEWSLETTER_GUTTER;
}

function mnltr_templates_get_width_percentage( $width_factor ) {

	return $width_factor * 100;
}


function mnltr_templates_get_wysiwyg_content( $field_name ) {

	return '<div class = "' . apply_filters( 'mnltr_wysiwyg_content_classes', 'text' ) . '">' . get_sub_field( $field_name ) . '</div>';
}

function mnltr_templates_fix_img_attributes( $html, $width_factor ) {

	$new_width = mnltr_templates_get_width_pixels( $width_factor );

	$fixed_width = preg_replace( '/(<img[^>]*\s*width\s*=\s*\")[^\"]*(\")/', '${1}' . $new_width . '${2}', $html );

	$stripped_height = preg_replace( '/(<img[^>]*\s*)height\s*=\s*\"[^\"]*\"/', '$1', $fixed_width );

	return $stripped_height;
}
add_filter( 'mnltr_column_content', 'mnltr_templates_fix_img_attributes', 10, 2 );

?>