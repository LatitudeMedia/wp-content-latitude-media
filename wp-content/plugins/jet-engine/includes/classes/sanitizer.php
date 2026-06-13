<?php
/**
 * Sanitizer method package.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_Sanitizer {

	/**
	 * Recursively sanitize an array or string
	 *
	 * @param mixed $input The input to sanitize.
	 * @param callable|null $callback Optional callback function to apply to strings.
	 * @return mixed Sanitized input.
	 */
	public static function sanitize_array_recursively( $input, $callback = null ) {

		if ( is_array( $input ) ) {
			foreach ( $input as $key => $value ) {
				$input[ $key ] = self::sanitize_array_recursively( $value );
			}
		} elseif ( is_string( $input ) ) {
			if ( is_callable( $callback ) ) {
				$input = call_user_func( $callback, $input );
			} elseif ( function_exists( 'sanitize_text_field' ) ) {
				// Fallback to sanitize_text_field if no callback provided
				// This is useful for sanitizing strings in WordPress context
				$input = sanitize_text_field( $input );
			}
		}

		return $input;
	}

	/**
	 * Sanitize a string to ensure it is a valid HTML tag.
	 *
	 * @param string $input The input string to sanitize.
	 * @return string Sanitized HTML tag.
	 */
	public static function sanitize_html_tag( $input ) {
		$available_tags = array(
			'div',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'span',
			'a',
			'section',
			'header',
			'footer',
			'main',
			'b',
			'em',
			'i',
			'nav',
			'article',
			'aside',
			'tr',
			'ul',
			'ol',
			'li'
		);

		return in_array( strtolower( $input ), $available_tags ) ? $input : 'div';
	}

	/**
	 * Ensures a string is a valid SQL 'order by' clause.
	 *
	 * Accepts one or more columns, with or without a sort order (ASC / DESC).
	 * e.g. 'column_1', 'column_1, column_2', 'column_1 ASC, column_2 DESC' etc.
	 *
	 * Also accepts 'posts.column_1', 'posts.column_1, column_2', 'posts.column_1 ASC, column_2 DESC' etc.
	 *
	 * Also accepts 'RAND()'.
	 *
	 * @param string $orderby Order by clause to be validated.
	 * @return string|false Returns $orderby if valid, false otherwise.
	 */
	public static function sanitize_sql_orderby( $orderby ) {

		if (
			preg_match( '/^\s*(([a-z0-9_\.]+|`[a-z0-9_\.]+`)(\s+(ASC|DESC))?\s*(,\s*(?=[a-z0-9_`\.])|$))+$/i', $orderby )
			|| preg_match( '/^\s*RAND\(\s*\)\s*$/i', $orderby )
			|| preg_match( '/^\s*FIELD\s*\(.*?\)\s*$/i', $orderby )
		) {
			return $orderby;
		}

		return false;
	}

	/**
	 * Sanitize inline CSS to remove potentially dangerous values.
	 *
	 * @param string $css The CSS string to sanitize.
	 * @return string Sanitized CSS.
	 */
	public static function sanitize_inline_css( $css ) {

		// Remove potentially dangerous values
		$css = preg_replace( '/expression\s*\(.*?\)/i', '', $css );
		$css = preg_replace( '/url\s*\(\s*[\'"]?\s*javascript:.*?[\'"]?\s*\)/i', '', $css );
		$css = preg_replace( '/@import\s+url\s*\(.*?\);?/i', '', $css );
		$css = preg_replace( '/behavior\s*:\s*url\s*\(.*?\)/i', '', $css );

		return $css;
	}
}
