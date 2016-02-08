<?php


class wp_stripe_menu {

	function register_menu() {

		$svg_img = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABE0lEQVQ4T6VT0VHDMBSTJijdgE7QZoMyAcckDRM03SAdgQ1gAhghmQBGyAbi5HN6xrF7R+u7fDj2kyW9JyJbkh4BHADsAezi8QDgC8CZ5E9awnQjqY/FOW6670m+zj8uAJL8yvZaZXI2kGy8DwBXXj7FomMB2HJaRs3fhQtPJK3bD9iPdwCr7N7GACXdAV1SZ1kkXyQZ4DkDOBugpP1EsosArUkAeCiwHAzgw3y5VQ3JSZILDeIvl4AagAEnAJb35t5HNgszaxI+AJiFB2oiuY5GfmZUx5qJswemDZJ9hUEw0aNbaqOLzcTL7rsjuZGb+wdp1vTPUR5JhqDdEqYwZIswJUzsiS94fOdwjTHOTuKfOP8CYP+D9jlceu0AAAAASUVORK5CYII=';

		add_menu_page( 'Stripe for WordPress', 'Stripe', 'manage_options', 'wp-stripe-app', array( $this, 'admin_page' ), $svg_img, 58 );
		add_submenu_page( 'wp-stripe-app', 'License', 'License', 'manage_options', 'stripe-wp-license', array( $this, 'edd_sample_license_page' ) );
	}

	function admin_page() {

		echo
		'<div class="container app-container" ng-app="stripe-wp">
                <div class="row">
                    <div class="col-sm-12">
                        <h1>' . __('Stripe for WordPress', 'wp-stripe' ) . '</h1>
                        <nav class="navbar navbar-default">
                            <div class="collapse navbar-collapse">
                                <ul class="nav navbar-nav">
                                    <li ng-class="isActiveNav(\'{{item.state}}\');" ng-repeat="item in nav_items track by $index">
                                        <a ui-sref="{{item.state}}">{{item.title}}</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
                <div id="admin-js-app" class="row app-template-wrapper">
                    <div class="col-sm-12" ui-view></div>
                </div>
            </div>';

	}

	function edd_sample_license_page() {
		$license 	= get_option( 'stripe_wp_license_key', false );
		$status 	= get_option( 'stripe_wp_license_status', false );
		?>
		<div class="wrap">
		<h2><?php _e('Plugin License Options'); ?></h2>
		<form method="post" action="<?php echo admin_url('admin.php?page=stripe-wp-license'); ?>">

			<?php settings_fields('edd_sample_license'); ?>

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e('License Key'); ?>
					</th>
					<td>
						<input id="stripe_wp_license_key" name="stripe_wp_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
						<label class="description" for="stripe_wp_license_key"><?php _e('Enter your license key'); ?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e('License Status'); ?>
					</th>
					<td>
						<?php if( $status !== false && $status == 'valid' ) { ?>
							<span style="color:green;"><?php _e('active'); ?></span>
						<?php } else { ?>
							<span style="color:green;"><?php _e('Invalid or Not Set'); ?></span>
						<?php } ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e('License Activation'); ?>
					</th>
					<td>
						<?php if( $status !== false && $status == 'valid' ) { ?>
							<?php wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' ); ?>
							<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
						<?php } else {
							wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' ); ?>
							<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License'); ?>"/>
						<?php } ?>
					</td>
				</tr>
				</tbody>
			</table>
			<?php //submit_button(); ?>

		</form>
		<?php
	}

}

?>