<?php
	class wp_stripe_shortcodes {

		function stripe_wp_customer( $atts ) {
			$a = shortcode_atts( array(
				'plan_id' => false
			), $atts );

			return $this->customer_signup( $a );
		}

		function customer_signup( $a ) {
			if( !$a['plan_id'] ) {
				return '<div>You need a plan ID</div>';
			}
			$app = '<div ng-app="stripe-wp">';
				$app .= '<stripe-customer plan-id="' . $a['plan_id'] . '"></stripe-customer>';
			$app .= '</div>';

			return $app;

		}
	}
?>