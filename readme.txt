=== F4 Shipping Phone and E-Mail for WooCommerce ===
Contributors: faktorvier
Donate link: https://www.faktorvier.ch/donate/
Tags: woocommerce, checkout, shipping, telephone, email, field, fields, shop, ecommerce, order, account
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 7.0
Stable tag: 1.0.14
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

== Frequently Asked Questions ==

= Since WooCommerce 5.6 the shipping phone is missing in the email addresses and on the order page =

Since WooCommerce 5.6 the shipping phone is natively supported in the formatted shipping address. You have to make sure that your template files
(emails/email-addresses.php, emails/plain/email-addresses.php, order/order-details-customer.php) are up-to-date (@version 5.6.0). If your template is not at
least 5.6 compatible then you can simply add the following hook to your functions.php. This hook should restore the previous functionality until your templates are up-to-date:

`add_filter('F4/WCSPE/append_phone_field_to_formatted_address', '__return_true')`

= The shipping email/phone fields are displayed wrong or different than the billing email/phone fields =

Since WooCommerce 5.6 the order of our shipping email/phone fields is different than the billing email/phone fields. Thats because our simple solution to add
this fields to every theme without changing the code is limited and the WooCommerce 5.6 update changes a few things in the template files that prevents us from
displaying the fields in the right order. Also the shipping fields may look different than the billing fields, because we don't add any html code to format the output.
If you want to change the order of the billing phone/email or the displayed output, you can follow these steps to disable our default output and add your own code:

Add the following hook to your theme (functions.php):

	add_filter('F4/WCSPE/append_email_field_to_formatted_address', '__return_false');
	add_filter('F4/WCSPE/append_phone_field_to_formatted_address', '__return_false'); // only for versions lesser than 5.6

Search in the template file `emails/email-addresses.php` for the following code:

	<address class="address">
		<?php echo wp_kses_post( $shipping ); ?>
		<?php if ( $order->get_shipping_phone() ) : ?>
			<br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); ?>
		<?php endif; ?>
	</address>

and replace it with this code:

	<address class="address">
		<?php echo wp_kses_post( $shipping ); ?>
		<?php if ( $order->get_shipping_phone() ) : ?>
			<br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); ?>
		<?php endif; ?>
		<?php if ( $order->get_meta('_shipping_email') ) : ?>
			<br/><?php echo esc_html( $order->get_meta('_shipping_email') ); ?>
		<?php endif; ?>
	</address>

Search in the template file `emails/plain/email-addresses.php` for the following code:

	if ( $order->get_shipping_phone() ) {
		echo $order->get_shipping_phone() . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

and replace it with this code:

	if ( $order->get_shipping_phone() ) {
		echo $order->get_shipping_phone() . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( $order->get_meta('_shipping_email') ) {
		echo $order->get_meta('_shipping_email') . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

Search in the template file `order/order-details-customer.php` for the following code:

	<?php if ( $order->get_shipping_phone() ) : ?>
		<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
	<?php endif; ?>

and replace it with this code:

	<?php if ( $order->get_shipping_phone() ) : ?>
		<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
	<?php endif; ?>

	<?php if ( $order->get_meta('_shipping_email') ) : ?>
		<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_meta('_shipping_email') ); ?></p>
	<?php endif; ?>

The code may vary in your theme, you just have to look for similar looking code.

= Is it really free? =

Yes, absolutely!

== Screenshots ==

1. Fields in checkout shipping form
2. Fields on order confirmation page
3. Fields in order confirmation e-mail
4. Fields on the order admin page
5. Fields in edit address form
6. Field configuration in WooCommerce settings

== Changelog ==

= 1.0.14 =
* Support WordPress 5.9

= 1.0.13 =
* Fix phone field output in formatted address for WooCommerce 5.6
* Add hooks to individually hide email or phone field in formatted address
* Add some FAQ
* Support WooCommerce 5.6

= 1.0.12 =
* Fix condition for formatted part in address block
* Support WooCommerce 5.5
* Support WordPress 5.8

= 1.0.11 =
* Support WooCommerce 5.0
* Support WordPress 5.7

= 1.0.10 =
* Support WooCommerce 4.8
* Support WordPress 5.6

= 1.0.9 =
* Save guest checkout fields in session

= 1.0.8 =
* Support WooCommerce 4.4
* Support WordPress 5.5

= 1.0.7 =
* Update translations

= 1.0.6 =
* Support WooCommerce 4.0
* Support WordPress 5.4

= 1.0.5 =
* Fix privacy export and erase

= 1.0.4 =
* Add donation link
* Rename plugin according to the new naming conventions

= 1.0.3 =
* Fix formatted output

= 1.0.2 =
* Fix "load shipping address" function in order backend

= 1.0.1 =
* Update plugin slug and basename for better compatibility with the plugin name

= 1.0.0 =
* Initial stable release
