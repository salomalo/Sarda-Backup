<?php

namespace ACP\Editing;

use AC;
use ACP\Editing;

class Addon extends AC\Addon {

	/**
	 * @var TableScreen
	 */
	private $table_screen;

	/**
	 * @since 4.0
	 */
	function __construct() {
		AC\Autoloader::instance()->register_prefix( __NAMESPACE__, $this->get_dir() . 'classes/' );
		AC\Autoloader\Underscore::instance()->add_alias( __NAMESPACE__ . '\Editable', 'ACP_Column_EditingInterface' );

		// Settings screen
		add_action( 'ac/column/settings', array( $this, 'register_column_settings' ) );
		add_action( 'ac/settings/general', array( $this, 'register_general_settings' ) );

		// Table screen
		$this->table_screen = new TableScreen();
	}

	protected function get_file() {
		return __FILE__;
	}

	public function table_screen() {
		return $this->table_screen;
	}

	/**
	 * @since 4.0
	 */
	public function get_version() {
		return ACP()->get_version();
	}

	public function helper() {
		return new Helper();
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return Editing\Model|false
	 */
	public function get_editing_model( $column ) {
		if ( ! $column instanceof Editing\Editable ) {
			return false;
		}

		$list_screen = $column->get_list_screen();

		if ( ! $list_screen instanceof ListScreen ) {
			return false;
		}

		$model = $column->editing();

		$model->set_strategy( $list_screen->editing( $model ) );

		return $model;
	}

	/**
	 * @since 3.1.2
	 *
	 * @param AC\Admin\Page\Settings $settings
	 */
	public function register_general_settings( $settings ) {
		$settings->single_checkbox( array(
			'name'         => 'custom_field_editable',
			'label'        => __( 'Enable inline editing for Custom Fields. Default is <code>off</code>', 'codepress-admin-columns' ),
			'instructions' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'Inline edit will display all the raw values in an editable text field.', 'codepress-admin-columns' ),
				sprintf(
					__( "Please read <a href='%s'>our documentation</a> if you plan to use these fields.", 'codepress-admin-columns' ),
					ac_get_site_utm_url( 'documentation/faq/enable-inline-editing-custom-fields/', 'general-settings' )
				)
			),
		) );
	}

	/**
	 * Register setting for editing
	 *
	 * @param AC\Column $column
	 */
	public function register_column_settings( $column ) {
		if ( $model = $this->get_editing_model( $column ) ) {
			$model->register_settings();
		}
	}

}