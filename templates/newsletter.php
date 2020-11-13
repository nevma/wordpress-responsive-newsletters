<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

/*
 * Disable real user monitoring (RUM) of New Relic, if the extension is active..
 * Otherwise it can potentially inject JS code into the final HTML output, which
 * can cause 3rd party mass mail services to reject the e-mail content as potentially
 * unsafe.
 */
if ( extension_loaded( 'newrelic' ) && function_exists( 'newrelic_disable_autorum' ) ) {
    newrelic_disable_autorum();
}

// Load hacks targeting Microsoft Outlook
if ( apply_filters( 'mnltr_apply_mso_custom_markup', true ) ) {
	include 'outlook.php';
}

the_post();

// Gather newsletter-wide data
global $newsletter_data;
$newsletter_data = mnltr_templates_get_newsletter_data();

// Maybe make a dummy call to wp_head() and wp_footer() to trick plugins that will do it themselves otherwise, spoiling the output.
if ( apply_filters( 'mnltr_do_dummy_head_footer', false ) ) {
	ob_start();
	wp_head();
	wp_footer();
	ob_clean();
}

// Buffer the output.
ob_start();

	mnltr_templates_body_open( $newsletter_data['stylesheets'] );

		do_action( 'mnltr_before_all_layouts' );

		if ( have_rows( 'mnltr_rows' ) ) {

			while ( have_rows( 'mnltr_rows' ) ) { the_row();

				do_action( 'mnltr_before_layout', get_row_layout() );

				$layout_classes = array(
					'layout',
					'layout-' . get_row_layout(),
				);

				if ( is_array( get_sub_field( 'mnltr_layout_classes' ) ) ) {
					$layout_classes = array_merge( $layout_classes, get_sub_field( 'mnltr_layout_classes' ) );
				}

				mnltr_templates_row_open( $layout_classes );
				include mnltr_get_templates_dir_path() . 'layout-' . get_row_layout() . '.php';
				mnltr_templates_row_close();

				do_action( 'mnltr_after_layout', get_row_layout() );
			}
		}

		do_action( 'mnltr_after_all_layouts' );

	mnltr_templates_body_close();

// Get the buffered output.
$html = ob_get_clean();

if ( mnltr_templates_should_emogrify() ) {

	// Concatenate all css files.
	$css = mnltr_prepare_css_for_emogrification( $newsletter_data['stylesheets'] );
	$css .= mnltr_templates_get_structural_css();
	$emogrifier = new \Pelago\Emogrifier( $html, $css );
	$html = $emogrifier->emogrify();
	
	$html = apply_filters( 'mnltr_html', $html );
	
}

echo $html;

?>