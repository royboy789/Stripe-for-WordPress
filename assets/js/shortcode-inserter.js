(function($){
    $(document).ready(function(){
        $('body').on('click', '#wp-stripe-shortcode-inserter', function(e){
            e.preventDefault();
            swal({
                title: "Choose A Plan",
                text:
                    '<div id="wp-stripe-shortcode-inserter-app"><div>' +
                        '<div ng-controller="PlansList">' +
                            '<span class="btn btn-primary buffer" ng-repeat="plan in plans" ng-click="insertShortCode( plan.id )">{{plan.name}} - ${{plan.amount / 100}} / {{plan.interval}}</span>' +
                            '<span class="red" ng-if="!plans.length">No Plans</span>' +
                        '</div>' +
                    '</div></div>',
                html: true,
                showCancelButton: true,
                showConfirmButton: false
            });
            angular.element($('#wp-stripe-shortcode-inserter-app')[0]).ready(function () {
                angular.bootstrap($('#wp-stripe-shortcode-inserter-app')[0], ['stripe-wp']);
            });
        });
    });
}(jQuery))