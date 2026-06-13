<?php
namespace Jet_Engine\Query_Builder\Queries;

class SQL_Query extends Base_Query {

	public $current_query = null;
	public $calc_columns = array();

	public $sql_query_strings = array(
		'items' => array(
			'query' => '',
			'error' => '',
		),
		'count' => array(
			'query' => '',
			'error' => '',
		),
	);

	private $keep_macros = false;

	/**
	 * Check whether SQL generation should preserve raw macro placeholders.
	 *
	 * When enabled, helper methods skip normal value escaping/formatting so the
	 * generated SQL can still contain unresolved macros for preview or export use.
	 *
	 * @return bool True when macros should be kept untouched in generated SQL.
	 */
	public function is_keep_macros() {
		return $this->keep_macros;
	}

	/**
	 * Enable "keep macros" mode for SQL generation helpers.
	 *
	 * @return void
	 */
	public function set_keep_macros() {
		$this->keep_macros = true;
	}

	/**
	 * Disable "keep macros" mode and restore normal value preparation.
	 *
	 * @return void
	 */
	public function reset_keep_macros() {
		$this->keep_macros = false;
	}

	/**
	 * Returns queries items
	 *
	 * @return object[] Database query results.
	 */
	public function _get_items() {

		$sql    = $this->build_sql_query();
		$result = $this->wpdb()->get_results( $sql );


		if ( $this->wpdb()->last_error ) {
			$this->sql_query_strings['items']['error'] = $this->wpdb()->last_error;
		}

		$cast_to_class = ! empty( $this->query['cast_object_to'] ) ? $this->query['cast_object_to'] : false;

		if ( $cast_to_class && ( class_exists( $cast_to_class ) || function_exists( $cast_to_class ) ) ) {
			$result = array_map( function( $item ) use ( $cast_to_class ) {

				if ( class_exists( $cast_to_class ) ) {
					return new $cast_to_class( $item );
				} elseif ( function_exists( $cast_to_class ) ) {
					return call_user_func( $cast_to_class, $item );
				} else {
					return $item;
				}

			}, $result );
		} else {

			$start_index = $this->get_start_item_index_on_page() - 1;

			$result = array_map( function( $item, $index ) use ( $start_index ) {
				$item->sql_query_item_id = $this->id . '-' . ( $start_index + $index );
				return $item;
			}, $result, array_keys( $result ) );
		}

		return $result;
	}

	/**
	 * Return the current paginated page for this SQL query instance.
	 *
	 * The value is populated from filtered query props during listing load-more
	 * requests and defaults to the first page when no explicit page is set.
	 *
	 * @return int Current page number, always at least `1`.
	 */
	public function get_current_items_page() {

		if ( empty( $this->final_query['_page'] ) ) {
			return 1;
		} else {
			return absint( $this->final_query['_page'] );
		}

	}

	/**
	 * Calculate the total number of rows available for the current SQL query.
	 *
	 * Uses the cached count when available, otherwise builds a count query and
	 * stores the result in the query cache for subsequent calls.
	 *
	 * @return int Total number of matched rows.
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( false !== $cached ) {
			return $cached;
		}

		$this->setup_query();

		$sql = $this->build_sql_query( true );

		if ( 'nosql' === $sql ) {
			$result = count( $this->get_items() );
			$this->sql_query_strings['count']['error'] = '';
		} else {
			$result = $this->wpdb()->get_var( $sql );

			if ( $this->wpdb()->last_error ) {
				$this->sql_query_strings['count']['error'] = $this->wpdb()->last_error;
			}
		}

		$this->update_query_cache( $result, 'count' );

		return $result;
	}

	/**
	 * Determine how many items should be displayed on a single page.
	 *
	 * Per-page limit takes precedence over the generic limit because listing
	 * pagination uses `limit_per_page` to split a larger SQL result set.
	 *
	 * @return int Per-page limit or `0` when no limit is configured.
	 */
	public function get_items_per_page() {

		$this->setup_query();
		$limit = 0;

		if ( ! empty( $this->final_query['limit_per_page'] ) ) {
			$limit = absint( $this->final_query['limit_per_page'] );
		} elseif ( ! empty( $this->final_query['limit'] ) ) {
			$limit = absint( $this->final_query['limit'] );
		}

		return $limit;
	}

	/**
	 * Return the number of items that should be visible on the current page.
	 *
	 * This value may be smaller than the configured per-page limit on the final
	 * page when the total result count does not divide evenly.
	 *
	 * @return int Number of rows expected on the active page.
	 */
	public function get_items_page_count() {
		$result   = $this->get_items_total_count();
		$per_page = $this->get_items_per_page();

		if ( $per_page < $result ) {

			$page  = $this->get_current_items_page();
			$pages = $this->get_items_pages_count();

			if ( $page < $pages ) {
				$result = $per_page;
			} elseif ( $page == $pages ) {
				$offset = ( $page - 1 ) * $per_page;
				$result = $result - $offset;
			}

		}

		return $result;
	}

	/**
	 * Apply front-end filter values to supported SQL query properties.
	 *
	 * Only a small allowlisted subset of properties may be changed from the
	 * request. For `meta_query`, the request may only update values of trusted
	 * saved clauses; it cannot define new SQL structure.
	 *
	 * @param string $prop  Filtered property name received from the request.
	 * @param mixed  $value Filtered property value received from the request.
	 * @return void
	 */
	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case '_page':

				$page = absint( $value );

				if ( 0 < $page ) {
					$this->final_query['_page']  = $page;
				}

				break;

			case 'orderby':
			case 'order':
			case 'meta_key':

				$key = $prop;

				if ( 'orderby' === $prop ) {
					$key = 'type';
					$value = ( in_array( $value, array( 'meta_key', 'meta_value' ) ) ) ? 'CHAR' : 'DECIMAL';
				} elseif ( 'meta_key' === $prop ) {
					$key = 'orderby';
				}

				$this->set_filtered_order( $key, $value );
				break;

			case 'meta_query':

				if ( ! is_array( $value ) ) {
					break;
				}

				$this->merge_filtered_where_rows( $value );

