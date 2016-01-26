var wp_stripe = wp_stripe || {};

wp_stripe.app = angular.module( 'stripe-wp', ['ui.router', 'ngResource'] );

/*
 * Global Run
 */
wp_stripe.app.run( function( $rootScope, $state ) {
    $rootScope.isActiveNav = function( page ) {
        if( !$state.current.name ) { return }
        if( $state.current.name.indexOf( page ) >= 0 ) {
            return 'active';
        }

    }
});

/*
 * UI Router States
 */
wp_stripe.app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/');
    $stateProvider
        .state('main', {
            url: '/',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-main.html',
            controller: 'Settings',
        })
        .state('customers', {
            url: '/customers',
            templateUrl: stripe_wp_local.template_directory + '/stripe-wp-customers.list.html',
            controller: 'CustomerList'
        })
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
 * Stripe Factory
 */
wp_stripe.app.factory( 'Stripe', function( $resource, $q, $http ){
    return {
        get_settings: function(){
            var response = $q.defer();
            $http.get(stripe_wp_local.api_url + 'stripe-wp/settings').then(function(res) {
                response.resolve( res );
            });
            return response.promise;
        },
        save_settings: function( data ){
            var response = $q.defer();
            $http.post(stripe_wp_local.api_url + 'stripe-wp/settings', data).then(function(res) {
                response.resolve( res );
            });
            return response.promise;
        },
        get_customers: function( data ){
            var url = stripe_wp_local.api_url + 'stripe-wp/customers';
            if( data && data.id ){
                url = url + '?id=' + data.id;
            }
            var response = $q.defer();
            $http.get(url).then(function(res) {
                response.resolve( res );
            });
            return response.promise;
        }
    };
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
    Stripe.get_customers().then(function(res){
        $scope.more = res.data.has_more;
        $scope.customers = res.data.data;
    });

    $scope.loadMore = function(){
        if( !$scope.more ) {
            return false;
        }
        var data = {
            id: $scope.customers[ $scope.customers.length - 1].id
        }
        Stripe.get_customers(data).then(function(res){
            $scope.more = res.data.has_more;
            $scope.customers.push.apply($scope.customers, res.data.data);
        });

    }
}])