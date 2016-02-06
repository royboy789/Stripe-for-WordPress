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

    $rootScope.nav_items = [
        {
            title: 'Dashboard',
            state: 'dashboard'
        },
        {
            title: 'Settings',
            state: 'settings'
        },
        {
            title: 'Plans',
            state: 'plans'
        },
        {
            title: 'Customers',
            state: 'customers'
        },
        {
            title: 'Coupons',
            state: 'coupons'
        }
    ]

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
wp_stripe.app.controller( 'Settings', ['$scope', '$rootScope', '$timeout', '$http', 'Stripe', function( $scope, $rootScope, $timeout, $http, Stripe ) {
    console.log('loading Settings page..');

    Stripe.get_settings().then(function(res){

        $scope.settings = {
            keys: {},
            mode: '',
            confirmation: {}
        };

        $http.get( stripe_wp_local.api_url + 'wp/v2/pages?_wpnonce=' + stripe_wp_local.nonce).then(function(res) {
            $scope.pages = res.data;
        });

        if( res.data.mode ) {
            $scope.settings.mode = res.data.mode;
        }
        if( res.data.keys.test ) {
            $scope.settings.keys.test = res.data.keys.test;
        }
        if( res.data.keys.prod ) {
            $scope.settings.keys.prod = res.data.keys.prod;
        }

        $scope.settings.confirmation = res.data.confirmation

        if( !$scope.settings.confirmation.message ) {
            $scope.settings.confirmation.message = '';
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
            swal({
                title: 'Updated',
                text: 'Settings Updated Successfully',
                type: 'success'
            });
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

    $scope.delCheck = function( delinquent ) {
        if( delinquent ) {
            return 'delinquent';
        }
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
}]);

/*
 * Customer Edit
 */
wp_stripe.app.controller( 'CustomerEdit', ['$scope', '$rootScope', '$stateParams', '$state', '$timeout', 'Stripe', 'Users', function( $scope, $rootScope, $stateParams, $state, $timeout, Stripe, Users ) {
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
            swal("Saved", "Customer " + $scope.customer.id + " saved", "success")

        });
    };

    $scope.deleteCustomer = function() {

        if( $scope.customer.metadata.user_id ) {
            $scope.wp_user = false;
            Users.get({nonce: stripe_wp_local.nonce, id: $scope.customer.metadata.user_id }, function(res){
                $scope.wp_user = true;
            }, function(res){
                if( res.data.code == 'rest_user_invalid_id' ) {
                    $scope.wp_user = false;
                }
            });
        }

        swal({
            title: 'Delete Confirmation',
            text: 'Are you sure you want to delete customer: '+ $scope.customer.id + '?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            closeOnConfirm: false,
        },
        function( isConfirm ) {

            if( isConfirm && $scope.wp_user ) {
                swal({
                    title: 'Delete WordPress User?',
                    text: 'Do you also want to remove the WordPress user? Or just the Stripe User',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Both',
                    cancelButtonText: 'Stripe Only',
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function( isConfirm ) {
                    if( isConfirm ) {
                        // Delete WP User
                        Users.delete({nonce: stripe_wp_local.nonce, id: $scope.customer.metadata.user_id, force: true }, function(res){
                            // Delete Stripe Customer
                            Stripe.customer.delete( $scope.customer ).then(function(res){
                                if( res.data.deleted ){
                                    swal({
                                        title: 'Deleted',
                                        text: 'Stripe Customer Deleted Successfully',
                                        type: 'success'
                                    });

                                    $state.go('customers');

                                    swal({
                                        title: 'Deleted',
                                        text: 'Customer & User Deleted Successfully',
                                        type: 'success'
                                    });
                                }
                            });
                        });
                    } else {
                        Stripe.customer.delete( $scope.customer).then(function(res){
                            if( res.data.deleted ){
                                swal({
                                    title: 'Deleted',
                                    text: 'Stripe Customer Deleted Successfully',
                                    type: 'success'
                                });
                                $state.go('customers');
                            }
                        });
                    }
                })
            } else {
                Stripe.customer.delete( $scope.customer).then(function(res){
                    if( res.data.deleted ){
                        swal({
                            title: 'Deleted',
                            text: 'Stripe Customer Deleted Successfully',
                            type: 'success'
                        });
                        $state.go('customers');
                    }
                });
            }
        })

    };
}]);

/*
 * Plans List
 */
wp_stripe.app.controller( 'PlansList', ['$scope', '$rootScope', '$stateParams', '$timeout', 'Stripe', function( $scope, $rootScope, $stateParams, $timeout, Stripe ) {
    console.log('loading plans...');
    Stripe.plans.get_plan().then(function(res){
        $scope.plans = res.data.data;
    });

    $scope.insertShortCode = function( id ) {
        console.log( id );
        swal.close();
        jQuery('#wp-stripe-shortcode-inserter-app').empty();
        wp.media.editor.insert('[stripe-wp-customer plan_id="' + id + '"]');
    }

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
wp_stripe.app.controller( 'PlanEdit', ['$scope', '$rootScope', '$stateParams', '$state', '$timeout', 'Stripe', function( $scope, $rootScope, $stateParams, $state, $timeout, Stripe ) {
    console.log('loading plans...');
    Stripe.plans.get_plan( $stateParams ).then(function(res){
        $scope.plan = res.data;
    });

    $scope.savePlan = function() {
        Stripe.plans.save_plan( $scope.plan ).then(function(res){
            $scope.plan = res.data;
            swal({
                title: 'Updated',
                text: 'Plan Updated Successfully',
                type: 'success'
            });
        });
    };

    $scope.deletePlan = function() {
        swal({
            title: 'Delete Stripe Plan',
            text: 'Are you sure you want to remove this plan?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            closeOnConfirm: false,
        },
        function( isConfirm ) {
            Stripe.plans.delete_plan($scope.plan).then(function (res) {
                if (res.data.deleted) {
                    swal({
                        title: 'Deleted',
                        text: 'Plan Deleted Successfully',
                        type: 'success'
                    });
                    $state.go('plans');
                }
            });
        });
    }

}]);


/*
 * Coupons Controller
 */
wp_stripe.app.controller( 'CouponList', ['$scope', '$rootScope', 'Stripe', function( $scope, $rootScope, Stripe ) {
    Stripe.coupons.get_coupons().then(function (res) {
        $scope.more = res.data.has_more;
        $scope.coupons = res.data.data;
    });
}]);


/*
 * Coupons Detail
 */
wp_stripe.app.controller( 'CouponDetail', ['$scope', '$rootScope', '$stateParams', 'Stripe', function( $scope, $rootScope, $stateParams, Stripe ) {
    Stripe.coupons.get_coupons( $stateParams ).then(function(res){
        $scope.coupon = res.data;
    });

}]);

/*
 * New Customer Directive
 */
wp_stripe.app.directive('stripeCustomer', function() {
    return {
        restrict: 'E',
        templateUrl: stripe_wp_local.template_directory + '/directives/stripe-wp-customer.new.html',
        scope: {
            planId: '@planId'
        },
        controller: ['$scope', 'Stripe', 'Users', function($scope, Stripe, Users){
            $scope.group_step = 1;
            $scope.coupon_invalid = false;
            $scope.change_step = function( step, back ) {
                back = typeof back !== 'undefined' ? back : false;

                if( $scope.group_step > parseInt( step ) ) {
                    back = true;
                }

                if( back || $scope.stepValidate( $scope.group_step.toString() ) ) {
                    $scope.group_step = step;
                } else {
                    swal({
                        'title' : 'Required Fields',
                        'text' : 'All Fields Required',
                        'type' : 'error'
                    });
                }
            }
            $scope.user = {
                cc: {
                    exp: {}
                }
            };

            $scope.stepValidate = function( step ) {
                switch( step ) {
                    case '1':
                        if (
                            $scope.user.name &&
                            $scope.user.name.first &&
                            $scope.user.name.last &&
                            $scope.user.phone &&
                            $scope.user.address &&
                            $scope.user.address.line1 &&
                            $scope.user.address.city &&
                            $scope.user.address.postal_code &&
                            $scope.user.address.state.length
                        ) {
                            return true;
                        }
                    break;
                    case '2':
                        if(
                            $scope.user.email &&
                            $scope.user.username &&
                            $scope.user.pass &&
                            $scope.pass
                        ) {
                            if( $scope.pass != $scope.user.pass ) {
                                $scope.pass_mismatch = true;
                            } else {
                                $scope.pass_mismatch = false;
                                return true;
                            }
                        }
                    break;
                    case '3':
                        if(
                            $scope.user.cc.number &&
                            $scope.user.cc.exp.month &&
                            $scope.user.cc.exp.year &&
                            $scope.user.cc.cvc
                        ) {
                            return true;
                        }
                    break;
                    default:
                        return false;

                }
            }


            $scope.newUser = function() {
                if( !$scope.planId ) {
                    swal({
                        title: 'No Plan ID Set',
                        text: 'No Subscription Plan ID Set',
                        type: 'error',
                    });
                    return false;
                }
                $scope.user.plan_id = $scope.planId;

                if( $scope.user.coupon ) {
                    Stripe.coupons.get_coupons({id: $scope.user.coupon}).then(function(res){
                        if( !res.data.id ) {
                            $scope.coupon_invalid = true;
                        } else {
                            $scope.coupon_invalid = false;
                            Stripe.customer.new( $scope.user ).then(function(res){
                                $scope.user = {
                                    cc: {}
                                };

                                if( !stripe_wp_local.confirmation.type ) {
                                    swal({
                                        'title' : 'Success!',
                                        'text' : 'We have successfully added you!',
                                        'type' : 'success'
                                    });
                                }

                                if( stripe_wp_local.confirmation.type == 'message' ) {
                                    swal({
                                        'title' : 'Success!',
                                        'text' : stripe_wp_local.confirmation.message,
                                        'type' : 'success'
                                    });
                                }

                                if( stripe_wp_local.confirmation.type == 'page' ) {
                                    window.location = stripe_wp_local.confirmation.page + '?new_cus=' + res.data.metadata.user_id;
                                }

                            }, function(res){
                                console.log( 'error', res );
                                if( res.data.message && res.data.message.indexOf('Sorry, that username') > -1 ) {
                                    $scope.group_step = 2;
                                };
                                if( res.data.data.user ) {
                                    Users.delete({nonce: stripe_wp_local.nonce, id: res.data.data.user, force: true }, function(res){
                                        console.log('user_delete', res );
                                    });
                                }
                                swal({
                                    'title' : 'Error',
                                    'text' : 'An Error Occured: ' + res.data.message,
                                    'type' : 'error'
                                });
                            });
                        }
                    }, function( error ) {
                        console.log( error );
                        $scope.coupon_invalid = true;
                        return false;
                    });
                } else {
                    Stripe.customer.new( $scope.user ).then(function(res){
                        $scope.user = {
                            cc: {}
                        };

                        if( !stripe_wp_local.confirmation.type ) {
                            swal({
                                'title' : 'Success!',
                                'text' : 'We have successfully added you!',
                                'type' : 'success'
                            });
                        }

                        if( stripe_wp_local.confirmation.type == 'message' ) {
                            swal({
                                'title' : 'Success!',
                                'text' : stripe_wp_local.confirmation.message,
                                'type' : 'success'
                            });
                        }

                        if( stripe_wp_local.confirmation.type == 'page' ) {
                            window.location = stripe_wp_local.confirmation.page + '?new_cus=' + res.data.metadata.user_id;
                        }

                    }, function(res){
                        console.log( 'error', res );
                        if( res.data.message && res.data.message.indexOf('Sorry, that username') > -1 ) {
                            $scope.group_step = 2;
                        };
                        if( res.data.data.user ) {
                            Users.delete({nonce: stripe_wp_local.nonce, id: res.data.data.user, force: true }, function(res){
                                console.log('user_delete', res );
                            });
                        }
                        swal({
                            'title' : 'Error',
                            'text' : 'An Error Occured: ' + res.data.message,
                            'type' : 'error'
                        });
                    });
                }

            }
        }]
    }
});