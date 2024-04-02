<?php

/*
Plugin Name: F4 Shipping Phone and E-Mail for WooCommerce
Plugin URI: https://github.com/faktorvier/f4-woocommerce-shipping-phone-and-e-mail
Description: Adds fields for e-mail and/or telephone to the WooCommerce shipping address.
Version: 1.0.19
Author: FAKTOR VIER
Author URI: https://www.f4dev.ch
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: f4-woocommerce-shipping-phone-and-e-mail
Domain Path: /languages/
Requires Plugins: woocommerce
WC requires at least: 8.0
WC tested up to: 8.7

This plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this plugin. If not, see https://www.gnu.org/licenses/gpl-2.0.html.

*/

if(!defined('ABSPATH')) exit; // don't access directly

define('F4_WCSPE_VERSION', '1.0.19');

define('F4_WCSPE_SLUG', 'f4-woocommerce-shipping-phone-and-e-mail');
define('F4_WCSPE_MAIN_FILE', __FILE__);
define('F4_WCSPE_BASENAME', plugin_basename(F4_WCSPE_MAIN_FILE));
define('F4_WCSPE_PATH', dirname(F4_WCSPE_MAIN_FILE) . DIRECTORY_SEPARATOR);
define('F4_WCSPE_URL', plugins_url('/', F4_WCSPE_MAIN_FILE));
define('F4_WCSPE_PLUGIN_FILE', basename(F4_WCSPE_BASENAME));
define('F4_WCSPE_PLUGIN_FILE_PATH', F4_WCSPE_PATH . F4_WCSPE_PLUGIN_FILE);

// Add autoloader
spl_autoload_register(function($class) {
	$class = ltrim($class, '\\');
	$ns_prefix = 'F4\\WCSPE\\';

	if(strpos($class, $ns_prefix) !== 0) {
		return;
	}

	$class_name = str_replace($ns_prefix, '', $class);
	$class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
	$class_file = F4_WCSPE_PATH . 'modules' . DIRECTORY_SEPARATOR . $class_path . '.php';

	if(file_exists($class_file)) {
		require_once $class_file;
	}
});

// Init core
F4\WCSPE\Core\Hooks::init();

?>
