<?php
require 'stripe-lib/init.php';

class wp_stripe_customers {

	function set_api_key() {

		$settings['mode']         = get_option( 'stripe_wp_mode', false );
		$settings['keys']['prod'] = get_option( 'stripe_wp_live_key', false );
		$settings['keys']['test'] = get_option( 'stripe_wp_test_key', false );

		if ( $settings['mode'] && $settings['mode'] == 'test' && $settings['keys']['test'] ) {
			\Stripe\Stripe::setApiKey( $settings['keys']['test'] );
		}

		if ( $settings['mode'] && $settings['mode'] == 'prod' && $settings['keys']['prod'] ) {
			\Stripe\Stripe::setApiKey( $settings['keys']['prod'] );
		}

	}

	function get_customers( WP_REST_Request $data ) {
		$this->set_api_key();

		$data = $data->get_params();


		try {
			$args = array( 'limit' => 10 );
			if ( isset( $data['starting_after'] ) ) {
				$args['starting_after'] = $data['starting_after'];
				$customers              = \Stripe\Customer::all( $args );
			} elseif ( ! isset( $data['starting_after'] ) && ! isset( $data['id'] ) ) {
				$customers = \Stripe\Customer::all( array( 'limit' => 10 ) );
			} elseif ( isset( $data['id'] ) ) {
				$customers = \Stripe\Customer::retrieve( $data['id'] );
			}

			return new WP_REST_Response( $customers, 200 );

		} catch ( \Stripe\Error\RateLimit $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( \Stripe\Error\Authentication $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );

		} catch ( \Stripe\Error\ApiConnection $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( Exception $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

		}
	}

	function save_customer( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if ( ! isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No Customer ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$customer = \Stripe\Customer::retrieve( $data['id'] );

			if ( isset( $data['account_balance'] ) ) {
				$customer->account_balance = $data['account_balance'];
			}
			if ( isset( $data['description'] ) ) {
				$customer->description = $data['description'];
			}
			if ( isset( $data['shipping'] ) && isset( $data['shipping']['address']['line1'] ) ) {
				$shipping = array(
					"address" => array(
						"line1"       => $data['shipping']['address']['line1'],
						"line2"       => $data['shipping']['address']['line2'],
						"city"        => $data['shipping']['address']['city'],
						"postal_code" => $data['shipping']['address']['postal_code'],
						"state"       => $data['shipping']['address']['state']
					),
					"name"    => $data['shipping']['name'],
				);
			}

			if ( isset( $data['shipping'] ) && isset( $data['shipping']['phone'] ) ) {
				$shipping['phone'] = $data['shipping']['phone'];
			}

			if ( isset( $shipping ) && ! empty( $shipping ) ) {
				$customer->shipping = $shipping;
			}

			$customer->save();

			return new WP_REST_Response( $customer, 200 );

		} catch ( \Stripe\Error\RateLimit $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( \Stripe\Error\Authentication $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );

		} catch ( \Stripe\Error\ApiConnection $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( Exception $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];


		}
	}

	function delete_customer( WP_REST_Request $request ) {
		$this->set_api_key();
		$data = $request->get_params();

		if ( ! isset( $data['id'] ) ) {
			return new WP_Error( 'data', __( 'No Customer ID Set' ), array( 'status' => 404 ) );
		}

		try {

			$customer = \Stripe\Customer::retrieve( $data['id'] );

			$customer->delete();

			return new WP_REST_Response( $customer, 200 );

		} catch ( \Stripe\Error\RateLimit $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( \Stripe\Error\Authentication $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );

		} catch ( \Stripe\Error\ApiConnection $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array( 'status' => $e->getHttpStatus() ) );
		} catch ( Exception $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];
		}

	}

	function new_customer( WP_REST_Request $request ) {

		$data = $request->get_params();
		$this->set_api_key();

		try {

			$wp_user_id = wp_insert_user( array(
				'user_login' => $data['username'],
				'user_email' => $data['email'],
				'user_pass'  => $data['pass']
			) );

			if ( is_wp_error( $wp_user_id ) ) {
				return new WP_Error( 'wp-user', __( $wp_user_id->get_error_message() ), array( 'status' => 401 ) );
			}

			$customer_data = array(
				"source"   => $this->card_token( $data ),
				"email"    => $data['email'],
				"plan"     => $data['plan_id'],
				"metadata" => array(
					'user_id' => $wp_user_id
				),
				"shipping" => array(
					"address" => array(
						"line1"       => $data['address']['line1'],
						"city"        => $data['address']['city'],
						"postal_code" => $data['address']['postal_code'],
						"state"       => $data['address']['state']
					),
					"name"    => $data['name']['first'] . ' ' . $data['name']['last'],
					"phone"   => $data['phone'],
				)
			);

			if ( isset( $data['address']['line2'] ) ) {
				$customer_data['shipping']['address']['line2'] = $data['address']['line2'];
			}
			if ( isset( $data['coupon'] ) ) {
				$customer_data['coupon'] = $data['coupon'];
			}

			$customer = \Stripe\Customer::create( $customer_data );

			update_user_meta( $wp_user_id, '__stripe_cus_id', $customer->id );


			return new WP_REST_Response( $customer, 200 );

		} catch ( \Stripe\Error\RateLimit $e ) {

			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array(
				'status' => $e->getHttpStatus(),
				'user'   => $wp_user_id
			) );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array(
				'status' => $e->getHttpStatus(),
				'user'   => $wp_user_id
			) );
		} catch ( \Stripe\Error\Authentication $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array(
				'status' => $e->getHttpStatus(),
				'user'   => $wp_user_id
			) );

		} catch ( \Stripe\Error\ApiConnection $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array(
				'status' => $e->getHttpStatus(),
				'user'   => $wp_user_id
			) );
		} catch ( \Stripe\Error\Base $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array(
				'status' => $e->getHttpStatus(),
				'user'   => $wp_user_id
			) );
		} catch ( Exception $e ) {
			$body = $e->getJsonBody();
			$err  = $body['error'];

			return new WP_Error( $err['type'], __( $err['message'] ), array(
				'status' => $e->getHttpStatus(),
				'user'   => $wp_user_id
			) );
		}

	}

	function card_token( $data ) {

		$this->set_api_key();

		$token = \Stripe\Token::create( array(
			"card" => array(
				"number"    => $data['cc']['number'],
				"exp_month" => $data['cc']['exp']['month'],
				"exp_year"  => $data['cc']['exp']['year'],
				"cvc"       => $data['cc']['cvc']
			)
		) );

		return $token;

	}
}

?>