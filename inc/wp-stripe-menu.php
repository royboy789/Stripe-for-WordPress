<?php


class wp_stripe_menu {

	function register_menu() {

		$svg_img = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABE0lEQVQ4T6VT0VHDMBSTJijdgE7QZoMyAcckDRM03SAdgQ1gAhghmQBGyAbi5HN6xrF7R+u7fDj2kyW9JyJbkh4BHADsAezi8QDgC8CZ5E9awnQjqY/FOW6670m+zj8uAJL8yvZaZXI2kGy8DwBXXj7FomMB2HJaRs3fhQtPJK3bD9iPdwCr7N7GACXdAV1SZ1kkXyQZ4DkDOBugpP1EsosArUkAeCiwHAzgw3y5VQ3JSZILDeIvl4AagAEnAJb35t5HNgszaxI+AJiFB2oiuY5GfmZUx5qJswemDZJ9hUEw0aNbaqOLzcTL7rsjuZGb+wdp1vTPUR5JhqDdEqYwZIswJUzsiS94fOdwjTHOTuKfOP8CYP+D9jlceu0AAAAASUVORK5CYII=';

		add_menu_page( 'Stripe for WordPress', 'Stripe', 'manage_options', 'wp-stripe-app', array( $this, 'admin_page' ), $svg_img, 58 );
	}

	function admin_page() {

		echo
		'<div class="container app-container" ng-app="stripe-wp">
                <div class="row">
                    <div class="col-sm-12">
                        <h1>Stripe for WordPress</h1>
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

}

?>