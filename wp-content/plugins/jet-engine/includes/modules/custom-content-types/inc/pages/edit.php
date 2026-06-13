<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Pages;

use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Engine\Modules\Custom_Content_Types\DB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Edit extends \Jet_Engine_CPT_Page_Base {

	/**
	 * Page slug
	 *
	 * @return string
	 */
	public function get_slug() {
		if ( $this->item_id() ) {
			return 'edit';
		} else {
			return 'add';
		}
	}

	/**
	 * Page name
	 *
	 * @return string
	 */
	public function get_name() {
		if ( $this->item_id() ) {
			return esc_html__( 'Edit Content Type', 'jet-engine' );
		} else {
			return esc_html__( 'Add New Content Type', 'jet-engine' );
		}
	}

	/**
	 * Returns currently requested items ID.
	 * If this funciton returns an empty result - this is add new item page
	 *
	 * @return [type] [description]
	 */
	public function item_id() {
		return isset( $_GET['id'] ) ? esc_attr( $_GET['id'] ) : false;
	}

	/**
	 * Register add controls
	 * @return [type] [description]
	 */
	public function page_specific_assets() {

		$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

		$ui = new \CX_Vue_UI( $module_data );

		\CX_Vue_UI::$templates_path = Module::instance()->module_path( 'templates/admin/rewrite/' );

		$ui->enqueue_assets();

		if ( ! class_exists( '\Jet_Engine_Meta_Boxes_Page_Edit' ) ) {
			require_once jet_engine()->plugin_path( 'includes/components/meta-boxes/pages/edit.php' );
			\Jet_Engine_Meta_Boxes_Page_Edit::enqueue_meta_fields( array(
				'title'    => __( 'Fields', 'jet-engine' ),
				'button'   => __( 'New Field', 'jet-engine' ),
				'disabled' => array(),
			) );
		}

		wp_enqueue_script(
			'jet-engine-cct-delete-dialog',
			Module::instance()->module_url( 'assets/js/admin/delete-dialog.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch', ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-cct-delete-dialog',
			'JetEngineCCTDeleteDialog',
			array(
				'api_path' => jet_engine()->api->get_route( 'delete-content-type' ),
				'redirect' => $this->manager->get_page_link( 'list' ),
			)
		);

		wp_enqueue_script(
			'jet-engine-cct-edit',
			Module::instance()->module_url( 'assets/js/admin/edit.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch' ),
			jet_engine()->get_version(),
			true
		);

		$id = $this->item_id();

		if ( $id ) {
			$button_label = __( 'Update Content Type', 'jet-engine' );
			$redirect     = false;
		} else {
			$button_label = __( 'Add Content Type', 'jet-engine' );
			$redirect     = $this->manager->get_edit_item_link( '%id%' );
		}

		wp_localize_script(
			'jet-engine-cct-edit',
			'JetEngineCCTConfig',
			$this->manager->get_admin_page_config( array(
				'api_path_edit'     => jet_engine()->api->get_route( $this->get_slug() . '-content-type' ),
				'item_id'           => $id,
				'edit_button_label' => $button_label,
				'redirect'          => $redirect,
				'post_types'        => \Jet_Engine_Tools::get_post_types_for_js(),
				'db_prefix'         => DB::table_prefix(),
				'positions'         => $this->get_positions(),
				'default_position'  => \Jet_Engine_Tools::get_default_menu_position(),
				'rest_base'         => rest_url( '/jet-cct/' ),
				'service_fields'    => Module::instance()->manager->get_service_fields( array(
					'add_id_field' => true,
					'has_single'   => true,
				) ),
				'common_api_args'   => Module::instance()->rest_controller->get_common_args(),
				'help_links'        => array(
					array(
						'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-how-to-create-a-custom-content-type/?utm_source=jetengine&utm_medium=custom-content-type&utm_campaign=need-help',
						'label' => __( 'How to Create a Custom Content Type', 'jet-engine' ),
					),
					array(
						'url'   => 'https://crocoblock.com/wp-content/uploads/2020/11/Croco-blog-post-760x690-1-1024x930.png',
						'label' => __( 'How to choose: Custom Content Type vs Custom Post Type', 'jet-engine' ),
					),
				),
				'reserved_column_names' => $this->get_reserved_column_names(),
				'field_errors' => array(
					'sql_reserved'        => wp_strip_all_tags( __( '"%s" seems to be a MySQL reserved word. Using it as a field name will probably cause issues. If you experience any, rename this field.', 'jet-engine' ) ),
					'jet_engine_reserved' => wp_strip_all_tags( __( '"%s" cannot be used as a CCT field name. It is one of default CCT column names. Please, choose another name for this field.', 'jet-engine' ) ),
				),
			) )
		);

		wp_add_inline_style( 'common', 'input.cx-vui-input[disabled="disabled"] {opacity:.5;}' );

		add_action( 'admin_footer', array( $this, 'add_page_template' ) );

	}

	public function get_reserved_column_names() {
		$reserved_words = [
			"ACCESSIBLE",
			"ADD",
			"ALL",
			"ALTER",
			"ANALYZE",
			"AND",
			"AS",
			"ASC",
			"ASENSITIVE",
			"BEFORE",
			"BETWEEN",
			"BIGINT",
			"BINARY",
			"BLOB",
			"BOTH",
			"BY",
			"CALL",
			"CASCADE",
			"CASE",
			"CHANGE",
			"CHAR",
			"CHARACTER",
			"CHECK",
			"COLLATE",
			"COLUMN",
			"CONDITION",
			"CONSTRAINT",
			"CONTINUE",
			"CONVERT",
			"CREATE",
			"CROSS",
			"CUBE",
			"CUME_DIST",
			"CURRENT_DATE",
			"CURRENT_TIME",
			"CURRENT_TIMESTAMP",
			"CURRENT_USER",
			"CURSOR",
			"DATABASE",
			"DATABASES",
			"DAY_HOUR",
			"DAY_MICROSECOND",
			"DAY_MINUTE",
			"DAY_SECOND",
			"DEC",
			"DECIMAL",
			"DECLARE",
			"DEFAULT",
			"DELAYED",
			"DELETE",
			"DENSE_RANK",
			"DESC",
			"DESCRIBE",
			"DETERMINISTIC",
			"DISTINCT",
			"DISTINCTROW",
			"DIV",
			"DOUBLE",
			"DROP",
			"DUAL",
			"EACH",
			"ELSE",
			"ELSEIF",
			"EMPTY",
			"ENCLOSED",
			"ESCAPED",
			"EXCEPT",
			"EXISTS",
			"EXIT",
			"EXPLAIN",
			"EXTERNAL",
			"FALSE",
			"FETCH",
			"FIRST_VALUE",
			"FLOAT",
			"FLOAT4",
			"FLOAT8",
			"FOR",
			"FORCE",
			"FOREIGN",
			"FROM",
			"FULLTEXT",
			"FUNCTION",
			"GENERATED",
			"GET",
			"GRANT",
			"GROUP",
			"GROUPING",
			"GROUPS",
			"HAVING",
			"HIGH_PRIORITY",
			"HOUR_MICROSECOND",
			"HOUR_MINUTE",
			"HOUR_SECOND",
			"IF",
			"IGNORE",
			"IN",
			"INDEX",
			"INFILE",
			"INNER",
			"INOUT",
			"INSENSITIVE",
			"INSERT",
			"INT",
			"INT1",
			"INT2",
			"INT3",
			"INT4",
			"INT8",
			"INTEGER",
			"INTERSECT",
			"INTERVAL",
			"INTO",
			"IO_AFTER_GTIDS",
			"IO_BEFORE_GTIDS",
			"IS",
			"ITERATE",
			"JOIN",
			"JSON_TABLE",
			"KEY",
			"KEYS",
			"KILL",
			"LAG",
			"LAST_VALUE",
			"LATERAL",
			"LEAD",
			"LEADING",
			"LEAVE",
			"LEFT",
			"LIBRARY",
			"LIKE",
			"LIMIT",
			"LINEAR",
			"LINES",
			"LOAD",
			"LOCALTIME",
			"LOCALTIMESTAMP",
			"LOCK",
			"LONG",
			"LONGBLOB",
			"LONGTEXT",
			"LOOP",
			"LOW_PRIORITY",
			"MANUAL",
			"MATCH",
			"MAXVALUE",
			"MEDIUMBLOB",
			"MEDIUMINT",
			"MEDIUMTEXT",
			"MIDDLEINT",
			"MINUTE_MICROSECOND",
			"MINUTE_SECOND",
			"MOD",
			"MODIFIES",
			"NATURAL",
			"NOT",
			"NO_WRITE_TO_BINLOG",
			"NTH_VALUE",
			"NTILE",
			"NULL",
			"NUMERIC",
			"OF",
			"ON",
			"OPTIMIZE",
			"OPTIMIZER_COSTS",
			"OPTION",
			"OPTIONALLY",
			"OR",
			"ORDER",
			"OUT",
			"OUTER",
			"OUTFILE",
			"OVER",
			"PARALLEL",
			"PARTITION",
			"PERCENT_RANK",
			"PRECISION",
			"PRIMARY",
			"PROCEDURE",
			"PURGE",
			"QUALIFY",
			"RANGE",
			"RANK",
			"READ",
			"READS",
			"READ_WRITE",
			"REAL",
			"RECURSIVE",
			"REFERENCES",
			"REGEXP",
			"RELEASE",
			"RENAME",
			"REPEAT",
			"REPLACE",
			"REQUIRE",
			"RESIGNAL",
			"RESTRICT",
			"RETURN",
			"REVOKE",
			"RIGHT",
			"RLIKE",
			"ROW",
			"ROWS",
			"ROW_NUMBER",
			"SCHEMA",
			"SCHEMAS",
			"SECOND_MICROSECOND",
			"SELECT",
			"SENSITIVE",
			"SEPARATOR",
			"SET",
			"SHOW",
			"SIGNAL",
			"SMALLINT",
			"SPATIAL",
			"SPECIFIC",
			"SQL",
			"SQLEXCEPTION",
			"SQLSTATE",
			"SQLWARNING",
			"SQL_BIG_RESULT",
			"SQL_CALC_FOUND_ROWS",
			"SQL_SMALL_RESULT",
			"SSL",
			"STARTING",
			"STORED",
			"STRAIGHT_JOIN",
			"SYSTEM",
			"TABLE",
			"TABLESAMPLE",
			"TERMINATED",
			"THEN",
			"TINYBLOB",
			"TINYINT",
			"TINYTEXT",
			"TO",
			"TRAILING",
			"TRIGGER",
			"TRUE",
			"UNDO",
			"UNION",
			"UNIQUE",
			"UNLOCK",
			"UNSIGNED",
			"UPDATE",
			"USAGE",
			"USE",
			"USING",
			"UTC_DATE",
			"UTC_TIME",
			"UTC_TIMESTAMP",
			"VALUES",
			"VARBINARY",
			"VARCHAR",
			"VARCHARACTER",
			"VARYING",
			"VIRTUAL",
			"WHEN",
			"WHERE",
			"WHILE",
			"WINDOW",
			"WITH",
			"WRITE",
			"XOR",
			"YEAR_MONTH",
			"ZEROFILL",
			"EXTERNAL",
			"LIBRARY"
		];

		return $reserved_words;
	}

	/**
	 * Returns available positions list
	 *
	 * @return [type] [description]
	 */
	public function get_positions() {
		return apply_filters(
			'jet-engine/options-pages/available-positions',
			\Jet_Engine_Tools::get_available_menu_positions()
		);
	}

	/**
	 * Print add/edit page template
	 */
	public function add_page_template() {

		ob_start();
		include Module::instance()->module_path( 'templates/admin/edit.php' );
		$content = ob_get_clean();

		printf( '<script type="text/x-template" id="jet-cct-form">%s</script>', $content );

		ob_start();
		include Module::instance()->module_path( 'templates/admin/delete-dialog.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-cct-delete-dialog">%s</script>', $content );

	}

	/**
	 * Adds template for meta fields component
	 */
	public static function add_meta_fields_template() {

		ob_start();
		include jet_engine()->get_template( 'admin/pages/meta-boxes/fields.php' );
		$content = ob_get_clean();

		printf( '<script type="text/x-template" id="jet-meta-fields">%s</script>', $content );

	}

	/**
	 * Renderer callback
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<br>
		<div id="jet_cct_form"></div>
		<?php
	}

}
