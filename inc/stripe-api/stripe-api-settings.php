<?php

class wp_stripe_settings {
	function get_settings( WP_REST_Request $data ) {

		$data = $data->get_params();

		$settings['mode'] = get_option( 'stripe_wp_mode', false );
		$settings['keys']['prod'] = get_option( 'stripe_wp_live_key', false );
		$settings['keys']['test'] = get_option( 'stripe_wp_test_key', false );
		$settings['confirmation']['type'] = get_option( 'stripe_wp_confirmation_type', false );
		$settings['confirmation']['page_id'] = get_option( 'stripe_wp_confirmation_page', false );
		$settings['confirmation']['message'] = get_option( 'stripe_wp_confirmation_message', false );

		if( isset( $data['more_settings'] ) && !empty( $data['more_settings'] ) ) {
			foreach( $data['more_settings'] as $key => $value ) {
				$settings[$value] = get_option( $value, false );
			}
		}


		return new WP_REST_Response( $settings, 200 );
	}

	function save_settings( WP_REST_Request $request ) {

		$data = $request->get_params();

		if( isset( $data['mode'] ) ) {
			update_option( 'stripe_wp_mode', $data['mode'] );
		}

		if( isset( $data['keys']['test'] ) ) {
			update_option( 'stripe_wp_test_key', $data['keys']['test'] );
		}

		if( isset( $data['keys']['prod'] ) ) {
			update_option( 'stripe_wp_live_key', $data['keys']['prod'] );
		}

		if( isset( $data['confirmation']['type'] ) ) {
			update_option( 'stripe_wp_confirmation_type', $data['confirmation']['type'] );
		}

		if( isset( $data['confirmation']['page_id'] ) ) {
			update_option( 'stripe_wp_confirmation_page', $data['confirmation']['page_id'] );
		}

		if( isset( $data['confirmation']['message'] ) ) {
			update_option( 'stripe_wp_confirmation_message', $data['confirmation']['message'] );
		}

		if( isset( $data['more_settings'] ) && !empty( $data['more_settings'] ) ) {
			foreach( $data['more_settings'] as $key => $value ) {
				$settings[$key] = update_option( $key, $value );
			}
		}

		$settings['mode'] = get_option( 'stripe_wp_mode', false );
		$settings['keys']['prod'] = get_option( 'stripe_wp_live_key', false );
		$settings['keys']['test'] = get_option( 'stripe_wp_test_key', false );
		$settings['confirmation']['type'] = get_option( 'stripe_wp_confirmation_type', false );
		$settings['confirmation']['page_id'] = get_option( 'stripe_wp_confirmation_page', false );
		$settings['confirmation']['message'] = get_option( 'stripe_wp_confirmation_message', false );

		if( isset( $data['more_settings'] ) && !empty( $data['more_settings'] ) ) {
			foreach( $data['more_settings'] as $key => $value ) {
				$settings[$key] = get_option( $key, false );
			}
		}

		return new WP_REST_Response( $settings, 200 );
	}
}
?>