<?php
/**
 * Empty Cart Button for WooCommerce - Section Settings
 *
 * @version 1.2.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Empty_Cart_Button_Settings_Section' ) ) :

class Alg_WC_Empty_Cart_Button_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_empty_cart_button',                   array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_empty_cart_button' . '_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 1.2.1
	 * @since   1.0.0
	 */
	function get_settings() {
		return array_merge( $this->get_section_settings(), array(
			array(
				'title'     => __( 'Reset Settings', 'empty-cart-button-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_wc_empty_cart_button' . '_' . $this->id . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'empty-cart-button-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'empty-cart-button-for-woocommerce' ) . '</strong>',
				'id'        => 'alg_wc_empty_cart_button' . '_' . $this->id . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_wc_empty_cart_button' . '_' . $this->id . '_reset_options',
			),
		) );
	}

}

endif;
