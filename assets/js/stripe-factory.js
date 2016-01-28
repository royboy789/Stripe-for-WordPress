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
        customer: {
            get_customers: function( data ){
                var url = stripe_wp_local.api_url + 'stripe-wp/customers';
                if( data && data.starting_after ){
                    url = url + '?starting_after=' + data.starting_after;
                }
                if( data && data.id ){
                    url = url + '?id=' + data.id;
                }
                var response = $q.defer();
                $http.get(url).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            },
            save: function( data ){
                var url = stripe_wp_local.api_url + 'stripe-wp/customers/';
                if( data.id ) {
                    url = url + data.id;
                }
                var response = $q.defer();
                $http.post(url, data).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            },
            get: function( data ){
                var url = stripe_wp_local.api_url + 'stripe-wp/customers/';
                if( data.id ) {
                    url = url + data.id;
                }
                var response = $q.defer();
                $http.get(url).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            },
            delete: function( data ){
                var url = stripe_wp_local.api_url + 'stripe-wp/customers/';
                if( data.id ) {
                    url = url + data.id;
                } else {
                    return 'No Customer ID Set';
                }
                var response = $q.defer();
                $http.delete(url).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            },
        },
        plans: {
            get_plan: function( data ){
                var url = stripe_wp_local.api_url + 'stripe-wp/plans/';
                if( data && data.id ) {
                    url = url + data.id;
                }
                var response = $q.defer();
                $http.get(url).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            },
            save_plan: function( data ) {
                var url = stripe_wp_local.api_url + 'stripe-wp/plans/';
                if( data.id ) {
                    url = url + data.id;
                } else {
                    return 'No ID set';
                }
                var response = $q.defer();
                $http.post(url, data).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            },
            delete_plan: function( data ) {
                var url = stripe_wp_local.api_url + 'stripe-wp/plans/';
                if( data.id ) {
                    url = url + data.id;
                } else {
                    return 'No ID set';
                }
                var response = $q.defer();
                $http.delete(url, data).then(function(res) {
                    response.resolve( res );
                });
                return response.promise;
            }
        }
    };
});