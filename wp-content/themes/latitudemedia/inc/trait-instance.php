<?php
/**
 * Trait file for Instance.
 */

namespace LatitudeMedia;

/**
 * Make a class into a singleton.
 */
trait Instance {
    /**
     * Existing instances.
     *
     * @var Instance
     */
    protected static $instance;

    /**
     * Get class instance.
     *
     * @return Instance
     */
    public static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new self;
            self::$instance->setup();
        }
        return self::$instance;
    }


    /**
     * Instance constructor
     */
    public function __construct() {
        // Silence is golden. Use the `setup()` method you must.
    }

    /**
     * Singleton setup
     */
    protected function setup() {
        // Override in implementing class with actions that should happen once per request.
    }
}
