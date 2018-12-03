<?php

namespace F4\WCSPE\Core;

/**
 * Core Hooks
 *
 * Hooks for the Core module
 *
 * @since 1.0.0
 * @package F4\WCSPE\Core
 */
class Hooks {
	/**
	 * @var array $settings All the module settings
	 */
	protected static $settings = array(
		'phone_field_enabled' => 'billing',
		'email_field_enabled' => 'billing'
	);

	/**
	 * Initialize the hooks
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_action('plugins_loaded', __NAMESPACE__ . '\\Hooks::core_loaded');
		add_action('init', __NAMESPACE__ . '\\Hooks::load_textdomain');
	}

	/**
	 * Fires once the core module is loaded
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function core_loaded() {
		do_action('F4/WCSPE/Core/set_constants');
		do_action('F4/WCSPE/Core/loaded');

		// Checkout and account fields
		add_filter('woocommerce_checkout_fields', __NAMESPACE__ . '\\Hooks::add_checkout_shipping_fields', 99);
		add_filter('woocommerce_shipping_fields', __NAMESPACE__ . '\\Hooks::add_address_shipping_fields', 99, 2);

		// Formatted address
		add_filter('woocommerce_order_formatted_shipping_address', __NAMESPACE__ . '\\Hooks::add_fields_to_formatted_order_address', 99, 2);
		add_filter('woocommerce_localisation_address_formats', __NAMESPACE__ . '\\Hooks::append_fields_to_localisation_address_formats', 99);
		add_filter('woocommerce_formatted_address_replacements', __NAMESPACE__ . '\\Hooks::replace_fields_in_formatted_address', 99, 2);

		// Backend
		add_filter('woocommerce_get_settings_account', __NAMESPACE__ . '\\Hooks::add_settings_fields', 99);
		add_filter('woocommerce_customer_meta_fields', __NAMESPACE__ . '\\Hooks::add_customer_meta_fields', 99);
		add_filter('woocommerce_admin_shipping_fields', __NAMESPACE__ . '\\Hooks::add_admin_shipping_fields', 99);
		add_action('current_screen', __NAMESPACE__ . '\\Hooks::add_fields_to_order_preview_template', 99);
		add_filter('woocommerce_admin_order_preview_get_order_details', __NAMESPACE__ . '\\Hooks::admin_order_preview_get_order_details', 99, 2);
		add_filter('plugin_action_links_' . F4_WCSPE_BASENAME, __NAMESPACE__ . '\\Hooks::add_settings_link_to_plugin_list');

		// Payment
		add_filter('woocommerce_paypal_args', __NAMESPACE__ . '\\Hooks::overwrite_paypal_args', 99, 2);

		// Privacy
		add_filter('woocommerce_privacy_export_customer_personal_data_props', __NAMESPACE__ . '\\Hooks::privacy_customer_personal_data_props', 99, 2);
		add_filter('woocommerce_privacy_erase_customer_personal_data_props', __NAMESPACE__ . '\\Hooks::privacy_customer_personal_data_props', 99, 2);
		add_filter('woocommerce_privacy_export_customer_personal_data_prop_value', __NAMESPACE__ . '\\Hooks::privacy_export_customer_personal_data_prop_value', 99, 3);
		add_filter('woocommerce_privacy_erase_customer_personal_data_prop', __NAMESPACE__ . '\\Hooks::privacy_erase_customer_personal_data_prop', 99, 3);

		add_filter('woocommerce_privacy_remove_order_personal_data_props', __NAMESPACE__ . '\\Hooks::privacy_order_personal_data_props', 99, 2);
		add_filter('woocommerce_privacy_export_order_personal_data_props', __NAMESPACE__ . '\\Hooks::privacy_order_personal_data_props', 99, 2);
		add_filter('woocommerce_privacy_export_order_personal_data_prop', __NAMESPACE__ . '\\Hooks::privacy_export_order_personal_data_prop', 99, 3);
		add_action('woocommerce_privacy_remove_order_personal_data', __NAMESPACE__ . '\\Hooks::privacy_remove_order_personal_data', 99);

		// REST
		//add_filter('woocommerce_rest_customer_schema', __NAMESPACE__ . '\\Hooks::rest_customer_schema', 99); // $properties
		//add_filter('woocommerce_api_customer_response', __NAMESPACE__ . '\\Hooks::api_customer_response', 99, 4) // $customer_data, $customer, $fields, $this->server
		//add_filter('woocommerce_api_order_response', __NAMESPACE__ . '\\Hooks::api_order_response', 99, 4); // $order_data, $order, $fields, $this->server
		//add_filter('woocommerce_api_customer_shipping_address', __NAMESPACE__ . '\\Hooks::api_customer_shipping_address', 99); // $shipping_address
		//add_filter('woocommerce_rest_shop_order_schema', __NAMESPACE__ . '\\Hooks::rest_shop_order_schema', 99); // $properties

		//add_action('woocommerce_api_create_order', __NAMESPACE__ . '\\Hooks::api_create_order', 99, 3); // $order->get_id(), $data, $this // add phone/email to order
		//add_action('woocommerce_api_edit_order', __NAMESPACE__ . '\\Hooks::api_edit_order', 99, 3); // $order->get_id(), $data, $this // add phone/email to order

		self::load_settings();
	}

	/**
	 * Load module settings
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function load_settings() {
		self::$settings = apply_filters('F4/WCSPE/load_settings', array(
			'phone_field_enabled' => get_option('woocommerce_enable_shipping_field_phone', 'billing'),
			'email_field_enabled' => get_option('woocommerce_enable_shipping_field_email', 'billing'),
		));
	}

	/**
	 * Load plugin textdomain
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function load_textdomain() {
		$locale = apply_filters('plugin_locale', get_locale(), 'f4-wc-shipping-phone-email');
		load_plugin_textdomain('f4-wc-shipping-phone-email', false, plugin_basename(F4_WCSPE_PATH . 'Core/Lang') . '/');
	}

	/**
	 * Add fields to the checkout shipping address form
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_checkout_shipping_fields($fields) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			if(self::$settings['phone_field_enabled'] === 'billing') {
				if(isset($fields['billing']['billing_phone'])) {
					$fields['shipping']['shipping_phone'] = $fields['billing']['billing_phone'];
				}
			} else {
				$fields['shipping']['shipping_phone'] = array(
					'label' => __('Phone', 'woocommerce'),
					'required' => self::$settings['phone_field_enabled'] === 'required',
					'type' => 'tel',
					'class' => array('form-row-wide'),
					'validate' => array('phone'),
					'autocomplete' => 'tel',
					'priority' => 100
				);
			}

			if(isset($fields['shipping']['shipping_phone'])) {
				$fields['shipping']['shipping_phone'] = apply_filters('F4/WCSPE/checkout_field_phone', $fields['shipping']['shipping_phone']);
			}
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			if(self::$settings['email_field_enabled'] === 'billing') {
				if(isset($fields['billing']['billing_email'])) {
					$fields['shipping']['shipping_email'] = $fields['billing']['billing_email'];
				}
			} else {
				$fields['shipping']['shipping_email'] = array(
					'label' => __('Email address', 'woocommerce'),
					'required' => self::$settings['email_field_enabled'] === 'required',
					'type' => 'email',
					'class' => array('form-row-wide'),
					'validate' => array('email'),
					'autocomplete' => 'tel',
					'priority' => 110
				);
			}

			if(isset($fields['shipping']['shipping_email'])) {
				$fields['shipping']['shipping_email'] = apply_filters('F4/WCSPE/checkout_field_email', $fields['shipping']['shipping_email']);
			}
		}

		return $fields;
	}

	/**
	 * Add fields shipping address
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_address_shipping_fields($address_fields, $country) {
		if(self::$settings['phone_field_enabled'] === 'billing' || self::$settings['email_field_enabled'] === 'billing') {
			$billing_address_fields = WC()->countries->get_address_fields($country, 'billing_');
		}

		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			if(self::$settings['phone_field_enabled'] === 'billing') {
				if(isset($billing_address_fields['billing_phone'])) {
					$address_fields['shipping_phone'] = $billing_address_fields['billing_phone'];
				}
			} else {
				$address_fields['shipping_phone'] = array(
					'label' => __('Phone', 'woocommerce'),
					'required' => self::$settings['phone_field_enabled'] === 'required',
					'type' => 'tel',
					'class' => array('form-row-wide'),
					'validate' => array('phone'),
					'autocomplete' => 'tel',
					'priority' => 100
				);
			}

			if(isset($address_fields['shipping_phone'])) {
				$address_fields['shipping_phone'] = apply_filters('F4/WCSPE/address_field_phone', $address_fields['shipping_phone']);
			}
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			if(self::$settings['email_field_enabled'] === 'billing') {
				if(isset($billing_address_fields['billing_email'])) {
					$address_fields['shipping_email'] = $billing_address_fields['billing_email'];
				}
			} else {
				$address_fields['shipping_email'] = array(
					'label' => __('Email address', 'woocommerce'),
					'required' => self::$settings['email_field_enabled'] === 'required',
					'type' => 'email',
					'class' => array('form-row-wide'),
					'validate' => array('email'),
					'autocomplete' => 'tel',
					'priority' => 110
				);
			}

			if(isset($address_fields['shipping_email'])) {
				$address_fields['shipping_email'] = apply_filters('F4/WCSPE/address_field_email', $address_fields['shipping_email']);
			}
		}

		return $address_fields;
	}

	/**
	 * Add fields to formatted order address
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_fields_to_formatted_order_address($address, $order) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$address['address_type'] = 'shipping';
			$address['phone'] = get_post_meta($order->get_id(), '_shipping_phone', true);
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$address['address_type'] = 'shipping';
			$address['email'] = get_post_meta($order->get_id(), '_shipping_email', true);
		}

		return $address;
	}

	/**
	 * Add fields to localisation address formats
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function append_fields_to_localisation_address_formats($formats) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			foreach($formats as $country => &$format) {
				$format .= "\n{phone}";
			}
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			foreach($formats as $country => &$format) {
				$format .= "\n{email}";
			}
		}

		return $formats;
	}

	/**
	 * Replace fields in formatted address
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function replace_fields_in_formatted_address($replace, $args) {
		if(self::$settings['phone_field_enabled'] === 'hidden' && self::$settings['email_field_enabled'] === 'hidden') {
			return $replace;
		}

		$current_screen = function_exists('get_current_screen') ? get_current_screen() : null;
		$current_screen = isset($current_screen->id) ? $current_screen->id : null;

		$is_shipping_address = isset($args['address_type']) && $args['address_type'] === 'shipping';
		$is_order_admin = in_array($current_screen, array('edit-shop_order', 'shop_order'));
		$is_order_preview = isset($_REQUEST['action']) && $_REQUEST['action'] === 'woocommerce_get_order_details';

		if(apply_filters('F4/WCSPE/append_fields_to_formatted_address', $is_shipping_address && !$is_order_admin && !$is_order_preview, $args)) {
			if(isset($args['phone'])) {
				$replace['{phone}'] = $args['phone'];
				$replace['{phone_upper}'] = strtoupper($args['phone']);
			}

			if(isset($args['email'])) {
				$replace['{email}'] = $args['email'];
				$replace['{email_upper}'] = strtoupper($args['email']);
			}
		} else {
			$replace['{phone_upper}'] = $replace['{phone}'] = '';
			$replace['{email_upper}'] = $replace['{email}'] = '';
		}

		return $replace;
	}

	/**
	 * Add settings fields to the woocommerce settings page
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_settings_fields($settings) {
		// Section start
		$fields_settings = array(
			array(
				'title' => __('Shipping Phone Number and E-Mail fields', 'f4-wc-shipping-phone-email'),
				'type' => 'title',
				'id' => 'shipping-phone-email'
			)
		);

		// Phone field enabled
		$fields_settings[] = array(
			'title' => __('Shipping Phone Number', 'f4-wc-shipping-phone-email'),
			'desc' => __('Default = The same settings as the delivery address will be used. ', 'f4-wc-shipping-phone-email'),
			'id' => 'woocommerce_enable_shipping_field_phone',
			'type' => 'select',
			'default' => 'billing',
			'css' => 'min-width:300px;',
			'desc_tip' =>  true,
			'options' => array(
				'billing' => __('Default', 'woocommerce'),
				'hidden' => __('Hidden', 'woocommerce'),
				'optional' => __('Optional', 'woocommerce'),
				'required' => __('Required', 'woocommerce')
			)
		);

		// E-mail field enabled
		$fields_settings[] = array(
			'title' => __('Shipping Email Address', 'f4-wc-shipping-phone-email'),
			'desc' => __('Default = The same settings as the delivery address will be used. ', 'f4-wc-shipping-phone-email'),
			'id' => 'woocommerce_enable_shipping_field_email',
			'type' => 'select',
			'default' => 'billing',
			'css' => 'min-width:300px;',
			'desc_tip' =>  true,
			'options' => array(
				'billing' => __('Default', 'woocommerce'),
				'hidden' => __('Hidden', 'woocommerce'),
				'optional' => __('Optional', 'woocommerce'),
				'required' => __('Required', 'woocommerce')
			)
		);

		// Section end
		$fields_settings[] = array(
			'type' => 'sectionend',
			'id' => 'shipping-phone-email'
		);

		$fields_settings = apply_filters('F4/WCSPE/settings_fields', $fields_settings);

		// Insert after registration options
		$insert_at_position = null;

		foreach($settings as $index => $setting) {
			if($setting['type'] === 'sectionend' && $setting['id'] === 'account_registration_options') {
				$insert_at_position = $index;
				break;
			}
		}

		array_splice($settings, $insert_at_position + 1, 0, $fields_settings);

		return $settings;
	}

	/**
	 * Add fields to backend user edit page
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_customer_meta_fields($fields) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$fields['shipping']['fields']['shipping_phone'] = apply_filters(
				'F4/WCSPE/customer_meta_field_phone',
				array(
					'label' => __('Phone', 'woocommerce'),
					'description' => ''
				)
			);
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$fields['shipping']['fields']['shipping_email'] = apply_filters(
				'F4/WCSPE/customer_meta_field_phone',
				array(
					'label' => __('Email address', 'woocommerce'),
					'description' => ''
				)
			);
		}

		return $fields;
	}

	/**
	 * Add fields to backend order
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_admin_shipping_fields($fields) {
		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$fields['email'] = apply_filters(
				'F4/WCSPE/admin_field_email',
				array(
					'label' => __('Email address', 'woocommerce')
				)
			);
		}

		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$fields['phone'] = apply_filters(
				'F4/WCSPE/admin_field_phone',
				array(
					'label' => __('Phone', 'woocommerce'),
					'wrapper_class' => '_billing_phone_field'
				)
			);
		}

		return $fields;
	}

	/**
	 * Add fields to backend order
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_fields_to_order_preview_template() {
		global $wc_list_table;

		if(is_object($wc_list_table) && get_class($wc_list_table) === 'WC_Admin_List_Table_Orders') {
			if(has_action('admin_footer', array($wc_list_table, 'order_preview_template'))) {
				remove_action('admin_footer', array($wc_list_table, 'order_preview_template'));
				add_action('admin_footer', __NAMESPACE__ . '\\Hooks::order_preview_template');
			}
		}
	}

	/**
	 * Output order preview template
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function order_preview_template() {
		global $wc_list_table;

		ob_start();
		$wc_list_table->order_preview_template();
		$template = ob_get_clean();

		$search = '<# if ( data.shipping_via ) { #>';
		$replace = '';

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$replace .= '<# if ( data.data.shipping.email ) { #>
				<strong>' . esc_html__('Email', 'woocommerce') . '</strong>
				<a href="mailto:{{ data.data.shipping.email }}">{{ data.data.shipping.email }}</a>
			<# } #>';
		}

		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$replace .= '<# if ( data.data.shipping.phone ) { #>
				<strong>' . esc_html__('Phone', 'woocommerce') . '</strong>
				<a href="tel:{{ data.data.shipping.phone }}">{{ data.data.shipping.phone }}</a>
			<# } #>';
		}

		echo apply_filters('F4/WCSPE/get_order_preview_template', str_replace($search, $replace . $search, $template));
	}

	/**
	 * Add fields to order preview template details
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function admin_order_preview_get_order_details($details, $order) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$details['data']['shipping']['phone'] = apply_filters(
				'F4/WCSPE/get_order_preview_detail_phone',
				get_post_meta($order->get_id(), '_shipping_phone', true)
			);
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$details['data']['shipping']['email'] = apply_filters(
				'F4/WCSPE/get_order_preview_detail_email',
				get_post_meta($order->get_id(), '_shipping_email', true)
			);
		}

		return $details;
	}

	/**
	 * Add settings link to plugin list
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_settings_link_to_plugin_list($links) {
		$links[] = '<a href="' . admin_url('admin.php?page=wc-settings&tab=account') . '">' . __('Settings') . '</a>';

		return $links;
	}

	/**
	 * Overwrite paypal arguments
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function overwrite_paypal_args($args, $order) {
		if(self::$settings['email_field_enabled'] === 'hidden' && self::$settings['phone_field_enabled'] === 'hidden') {
			return $args;
		}

		// Overwrite email
		if(self::$settings['email_field_enabled'] !== 'hidden') {
			// Get paypal gateway
			$wc_gateways      = new \WC_Payment_Gateways();
			$payment_gateways = $wc_gateways->get_available_payment_gateways();
			$paypal_gateway = null;

			foreach($payment_gateways  as $payment_gateway_name => $payment_gateway) {
				if($payment_gateway_name === 'paypal') {
					$paypal_gateway = $payment_gateway;
				}
			}

			if($paypal_gateway->get_option('send_shipping') === 'yes') {
				$args['email'] = get_post_meta($order->get_id(), '_shipping_email', true);
			}
		}

		// Overwrite phone
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$shipping_phone = get_post_meta($order->get_id(), '_shipping_phone', true);

			if(in_array($order->get_shipping_country(), array('US', 'CA'), true)) {
				$phone_number = str_replace(array('(', '-', ' ', ')', '.'), '', $shipping_phone);
				$phone_number = ltrim($phone_number, '+1');

				$args['night_phone_a'] = substr($phone_number, 0, 3);
				$args['night_phone_b'] = substr($phone_number, 3, 3);
				$args['night_phone_c'] = substr($phone_number, 6, 4);
			} else {
				$args['night_phone_b'] = $shipping_phone;
			}
		}

		return $args;
	}

	/**
	 * Add fields to the privacy customer data props
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function privacy_customer_personal_data_props($props, $customer) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$props['shipping_phone'] = __('Shipping Phone Number', 'f4-wc-shipping-phone-email');
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$props['shipping_email'] = __('Shipping Email Address', 'f4-wc-shipping-phone-email');
		}

		return $props;
	}

	/**
	 * Get privacy customer data props values
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function privacy_export_customer_personal_data_prop_value($value, $prop, $customer) {
		if($prop === 'shipping_phone') {
			$value = $customer->get_meta('shipping_phone');
		} elseif($prop === 'shipping_email') {
			$value = $customer->get_meta('shipping_email');
		}

		return $value;
	}

	/**
	 * Remove privacy customer data props values
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function privacy_erase_customer_personal_data_prop($erased, $prop, $customer) {
		if($prop === 'shipping_phone') {
			$customer->delete_meta_data('shipping_phone');
		} elseif($prop === 'shipping_email') {
			$customer->delete_meta_data('shipping_email');
		}

		return $erased;
	}

	/**
	 * Add fields to the privacy order data props
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function privacy_order_personal_data_props($props, $order) {
		if(self::$settings['phone_field_enabled'] !== 'hidden') {
			$props['shipping_phone'] = __('Shipping Phone Number', 'f4-wc-shipping-phone-email');
		}

		if(self::$settings['email_field_enabled'] !== 'hidden') {
			$props['shipping_email'] = __('Shipping Email Address', 'f4-wc-shipping-phone-email');
		}

		return $props;
	}

	/**
	 * Get privacy order data props values
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function privacy_export_order_personal_data_prop($value, $prop, $order) {
		if($prop === 'shipping_phone') {
			$value = get_post_meta($order->get_id(), '_shipping_phone', true);
		} elseif($prop === 'shipping_email') {
			$value = get_post_meta($order->get_id(), '_shipping_email', true);
		}

		return $value;
	}

	/**
	 * Remove privacy order data props values
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function privacy_remove_order_personal_data($order) {
		delete_post_meta($order->get_id(), '_shipping_phone');
		delete_post_meta($order->get_id(), '_shipping_email');

		return $value;
	}
}

?>
