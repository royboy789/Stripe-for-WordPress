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
			\Stripe\Stripe::setApiKey( $settings['keys']['prod'] );
		}

	}

	function get_customers( WP_REST_Request $data ) {
		$this->set_api_key();

		$data = $data->get_params();


		try {
			$args = array( 'limit' => 10 );
			if( isset( $data['starting_after'] ) ) {
				$args['starting_after'] = $data['starting_after'];
				$customers = \Stripe\Customer::all($args);
			} elseif( !isset( $data['starting_after'] ) && !isset( $data['id'] ) ) {
				$customers = \Stripe\Customer::all(array('limit' => 10));
			} elseif( isset( $data['id'] ) ) {
				$customers = \Stripe\Customer::retrieve( $data['id'] );
			}
			return new WP_REST_Response( $customers, 200 );

		} catch( Stripe_AuthenticationError $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch( Stripe_Error $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );
		}
	}

	function save_customer( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if( !isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No Customer ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$customer = \Stripe\Customer::retrieve( $data['id'] );

			if( isset( $data['account_balance'] ) ) {
				$customer->account_balance = $data['account_balance'];
			}
			if( isset( $data['description'] ) ) {
				$customer->description = $data['description'];
			}

			$customer->save();

			return new WP_REST_Response( $customer, 200 );

		} catch( Stripe_AuthenticationError $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch( Stripe_Error $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );
		}

	}

	function delete_customer( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if( !isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No Customer ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$customer = \Stripe\Customer::retrieve( $data['id'] );

			$customer->delete();

			return new WP_REST_Response( $customer, 200 );

		} catch( Stripe_AuthenticationError $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch( Stripe_Error $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );

		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => 403 ) );
		}

	}
}
?>