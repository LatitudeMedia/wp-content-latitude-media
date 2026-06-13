<?php
/**
 * Trait to handle setup listing data and current object anywhere you need
 */
trait Jet_Engine_Setup_Listing_Trait {

	/**
	 * Setup listing
	 * @param  [type] $listing_settings [description]
	 * @param  mixed $object_id         Can be passed object_id and method will try to setup object, or object itself and method just pass it
	 * @return [type]                   [description]
	 */
	public function setup_listing( $listing_settings = array(), $object_id = null, $glob = false, $listing_id = false ) {

		if ( ! empty( $listing_settings ) ) {
			jet_engine()->listings->data->set_listing( jet_engine()->listings->get_new_doc( $listing_settings, $listing_id ) );
		} else {
			$listing_settings = jet_engine()->listings->data->get_listing_settings( $listing_id );
		}

		$source = ! empty( $listing_settings['listing_source'] ) ? $listing_settings['listing_source'] : 'posts';

		switch ( $source ) {

			case 'posts':
			case 'repeater':

				if ( $glob ) {

					global $post;

					if ( ! is_object( $object_id ) ) {
						$post = get_post( $object_id );
						setup_postdata( $post );
						$object = $post;
					} else {
						$object = $object_id;
						$post   = $object;
						setup_postdata( $post );
					}

				} else {
					if ( ! is_object( $object_id ) ) {
						$object = get_post( $object_id );
					} else {
						$object = $object_id;
					}
				}

				break;

			case 'terms':

				$tax = ! empty( $listing_settings['listing_tax'] ) ? $listing_settings['listing_tax'] : '';

				if ( ! is_object( $object_id ) ) {
					$object = get_term( $object_id, $tax );
				} else {
					$object = $object_id;
				}

				break;

			case 'users':
				if ( ! is_object( $object_id ) ) {
					$object = get_user_by( 'ID', $object_id );
				} else {
					$object = $object_id;
				}
				break;

			default:

				if ( ! is_object( $object_id ) ) {
					$object = apply_filters(
						'jet-engine/listing/render/object/' . $source,
						false,
						$object_id,
						$listing_settings,
						$this
					);
				} else {
					$object = $object_id;
				}

				break;

		}

		jet_engine()->listings->data->set_current_object( $object );
	}
}
