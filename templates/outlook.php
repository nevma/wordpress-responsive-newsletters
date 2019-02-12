<?php

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mnltr_outlook_conditional_open() {

	return "\n<!--[if gte mso 9]>";

}

function mnltr_outlook_conditional_close() {

	return "\n<![endif]-->";

}

function mnltr_outlook_open_row_container() {

	echo mnltr_outlook_conditional_open()
	. "\n" . '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">'
	. "\n". '<tr>'
	. mnltr_outlook_conditional_close();

}

function mnltr_outlook_close_row_container() {

	echo mnltr_outlook_conditional_open()
	. "\n". '</tr>'
	. "\n". '</table>'
	. mnltr_outlook_conditional_close();

}

function mnltr_outlook_open_column_container( $width_factor ) {

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

	echo mnltr_outlook_conditional_open()
	. "\n". '<td align="center" valign="top" width="' . $width_pixels . '">'
	. mnltr_outlook_conditional_close();

}

function mnltr_outlook_close_column_container() {

	echo mnltr_outlook_conditional_open()
	. "\n". '</td>'
	. mnltr_outlook_conditional_close();

}



/**
 * Outputs special XML directives for 120dpi Outlook to instruct to use 96dpi
 * for images instead. See https://www.courtneyfantinato.com/correcting-outlook-
 * dpi-scaling-issues/ for more details.
 * 
 * @return void
 */
function mnlrt_outlook_fix_dpi_for_images () { ?>

	<!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]--> 

<?php }



/**
 * Outputs special XML directives for 120dpi Outlook to instruct to use 96dpi
 * for images instead. See https://www.courtneyfantinato.com/correcting-outlook-
 * dpi-scaling-issues/ for more details.
 * 
 * @return void
 */
function mnlrt_outlook_fixes () { ?>

	<!--[if gte mso 9]>
	<style type="text/css">
		<?php echo file_get_contents( mnltr_templates_get_outlook_stylesheet_path() ); ?>
	</style>
    <![endif]-->

<?php }



add_action( 'mnltr_head',               'mnlrt_outlook_fix_dpi_for_images',     10 );
add_action( 'mnltr_head',               'mnlrt_outlook_fixes',                  10 );
add_action( 'mnltr_after_row_open',     'mnltr_outlook_open_row_container',     10 );
add_action( 'mnltr_before_row_close',   'mnltr_outlook_close_row_container',    10 );
add_action( 'mnltr_before_column_open', 'mnltr_outlook_open_column_container',  10 );
add_action( 'mnltr_after_column_close', 'mnltr_outlook_close_column_container', 10 );

?>