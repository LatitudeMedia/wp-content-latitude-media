<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Field {

	public $field_type = 'advanced-date';

	public $view;
	public $data;
	public $rest;

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$base_path = jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/' );
		$base_url  = jet_engine()->plugin_url( 'includes/modules/calendar/assets/' );

		require_once $base_path . 'view.php';
		require_once $base_path . 'data.php';
		require_once $base_path . 'rest-api.php';
		require_once $base_path . 'form-builder/register.php';

		$this->view = new Jet_Engine_Advanced_Date_Field_View( $this->field_type );
		$this->data = new Jet_Engine_Advanced_Date_Field_Data( $this->field_type );
		$this->rest = new Jet_Engine_Advanced_Date_Field_Rest_API( $this->field_type );

		new \Jet_Engine\Dynamic_Calendar\Advanced_Date_Field\Form_Builder(
			$base_path . 'form-builder/',
			$base_url
		);

		add_action(
			'jet-engine/callbacks/register',
			array( $this, 'register_advanced_date_callbacks' )
		);

		add_filter(
			'jet-engine/listing/dynamic-field/callback-args',
			array( $this, 'add_field_name_to_callback_args' ),
			10, 4
		);

		add_filter(
			'jet-engine/listings/allowed-callbacks-args',
			array( $this, 'modify_date_format_conditions' ),
			10, 4
		);

		add_action(
			'jet-engine/meta-fields/enqueue-assets',
			array( $this, 'add_editor_js' )
		);

		add_action(
			'jet-engine/meta-boxes/templates/fields/controls',
			array( $this, 'editor_controls' )
		);

	}

	public function editor_controls() {
		?>
		<cx-vui-select
			label="<?php _e( 'Recurrence Format', 'jet-engine' ); ?>"
			description="<?php _e( 'Defines UI to set up recurrent dates on edit screen', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="[
				{
					value: 'rrule',
					label: '<?php _e( 'Recurrence Rule', 'jet-engine' ); ?>'
				},
				{
					value: 'manual',
					label: '<?php _e( 'Manually', 'jet-engine' ); ?>'
				}
			]"
			:value="field.field_ui_format || 'rrule'"
			@input="setFieldProp( 'field_ui_format', $event )"
			:conditions="[
				{
					'input':   field.object_type,
					'compare': 'equal',
					'value':   'field',
				},
				{
					'input':   field.type,
					'compare': 'equal',
					'value':   'advanced-date',
				}
			]"
		></cx-vui-select>
		<cx-vui-switcher
			label="<?php _e( 'Allow timepicker', 'jet-engine' ); ?>"
			description="<?php _e( 'Allows to set exact time in addition to date fields.', 'jet-engine' ); ?>"
			:value="field.allow_timepicker"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			@input="setFieldProp( 'allow_timepicker', $event )"
			:conditions="[
				{
					'input':   field.object_type,
					'compare': 'equal',
					'value':   'field',
				},
				{
					'input':   field.type,
					'compare': 'equal',
					'value':   'advanced-date',
				}
			]"
		></cx-vui-switcher>
		<?php
	}

	/**
	 * Add Meta boxes editor JS file
	 */
	public function add_editor_js() {
		wp_enqueue_script(
			'jet-engine-advanced-date-meta-boxes',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/js/meta-boxes.js' ),
			array( 'jet-plugins' ),
			jet_engine()->get_version(),
			true
		);
	}

	/**
	 * Regsiter advanced date realted JetEngine callback(s)
	 *
	 * @param  [type] $callbacks [description]
	 * @return [type]            [description]
	 */
	public function register_advanced_date_callbacks( $callbacks ) {
		$callbacks->register_callback(
			'jet_engine_advanced_date_next',
			__( 'Advanced date: Get next date', 'jet-engine' )
		);

		$callbacks->register_callback(
			'jet_engine_advanced_end_date_next',
			__( 'Advanced date: Get next end date', 'jet-engine' )
		);
	}

	/**
	 * Add jet_engine_advanced_date_next callback to date_format callback control conditions
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function modify_date_format_conditions( $args = [] ) {
		$args['date_format']['condition']['filter_callback'][] = 'jet_engine_advanced_date_next';
		$args['date_format']['condition']['filter_callback'][] = 'jet_engine_advanced_end_date_next';
		$args['include_future_end_dates'] = [
			'label'       => __( 'Include future end dates', 'jet-engine' ),
			'description' => __( 'If enabled, the first date pair with an end date in the nearest future will be returned when no date pair with upcoming start date is found.', 'jet-engine' ),
			'type'        => 'switcher',
			'default'     => '',
			'condition'   => [
				'dynamic_field_filter' => 'yes',
				'filter_callback'    => [ 'jet_engine_advanced_date_next' ],
			],
		];

		return $args;
	}

	/**
	 * Adjust jet_engine_advanced_date_next callback arguments before apply the callback
	 *
	 * @param [type] $args     [description]
	 * @param [type] $callback [description]
	 * @param array  $settings [description]
	 * @param [type] $widget   [description]
	 */
	public function add_field_name_to_callback_args( $args, $callback, $settings = array(), $widget = null ) {

		if ( ! in_array( $callback, [ 'jet_engine_advanced_date_next', 'jet_engine_advanced_end_date_next' ] ) ) {
			return $args;
		}

		$format = ! empty( $settings['date_format'] ) ? $settings['date_format'] : 'd F Y';
		$field  = ! empty( $settings['dynamic_field_post_meta_custom'] ) ? $settings['dynamic_field_post_meta_custom'] : false;
		$include_future_end_dates = ! empty( $settings['include_future_end_dates'] ) ? $settings['include_future_end_dates'] : false;
		$include_future_end_dates = filter_var( $include_future_end_dates, FILTER_VALIDATE_BOOLEAN );

		if ( ! $field && isset( $settings['dynamic_field_post_meta'] ) ) {
			$field = ! empty( $settings['dynamic_field_post_meta'] ) ? $settings['dynamic_field_post_meta'] : false;
		}

		$args[] = $format;
		$args[] = $field;
		$args[] = $include_future_end_dates;

		return $args;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return static
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}

