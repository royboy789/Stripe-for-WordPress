/*
 * UI Router States
 */
wp_stripe.app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/');
    $stateProvider
        .state('dashboard', {
            url: '/',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-dashboard.html'
        })
        .state('settings', {
            url: '/settings',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-settings.html',
            controller: 'Settings',
        })
        .state('customers', {
            url: '/customers',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-customers.list.html',
            controller: 'CustomerList'
        })
        .state('customerDetail', {
            url: '/customers/detail/:id',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-customers.detail.html',
            controller: 'CustomerDetail'
        })
        .state('customerEdit', {
            url: '/customers/edit/:id',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-customers.edit.html',
            controller: 'CustomerEdit'
        })
        .state('plans', {
            url: '/plans',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-plans.list.html',
            controller: 'PlansList'
        })
        .state('planDetail', {
            url: '/plans/detail/:id',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-plans.detail.html',
            controller: 'PlanDetail'
        })
        .state('planEdit', {
            url: '/plans/edit/:id',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-plans.edit.html',
            controller: 'PlanEdit'
        })
        .state('coupons', {
            url: '/coupons',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-coupons.list.html',
            controller: 'CouponList'
        })
        .state('couponDetail', {
            url: '/coupons/detail/:id',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-coupons.detail.html',
            controller: 'CouponDetail'
        })
});