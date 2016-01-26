<?php

class wp_stripe_scripts {

	function scripts() {
		wp_enqueue_script( 'wp-stripe-app', WP_STRIPE_URL . '/build/js/wp-stripe-scripts.js', array( 'jquery' ), WP_STRIPE_VERSION, false );
		wp_localize_script( 'wp-stripe-app', 'stripe_wp_local',
				array(
						'api_url' => get_rest_url(),
						'template_directory' => WP_STRIPE_URL . 'templates',
						'nonce' => wp_create_nonce( 'wp_rest' ),
				)
		);
	}

	function styles() {
		wp_enqueue_style( 'wp-stripe-styles', WP_STRIPE_URL . '/build/css/wp-stripe-styles.css', array(), WP_STRIPE_VERSION, 'all' );
	}
}
?>