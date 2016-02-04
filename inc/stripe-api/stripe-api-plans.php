<?php
//require 'stripe-lib/init.php';

class wp_stripe_plans {

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

	function get_plans( WP_REST_Request $data ) {
		$this->set_api_key();

		$data = $data->get_params();


		try {
			$args = array( 'limit' => 10 );

			if( isset( $data['starting_after'] ) ) {
				$args['starting_after'] = $data['starting_after'];
				$plans = \Stripe\Plan::all($args);
			} elseif( !isset( $data['starting_after'] ) && !isset( $data['id'] ) ) {
				$plans = \Stripe\Plan::all(array('limit' => 10));
			} elseif( isset( $data['id'] ) ) {
				$plans = \Stripe\Plan::retrieve( $data['id'] );
			}
			return new WP_REST_Response( $plans, 200 );

		} catch (\Stripe\Error\RateLimit $e) {

			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));

		} catch (\Stripe\Error\InvalidRequest $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (\Stripe\Error\Authentication $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));

		} catch (\Stripe\Error\ApiConnection $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (\Stripe\Error\Base $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (Exception $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
		}
	}

	function save_plan( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if( !isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No Customer ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$plan = \Stripe\Plan::retrieve( $data['id'] );

			if( isset( $data['name'] ) ) {
				$plan->name = $data['name'];
			}
			if( isset( $data['description'] ) ) {
				$plan->statement_descriptor = $data['statement_descriptor'];
			}

			$plan->save();

			return new WP_REST_Response( $plan, 200 );

		} catch (\Stripe\Error\RateLimit $e) {

			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));

		} catch (\Stripe\Error\InvalidRequest $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (\Stripe\Error\Authentication $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));

		} catch (\Stripe\Error\ApiConnection $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (\Stripe\Error\Base $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (Exception $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
		}

	}

	function delete_plan( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if( !isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No Customer ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$plan = \Stripe\Plan::retrieve( $data['id'] );
			$plan->delete();
			return new WP_REST_Response( $plan, 200 );

		} catch (\Stripe\Error\RateLimit $e) {

			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));

		} catch (\Stripe\Error\InvalidRequest $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (\Stripe\Error\Authentication $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));

		} catch (\Stripe\Error\ApiConnection $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (\Stripe\Error\Base $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
			return new WP_Error($err['type'], __($err['message']), array('status' => $e->getHttpStatus()));
		} catch (Exception $e) {
			$body = $e->getJsonBody();
			$err = $body['error'];
		}

	}

}
?>