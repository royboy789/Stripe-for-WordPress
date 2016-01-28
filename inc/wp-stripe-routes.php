<?php

require 'stripe-api/stripe-api-settings.php';
require 'stripe-api/stripe-api-customers.php';
require 'stripe-api/stripe-api-plans.php';
class wp_stripe_routes {

	function register_routes() {
		/*
		 * Setup Classes
		 */
		$settings_api = new wp_stripe_settings();
		$customers_api = new wp_stripe_customers();
		$plans_api = new wp_stripe_plans();


		/**
		 * Settings API
		 */
		register_rest_route( 'stripe-wp', '/settings', array(
			'methods' => 'GET',
			'callback' => array( $settings_api, 'get_settings' ),
		));

		register_rest_route( 'stripe-wp', '/settings', array(
			'methods' => 'POST',
			'callback' => array( $settings_api, 'save_settings' ),
		));

		/**
		 * Customers API
		 */
		register_rest_route( 'stripe-wp', '/customers', array(
			array(
				'methods' => 'GET',
				'callback' => array( $customers_api, 'get_customers' ),
			)
		));

		register_rest_route( 'stripe-wp', '/customers/(?P<id>.+)', array(
			array(
				'methods' => 'POST',
				'callback' => array( $customers_api, 'save_customer' ),
			),
			array(
				'methods' => 'GET',
				'callback' => array( $customers_api, 'get_customers' ),
			),
			array(
				'methods' => 'DELETE',
				'callback' => array( $customers_api, 'delete_customer' ),
			),
		));

		/**
		 * Plans
		 */
		register_rest_route( 'stripe-wp', '/plans', array(
			array(
				'methods' => 'GET',
				'callback' => array( $plans_api, 'get_plans' ),
			)
		));

		register_rest_route( 'stripe-wp', '/plans/(?P<id>.+)', array(
			array(
				'methods' => 'POST',
				'callback' => array( $plans_api, 'save_plan' ),
			),
			array(
				'methods' => 'GET',
				'callback' => array( $plans_api, 'get_plans' ),
			),
			array(
				'methods' => 'DELETE',
				'callback' => array( $plans_api, 'delete_plan' )
			)
		));

	}
}
?>