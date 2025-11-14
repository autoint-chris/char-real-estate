<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package Property_Listings
 */

class Property_Listings_Admin {

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
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            PROPERTY_LISTINGS_PLUGIN_URL . 'assets/css/property-listings-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            PROPERTY_LISTINGS_PLUGIN_URL . 'assets/js/property-listings-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name,
            'propertyListingsAdmin',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('property_listings_ajax'),
            )
        );
    }

    /**
     * Add admin menu for settings.
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=property',
            __('Property Listings Settings', 'property-listings'),
            __('Settings', 'property-listings'),
            'manage_options',
            'property-listings-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render the settings page.
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Save settings if form submitted
        if (isset($_POST['property_listings_settings_nonce']) &&
            wp_verify_nonce($_POST['property_listings_settings_nonce'], 'property_listings_settings')) {
            $this->save_settings();
        }

        $settings = get_option('property_listings_settings', array());
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('property_listings_settings', 'property_listings_settings_nonce'); ?>

                <h2><?php _e('Image Service Settings', 'property-listings'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="image_service_enabled">
                                <?php _e('Enable Image Service', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="image_service_enabled" name="image_service_enabled"
                                   value="1" <?php checked(isset($settings['image_service_enabled']) && $settings['image_service_enabled']); ?> />
                            <p class="description">
                                <?php _e('Enable integration with external image service', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="image_service_type">
                                <?php _e('Service Type', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="image_service_type" name="image_service_type">
                                <option value="api" <?php selected(isset($settings['image_service_type']) ? $settings['image_service_type'] : 'api', 'api'); ?>>
                                    <?php _e('API', 'property-listings'); ?>
                                </option>
                                <option value="server" <?php selected(isset($settings['image_service_type']) ? $settings['image_service_type'] : 'api', 'server'); ?>>
                                    <?php _e('Server Location', 'property-listings'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('Choose whether to connect via API or server location', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="image_service_url">
                                <?php _e('Service URL/Path', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="image_service_url" name="image_service_url"
                                   value="<?php echo esc_attr(isset($settings['image_service_url']) ? $settings['image_service_url'] : ''); ?>"
                                   class="regular-text" />
                            <p class="description">
                                <?php _e('Enter the API URL or server path for the image service', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="image_service_api_key">
                                <?php _e('API Key', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="password" id="image_service_api_key" name="image_service_api_key"
                                   value="<?php echo esc_attr(isset($settings['image_service_api_key']) ? $settings['image_service_api_key'] : ''); ?>"
                                   class="regular-text" />
                            <p class="description">
                                <?php _e('Enter the API key if required (only needed for API type)', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'property-listings')); ?>
            </form>

            <hr />

            <h2><?php _e('Getting Started', 'property-listings'); ?></h2>
            <div class="card">
                <h3><?php _e('Image Service Configuration', 'property-listings'); ?></h3>
                <p>
                    <?php _e('This plugin supports two methods for retrieving property images:', 'property-listings'); ?>
                </p>
                <ul>
                    <li><strong><?php _e('API:', 'property-listings'); ?></strong> <?php _e('Connect to a REST API that provides property images', 'property-listings'); ?></li>
                    <li><strong><?php _e('Server Location:', 'property-listings'); ?></strong> <?php _e('Access images from a network server or file share', 'property-listings'); ?></li>
                </ul>
                <p>
                    <?php _e('Configure the settings above once you have the details from your image service provider.', 'property-listings'); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Save plugin settings.
     *
     * @since 1.0.0
     */
    private function save_settings() {
        $settings = array(
            'image_service_enabled' => isset($_POST['image_service_enabled']) ? true : false,
            'image_service_type' => sanitize_text_field($_POST['image_service_type']),
            'image_service_url' => esc_url_raw($_POST['image_service_url']),
            'image_service_api_key' => sanitize_text_field($_POST['image_service_api_key']),
        );

        update_option('property_listings_settings', $settings);

        add_settings_error(
            'property_listings_messages',
            'property_listings_message',
            __('Settings Saved', 'property-listings'),
            'updated'
        );

        settings_errors('property_listings_messages');
    }
}
