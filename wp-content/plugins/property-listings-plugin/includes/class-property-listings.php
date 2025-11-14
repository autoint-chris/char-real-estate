<?php
/**
 * The core plugin class.
 *
 * @package Property_Listings
 */

class Property_Listings {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since 1.0.0
     * @var Property_Listings_Loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since 1.0.0
     * @var string
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since 1.0.0
     * @var string
     */
    protected $version;

    /**
     * Initialize the plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->version = PROPERTY_LISTINGS_VERSION;
        $this->plugin_name = 'property-listings';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since 1.0.0
     */
    private function load_dependencies() {
        // Loader class
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-listings-loader.php';

        // Custom Post Type
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-post-type.php';

        // Meta Boxes
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-meta-boxes.php';

        // Image Service Integration
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-image-service.php';

        // AJAX Handler
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-ajax-handler.php';

        // Admin specific
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'admin/class-property-listings-admin.php';

        // Public specific
        require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'public/class-property-listings-public.php';

        $this->loader = new Property_Listings_Loader();
    }

    /**
     * Register all hooks related to admin functionality.
     *
     * @since 1.0.0
     */
    private function define_admin_hooks() {
        $plugin_admin = new Property_Listings_Admin($this->get_plugin_name(), $this->get_version());

        // Enqueue admin styles and scripts
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Register custom post type
        $property_post_type = new Property_Post_Type();
        $this->loader->add_action('init', $property_post_type, 'register');

        // Register meta boxes
        $property_meta_boxes = new Property_Meta_Boxes();
        $this->loader->add_action('add_meta_boxes', $property_meta_boxes, 'add_meta_boxes');
        $this->loader->add_action('save_post', $property_meta_boxes, 'save_meta_boxes', 10, 2);

        // Add admin menu for settings
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
    }

    /**
     * Register all hooks related to public functionality.
     *
     * @since 1.0.0
     */
    private function define_public_hooks() {
        $plugin_public = new Property_Listings_Public($this->get_plugin_name(), $this->get_version());

        // Enqueue public styles and scripts
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all hooks.
     *
     * @since 1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the loader.
     *
     * @since 1.0.0
     * @return Property_Listings_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_version() {
        return $this->version;
    }
}
