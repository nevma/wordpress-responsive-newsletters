<?php

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

function mnltr_cf_register_custom_fields() {

	$acf_fields = mnltr_get_option( 'acf_fields' );

	// Register field groups

	// Register Newsletter Appearance field group
	acf_add_local_field_group( array(
		'key' => $acf_fields['side']['key'],
		'title' => 'Newsletter Appearance',
		'fields' => array(),
		'location' => array(
			array(
				array(
					
					'param' => 'post_type',
					'operator' => '==',
					'value' => mnltr_get_newsletter_cpt_name(),
				) 
			) 
		),
		'position' => 'side',
	));

	// Register Newsletter Structure field group
	acf_add_local_field_group( array(
		'key' => $acf_fields['newsletter_structure']['key'],
		'title' => 'Newsletter Structure',
		'fields' => array(),
		'location' => array(
			array(
				array(
					
					'param' => 'post_type',
					'operator' => '==',
					'value' => mnltr_get_newsletter_cpt_name(),
				) 
			) 
		),
		'hide_on_screen' => array( 'the_content' ),
	));

	// Register fields

	// Register Skin field
	acf_add_local_field( array(
		'key' => $acf_fields['skin']['key'],
		'parent' => $acf_fields['side']['key'],
		'name' => 'mnltr_skin',
		'label' => 'Skin',
		'type' => 'select',
		'choices' => mnltr_cf_get_skin_choices_for_acf(),
	));

	

	// Describe the layouts to be registered
	$layouts = array(
		array(
			'name' => 'single_column',
			'label' => 'Single Column',
			'columns' => array( 100 ),
		),
		array(
			'name' => 'two_columns_equal',
			'label' => 'Two Columns (Equal)',
			'columns' => array( 50, 50 ),
		),
		array(
			'name' => 'two_columns_1_2',
			'label' => 'Two Columns (1/3 + 2/3)',
			'columns' => array( 30, 70 ),
		),
		array(
			'name' => 'two_columns_2_1',
			'label' => 'Two Columns (2/3 + 1/3)',
			'columns' => array( 70, 30 ),
		),
		array(
			'name' => 'three_columns',
			'label' => 'Three Columns',
			'columns' => array( 33, 33, 33 ),
		),
	);

	// These are the layouts. They need to be gathered and passed as an argument when the Flexible Content field is registered, they can't be added later.
	$acf_layouts = array();

	// Register each layout and its sub-fields
	foreach ( $layouts as $layout ) {

		// Keep the layout ACF key for convenience
		$layout_key = $acf_fields[ $layout['name'] ]['key'];

		// These are the sub-fields of each layout. They need to be gathered and passed as an argument when the layout is registered, they can't be added later.
		$layout_subfields = array();

		// Create the classes sub-field (select)
		$layout_classes = apply_filters( 'mnltr_layout_classes', array(), $layout['name'] );

		if ( ! is_array( $layout_classes ) ) {
			$layout_classes = array();
		}

		$layout_subfields[] = array(
			'key' => $acf_fields[ $layout['name'] . '/classes' ]['key'],
			'name' => 'mnltr_layout_classes',
			'label' => 'Layout Types',
			'type' => 'select',
			'choices' => $layout_classes,
			'multiple' => 1,
			'ui' => 1,
		);

		// Create the Columns' sub-fields
		foreach ( $layout['columns'] as $index => $width ) {

			$col_index = $index + 1;

			$layout_subfields[] = array(

				'key' => $acf_fields[ $layout['name'] . '/column_' . $col_index ]['key'],
				'name' => 'column_' . $col_index,
				'label' => 'Column ' . $col_index,
				'type' => 'wysiwyg',
				'wrapper' => array(
					'width' => $width,
				),
			);
		}

		// Finally, register the Layout itself
		$acf_layouts[] = array(
			'key' => $acf_fields[ $layout['name'] ]['key'],
			'parent' => $acf_fields['rows']['key'],
			'name' => $layout['name'],
			'label' => $layout['label'],
			'type' => 'flexible_content',
			'sub_fields' => $layout_subfields,
		);
	}
	
	// Register Rows field
	acf_add_local_field( array(
		'key' => $acf_fields['rows']['key'],
		'parent' => $acf_fields['newsletter_structure']['key'],
		'name' => 'mnltr_rows',
		'label' => 'Newsletter Rows',
		'type' => 'flexible_content',
		'layouts' => $acf_layouts,
	));

	// Finally, make sure to delete any field values cached before their registration

	/*
	 * This procedure is required to prevent the following scenario:
	 * 
	 * 1. This plugin makes a call to get_field in order to get the current
	 * newsletter's skin. This is performed before the field is registered to ACF,
	 * since the various ACF field properties can be manipulated from within the 
	 * selected skin's functions.php file, via exposed filter hooks. So, it's necessary
	 * to determine the selected skin and include its functions file, prior to field
	 * registration.
	 * 
	 * 2. ACF, before getting the field's value, attempts to locate the field itself.
	 * Since the field is not yet registered, the field's properties' value resolves to
	 * boolean false.
	 * 
	 * 3. ACF, for the sake of speed, caches its fields' properties in WP's object
	 * cache. So a boolean false is cached. Note that this is the field's properties,
	 * being cached, and not just its value.
	 * 
	 * 4. Further down the execution, the field eventually gets registered, along with
	 * its proper choices (skins). ACF doesn't refresh the cached values (used to, but
	 * doesn't any more.). See the commented-out calls to wp_cache_delete at ACF's file:
	 * core/local.php: 225 (referring to ACF v5.3.8.1).
	 * 
	 * 5. When the time comes to render the field in the newsletter edit page, in the
	 * admin, its settings are retrieved from the cache as boolean false, and the field
	 * isn't properly populated.
	 *
	 * Maybe this behaviour of ACF will change in the future, so this uncache operation
	 * will no longer be needed, thus allowing for a faster admin page load.
	 */
	foreach( $acf_fields as $field ) {

		if ( $field['type'] == 'field' ) {

			wp_cache_delete( "get_field/key={$field['key']}", 'acf' );
		}
	}
}

