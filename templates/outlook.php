<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

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

	echo mnltr_outlook_conditional_open()
	. "\n". '<td align="center" valign="top">'
	. mnltr_outlook_conditional_close();
}

function mnltr_outlook_close_column_container() {

	echo mnltr_outlook_conditional_open()
	. "\n". '</td>'
	. mnltr_outlook_conditional_close();
}

add_action( 'mnltr_after_row_open', 'mnltr_outlook_open_row_container', 10 );
add_action( 'mnltr_before_row_close', 'mnltr_outlook_close_row_container', 10 );
add_action( 'mnltr_before_column_open', 'mnltr_outlook_open_column_container', 10, 1 );
add_action( 'mnltr_after_column_close', 'mnltr_outlook_close_column_container', 10 );

?>