var wp_stripe = wp_stripe || {};

wp_stripe.app = angular.module( 'stripe-wp', ['ui.router', 'ngResource'] );

/*
 * Global Run
 */
wp_stripe.app.run( function( $rootScope, $state ) {
    $rootScope.isActiveNav = function (page) {
        if (!$state.current.name) {
            return
        }
        if ($state.current.name.indexOf(page) >= 0) {
            return 'active';
        }
    };

    $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
        //console.log(toState);
    });
});


/*
 * Filter to trust HTML
*/
wp_stripe.app.filter( 'to_trusted', function( $sce ){
    return function( text ){
        return $sce.trustAsHtml( text );
    }
});


/*
 * Settings Controller
 */
wp_stripe.app.controller( 'Settings', ['$scope', '$rootScope', '$timeout', 'Stripe', function( $scope, $rootScope, $timeout, Stripe ) {
    console.log('loading Settings page..');

    Stripe.get_settings().then(function(res){
        $scope.settings = {
            keys: {},
            mode: ''
        };
        if( res.data.mode ) {
            $scope.settings.mode = res.data.mode;
        }
        if( res.data.keys.test ) {
            $scope.settings.keys.test = res.data.keys.test;
        }
        if( res.data.keys.prod ) {
            $scope.settings.keys.prod = res.data.keys.prod;
        }
    }, function(res){
        $scope.settings = {
            keys: {
                prod: '',
                test: ''
            }
        }
    });

    $scope.updateKeys = function(){
        Stripe.save_settings($scope.settings).then(function(res){
            $scope.updated = true;
            $timeout(function(){
                $scope.updated = false;
            }, 1500)
        })
    }

}]);

/*
 * Customers Controller
 */
wp_stripe.app.controller( 'CustomerList', ['$scope', '$rootScope', 'Stripe', function( $scope, $rootScope, Stripe ) {
    Stripe.customer.get_customers().then(function(res){
        $scope.more = res.data.has_more;
        $scope.customers = res.data.data;
    });

    $scope.loadMore = function(){
        if( !$scope.more ) {
            return false;
        };
        var data = {
            starting_after: $scope.customers[$scope.customers.length - 1].id
        };
        Stripe.customer.get_customers(data).then(function(res){
            $scope.more = res.data.has_more;
            $scope.customers.push.apply($scope.customers, res.data.data);
        });

    }
}]);

/*
 * Customer Detail
 */
wp_stripe.app.controller( 'CustomerDetail', ['$scope', '$rootScope', '$stateParams', 'Stripe', function( $scope, $rootScope, $stateParams, Stripe ) {
    console.log('loading customer...');
    Stripe.customer.get_customers( $stateParams).then(function(res){
        $scope.customer = res.data;
    });

    $scope.showValue = function( key ) {
        var no_show = ['subscriptions', 'metadata', 'account_balance'];
        if( no_show.indexOf(key) > -1 ) { return false; } else { return true; }
    }
}]);

/*
 * Customer Edit
 */
wp_stripe.app.controller( 'CustomerEdit', ['$scope', '$rootScope', '$stateParams', '$timeout', 'Stripe', function( $scope, $rootScope, $stateParams, $timeout, Stripe ) {
    console.log('loading customer...');
    Stripe.customer.get( $stateParams ).then(function(res){
        $scope.customer = res.data;
    });

    $scope.showInput = function( key ) {
        var no_show = ['account_balance', 'coupon', 'description', 'metadata' ];
        if( no_show.indexOf(key) > -1 ) { return true; } else { return false; }
    };

    $scope.saveCustomer = function() {
        Stripe.customer.save( $scope.customer ).then(function(res){
            $scope.customer = res.data;
            $scope.updated = true;
            $timeout(function(){
                $scope.updated = false;
            }, 2000)
        });
    }
}]);

/*
 * Plans List
 */
wp_stripe.app.controller( 'PlansList', ['$scope', '$rootScope', '$stateParams', '$timeout', 'Stripe', function( $scope, $rootScope, $stateParams, $timeout, Stripe ) {
    console.log('loading plans...');
    Stripe.plans.get_plan().then(function(res){
        $scope.plans = res.data.data;
    });

}]);

/*
 * Plans Detail
 */
wp_stripe.app.controller( 'PlanDetail', ['$scope', '$rootScope', '$stateParams', '$timeout', 'Stripe', function( $scope, $rootScope, $stateParams, $timeout, Stripe ) {
    console.log('loading plans...');
    Stripe.plans.get_plan( $stateParams ).then(function(res){
        $scope.plan = res.data;
    });

}]);

/*
 * Plans Edit
 */
wp_stripe.app.controller( 'PlanEdit', ['$scope', '$rootScope', '$stateParams', '$timeout', 'Stripe', function( $scope, $rootScope, $stateParams, $timeout, Stripe ) {
    console.log('loading plans...');
    Stripe.plans.get_plan( $stateParams ).then(function(res){
        $scope.plan = res.data;
    });

    $scope.savePlan = function() {
        Stripe.plans.save_plan( $scope.plan ).then(function(res){
            $scope.plan = res.data;
            $scope.updated = true;
            $timeout(function(){
                $scope.updated = false;
            }, 2000)
        });
    }

}]);