function mnltr_cf_get_skin_choices_for_acf( $skins = null ) {

	if ( null === $skins ) {
		$skins = mnltr_skins_get_skins();
	}
	
	$choices = array ();
	
	foreach ( $skins as $skin ) {
		
		$choices[ mnltr_skins_shrink_skin_path( $skin[ 'skin_path' ] ) ] = $skin[ 'name' ];
	}
	
	return $choices;
}

function mnltr_cf_get_skin( $post_id = null ) {

	$shrinked_path = get_field( 'mnltr_skin', $post_id );
	
	$expanded_path = mnltr_skins_expand_skin_path( $shrinked_path );
	
	$skin = mnltr_skins_get_skin_data( $expanded_path );
	
	if ( is_wp_error( $skin ) ) {
	
		return mnltr_skins_get_skin_data( mnltr_skins_get_built_in_skin_container_path() . 'default' );
	}
	
	return $skin;
}

function mnltr_cf_generate_acf_key( $type = null ) {

	if ( $type === 'field' ) {

		$prefix = 'field_';

	} elseif ( $type === 'field_group' ) {

		$prefix = 'group_';

	} else {

		$prefix = '';
	}

	return uniqid( $prefix );
}

/**
 * Will generate and update the stored ACF keys, whenever changes occur in the custom
 * fields that the plugin uses. This is expected to happen on plugin installation and
 * possibly on version updates.
 */
function mnltr_cf_maybe_update_acf_keys() {

	if ( ! current_user_can( 'manage_options' ) ) {return;}

	$save_needed = false;

	// Get the current and saved fields;
	$current_fields = mnltr_cf_get_acf_fields();

	$saved_fields = mnltr_get_option( 'acf_fields' );

	// Ensure we always handle an array, even on first plugin load.
	if ( ! $saved_fields ) {

		$saved_fields = array();
	}

	// Run through the current fields. Saved fields that no longer exist, will automatically discarded that way.
	foreach ( $current_fields as $field => $data ) {

		// If any one field is new (not saved) or its type has changed, generate a key for it and trigger a field update.
		if ( empty( $saved_fields[ $field ] )
			|| $data['type'] !== $saved_fields[ $field ]['type'] ) {

			$save_needed = true;

			$current_fields[ $field ]['key'] = mnltr_cf_generate_acf_key( $data['type'] );

		} else {

			// For existing (saved) fields, simply keep their key.
			$current_fields[ $field ]['key'] = $saved_fields[ $field ]['key'];

		}
	}

	// Save as needed.
	if ( $save_needed ) {

		mnltr_save_option( 'acf_fields', $current_fields );
	}
}

function mnltr_cf_get_acf_fields() {

	$acf_fields = array(
		'side'=> array(
			'type' => 'field_group',
		),
		'newsletter_structure'=> array(
			'type' => 'field_group',
		),
		'skin' => array(
			'type' => 'field',
		),
		'rows' => array(
			'type' => 'field',
		),
		'single_column' => array(
			'type' => 'layout',
		),
		'single_column/classes' => array(
			'type' => 'field',
		),
		'single_column/column_1' => array(
			'type' => 'field',
		),
		'two_columns_equal' => array(
			'type' => 'layout',
		),
		'two_columns_equal/classes' => array(
			'type' => 'field',
		),
		'two_columns_equal/column_1' => array(
			'type' => 'field',
		),
		'two_columns_equal/column_2' => array(
			'type' => 'field',
		),
		'two_columns_1_2' => array(
			'type' => 'layout',
		),
		'two_columns_1_2/classes' => array(
			'type' => 'field',
		),
		'two_columns_1_2/column_1' => array(
			'type' => 'field',
		),
		'two_columns_1_2/column_2' => array(
			'type' => 'field',
		),
		'two_columns_2_1' => array(
			'type' => 'layout',
		),
		'two_columns_2_1/classes' => array(
			'type' => 'field',
		),
		'two_columns_2_1/column_1' => array(
			'type' => 'field',
		),
		'two_columns_2_1/column_2' => array(
			'type' => 'field',
		),
		'three_columns' => array(
			'type' => 'layout',
		),
		'three_columns/classes' => array(
			'type' => 'field',
		),
		'three_columns/column_1' => array(
			'type' => 'field',
		),
		'three_columns/column_2' => array(
			'type' => 'field',
		),
		'three_columns/column_3' => array(
			'type' => 'field',
		),
	);

	return $acf_fields;
}

?>