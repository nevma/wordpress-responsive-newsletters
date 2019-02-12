<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

$column_order = 1;

mnltr_templates_column( mnltr_templates_get_wysiwyg_content( 'column_1' ), '1/3', $column_order++ );
mnltr_templates_column( mnltr_templates_get_wysiwyg_content( 'column_2' ), '1/3', $column_order++ );
mnltr_templates_column( mnltr_templates_get_wysiwyg_content( 'column_3' ), '1/3', $column_order++ );

?>