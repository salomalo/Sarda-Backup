<?php

/**
 * The Theme My Login Redirection Extension
 *
 * @package Theme_My_Login_Redirection
 */

/*
Plugin Name: Theme My Login Redirection
Plugin URI: https://thememylogin.com/extensions/redirection
Description: Adds redirection support to Theme My Login.
Author: Theme My Login
Author URI: https://thememylogin.com
Version: 1.0.1
Text Domain: theme-my-login-redirection
Network: true
*/

// Bail if TML is not active
if ( ! class_exists( 'Theme_My_Login_Extension' ) ) {
	return;
}

/**
 * The class used to implement the Redirection extension.
 */
class Theme_My_Login_Redirection extends Theme_My_Login_Extension {

	/**
	 * The extension name.
	 *
	 * @var string
	 */
	protected $name = 'theme-my-login-redirection';

	/**
	 * The extension version.
	 *
	 * @var string
	 */
	protected $version = '1.0.1';

	/**
	 * The extension's documentation URL.
	 *
	 * @var string
	 */
	protected $documentation_url = 'https://docs.thememylogin.com/category/28-redirection';

	/**
	 * The extension's support URL.
	 *
	 * @var string
	 */
	protected $support_url = 'https://thememylogin.com/support';

	/**
	 * The extension's store URL.
	 *
	 * @var string
	 */
	protected $store_url = 'https://thememylogin.com';

	/**
	 * The extension's item ID.
	 *
	 * @var int
	 */
	protected $item_id = 52;

	/**
	 * The option name used to store the license key.
	 *
	 * @var string
	 */
	protected $license_key_option = 'tml_redirection_license_key';

	/**
	 * The option name used to store the license status.
	 *
	 * @var string
	 */
	protected $license_status_option = 'tml_redirection_license_status';

	/**
	 * Set class properties.
	 *
	 * @since 1.0
	 */
	protected function set_properties() {
		$this->title = __( 'Redirection', 'theme-my-login-redirection' );
	}

	/**
	 * Include extension files.
	 *
	 * @since 1.0
	 */
	protected function include_files() {
		require $this->path . 'functions.php';

		if ( is_admin() ) {
			require $this->path . 'admin.php';
		}
	}

	/**
	 * Add extension actions.
	 *
	 * @since 1.0
	 */
	protected function add_actions() {
		// Add the referer field to the login form
		add_action( 'wp', 'tml_redirection_add_referer_field_to_login_form' );

		if ( is_admin() ) {
			// Add admin styles
			add_action( 'admin_print_styles', 'tml_redirection_admin_print_styles' );
		}
	}

	/**
	 * Add extension filters.
	 *
	 * @since 1.0
	 */
	protected function add_filters() {
		// Handle login redirect
		add_filter( 'login_redirect', 'tml_redirection_login_redirect', 10, 3 );

		// Handle logout redirect
		add_filter( 'logout_redirect', 'tml_redirection_logout_redirect', 10, 3 );
	}

	/**
	 * Get the extension settings page args.
	 *
	 * @since 1.0
	 *
	 * @return array The extension settings page args.
	 */
	public function get_settings_page_args() {
		return array(
			'page_title' => __( 'Theme My Login Redirection Settings', 'theme-my-login-recaptcha' ),
			'menu_title' => __( 'Redirection', 'theme-my-login-redirection' ),
			'menu_slug' => 'theme-my-login-redirection',
		);
	}

	/**
	 * Get the extension settings sections.
	 *
	 * @since 1.0
	 *
	 * @return array The extension settings sections.
	 */
	public function get_settings_sections() {
		return tml_redirection_admin_get_settings_sections();
	}

	/**
	 * Get the extension settings fields.
	 *
	 * @since 1.0
	 *
	 * @return array The extension settings fields.
	 */
	public function get_settings_fields() {
		return tml_redirection_admin_get_settings_fields();
	}

	/**
	 * Update the extension.
	 *
	 * @since 1.0
	 */
	protected function update() {
		$version = get_site_option( '_tml_redirection_version' );

		if ( version_compare( $version, $this->version, '>=' ) ) {
			return;
		}

		tml_redirection_migrate_options();

		update_site_option( '_tml_redirection_version', $this->version );
	}
}

tml_register_extension( new Theme_My_Login_Redirection( __FILE__ ) );
