wp_stripe.app.factory( 'Users', function( $resource, $q, $http ){
   return  $resource( stripe_wp_local.api_url + 'wp/v2/users/:id?_wpnonce=:nonce', {
       id: '@id',
       nonce: '@nonce'
   })
});
