<?php
/*
Plugin Name: Empty Cart Button for WooCommerce
Plugin URI: https://wpfactory.com/item/empty-cart-button-woocommerce/
Description: "Empty cart" button for WooCommerce.
Version: 1.2.1
Author: Algoritmika Ltd
Author URI: http://algoritmika.com
Text Domain: empty-cart-button-for-woocommerce
Domain Path: /langs
Copyright: � 2018 Algoritmika Ltd.
WC tested up to: 3.4
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

if ( 'empty-cart-button-for-woocommerce.php' === basename( __FILE__ ) ) {
	// Check if Pro is active, if so then return
	$plugin = 'empty-cart-button-for-woocommerce-pro/empty-cart-button-for-woocommerce-pro.php';
	if (
		in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
		( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

if ( ! class_exists( 'Alg_WC_Empty_Cart_Button' ) ) :

/**
 * Main Alg_WC_Empty_Cart_Button Class
 *
 * @class   Alg_WC_Empty_Cart_Button
 * @version 1.2.1
 * @since   1.0.0
 */
final class Alg_WC_Empty_Cart_Button {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.2.1';

	/**
	 * @var   Alg_WC_Empty_Cart_Button The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Empty_Cart_Button Instance
	 *
	 * Ensures only one instance of Alg_WC_Empty_Cart_Button is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_Empty_Cart_Button - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Empty_Cart_Button Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @access  public
	 */
	function __construct() {

		// Set up localisation
		load_plugin_textdomain( 'empty-cart-button-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Include required files
		$this->includes();

		// Settings & Scripts
		if ( is_admin() ) {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		}
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @version 1.2.1
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_empty_cart_button' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'empty-cart-button-for-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a target="_blank" href="https://wpfactory.com/item/empty-cart-button-woocommerce/">' . __( 'Unlock All', 'empty-cart-button-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.2.1
	 * @since   1.0.0
	 */
	function includes() {
		// Settings
		require_once( 'includes/admin/class-alg-wc-empty-cart-button-settings-section.php' );
		$this->settings = array();
		$this->settings['general'] = require_once( 'includes/admin/class-alg-wc-empty-cart-button-settings-general.php' );
		if ( is_admin() && get_option( 'alg_wc_empty_cart_button_version', '' ) !== $this->version ) {
			update_option( 'alg_wc_empty_cart_button_version', $this->version );
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
		// Core
		require_once( 'includes/class-alg-wc-empty-cart-button-core.php' );
	}

	/**
	 * version_updated.
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 */
	function version_updated() {
		foreach ( $this->settings as $section ) {
			foreach ( $section->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}
	}

	/**
	 * Add Empty Cart Button settings tab to WooCommerce settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = include( 'includes/admin/class-alg-wc-settings-empty-cart-button.php' );
		return $settings;
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_wc_empty_cart_button' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Empty_Cart_Button to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_Empty_Cart_Button
	 */
	function alg_wc_empty_cart_button() {
		return Alg_WC_Empty_Cart_Button::instance();
	}
}

alg_wc_empty_cart_button();
