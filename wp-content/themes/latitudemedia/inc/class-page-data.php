<?php

namespace LatitudeMedia;

class Page_Data
{
    use Instance;

    protected $excluded = [];

    protected $featured = null;

    /**
     * @param array $items
     */
    public function addItems( array $items = []) {
        if( count($items) ) {
            $this->excluded = array_merge($this->excluded, $items);
        }
    }

    /**
     * @param $item
     */
    public function setFeatured( $item ) {
        if( !empty($item) ) {
            $this->featured = $item;
            $this->excluded[] = $item;
        }
    }

    /**
     * @return array
     */
    public function getItems() : array {
        return $this->excluded;
    }

    public function getFeatured() {
        return $this->featured;
    }
}

/**
 * Initialize the landing page class on theme setup
 *
 * @return object the landing page class
 */
function Page_Data() {
    return Page_Data::instance();
}
add_action( 'after_setup_theme', 'LatitudeMedia\Page_Data', 10 );