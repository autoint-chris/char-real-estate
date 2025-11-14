<?php
/**
 * Fired during plugin activation.
 *
 * @package Property_Listings
 */

class Property_Listings_Activator {

    /**
     * Plugin activation logic.
     *
     * @since 1.0.0
     */
    public static function activate() {
        // Register the custom post type
        self::register_property_post_type();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set default options
        self::set_default_options();
    }

    /**
     * Register the property post type.
     *
     * @since 1.0.0
     */
    private static function register_property_post_type() {
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-post-type.php';
        $property_post_type = new Property_Post_Type();
        $property_post_type->register();
    }

    /**
     * Set default plugin options.
     *
     * @since 1.0.0
     */
    private static function set_default_options() {
        $default_options = array(
            'image_service_enabled' => false,
            'image_service_type' => 'api', // 'api' or 'server'
            'image_service_url' => '',
            'image_service_api_key' => '',
        );

        add_option('property_listings_settings', $default_options);
    }
}
