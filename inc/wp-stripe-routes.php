<?php

require 'stripe-api/stripe-api-settings.php';
require 'stripe-api/stripe-api-customers.php';

class wp_stripe_routes {



	function register_routes() {
		/*
		 * Setup Classes
		 */
		$settings_api = new wp_stripe_settings();
		$customers_api = new wp_stripe_customers();


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
			'methods' => 'GET',
			'callback' => array( $customers_api, 'get_customers' ),
		));
	}
}
?>