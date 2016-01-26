<?php
/**
 * Plugin Name: Stripe For WordPress
 * Description: A way to manage your Stripe subscriptions, customers, and products
 * Author: Roy Sivan
 * Author URI: http://www.roysivan.com
 * Version: 0.1
 * Plugin URI: https://github.com/royboy789/Stripe-for-WordPress
 * License: GPL3+
 * Text Domain: wp-stripe
 */

define( 'WP_STRIPE_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_STRIPE_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_STRIPE_VERSION', '0.1' );

require 'inc/wp-stripe-menu.php';
require 'inc/wp-stripe-scripts.php';
require 'inc/wp-stripe-routes.php';

class wp_stripe {

	function admin_page() {
		$wp_stripe_menu = new wp_stripe_menu();
		$wp_stripe_menu->register_menu();
	}

	function stripe_scripts() {
		$wp_stripe_scripts = new wp_stripe_scripts();
		$wp_stripe_scripts->scripts();
		$wp_stripe_scripts->styles();
	}
	function stripe_routes() {
		$wp_stripe_routes = new wp_stripe_routes();
		$wp_stripe_routes->register_routes();
	}

}

$WP_STRIPE = new wp_stripe();

/*
 * Stripe for WP Admin Menu
 */
	add_action( 'admin_menu', array( $WP_STRIPE, 'admin_page' ) );

/*
 * Stripe for WP Scripts and Styles
 */
	add_action( 'admin_enqueue_scripts', array( $WP_STRIPE, 'stripe_scripts' ) );

/*
 * Stripe for WP API Routes
 */
	add_action( 'rest_api_init', array( $WP_STRIPE, 'stripe_routes' ) );

?>