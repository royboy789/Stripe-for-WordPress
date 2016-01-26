<?php

class wp_stripe_settings {
	function get_settings( $data ) {

		$settings['mode'] = get_option( 'stripe_wp_mode', false );
		$settings['keys']['prod'] = get_option( 'stripe_wp_live_key', false );
		$settings['keys']['test'] = get_option( 'stripe_wp_test_key', false );


		return new WP_REST_Response( $settings, 200 );
	}

	function save_settings( WP_REST_Request $data ) {

		$keys = $data->get_params();

		if( isset( $keys['mode'] ) ) {
			update_option( 'stripe_wp_mode', $keys['mode'] );
		}

		if( isset( $keys['keys']['test'] ) ) {
			update_option( 'stripe_wp_test_key', $keys['keys']['test'] );
		}

		if( isset( $keys['keys']['prod'] ) ) {
			update_option( 'stripe_wp_live_key', $keys['keys']['prod'] );
		}

		$settings['mode'] = get_option( 'stripe_wp_mode', false );
		$settings['keys']['prod'] = get_option( 'stripe_wp_live_key', false );
		$settings['keys']['test'] = get_option( 'stripe_wp_test_key', false );

		return new WP_REST_Response( $settings, 200 );
	}
}
?>