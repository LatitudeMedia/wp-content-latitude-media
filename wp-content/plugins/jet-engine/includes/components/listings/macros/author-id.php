<?php
namespace Jet_Engine\Macros;

/**
 * Returns ID of current post author.
 */
class Author_Id extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'author_id';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Post author ID', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$author_id = null;

		$macros_object = $this->get_macros_object();

		global $authordata;

		if ( $authordata ) {
			$author_id = get_the_author_meta( 'ID' );
		} else {

			$post = get_post();

			if ( $post ) {
				$author_id = get_the_author_meta( 'ID', $post->post_author );
			}
		}

		$author_id = apply_filters( 'jet-engine/listings/macros/author-id', $author_id, $macros_object );

		return $author_id;
	}
}