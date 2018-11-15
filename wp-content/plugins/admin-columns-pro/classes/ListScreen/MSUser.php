<?php

namespace ACP\ListScreen;

use AC;
use ACP\Filtering;

class MSUser extends AC\ListScreen\User {

	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Network Users' ) )
		     ->set_singular_label( __( 'Network User' ) )
		     ->set_key( 'wp-ms_users' )
		     ->set_screen_base( 'users-network' )
		     ->set_screen_id( 'users-network' )
		     ->set_group( 'network' )
		     ->set_network_only( true );
	}

	/**
	 * @return \WP_MS_Users_List_Table
	 */
	public function get_list_table() {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-ms-users-list-table.php' );

		return new \WP_MS_Users_List_Table( array( 'screen' => $this->get_screen_id() ) );
	}

	/**
	 * @since 2.0
	 * @return string Link
	 */
	public function get_screen_link() {
		return network_admin_url( 'users.php' );
	}

	/**
	 * @since 4.0
	 * @return string HTML
	 */
	public function get_single_row( $id ) {
		ob_start();
		$this->get_list_table()->single_row( $this->get_object( $id ) );

		return ob_get_clean();
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\User( $model );
	}

}