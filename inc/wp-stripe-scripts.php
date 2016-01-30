<?php

class wp_stripe_scripts {

	function admin_scripts() {
		wp_enqueue_script( 'wp-stripe-app', WP_STRIPE_URL . '/build/js/wp-stripe-scripts.js', array( 'jquery' ), WP_STRIPE_VERSION, false );

		$local_object = array(
			'api_url' => get_rest_url(),
			'template_directory' => WP_STRIPE_URL . 'templates',
			'nonce' => wp_create_nonce( 'wp_rest' )
		);

		if( get_option( 'stripe_wp_confirmation_type', false ) == 'page' && get_option( 'stripe_wp_confirmation_page', false ) ) {
			$local_object['confirmation'] = array( 'type' => 'page', 'page' => get_permalink( get_option( 'stripe_wp_confirmation_page', false ) ) );
		}
		if( get_option( 'stripe_wp_confirmation_type', false ) == 'message' && get_option( 'stripe_wp_confirmation_message', false ) ) {
			$local_object['confirmation'] = array( 'type' => 'message', 'message' => get_permalink( get_option( 'stripe_wp_confirmation_message', false ) ) );
		}

		wp_localize_script( 'wp-stripe-app', 'stripe_wp_local',
			$local_object
		);
	}

	function admin_styles() {
		wp_enqueue_style( 'wp-stripe-styles', WP_STRIPE_URL . '/build/css/wp-stripe-styles.css', array(), WP_STRIPE_VERSION, 'all' );
	}

	function fed_scripts() {
		wp_enqueue_style( 'wp-stripe-fed-styles', WP_STRIPE_URL . '/build/front-end/css/wp-stripe-fed-styles.css', array(), WP_STRIPE_VERSION, 'all' );
		wp_enqueue_script( 'wp-stripe-fed-scripts', WP_STRIPE_URL . '/build/front-end/js/stripe-wp-fed-scripts.js', array( 'jquery' ), WP_STRIPE_VERSION, false );

		$local_object = array(
			'api_url' => get_rest_url(),
			'template_directory' => WP_STRIPE_URL . 'templates',
			'nonce' => wp_create_nonce( 'wp_rest' )
		);

		if( get_option( 'stripe_wp_confirmation_type', false ) == 'page' && get_option( 'stripe_wp_confirmation_page', false ) ) {
			$local_object['confirmation'] = array( 'type' => 'page', 'page' => get_permalink( get_option( 'stripe_wp_confirmation_page', false ) ) );
		}
		if( get_option( 'stripe_wp_confirmation_type', false ) == 'message' && get_option( 'stripe_wp_confirmation_message', false ) ) {
			$local_object['confirmation'] = array( 'type' => 'message', 'message' => get_permalink( get_option( 'stripe_wp_confirmation_message', false ) ) );
		}

		wp_localize_script( 'wp-stripe-fed-scripts', 'stripe_wp_local',
			$local_object
		);
	}
}
?>