/**
 * As the JetEngine callbacks support only functions
 * so we need to register appropriate function for the callback
 */
function jet_engine_advanced_date_next( $result, $format = 'd F Y', $field = '', $include_future_end_dates = false ) {

	$time      = time();
	$post_id   = jet_engine()->listings->data->get_current_object_id();
	$dates     = Jet_Engine_Advanced_Date_Field::instance()->data->get_dates( $post_id, $field );
	$next_date = false;
	$next_end  = false;

	if ( empty( $dates ) ) {
		return;
	}

	$field_config = Jet_Engine_Advanced_Date_Field::instance()->data->get_field_config( $post_id, $field, true );

	if ( ! $include_future_end_dates && empty( $field_config->is_recurring ) && empty( $field_config->dates ) ) {
		if ( empty( $result ) || $result < $time ) {
			return;
		}

		$result_date = jet_engine_date( $format, $result );
		$end_date    = $field_config->end_date ?? false;

		if ( ! empty( $field_config->is_end_date ) && ! empty( $end_date ) ) {
			if ( ! empty( $field_config->end_time ) ) {
				$end_date = $end_date . ' ' . $field_config->end_time;
			}

			$end_date  = jet_engine_date( $format, strtotime( $end_date ) );
			$md_format = apply_filters(
				'jet-engine/calendar/advanced-date/multiday-format',
				'%1$s - %2$s', $result_date, $end_date
			);
	
			return sprintf( $md_format, $result_date, $end_date );
		} else {
			return $result_date;
		}
	}

	$date_pairs = Jet_Engine_Advanced_Date_Field::instance()->data->get_date_pairs( $post_id, $field );

	usort( $date_pairs, function( $a, $b ) {
		return intval( $a['start'] ) <=> intval( $b['start'] );
	} );

	foreach ( $date_pairs as $pair ) {
		if ( $pair['start'] > $time ) {
			$next_date = $pair['start'];
			$next_end  = $pair['end'];
			break;
		}
	}

	$no_past_date = $next_date < $time;

	if ( $no_past_date && ! $include_future_end_dates ) {
		return;
	}

	if ( $no_past_date && $include_future_end_dates ) {
		usort( $date_pairs, function( $a, $b ) {
			return intval( $a['end'] ) <=> intval( $b['end'] );
		} );
		
		$next_date = false;
		$next_end  = false;

		foreach ( $date_pairs as $pair ) {
			if ( $pair['end'] > $time ) {
				$next_date = $pair['start'];
				$next_end  = $pair['end'];
				break;
			}
		}
	}
	
	if ( $next_date === false ) {
		return;
	}

	$result_date = jet_engine_date( $format, $next_date );

	if ( $next_end !== false ) {
		$end_date  = jet_engine_date( $format, $next_end );
		$md_format = apply_filters(
			'jet-engine/calendar/advanced-date/multiday-format',
			'%1$s - %2$s', $result_date, $end_date
		);

		$result_date = sprintf( $md_format, $result_date, $end_date );

	}

	return $result_date;

}

/**
 * As the JetEngine callbacks support only functions
 * so we need to register appropriate function for the callback
 */
function jet_engine_advanced_end_date_next( $result, $format = 'd F Y', $field = '' ) {

	$time      = time();
	$post_id   = jet_engine()->listings->data->get_current_object_id();
	$dates     = Jet_Engine_Advanced_Date_Field::instance()->data->get_end_dates( $post_id, $field );
	$next_date = false;

	if ( empty( $dates ) ) {
		return;
	}

	$field_config = Jet_Engine_Advanced_Date_Field::instance()->data->get_field_config( $post_id, $field, true );

	if ( empty( $field_config->is_recurring ) && empty( $field_config->dates ) ) {
		if ( empty( $dates[0] ) || $dates[0] < $time ) {
			return;
		}

		return jet_engine_date( $format, $dates[0] );
	}

	// https://github.com/Crocoblock/issues-tracker/issues/10072
	asort( $dates, SORT_NUMERIC );

	foreach ( $dates as $date ) {
		if ( $date > $time ) {
			$next_date = $date;
			break;
		}
	}

	if ( $next_date === false ) {
		return;
	}

	$result_date = jet_engine_date( $format, $next_date );

	return $result_date;

}

