<?php
/**
 * Class PW_CMB2_Field_Google_Maps.
 */
class PW_CMB2_Field_Google_Maps {

	/**
	 * Hooking into CMB2.
	 */
	public function __construct() {
		add_filter( 'cmb2_render_pw_map', array( $this, 'render_pw_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_pw_map', array( $this, 'sanitize_pw_map' ), 10, 4 );
		add_filter( 'pw_google_api_key', array( $this, 'google_api_key_constant' ) );
	}

	/**
	 * Render field.
	 */
	public function render_pw_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {

		// Get the Google API key from the field's parameters.
		$api_key = $field->args( 'api_key' );

		// Allow a custom hook to specify the key.
		$api_key = apply_filters( 'pw_google_api_key', $api_key );

		$this->setup_admin_scripts( $api_key );

		echo $field_type_object->input( array(
			'type'       => 'text',
			'name'       => $field->args('_name') . '[address]',
			'value'      => isset( $field_escaped_value['address'] ) ? $field_escaped_value['address'] : '',
			'class'      => 'large-text pw-map-search pw-map-address',
			'desc'       => '',
		) );

		echo '<div class="pw-map"></div>';

		$field_type_object->_desc( true, true );

		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[zoom]',
			'value'      => get_theme_mod( 'ova_career_zoom_map_default', 17 ),
			'class'      => 'pw-map-zoom',
			'desc'       => '',
		) );

		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[latitude]',
			'value'      => isset( $field_escaped_value['latitude'] ) ? $field_escaped_value['latitude'] : '',
			'class'      => 'pw-map-latitude',
			'desc'       => '',
		) );
		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[longitude]',
			'value'      => isset( $field_escaped_value['longitude'] ) ? $field_escaped_value['longitude'] : '',
			'class'      => 'pw-map-longitude',
			'desc'       => '',
		) );
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields.
	 */
	public function sanitize_pw_map( $override_value, $value, $object_id, $field_args ) {
		
		if ( isset( $field_args['split_values'] ) && $field_args['split_values'] ) {
			if ( ! empty( $value['address'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_address', $value['address'] );
			}

			if ( ! empty( $value['latitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_latitude', $value['latitude'] );
			}

			if ( ! empty( $value['longitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_longitude', $value['longitude'] );
			}
		}

		return $value;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	
	public function setup_admin_scripts($api_key) {

		wp_register_script( 'pw-google-maps-api', 'https://maps.googleapis.com/maps/api/js?key='.get_theme_mod( 'ova_career_google_key_map', '' ).'&libraries=places', null, null );

	}

	/**
	 * Default filter to return a Google API key constant if defined.
	 */
	public function google_api_key_constant( $google_api_key = null ) {

		// Allow the field's 'api_key' parameter or a custom hook to take precedence.
		if ( ! empty( $google_api_key ) ) {
			return $google_api_key;
		}

		if ( defined( 'PW_GOOGLE_API_KEY' ) ) {
			$google_api_key = PW_GOOGLE_API_KEY;
		}

		return $google_api_key;
	}
}

$pw_cmb2_field_google_maps = new PW_CMB2_Field_Google_Maps();