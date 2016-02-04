<?php

class wp_stripe_coupons {

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

	function get_coupons( WP_REST_Request $data ) {
		$this->set_api_key();

		$data = $data->get_params();


		try {
			$args = array( 'limit' => 10 );
			if( isset( $data['starting_after'] ) ) {
				$args['starting_after'] = $data['starting_after'];
				$coupons = \Stripe\Coupon::all($args);
			} elseif( !isset( $data['starting_after'] ) && !isset( $data['id'] ) ) {
				$coupons = \Stripe\Coupon::all(array('limit' => 10));
			} elseif( isset( $data['id'] ) ) {
				$coupons = \Stripe\Coupon::retrieve( $data['id'] );
			}
			return new WP_REST_Response( $coupons, 200 );

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

	function save_coupon( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if( !isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No coupon ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$coupon = \Stripe\Coupon::retrieve( $data['id'] );

			if( isset( $data['account_balance'] ) ) {
				$coupon->account_balance = $data['account_balance'];
			}
			if( isset( $data['description'] ) ) {
				$coupon->description = $data['description'];
			}
			if( isset( $data['shipping'] ) && isset( $data['shipping']['address']['line1'] ) ) {
				$shipping = array(
					"address" => array(
						"line1" => $data['shipping']['address']['line1'],
						"line2" => $data['shipping']['address']['line2'],
						"city" => $data['shipping']['address']['city'],
						"postal_code" => $data['shipping']['address']['postal_code'],
						"state" => $data['shipping']['address']['state']
					),
					"name" => $data['shipping']['name'],
				);
			}

			if( isset( $data['shipping'] ) && isset( $data['shipping']['phone'] ) ) {
				$shipping['phone'] = $data['shipping']['phone'];
			}

			if( isset( $shipping ) && !empty( $shipping ) ) {
				$coupon->shipping = $shipping;
			}

			$coupon->save();

			return new WP_REST_Response( $coupon, 200 );

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

	function delete_coupon( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if( !isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No coupon ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$coupon = \Stripe\Coupon::retrieve( $data['id'] );

			$coupon->delete();

			return new WP_REST_Response( $coupon, 200 );

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

	function new_coupon( WP_REST_Request $request ) {

		$data = $request->get_params();
		$this->set_api_key();

		try {

			$coupon = \Stripe\Coupon::create( $data );

			return new WP_REST_Response( $coupon, 200 );

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