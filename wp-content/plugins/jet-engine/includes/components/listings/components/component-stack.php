<?php
namespace Jet_Engine\Listings\Components;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Stack {

	/**
	 * Holds stack of called objects at the current moment.
	 *
	 * @var Component[]
	 */
	private $stack = array();

	private $stack_meta = array();

	/**
	 * Returns current stack
	 *
	 * @return Component[] Stack of components
	 */
	public function get_stack() {
		return $this->stack;
	}

	/**
	 * Returns stack depth
	 *
	 * @return int Stack depth
	 */
	public function get_depth() {
		return count( $this->stack );
	}

	/**
	 * Returns currently rendered component
	 *
	 * @return Component|null Current component instance or null if no component is being processed
	 */
	public function get_current_component() {
		return $this->stack[ count( $this->stack ) - 1 ] ?? null;
	}

	/**
	 * Check if component is processed now
	 *
	 * @param  Component $component Component instance to check;
	 *                              if not set, check if any component is being processed
	 * @return bool                 True if component being processed, false otherwise
	 */
	public function is_in_stack( $component = null ) {
		if ( empty( $component ) ) {
			return count( $this->stack ) > 0;
		}

		return in_array( $component, $this->stack );
	}

	/**
	 * Returns index of the component in the stack
	 *
	 * @param  Component $component Component instance to find in the stack
	 * @return int|null             Index of the component in the stack or null if not found
	 */
	public function get_component_stack_index( $component ) {
		return array_search( $component, $this->stack, true );
	}

	/**
	 * Returns parent component of the current one or of the component at the specified index
	 *
	 * @param  int|null $for_index Index of the component to get parent for;
	 *                             if not set, get parent for the current component
	 * @return Component|null      Parent component instance or null if no parent
	 */
	public function get_parent( $for_index = null ) {

		$index = $for_index;

		if ( null === $index ) {
			$index = count( $this->stack ) - 1;
		}

		if ( $index > 0 && isset( $this->stack[ $index - 1 ] ) ) {
			return $this->stack[ $index - 1 ];
		}

		return null;
	}

	/**
	 * Add component to the stack
	 *
	 * @param  Component $component Component instance to add to the stack
	 *
	 * @return bool                 True if component was added, false if already in stack
	 */
	public function increase_stack( $component ) {
		if ( ! $this->is_in_stack( $component ) ) {
			$this->stack[] = $component;
			return true;
		}

		return false;
	}

	/**
	 * Remove component from the stack
	 *
	 * @param Component $component Component instance to remove from the stack
	 */
	public function decrease_stack() {
		array_pop( $this->stack );
	}

	/**
	 * Set stack-level specific meta value
	 *
	 * @param  int    $index Index of the stack level
	 * @param  string $key   Meta key
	 * @return mixed         Meta value
	 */
	public function set_stack_meta( $index, $key, $value ) {

		if ( ! isset( $this->stack_meta[ $index ] ) ) {
			$this->stack_meta[ $index ] = array();
		}

		$this->stack_meta[ $index ][ $key ] = $value;
	}

	/**
	 * Get stack-level specific meta value
	 *
	 * @param  int    $index Index of the stack level
	 * @param  string $key   Meta key
	 * @return mixed         Meta value or null if not set
	 */
	public function get_stack_meta( $index, $key ) {
		if ( isset( $this->stack_meta[ $index ] ) && isset( $this->stack_meta[ $index ][ $key ] ) ) {
			return $this->stack_meta[ $index ][ $key ];
		}

		return null;
	}
}
