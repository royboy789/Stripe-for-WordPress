<?php


define( 'STORE_URL', 'http://roysivan.com' );
define( 'ITEM_NAME', 'Stripe for WordPress' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

function edd_sl_sample_plugin_updater() {

	$license_key = trim( get_option( 'stripe_wp_license_key' ) );
	$edd_updater = new EDD_SL_Plugin_Updater( STORE_URL, __FILE__, array(
			'version' 	=> WP_STRIPE_VERSION,	// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Roy Sivan'  // author of this plugin
		)
	);

}
add_action( 'admin_init', 'edd_sl_sample_plugin_updater', 0 );


function edd_sample_register_option() {
	register_setting('stripe_wp_sample_license', 'stripe_wp_license_key', 'edd_sanitize_license' );
}
add_action('admin_init', 'edd_sample_register_option');

function edd_sanitize_license( $new ) {
	$old = get_option( 'stripe_wp_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'stripe_wp_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
* this illustrates how to activate
* a license key
*************************************/

function edd_sample_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_activate'] ) ) {

	 	if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )
			return;

		update_option( 'stripe_wp_license_key', $_POST['stripe_wp_license_key'] );
		$license = trim( get_option( 'stripe_wp_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'stripe_wp_license_status', $license_data->license );

	}
}
add_action('admin_init', 'edd_sample_activate_license');


/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/

function edd_sample_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'stripe_wp_license_key' ) );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'stripe_wp_license_status' );

	}
}
add_action('admin_init', 'edd_sample_deactivate_license');


/************************************
* this illustrates how to check if
* a license key is still valid
* the updater does this for you,
* so this is only needed if you
* want to do something custom
*************************************/

function edd_sample_check_license() {

	global $wp_version;

	$license = trim( get_option( 'stripe_wp_license_key' ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) )
		return false;

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( $license_data->license == 'valid' ) {
		echo 'valid'; exit;
		// this license is still valid
	} else {
		echo 'invalid'; exit;
		// this license is no longer valid
	}
}
