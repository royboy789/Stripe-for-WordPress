#Stripe for WordPress
This is a Stripe plugin for WordPress. A lightweight E-Commerce solution. Instead of managing both WordPress Products and products / subscription in Stripe, have Stripe handle everything!

##API
This plugin utilizes both the __WordPress REST API__ and __Stripe PHP Lib API__ to make sure your data is up to date.

##Angular
Plugin is built in AngularJS, it is a single page client side application that runs in the WordPress admin dashboard.

##REST API Endpoints
I have created a few endpoins if you want to extend. All under the `stripe-wp` namespace  
+ `stripe-wp/settings` - save the secret keys and mode
+ `stripe-wp/customers` - __GET__ customers
+ `stripe-wp/customers/:id` - __GET__ customer |  __POST__ to save customer | __DELETE__ a customer
+ `stripe-wp/plans` - __GET__ plans
+ `stripe/plans/:id` - __GET__ plan | __POST__ to save plan | __DELETE__ a plan

#To Install
+ Clone Repo
+ Run `npm install`
+ Run `gulp`
+ Move `build`, `inc`, `templates`, `edd` and `stripe-for-wp.php` into a new directory within your `wp-content/plugins`
+ Login to your dashboard and activate the `Stripe for WordPress` plugin

##TO-DO
all to-do, issues, and feature requests are in [issues](https://github.com/royboy789/Stripe-for-WordPress/issues)
