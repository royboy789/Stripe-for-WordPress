<?php

require 'stripe-api/stripe-api-settings.php';
require 'stripe-api/stripe-api-customers.php';
require 'stripe-api/stripe-api-plans.php';
require 'stripe-api/stripe-api-coupons.php';

class wp_stripe_routes {

	function register_routes() {
		/*
		 * Setup Classes
		 */
		$settings_api = new wp_stripe_settings();
		$customers_api = new wp_stripe_customers();
		$plans_api = new wp_stripe_plans();
		$coupons_api = new wp_stripe_coupons();


		/**
		 * Settings API
		 */
		register_rest_route( 'stripe-wp', '/settings', array(
			'methods' => 'GET',
			'callback' => array( $settings_api, 'get_settings' ),
			'permission_callback' => array( $this, 'verify_nonce' )
		));

		register_rest_route( 'stripe-wp', '/settings', array(
			'methods' => 'POST',
			'callback' => array( $settings_api, 'save_settings' ),
			'permission_callback' => array( $this, 'verify_nonce' )
		));

		/**
		 * Customers API
		 */
		register_rest_route( 'stripe-wp', '/customers', array(
			array(
				'methods' => 'GET',
				'callback' => array( $customers_api, 'get_customers' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'POST',
				'callback' => array( $customers_api, 'new_customer' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			)
		));

		register_rest_route( 'stripe-wp', '/customers/(?P<id>.+)', array(
			array(
				'methods' => 'POST',
				'callback' => array( $customers_api, 'save_customer' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'GET',
				'callback' => array( $customers_api, 'get_customers' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'DELETE',
				'callback' => array( $customers_api, 'delete_customer' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
		));

		/**
		 * Plans
		 */
		register_rest_route( 'stripe-wp', '/plans', array(
			array(
				'methods' => 'GET',
				'callback' => array( $plans_api, 'get_plans' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			)
		));

		register_rest_route( 'stripe-wp', '/plans/(?P<id>.+)', array(
			array(
				'methods' => 'POST',
				'callback' => array( $plans_api, 'save_plan' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'GET',
				'callback' => array( $plans_api, 'get_plans' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'DELETE',
				'callback' => array( $plans_api, 'delete_plan' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			)
		));

		/**
		 * Coupons
		 */

		register_rest_route( 'stripe-wp', '/coupons', array(
			array(
				'methods' => 'GET',
				'callback' => array( $coupons_api, 'get_coupons' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'POST',
				'callback' => array( $coupons_api, 'new_coupon' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			)
		));

		register_rest_route( 'stripe-wp', '/coupons/(?P<id>.+)', array(
			array(
				'methods' => 'POST',
				'callback' => array( $coupons_api, 'save_coupon' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'GET',
				'callback' => array( $coupons_api, 'get_coupons' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
			array(
				'methods' => 'DELETE',
				'callback' => array( $coupons_api, 'delete_coupon' ),
				'permission_callback' => array( $this, 'verify_nonce' )
			),
		));

	}

	function verify_nonce( WP_REST_Request $request ) {
		$data = $request->get_params();
		if( !$data['_wpnonce'] ) {
			return false;
		} else {
			$verify = wp_verify_nonce( $data['_wpnonce'], 'wp_rest' );
			if( $verify > 0 ) {
				return true;
			} else {
				return false;
			}
		}
	}
}
?>