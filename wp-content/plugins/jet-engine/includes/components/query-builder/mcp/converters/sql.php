<?php
namespace Jet_Engine\Query_Builder\MCP\Converters;

class SQL implements Converter_Interface {

	use Common_Trait;

	public function convert( array $args ): array {

		$sql = '';

		if ( isset( $args['sql'] ) ) {
			$sql = (string) $args['sql'];
		} elseif ( isset( $args['manual_query'] ) ) {
			$sql = (string) $args['manual_query'];
		}

		// Sanitize the SQL query to prevent SQL injection or dangerous operations
		$sql = $this->sanitize_sql( $sql );

		// replace default table prefix with the {prefix} placeholder
		$sql = str_replace( 'wp_', '{prefix}', $sql );

		return [
			'advanced_mode' => true,
			'manual_query'	=> $sql,
		];
	}

	/**
	 * Sanitize the SQL query to allow only safe operations.
	 *
	 * This function removes any potentially dangerous SQL commands
	 * and ensures that only SELECT queries are allowed.
	 *
	 * @param string $sql The raw SQL query.
	 * @return string The sanitized SQL query.
	 */
	public function sanitize_sql( string $sql ): string {

		// Remove null bytes and normalize line endings.
		$sql = str_replace("\0", '', $sql);
		$sql = str_replace(["\r\n", "\r"], "\n", $sql);

		// Remove SQL comments: -- ..., # ..., and /* ... */ (non-greedy, dotall).
		$no_comments = preg_replace( [
			'/--[ \t].*?(?=\n|$)/u',
			'/#[^\n]*?/u',
			'/\/\*.*?\*\//us',
		], ['', '', ''], $sql );

		if ( ! is_string( $no_comments ) ) {
			return '';
		}

		// Collapse whitespace and trim.
		$normalized = trim( preg_replace( '/\s+/u', ' ', $no_comments ) );

		// Remove any semicolons to enforce a single statement (and re-check later).
		$normalized = str_replace( ';', '', $normalized );

		if ( $normalized === '' ) {
			return '';
		}

		// Check if the query starts with SELECT (case-insensitive)
		if ( ! preg_match( '/^select\s/i', $normalized ) ) {
			// Not a SELECT query, so clear the SQL to block dangerous operations
			$sql = '';
		}

		// Forbidden tokens: match only when they appear as standalone statements,
		// not substrings like "call_id" or "'call'".
		$forbidden_pattern = '/
			(?:^|\s)                # start of string or whitespace
			(insert|update|delete|replace|truncate|alter|create|drop|rename|
			grant|revoke|call|exec|handler|do) \b
			|
			\b(load\s+data|into\s+outfile|into\s+dumpfile|load_file|
			sleep\s*\(|benchmark\s*\(|unlock\s+tables|lock\s+tables|
			set\s+@|set\s+names|pragma)\b
		/ix';

		if ( preg_match( $forbidden_pattern, $normalized ) ) {
			return '';
		}

		// Disallow referencing system schemas.
		if ( preg_match(
			 '/\b(information_schema|performance_schema|mysql|sys)\b/i',
			 $normalized
		) ) {
			return '';
		}

		// Ensure quotes are roughly balanced to avoid broken parsing tricks.
		// Count unescaped single and double quotes.
		$check_balance = function ( string $s, string $quote ) {
			// Remove escaped quotes \" or \' then count remaining quote chars.
			$tmp = preg_replace( '/\\\\' . $quote . '/u', '', $s );
			return substr_count( $tmp, $quote ) % 2 === 0;
		};

		if ( ! $check_balance( $normalized, "'" ) || ! $check_balance( $normalized, '"' ) ) {
			return '';
		}

		return $sql;
	}
}
