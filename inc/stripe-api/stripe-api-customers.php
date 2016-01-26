<?php
require 'stripe-lib/init.php';

class wp_stripe_customers {

	function set_api_key() {

		$settings['mode'] = get_option( 'stripe_wp_mode', false );
		$settings['keys']['prod'] = get_option( 'stripe_wp_live_key', false );
		$settings['keys']['test'] = get_option( 'stripe_wp_test_key', false );

		if( $settings['mode'] && $settings['mode'] == 'test' && $settings['keys']['test'] ) {
			\Stripe\Stripe::setApiKey( $settings['keys']['test'] );
		}

		if( $settings['mode'] && $settings['mode'] == 'prod' && $settings['keys']['prod'] ) {
			\Stripe\Stripe::setApiKey( $settings['keys']['test'] );
		}

	}

	function get_customers() {
		$this->set_api_key();

		try {
			$customers = \Stripe\Customer::all(array("limit" => 100));
			return new WP_REST_Response( $customers, 200 );

		} catch( Stripe_AuthenticationError $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch( Stripe_Error $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch ( Stripe_CardError $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );
		}
	}

}
?>