				break;
		}

	}

	/**
	 * Normalize a list of saved SQL where rows into the internal trusted shape.
	 *
	 * Invalid rows are skipped so later matching/building logic only works with
	 * safe, well-formed clauses.
	 *
	 * @param array $rows Raw where rows from the current query configuration.
	 * @return array Prepared where rows that passed validation.
	 */
	protected function prepare_where_rows( $rows = array() ) {

		$prepared = array();

		if ( ! is_array( $rows ) ) {
			return $prepared;
		}

		foreach ( $rows as $row ) {
			$row = $this->prepare_where_row( $row );

			if ( $row ) {
				$prepared[] = $row;
			}
		}

		return $prepared;
	}

	/**
	 * Merge request-provided SQL meta query rows into the trusted runtime where tree.
	 *
	 * Incoming rows are sanitized once, existing rows are normalized, matched rows
	 * keep their trusted structure, and unmatched rows are appended as new clauses.
	 *
	 * @param array $rows Raw meta query rows received from the request.
	 * @return void
	 */
	protected function merge_filtered_where_rows( $rows = array() ) {

		$incoming_rows = $this->prepare_where_rows( $rows );

		if ( empty( $incoming_rows ) ) {
			return;
		}

		$existing_rows = ! empty( $this->final_query['where'] ) && is_array( $this->final_query['where'] )
			? $this->prepare_where_rows( $this->final_query['where'] )
			: array();

		$this->final_query['where'] = $this->merge_where_rows( $existing_rows, $incoming_rows );
	}

	/**
	 * Normalize a single SQL where row or nested group into the trusted format.
	 *
	 * This method validates column names, operators, relation glue, value shape,
	 * optional row identifiers, and clause-level flags before the row is used by
	 * filtering or SQL generation code.
	 *
	 * @param array $row Raw row definition from the query config or request.
	 * @return array|false Sanitized row array, or `false` when the row is invalid.
	 */
	public function prepare_where_row( $row ) {

		if ( ! is_array( $row ) ) {
			return false;
		}

		$is_group = ! empty( $row['is_group'] ) || ( isset( $row['relation'] ) && empty( $row['key'] ) && empty( $row['column'] ) );

		if ( $is_group ) {

			$prepared_row = array(
				'is_group' => true,
				'relation' => $this->sanitize_sql_relation( isset( $row['relation'] ) ? $row['relation'] : 'AND' ),
				'args'     => array(),
			);

			if ( ! empty( $row['_id'] ) && is_scalar( $row['_id'] ) ) {
				$prepared_row['_id'] = (string) $row['_id'];
			}

			$group_rows = ! empty( $row['args'] ) && is_array( $row['args'] ) ? $row['args'] : $row;

			unset( $group_rows['relation'], $group_rows['is_group'], $group_rows['args'], $group_rows['_id'] );

			foreach ( $group_rows as $inner_row ) {
				$inner_row = $this->prepare_where_row( $inner_row );

				if ( $inner_row ) {
					$prepared_row['args'][] = $inner_row;
				}
			}

			if ( empty( $prepared_row['args'] ) ) {
				return false;
			}

		} else {
			$column = ! empty( $row['column'] ) ? $row['column'] : false;

			if ( ! $column && ! empty( $row['key'] ) ) {
				$column = $row['key'];
			}

			$column = $this->sanitize_sql_column( $column );

			if ( ! $column ) {
				return false;
			}

			$prepared_row = array(
				'column'  => $column,
				'compare' => $this->sanitize_sql_compare( ! empty( $row['compare'] ) ? $row['compare'] : '=' ),
				'value'   => $this->sanitize_filtered_where_value( isset( $row['value'] ) ? $row['value'] : '' ),
				'type'    => $this->sanitize_sql_type( ! empty( $row['type'] ) ? $row['type'] : false ),
			);

			if ( ! empty( $row['_id'] ) && is_scalar( $row['_id'] ) ) {
				$prepared_row['_id'] = (string) $row['_id'];
			}

			if ( isset( $row['exclude_empty'] ) ) {
				$prepared_row['exclude_empty'] = filter_var( $row['exclude_empty'], FILTER_VALIDATE_BOOLEAN );
			}
		}

		return $prepared_row;
	}

	/**
	 * Merge front-end ordering overrides into the runtime `orderby` definition.
	 *
	 * The SQL listing filter flow only allows a narrow set of ordering-related
	 * overrides and stores them under the `custom` order configuration bucket.
	 *
	 * @param string $key   Order configuration key to update.
	 * @param mixed  $value Requested value for the order configuration key.
	 * @return void
	 */
	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['orderby'] ) ) {
			$this->final_query['orderby'] = array();
		}

		if ( ! isset( $this->final_query['orderby']['custom'] ) ) {
			$this->final_query['orderby'] = array_merge( array( 'custom' => array() ), $this->final_query['orderby'] );
		}

		$this->final_query['orderby']['custom'][ $key ] = $value;

	}

	/**
	 * Replace an existing trusted where row or append a new validated one.
	 *
	 * `_id` matches take precedence because they uniquely identify saved filter
	 * rows. Column/compare matching is kept as a fallback for legacy rows.
	 *
	 * @param array $row Candidate where row to merge into the runtime query.
	 * @return void
	 */
	public function update_where_row( $row ) {

		$row = $this->prepare_where_row( $row );

		if ( ! $row ) {
			return;
		}

		if ( empty( $this->final_query['where'] ) ) {
			$this->final_query['where'] = array();
		}

		$index = $this->find_where_row_index( $this->final_query['where'], $row );

		if ( false !== $index ) {
			$this->final_query['where'][ $index ] = $this->merge_where_row( $this->final_query['where'][ $index ], $row );
			return;
		}

		$this->final_query['where'][] = $row;

	}

	/**
	 * Normalize a requested SQL comparison operator against the canonical allowlist.
	 *
	 * @param string $compare Operator value received from config or request data.
	 * @return string Safe SQL operator, defaulting to `=`.
	 */
	protected function sanitize_sql_compare( $compare = '=' ) {

		static $allowed_operators = null;

		$compare = strtoupper( trim( (string) $compare ) );

		if ( null === $allowed_operators ) {
			$allowed_operators = array_keys( \Jet_Engine_Tools::operators_list( array(), ARRAY_A ) );
		}

		if ( ! in_array( $compare, $allowed_operators, true ) ) {
			return '=';
		}

		return $compare;
	}

	/**
	 * Normalize logical relation glue used between nested where clauses.
	 *
	 * @param string $relation Requested group relation.
	 * @return string `AND` or `OR`, falling back to `AND`.
	 */
	protected function sanitize_sql_relation( $relation = 'AND' ) {

		$relation = strtoupper( trim( (string) $relation ) );

		if ( ! in_array( $relation, array( 'AND', 'OR' ), true ) ) {
			return 'AND';
		}

		return $relation;
	}

	/**
	 * Validate a column reference for use inside SQL where clauses.
	 *
	 * Only plain identifiers and dot-qualified identifiers are accepted. Function
	 * calls and other SQL fragments are rejected even if `sanitize_sql_orderby()`
	 * would allow them for ORDER BY usage.
	 *
	 * @param mixed $column Requested column name or identifier.
	 * @return string|false Sanitized identifier, or `false` when invalid.
	 */
	protected function sanitize_sql_column( $column = false ) {

		if ( ! is_scalar( $column ) ) {
			return false;
		}

		$column = trim( (string) $column );

		if ( ! $column ) {
			return false;
		}

		if ( ! preg_match( '/^`?[a-z0-9_\.]+`?$/i', $column ) ) {
			return false;
		}

		return \Jet_Engine_Tools::sanitize_sql_orderby( $column );
	}

	/**
	 * Validate the declared SQL value type against JetEngine's known data types.
	 *
	 * @param mixed $type Requested SQL cast/type value.
	 * @return string|false Safe type string, or `false` when it is unsupported.
	 */
	protected function sanitize_sql_type( $type = false ) {

		static $allowed_types = null;

		if ( empty( $type ) || ! is_scalar( $type ) ) {
			return false;
		}

		$type_value    = trim( (string) $type );
		$type_check    = strtoupper( $type_value );

		if ( null === $allowed_types ) {
			$allowed_types = array_keys( \Jet_Engine_Tools::data_types_list( ARRAY_A ) );
		}

		if ( ! in_array( $type_check, $allowed_types, true ) ) {
			return false;
		}

		return $type_value;
	}

	/**
	 * Normalize filter values coming from the request into scalar-safe strings.
	 *
	 * Arrays are sanitized recursively because some operators legitimately accept
	 * multiple values, while unsupported value types are dropped to an empty string.
	 *
	 * @param mixed $value Filter value received from the request.
	 * @return string|array Sanitized scalar string or recursively sanitized array.
	 */
	protected function sanitize_filtered_where_value( $value = '' ) {

		if ( is_array( $value ) ) {
			return array_map( array( $this, 'sanitize_filtered_where_value' ), $value );
		}

		if ( is_scalar( $value ) || null === $value ) {
			return (string) $value;
		}

		return '';
	}

	/**
	 * Merge sanitized incoming rows into the current trusted where tree.
	 *
	 * Existing rows are updated in place when they match by `_id` or by
	 * `column + compare`; unmatched sanitized rows are appended.
	 *
	 * @param array $existing_rows Trusted existing where rows.
	 * @param array $incoming_rows Sanitized incoming where rows.
	 * @return array Merged where rows.
	 */
	protected function merge_where_rows( $existing_rows = array(), $incoming_rows = array() ) {

		if ( empty( $existing_rows ) ) {
			return $incoming_rows;
		}

		if ( empty( $incoming_rows ) ) {
			return $existing_rows;
		}

		$matched_indexes = array();

		foreach ( $incoming_rows as $incoming_row ) {
			$index = $this->find_where_row_index( $existing_rows, $incoming_row, $matched_indexes );

			if ( false === $index ) {
				$existing_rows[] = $incoming_row;
				$matched_indexes[] = count( $existing_rows ) - 1;
				continue;
			}

			$existing_rows[ $index ] = $this->merge_where_row( $existing_rows[ $index ], $incoming_row );

			if ( empty( $incoming_row['_id'] ) ) {
				$matched_indexes[] = $index;
			}
		}

		return $existing_rows;
	}

	/**
	 * Find an existing trusted where row that should be merged with the incoming row.
	 *
	 * Matching prefers `_id`. When `_id` is absent, leaf rows fall back to
	 * `column + compare`. Anonymous groups are never heuristically matched.
	 *
	 * @param array $existing_rows   Trusted existing where rows.
	 * @param array $incoming_row    Sanitized incoming where row.
	 * @param array $matched_indexes Indexes already consumed by fallback matching.
	 * @return int|false Matching row index or `false` when no match exists.
	 */
	protected function find_where_row_index( $existing_rows = array(), $incoming_row = array(), $matched_indexes = array() ) {

		if ( ! is_array( $incoming_row ) ) {
			return false;
		}

		$has_id      = ! empty( $incoming_row['_id'] ) && is_scalar( $incoming_row['_id'] );
		$is_group    = ! empty( $incoming_row['is_group'] );
		$incoming_id = $has_id ? (string) $incoming_row['_id'] : false;

		foreach ( $existing_rows as $index => $existing_row ) {
			if ( ! is_array( $existing_row ) ) {
				continue;
			}

			$existing_is_group = ! empty( $existing_row['is_group'] );

			if ( $has_id ) {
				if ( ! empty( $existing_row['_id'] ) && $incoming_id === (string) $existing_row['_id'] ) {
					return $index;
				}

				continue;
			}

			if ( in_array( $index, $matched_indexes, true ) ) {
				continue;
			}

			if ( $is_group || $existing_is_group ) {
				continue;
			}

			if ( isset( $existing_row['column'], $incoming_row['column'], $existing_row['compare'], $incoming_row['compare'] )
				&& $existing_row['column'] === $incoming_row['column']
				&& $existing_row['compare'] === $incoming_row['compare']
			) {
				return $index;
			}
		}

		return false;
	}

	/**
	 * Merge one incoming sanitized where row into an existing trusted row.
	 *
	 * Existing structure stays authoritative for matched rows. Only values are
	 * updated for leaf rows, while matched groups merge their child clauses
	 * recursively and preserve the existing group relation.
	 *
	 * @param array $existing_row Trusted existing row.
	 * @param array $incoming_row Sanitized incoming row.
	 * @return array Merged row.
	 */
	protected function merge_where_row( $existing_row = array(), $incoming_row = array() ) {

		if ( ! empty( $existing_row['is_group'] ) && ! empty( $incoming_row['is_group'] ) ) {
			$existing_row['relation'] = $this->sanitize_sql_relation( $existing_row['relation'] ?? 'AND' );
			$existing_row['args']     = $this->merge_where_rows(
				! empty( $existing_row['args'] ) && is_array( $existing_row['args'] ) ? $existing_row['args'] : array(),
				! empty( $incoming_row['args'] ) && is_array( $incoming_row['args'] ) ? $incoming_row['args'] : array()
			);

			return $existing_row;
		}

		$existing_row['value'] = $incoming_row['value'] ?? '';

		return $existing_row;
	}

	/**
	 * Calculate the total number of pages for the current SQL result set.
	 *
	 * @return int Total page count, always at least `1`.
	 */
	public function get_items_pages_count() {

		$per_page = $this->get_items_per_page();
		$total    = $this->get_items_total_count();

		if ( ! $per_page || ! $total ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	/**
	 * Access the global WordPress database object used by this query type.
	 *
	 * @return \wpdb WordPress database connection instance.
	 */
	public function wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Execute an aggregate or direct-value SQL query against the current result set.
	 *
	 * This helper reuses the generated SQL, optionally wraps grouped queries, and
	 * returns numeric values rounded to the requested decimal precision.
	 *
	 * @param string|null $column        Column to select or aggregate.
	 * @param string|null $function      Aggregate SQL function name, e.g. `COUNT`.
	 * @param int         $decimal_count Number of decimal places to round to.
	 * @return float Rounded numeric result from the query.
	 */
	public function get_var( $column = null, $function = null, $decimal_count = 0 ) {

		$this->setup_query();
		$sql = $this->build_sql_query();

		$quote = '';

		if ( $this->is_grouped() ) {
			$quote = '`';
		}

		if ( $function ) {
			if ( 'COUNT' === $function ) {
				$select = sprintf( '%1$s( %3$s%2$s%3$s )', $function, $column, $quote );
			} else {
				$select = sprintf( '%1$s( CAST( %4$s%2$s%4$s AS DECIMAL( 10, %3$s ) ) )', $function, $column, $decimal_count, $quote );
			}
		} else {
			$select = sprintf( '%2$s%1$s%2$s', $column, $quote );
		}

		$advanced_query = $this->get_advanced_query();

		if ( $advanced_query ) {
			$sql = rtrim( $sql );
			$sql = rtrim( $sql, ';' );
			$sql = 'SELECT ' . $select . ' FROM ( ' . $sql . ' ) AS advanced_query_result;';
			return round( floatval( $this->wpdb()->get_var( $sql ) ), $decimal_count );
		}

		if ( $this->is_grouped() ) {
			$sql = $this->wrap_grouped_query( $select, $sql );
		} else {
			$sql = preg_replace( '/SELECT (.+?) FROM/', 'SELECT ' . $select . ' FROM', $sql );
		}

		return round( floatval( $this->wpdb()->get_var( $sql ) ), $decimal_count );

	}

	/**
	 * Strip SQL comments and escaped slashes from a manually provided query string.
	 *
	 * The result is used as a preliminary cleanup step before safety validation.
	 *
	 * @param string $query Raw SQL string from advanced query mode.
	 * @return string Cleaned SQL string ready for safety checks.
	 */
	public function sanitize_sql( $query ) {

		/**
		 * ensure query is not stacked
		 * temporary disabled because can return false positive
		 *
		 * $query = explode( ';', $query );
		 * $query = $query[0];
		 */

		// Remove the / * * / style comments
		$query = preg_replace( '%(/\*)(.*?)(\*/)%s',"",$query );
		// Remove the — style comments
		$query = preg_replace( '%(–).*%',"",$query );

		$query = stripslashes( $query );

		return $query;

	}

	/**
	 * Perform a conservative safety check for advanced SQL query strings.
	 *
	 * Only read-only `SELECT` statements are allowed. Obvious destructive or
	 * schema-changing commands are rejected before execution.
	 *
	 * @param string $query SQL string to validate.
	 * @return bool True when the query passes the safety check.
	 */
	public function is_query_safe( $query ) {

		$query = trim( $query );

		// Should start from SELECT word
		if ( 0 !== strpos( $query, 'SELECT' ) ) {
			return false;
		}

		// Should not contain any dangerous SQL commands
		$disallowed = array(
			'DROP',
			'TRUNCATE',
			'DELETE',
			'COMMIT',
			'GRANT ALL',
			'CREATE',
			'REPLACE',
			'INSERT',
			'ALTER',
			'ADD ',
			'UPDATE',
		);

		foreach ( $disallowed as $command ) {
			if ( false !== strpos( $query, $command ) ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Return the advanced SQL query string when advanced mode is enabled.
	 *
	 * The method chooses the correct manual/count query, sanitizes it, validates
	 * that it is safe, expands the `{prefix}` placeholder, and applies macros.
	 *
	 * @param bool $is_count Whether the count-query variant should be returned.
	 * @return string|false Advanced SQL string, `nosql` for missing count SQL, or `false`.
	 */
	public function get_advanced_query( $is_count = false ) {

		if ( empty( $this->final_query['advanced_mode'] ) ) {
			return false;
		}

		if ( $is_count ) {
			$query = ! empty( $this->final_query['count_query'] ) ? $this->final_query['count_query'] : false;

			if ( ! $query ) {
				return 'nosql';
			}

		} else {
			$query = $this->final_query['manual_query'];
		}

		if ( ! $query ) {
			return false;
		}

		$query = $this->sanitize_sql( $query );

		if ( ! $this->is_query_safe( $query ) ) {
			return false;
		}

		$query = str_replace( '{prefix}', $this->wpdb()->prefix, $query );

		return $this->apply_macros( $query );

	}

	/**
	 * Build the SQL string used to fetch rows or count rows for this query.
	 *
	 * Advanced-mode SQL is returned as-is after validation. Otherwise the method
	 * delegates to the simple query builder that assembles SQL from query parts.
	 *
	 * @param bool $is_count Whether to build the count-query variant.
	 * @return string Final SQL query string.
	 */
	public function build_sql_query( $is_count = false ) {

		// Return advanced query early if set
		$advanced_query = $this->get_advanced_query( $is_count );

		if ( $advanced_query ) {
			if ( $is_count ) {
				$this->sql_query_strings['count']['query'] = $advanced_query;
			} else {
				$this->sql_query_strings['items']['query'] = $advanced_query;
			}

			return $advanced_query;
		}

		return $this->get_simple_query( $is_count );

	}

	/**
	 * Assemble the standard SQL query from the structured query configuration.
	 *
	 * This method builds SELECT, JOIN, WHERE, GROUP BY, ORDER BY, and LIMIT parts
	 * from the trusted runtime query configuration and caches reusable fragments.
	 *
	 * @param bool $is_count Whether to build the count-query variant.
	 * @return string Fully assembled SQL statement.
	 */
	public function get_simple_query( $is_count = false ) {
		$prefix = $this->wpdb()->prefix;

		$select_query = "SELECT ";

		$calc_column_aliases = array();

		$custom_column_aliases = ! empty( $this->final_query['set_column_aliases'] ) ? array_column(
			$this->final_query['column_aliases'] ?? array(),
			'column_alias',
			'column'
		) : array();

		if ( $is_count && ! $this->is_grouped() && empty( $this->final_query['limit'] ) ) {
			$select_query .= " COUNT(*) ";
		} else {

			$implode = array();

			if ( ! empty( $this->final_query['include_columns'] ) ) {
				foreach ( $this->final_query['include_columns'] as $col ) {
					$col_alias = ! empty( $custom_column_aliases[ $col ] ) ? $custom_column_aliases[ $col ] : $col;
					$implode[] = $col . " AS '" . $col_alias . "'";
				}
			} elseif ( ! empty( $custom_column_aliases ) ) {
				foreach ( $this->final_query['columns_for_alias'] ?? array() as $col ) {
					$col_alias = ! empty( $custom_column_aliases[ $col ] ) ? $custom_column_aliases[ $col ] : $col;
					$implode[] = $col . " AS '" . $col_alias . "'";
				}
			}

			if ( ! empty( $this->final_query['include_calc'] ) && ! empty( $this->final_query['calc_cols'] ) ) {
				foreach ( $this->final_query['calc_cols'] as $col ) {

					if ( empty( $col['column'] ) ) {
						continue;
					}

					$col_func = ! empty( $col['function'] ) ? $col['function'] : '';

					if ( 'custom' === $col_func ) {
						$custom_col      = ! empty( $col['custom_col'] ) ? $col['custom_col'] : '%1$s';
						$prepared_col    = str_replace( '%1$s', $col['column'], $custom_col );
						if ( ! $this->is_keep_macros() ) {
							$prepared_col = jet_engine()->listings->macros->do_macros( $prepared_col );
						}
						$prepared_col_as = sprintf( '%1$s(%2$s)', $col_func, $col['column'] );
					} else {
						$prepared_col    = sprintf( '%1$s(%2$s)', $col_func, $col['column'] );
						$prepared_col_as = $prepared_col;
					}

					if ( ! empty( $col['column_alias'] ) ) {
						$calc_column_aliases[ $prepared_col_as ] = $col['column_alias'];
						$prepared_col_as = $col['column_alias'];
					}

					$implode[] = $prepared_col . " AS '" . $prepared_col_as . "'";

					$this->calc_columns[] = $prepared_col_as;
				}
			}

			if ( ! empty( $implode ) ) {
				$select_query .= implode( ', ', $implode ) . " ";
			} else {
				$select_query .= "* ";
			}

		}

		if ( null === $this->current_query ) {

			$raw_table      = $this->final_query['table'];
			$prefixed_table = $prefix . $raw_table;
			$current_query  = "";

			$tables = array(
				$raw_table => 1
			);

			$current_query .= "FROM $prefixed_table AS $raw_table ";

			if ( ! empty( $this->final_query['use_join'] ) && ! empty( $this->final_query['join_tables'] ) ) {
				foreach ( $this->final_query['join_tables'] as $table ) {

					$type           = $table['type'];
					$raw_join_table = $table['table'];
					$join_table     = $prefix . $table['table'];

					if ( ! empty( $tables[ $raw_join_table ] ) ) {
						$tables[ $raw_join_table ] = $tables[ $raw_join_table ] + 1;
						$as_table = $raw_join_table . $tables[ $raw_join_table ];
					} else {
						$tables[ $raw_join_table ] = 1;
						$as_table = $raw_join_table;
					}

					$base_col    = $table['on_base'];
					$current_col = $table['on_current'];

					if ( false === strpos( $base_col, '.' ) ) {
						$base_col = $raw_table . '.' . $base_col;
					}

					$current_query .= "$type $join_table AS $as_table ON $base_col = $as_table.$current_col ";

				}
			}

			if ( ! empty( $this->final_query['where'] ) ) {

				$where = array();

				foreach ( $this->final_query['where'] as $row ) {
					$where[] = $row;
				}

				$where_relation = 'AND';

				if ( ! empty( $this->final_query['where_relation'] ) && count( $where ) > 1 ) {
					$where_relation = $this->sanitize_sql_relation( $this->final_query['where_relation'] );
				}

				$current_query .= $this->add_where_args( $where, $where_relation );
			}

			if ( ! empty( $this->final_query['group_results'] ) && ! empty( $this->final_query['group_by'] ) ) {
				$group_col = str_replace( '`', '', $this->final_query['group_by'] );
				$current_query .= " GROUP BY " . $group_col;
			}

			$this->current_query = $current_query;
		}

		$orderby_part = "";

		if ( ! empty( $this->final_query['orderby'] ) && ! $is_count ) {

			$orderby        = array();
			$orderby_part .= " ";

			foreach ( $this->final_query['orderby'] as $row ) {

				if ( empty( $row['orderby'] ) ) {
					continue;
				}

				$row['column'] = ! empty( $calc_column_aliases[ $row['orderby'] ] ) ? $calc_column_aliases[ $row['orderby'] ] : $row['orderby'];
				$orderby[] = $row;
			}

			$orderby_part .= $this->add_order_args( $orderby );
		}

		$limit_offset = "";

		if ( ! $this->is_keep_macros() ) {
			if ( ! $is_count ) {
				$limit = $this->get_items_per_page();
			} else {
				$limit = ! empty( $this->final_query['limit'] ) ? absint( $this->final_query['limit'] ) : 0;
			}
		} else {
			$limit = ! empty( $this->final_query['limit'] ) ? $this->final_query['limit'] : 0;
		}

		if ( $limit ) {
			$limit_offset .= " LIMIT";

			if ( ! $this->is_keep_macros() ) {
				$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;
			} else {
				$offset = ! empty( $this->final_query['offset'] ) ? $this->final_query['offset'] : 0;
			}

			if ( ! $is_count && ! empty( $this->final_query['_page'] ) ) {
				$page    = absint( $this->final_query['_page'] );
				$pages   = $this->get_items_pages_count();
				$_offset = ( $page - 1 ) * $this->get_items_per_page();
				$offset  = $offset + $_offset;

				// Fixed the following issue:
				// The last page has an incorrect number of posts if the `Total Query Limit` number
				// is not a multiple of the `Per Page Limit` number.
				if ( $page == $pages ) {
					$limit = $this->get_items_total_count() - $_offset;
				}
			}

			if ( $offset ) {
				$limit_offset .= " $offset, $limit";
			} else {
				$limit_offset .= " $limit";
			}
		}

		$result = apply_filters(
			'jet-engine/query-builder/build-query/result',
			$select_query . $this->current_query . $orderby_part . $limit_offset . ";",
			$this,
			$is_count
		);

		if ( $is_count && ( $this->is_grouped() || ! empty( $this->final_query['limit'] ) ) ) {
			$result = $this->wrap_grouped_query( 'COUNT(*)', $result );
		}

		if ( ! $this->is_keep_macros() ) {
			if ( $is_count ) {
				$this->sql_query_strings['count']['query'] = $result;
			} else {
				$this->sql_query_strings['items']['query'] = $result;
			}
		}

		return $result;
	}

	/**
	 * Build a preview-friendly version of the simple SQL query with raw macros kept.
	 *
	 * The runtime query is reset before and after the conversion so the preview
	 * does not mutate the normal execution state of the query instance.
	 *
	 * @return string Simple SQL string suitable for preview/export contexts.
	 */
	public function get_converted_sql() {
		$this->final_query = null;
		$this->reset_query();
		$this->setup_query();

		$this->final_query = $this->final_query_raw;

		$this->set_keep_macros();
		$result = $this->get_simple_query();
		$this->reset_keep_macros();

		$this->final_query = null;
		$this->reset_query();
		$this->setup_query();

		return $result;
	}

	/**
	 * Wrap an existing query so an outer SELECT can aggregate/group its results.
	 *
	 * @param string $select Select expression for the outer query.
	 * @param string $query  Inner SQL query to wrap.
	 * @return string Wrapped SQL query.
	 */
	public function wrap_grouped_query( $select, $query ) {
		$query = rtrim( $query, ';' );
		return "SELECT $select FROM ( $query ) AS grouped;";
	}

	/**
	 * Check whether the current runtime query groups rows by one or more columns.
	 *
	 * @return bool True when grouped result mode is enabled.
	 */
	public function is_grouped() {
		return ( ! empty( $this->final_query['group_results'] ) && ! empty( $this->final_query['group_by'] ) );
	}

	/**
	 * Build the ORDER BY fragment for the current SQL query.
	 *
	 * Columns are sanitized before being used in SQL. Calculated columns are
	 * handled separately because they may already be safe aliases.
	 *
	 * @param array $args Normalized order rows.
	 * @return string SQL ORDER BY fragment or an empty string.
	 */
	public function add_order_args( $args = array() ) {

		$query = '';
		$glue  = '';

		foreach ( $args as $arg ) {

			if ( in_array( $arg['column'], $this->calc_columns ) ) {
				$column = sprintf( '`%s`', $arg['column'] );
			} else {
				// Sanitize SQL `column name` string to prevent SQL injection.
				// See: https://github.com/Crocoblock/issues-tracker/issues/5251
				$column = \Jet_Engine_Tools::sanitize_sql_orderby( $arg['column'] );
			}

			$type   = ! empty( $arg['type'] ) ? $arg['type'] : 'CHAR';
			$order  = ! empty( $arg['order'] ) ? strtoupper( $arg['order'] ) : 'DESC';
			$order  = in_array( $order, array( 'ASC', 'DESC' ) ) ? $order : 'DESC';

			if ( ! $column ) {
				continue;
			}

			$query .= $glue;

			switch ( $type ) {
				case 'NUMERIC':
				case 'DECIMAL':
					$query .= "CAST( $column as DECIMAL )";
					break;

				case 'CHAR':
					$query .= $column;
					break;

				default:
					$query .= "CAST( $column as $type )";
					break;
			}

			$query .= " ";
			$query .= $order;

			$glue = ", ";
		}

		if ( $query ) {
			$query  = "ORDER BY " . $query;
		}

		return $query;
	}

	/**
	 * Build a simple nested OR expression for helper subqueries.
	 *
	 * When no explicit format is provided, the method derives an equality or
	 * inequality comparison format from the key syntax.
	 *
	 * @param string|null       $key    Column or key name used in the comparison.
	 * @param array             $value  Values that should be OR-combined.
	 * @param string|false      $format Optional sprintf-compatible SQL format string.
	 * @return string SQL fragment containing OR-combined comparisons.
	 */
	public function get_sub_query( $key = null, $value = null, $format = false ) {

		$query = '';
		$glue  = '';

		if ( ! $format ) {

			if ( false !== strpos( $key, '!' ) ) {
				$format = '%1$s != \'%2$s\'';
				$key    = ltrim( $key, '!' );
			} else {
				$format = '%1$s = \'%2$s\'';
			}

		}

		foreach ( $value as $child ) {
			$query .= $glue;
			$query .= sprintf( $format, esc_sql( $key ), esc_sql( $child ) );
			$glue   = ' OR ';
		}

		return $query;

	}

	/**
	 * Build a WHERE clause or nested where fragment from normalized where rows.
	 *
	 * The method recursively processes nested groups, skips empty dynamic clauses,
	 * and returns either a complete `WHERE ...` fragment or a nested fragment.
	 *
	 * @param array  $args             Normalized where rows or a nested group.
	 * @param string $rel              Logical relation used between sibling rows.
	 * @param bool   $add_where_string Whether to prepend the `WHERE` keyword.
	 * @return string SQL WHERE fragment.
	 */
	public function add_where_args( $args = array(), $rel = 'AND', $add_where_string = true ) {

		$query      = '';
		$multi_args = false;
		$rel        = $this->sanitize_sql_relation( $rel );

		if ( ! empty( $args['is_group'] )
			&& ! empty( $args['args'] )
			&& is_array( $args['args'] )
		) {
			$args = $args['args'];
			$add_where_string = false;
		}

		if ( ! empty( $args ) ) {

			$glue = '';
			$where_query = '';

			if ( count( $args ) > 1 ) {
				$multi_args = true;
			}

			foreach ( $args as $key => $arg ) {

				if ( is_array( $arg ) && isset( $arg['relation'] ) ) {
					$relation = $this->sanitize_sql_relation( $arg['relation'] );

					unset( $arg['relation'] );

					$clause = $this->add_where_args( $arg, $relation, false );

					if ( $clause ) {
						$clause = '( ' . $clause . ' )';
					}

				} else {

					$exclude_empty = ! empty( $arg['exclude_empty'] ) ? $arg['exclude_empty'] : false;
					$exclude_empty = filter_var( $exclude_empty, FILTER_VALIDATE_BOOLEAN );

					if ( $exclude_empty && \Jet_Engine_Tools::is_empty( $arg, 'value' ) ) {
						continue;
					}

					if ( is_array( $arg ) && isset( $arg['column'] ) ) {
						$column  = ! empty( $arg['column'] ) ? $arg['column'] : false;
						$compare = ! empty( $arg['compare'] ) ? $arg['compare'] : '=';
						$value   = ! empty( $arg['value'] ) ? $arg['value'] : '';
						$type    = ! empty( $arg['type'] ) ? $arg['type'] : false;
					} else {
						$column  = $key;
						$compare = '=';
						$value   = $arg;
						$type    = false;
					}

					$clause = $this->prepare_where_clause( $column, $compare, $value, $type );
				}

				if ( $clause ) {
					$where_query .= $glue;
					$where_query .= $clause;
					$glue   = ' ' . $rel . ' ';
				}

			}

			if ( ! empty( $where_query ) ) {

				if ( $add_where_string ) {
					$query .= ' WHERE ';
				}

				$query .= $where_query;
			}
		}

		return $query;

	}

	/**
	 * Convert a raw filter value into its SQL-safe representation for a given type.
	 *
	 * Numeric and timestamp-like types are cast to primitive values, while string
	 * values are quoted and escaped for direct interpolation into SQL fragments.
	 *
	 * @param string|int|float $value Raw value to prepare.
	 * @param string|false     $type  Declared query value type.
	 * @return string|int|float|false Prepared SQL-safe value or `false` for arrays.
	 */
	public function adjust_value_by_type( $value = '', $type = false ) {

		if ( is_array( $value ) ) {
			return false;
		}

		if ( $this->is_keep_macros() ) {
			return sprintf( "'%s'", $value );
		}

		if ( false !== strpos( strtolower( $type ), 'decimal' ) ) {
			$type = 'float';
		}

		switch ( $type ) {
			case 'integer':
				$value = absint( $value );
				break;
			case 'float':
				$value = floatval( $value );
				break;
			case 'timestamp':
				if ( ! \Jet_Engine_Tools::is_valid_timestamp( $value ) ) {
					$value = strtotime( $value );
				}
				$value = absint( $value );
				break;
			case 'date':
				$value = strtotime( $value );
				$value = sprintf( "'%s'", date( $value, 'Y-m-d H:i:s' ) );
				break;
			default:
				$value = sprintf( "'%s'", esc_sql( $value ) );
				break;
		}

		return $value;

	}

	/**
	 * Check whether a string starts with a specific substring.
	 *
	 * @param string $haystack Full string to inspect.
	 * @param string $needle   Expected prefix.
	 * @return bool True when the haystack starts with the needle.
	 */
	public function starts_with( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}

	/**
	 * Check whether a string ends with a specific substring.
	 *
	 * @param string $haystack Full string to inspect.
	 * @param string $needle   Expected suffix.
	 * @return bool True when the haystack ends with the needle.
	 */
	public function ends_with( $haystack, $needle ) {

		$length = strlen( $needle );

		if ( ! $length ) {
			return true;
		}

		return substr( $haystack, -$length ) === $needle;
	}

	/**
	 * Prepare a SQL LIKE-compatible value string.
	 *
	 * Existing wildcard markers are preserved; otherwise the value is wrapped in
	 * `%...%` so the resulting condition performs a contains-style match.
	 *
	 * @param string $value Raw filter value.
	 * @return string SQL-safe quoted LIKE value.
	 */
	public function prepare_value_for_like_operator( $value ) {
		if ( $this->is_keep_macros() ) {
			return sprintf( "'%%%s%%'", $value );
		}

		if ( $this->starts_with( $value, '%' ) || $this->ends_with( $value, '%' ) ) {
			return sprintf( "'%s'", $value );
		} else {
			return sprintf( "'%%%s%%'", esc_sql( $value ) );
		}
	}

	/**
	 * Build a single SQL comparison fragment from normalized where clause data.
	 *
	 * Supports scalar comparisons, list operators, between ranges, and recursive
	 * OR expansion when a scalar operator receives multiple values.
	 *
	 * @param string|false $column  SQL column reference.
	 * @param string       $compare SQL comparison operator.
	 * @param mixed        $value   Scalar or array value used in the comparison.
	 * @param string|false $type    Declared query value type.
	 * @return string SQL fragment for the prepared where clause.
	 */
	public function prepare_where_clause( $column = false, $compare = '=', $value = '', $type = false ) {

		$column  = $this->sanitize_sql_column( $column );
		$compare = $this->sanitize_sql_compare( $compare );
		$type    = $this->sanitize_sql_type( $type );

		if ( ! $column ) {
			return '';
		}

		// Handle EXISTS / NOT EXISTS as null checks without a value operand.
		if ( 'EXISTS' === $compare ) {
			return sprintf( '%s IS NOT NULL', esc_sql( $column ) );
		} elseif ( 'NOT EXISTS' === $compare ) {
			return sprintf( '%s IS NULL', esc_sql( $column ) );
		}

		$format = '%1$s %3$s %2$s';

		$array_operators = array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );

		if ( ! is_array( $value ) && in_array( $compare, $array_operators ) && false !== strpos( $value, ',' ) ) {
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		if ( is_array( $value ) ) {
			switch ( $compare ) {

				case 'IN':
				case 'NOT IN':

					array_walk( $value, function( &$item ) use ( $type ) {
						$item = $this->adjust_value_by_type( $item, $type );
					} );

					$value = sprintf( '( %s )', implode( ', ', $value ) );

					break;

				case 'BETWEEN':
				case 'NOT BETWEEN':

					$from = isset( $value[0] ) ? $value[0] : 0;
					$to   = isset( $value[1] ) ? $value[1] : $from;

					$from = $this->adjust_value_by_type( $from, $type );
					$to   = $this->adjust_value_by_type( $to, $type );

					$value = sprintf( '%1$s AND %2$s', $from, $to );

					break;

				default:
					$format = '(%2$s)';
					$args   = array();

					foreach ( $value as $val ) {
						$args[] = array(
							'column'  => $column,
							'compare' => $compare,
							'type'    => $type,
							'value'   => $val,
						);
					}

					$value = $this->add_where_args( $args, 'OR', false );
					break;

			}
		} else {

			if ( in_array( $compare, array( 'LIKE', 'NOT LIKE' ) ) ) {
				$value = $this->prepare_value_for_like_operator( $value );
			} else {
				$value = $this->adjust_value_by_type( $value, $type );
			}

			if ( in_array( $compare, array( 'IN', 'BETWEEN' ) ) ) {
				$compare = '=';
			} elseif ( in_array( $compare, array( 'NOT IN', 'NOT BETWEEN' ) ) ) {
				$compare = '!=';
			}

		}

		$result = sprintf( $format, esc_sql( $column ), $value, $compare );

		return $result;

	}

	/**
	 * Return the list of fields that can be referenced by this SQL query instance.
	 *
	 * Includes selected columns, aliased columns, and calculated columns so UI
	 * controls and other integrations can work with the effective field set.
	 *
	 * @return array Map of available field identifiers to their display labels.
	 */
	public function get_instance_fields() {

		$cols = array();

		if ( ! empty( $this->query['include_columns'] ) && empty( $this->final_query['advanced_mode'] ) ) {
			$cols = $this->query['include_columns'];
		} elseif ( ! empty( $this->query['default_columns'] ) ) {
			$cols = $this->query['default_columns'];
		}

		$result = array();

		$calc_columns = array();

		if ( ! empty( $this->query['include_calc'] ) && ! empty( $this->query['calc_cols'] ) ) {
			foreach ( $this->query['calc_cols'] as $col ) {

				if ( empty( $col['column'] ) ) {
					continue;
				}

				$col_func = ! empty( $col['function'] ) ? $col['function'] : '';

				if ( ! empty( $col['column_alias'] ) ) {
					$calc_label = sprintf( '%1$s (%2$s)', $col['column_alias'], 'calculated' );
					$calc_value = $col['column_alias'];
				} else {
					$calc_label = sprintf( '%1$s(%2$s)', $col_func, $col['column'] );
					$calc_value = $calc_label;
				}

				$calc_columns[ $calc_value ] = $calc_label;
			}
		}

		if ( ! empty( $cols ) ) {
			$custom_column_aliases = ! empty( $this->query['set_column_aliases'] ) ? array_column(
				$this->query['column_aliases'] ?? array(),
				'column_alias',
				'column'
			) : array();

			if ( ! empty( $custom_column_aliases
			     && ! empty( $this->query['columns_for_alias'] ) )
				 && empty( $this->query['include_columns'] ) ) {
				$cols = $this->query['columns_for_alias'];
			}

			foreach ( $cols as $col ) {
				$col = ! empty( $custom_column_aliases[ $col ] ) ? $custom_column_aliases[ $col ] : $col;
				$result[ $col ] = $col;
			}
		}

		if ( ! empty( $calc_columns ) ) {
			foreach ( $calc_columns as $name => $col ) {
				$result[ $name ] = $col;
			}
		}

		return $result;
	}

	/**
	 * List query keys whose values should retain raw dynamic placeholders.
	 *
	 * @return array Query config keys that should skip macro flattening in raw mode.
	 */
	public function get_args_to_dynamic() {
		return array(
			'manual_query',
			'count_query',
		);
	}

	/**
	 * Clear cached SQL fragments so the query can be rebuilt from current state.
	 *
	 * @return void
	 */
	public function reset_query() {
		$this->current_query = null;
	}

	/**
	 * Print SQL preview information before rendering query preview output.
	 *
	 * Displays the generated items query and any captured SQL errors, plus count
	 * query details when the count query fails separately.
	 *
	 * @return void
	 */
	public function before_preview_body() {
		print_r( esc_html__( ' FINAL QUERY PREVIEW:' ) . "\n" );
		print_r( $this->sql_query_strings['items']['query'] . "\n\n" );

		if ( $this->sql_query_strings['items']['error'] ) {
			print_r( esc_html__( ' QUERY ERROR:' ) . "\n" );
			print_r( $this->sql_query_strings['items']['error'] . "\n\n" );
			return;
		}

		if ( $this->sql_query_strings['count']['error'] ) {
			print_r( esc_html__( ' COUNT QUERY:' ) . "\n" );
			print_r( $this->sql_query_strings['count']['query'] . "\n\n" );
			print_r( esc_html__( ' COUNT QUERY ERROR:' ) . "\n" );
			print_r( $this->sql_query_strings['count']['error'] . "\n\n" );
		}
	}

}
