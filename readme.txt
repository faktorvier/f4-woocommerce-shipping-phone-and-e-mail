=== F4 Shipping Phone and E-Mail for WooCommerce ===
Contributors: faktorvier
Donate link: https://www.faktorvier.ch/donate/
Tags: woocommerce, checkout, shipping, telephone, email, field, fields, shop, ecommerce, order, account
Requires at least: 4.4.0
Tested up to: 5.1
Requires PHP: 5.6
Stable tag: 1.0.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds fields for e-mail and/or telephone to the WooCommerce shipping address.

== Description ==

F4 Shipping Phone and E-Mail for WooCommerce does exactly what the name says. It adds (often needed) fields
for e-mail and/or telephone number to the shipping address. Here are a few things the plugin does:

* Adds e-mail and/or telephone field to the shipping address checkout form
* Adds e-mail and/or telephone field to the edit shipping address form
* Adds e-mail and/or telephone field to the edit order backend page
* Shows e-mail and/or telephone field in privacy data export
* Erases e-mail and/or telephone data if privacy erase is requested
* Shows e-mail and/or telephone field in orders (thank you page, email etc.)
* Full integration into the PayPal payment gateway

= Usage =

This plugin works out-of-the-box. By default, the settings from the billing address
are used for both fields (e-mail = required and telephone = required, optional or hidden, according to the billing address settings).

You can change the settings for both fields on the Accounts & Privacy screen in your WooCommerce settings. Both fields you can hide or set to optional/required.

= Features overview =

* Adds e-mail and/or telephone fields
* Works without configuration
* Can be configurated for both fields
* Easy to use
* Lightweight and optimized
* 100% free!

= Planned features =

* Full integration into API and REST
* Compatibility check for other popular payment gateways

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/f4-woocommerce-shipping-phone-and-e-mail` directory, or install the plugin through the WordPress plugins screen directly
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Woocommerce -> Settings -> Accounts & Privacy screen to configure the plugin

== Screenshots ==

1. Fields in checkout shipping form
2. Fields on order confirmation page
3. Fields in order confirmation e-mail
4. Fields on the order admin page
5. Fields in edit address form
6. Field configuration in WooCommerce settings

== Changelog ==

= 1.0.3 =
* Fix formatted output

= 1.0.2 =
* Fix "load shipping address" function in order backend

= 1.0.1 =
* Update plugin slug and basename for better compatibility with the plugin name

= 1.0.0 =
* Initial stable release
