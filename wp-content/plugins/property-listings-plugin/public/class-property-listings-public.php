<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package Property_Listings
 */

class Property_Listings_Public {

    /**
     * The ID of this plugin.
     *
     * @since 1.0.0
     * @var string
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since 1.0.0
     * @var string
     */
    private $version;

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            PROPERTY_LISTINGS_PLUGIN_URL . 'assets/css/property-listings-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            PROPERTY_LISTINGS_PLUGIN_URL . 'assets/js/property-listings-public.js',
            array('jquery'),
            $this->version,
            false
        );
    }
}
