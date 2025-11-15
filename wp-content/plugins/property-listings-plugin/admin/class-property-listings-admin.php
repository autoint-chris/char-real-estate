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

                <h2><?php _e('Custom Fields Management', 'property-listings'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label><?php _e('Property Details Fields', 'property-listings'); ?></label>
                        </th>
                        <td>
                            <div id="custom-fields-container">
                                <?php
                                $custom_fields = isset($settings['custom_fields']) ? $settings['custom_fields'] : array();
                                if (!empty($custom_fields)):
                                    foreach ($custom_fields as $index => $field):
                                ?>
                                    <div class="custom-field-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                                        <p>
                                            <label><?php _e('Field Label:', 'property-listings'); ?></label>
                                            <input type="text" name="custom_fields[<?php echo $index; ?>][label]"
                                                   value="<?php echo esc_attr($field['label']); ?>" class="regular-text" />
                                        </p>
                                        <p>
                                            <label><?php _e('Field Type:', 'property-listings'); ?></label>
                                            <select name="custom_fields[<?php echo $index; ?>][type]" class="regular-text">
                                                <option value="text" <?php selected($field['type'], 'text'); ?>><?php _e('Text', 'property-listings'); ?></option>
                                                <option value="number" <?php selected($field['type'], 'number'); ?>><?php _e('Number', 'property-listings'); ?></option>
                                                <option value="textarea" <?php selected($field['type'], 'textarea'); ?>><?php _e('Textarea', 'property-listings'); ?></option>
                                                <option value="select" <?php selected($field['type'], 'select'); ?>><?php _e('Select Dropdown', 'property-listings'); ?></option>
                                            </select>
                                        </p>
                                        <p>
                                            <label><?php _e('Field Key:', 'property-listings'); ?></label>
                                            <input type="text" name="custom_fields[<?php echo $index; ?>][key]"
                                                   value="<?php echo esc_attr($field['key']); ?>" class="regular-text"
                                                   placeholder="<?php _e('e.g., square_footage', 'property-listings'); ?>" />
                                            <span class="description"><?php _e('Used to store the value (no spaces, lowercase)', 'property-listings'); ?></span>
                                        </p>
                                        <p>
                                            <label><?php _e('Options (for select):', 'property-listings'); ?></label>
                                            <input type="text" name="custom_fields[<?php echo $index; ?>][options]"
                                                   value="<?php echo isset($field['options']) ? esc_attr($field['options']) : ''; ?>" class="regular-text"
                                                   placeholder="<?php _e('Option 1, Option 2, Option 3', 'property-listings'); ?>" />
                                            <span class="description"><?php _e('Comma-separated values for select dropdown', 'property-listings'); ?></span>
                                        </p>
                                        <p>
                                            <button type="button" class="button remove-custom-field"><?php _e('Remove Field', 'property-listings'); ?></button>
                                        </p>
                                    </div>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <p>
                                <button type="button" id="add-custom-field" class="button"><?php _e('Add New Field', 'property-listings'); ?></button>
                            </p>
                            <p class="description">
                                <?php _e('These fields will appear on all property forms', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php _e('Property Features', 'property-listings'); ?></label>
                        </th>
                        <td>
                            <div id="custom-features-container">
                                <?php
                                $custom_features = isset($settings['custom_features']) ? $settings['custom_features'] : array();
                                if (!empty($custom_features)):
                                    foreach ($custom_features as $index => $feature):
                                ?>
                                    <div class="custom-feature-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
                                        <input type="text" name="custom_features[<?php echo $index; ?>][label]"
                                               value="<?php echo esc_attr($feature['label']); ?>" class="regular-text"
                                               placeholder="<?php _e('Feature name', 'property-listings'); ?>" />
                                        <input type="text" name="custom_features[<?php echo $index; ?>][key]"
                                               value="<?php echo esc_attr($feature['key']); ?>" class="regular-text"
                                               placeholder="<?php _e('feature_key', 'property-listings'); ?>" />
                                        <button type="button" class="button remove-custom-feature"><?php _e('Remove', 'property-listings'); ?></button>
                                    </div>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <p>
                                <button type="button" id="add-custom-feature" class="button"><?php _e('Add New Feature', 'property-listings'); ?></button>
                            </p>
                            <p class="description">
                                <?php _e('Add custom features/amenities that will appear as checkboxes', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Submission Form Settings', 'property-listings'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="require_login_to_submit">
                                <?php _e('Require Login', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="require_login_to_submit" name="require_login_to_submit"
                                   value="1" <?php checked(isset($settings['require_login_to_submit']) ? $settings['require_login_to_submit'] : true, true); ?> />
                            <p class="description">
                                <?php _e('Require users to be logged in to submit properties', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="auto_publish_submissions">
                                <?php _e('Auto-Publish Submissions', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="auto_publish_submissions" name="auto_publish_submissions"
                                   value="1" <?php checked(isset($settings['auto_publish_submissions']) && $settings['auto_publish_submissions']); ?> />
                            <p class="description">
                                <?php _e('Automatically publish submitted properties (if disabled, submissions will be set to "Pending Review")', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="send_admin_notifications">
                                <?php _e('Email Notifications', 'property-listings'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="send_admin_notifications" name="send_admin_notifications"
                                   value="1" <?php checked(isset($settings['send_admin_notifications']) ? $settings['send_admin_notifications'] : true, true); ?> />
                            <p class="description">
                                <?php _e('Send email notification to site admin when a property is submitted', 'property-listings'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'property-listings')); ?>
            </form>

            <hr />

            <h2><?php _e('Getting Started', 'property-listings'); ?></h2>

            <div class="card">
                <h3><?php _e('Property Submission Form', 'property-listings'); ?></h3>
                <p>
                    <?php _e('To display the property submission form on any page or post, use the following shortcode:', 'property-listings'); ?>
                </p>
                <p>
                    <code>[property_submission_form]</code>
                </p>
                <p>
                    <?php _e('Users can submit properties through this form. You can control whether submissions require login and whether they are automatically published or require review.', 'property-listings'); ?>
                </p>
            </div>

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
        // Sanitize custom fields
        $custom_fields = array();
        if (isset($_POST['custom_fields']) && is_array($_POST['custom_fields'])) {
            foreach ($_POST['custom_fields'] as $field) {
                if (!empty($field['label']) && !empty($field['key'])) {
                    $custom_fields[] = array(
                        'label' => sanitize_text_field($field['label']),
                        'type' => sanitize_text_field($field['type']),
                        'key' => sanitize_key($field['key']),
                        'options' => isset($field['options']) ? sanitize_text_field($field['options']) : '',
                    );
                }
            }
        }

        // Sanitize custom features
        $custom_features = array();
        if (isset($_POST['custom_features']) && is_array($_POST['custom_features'])) {
            foreach ($_POST['custom_features'] as $feature) {
                if (!empty($feature['label']) && !empty($feature['key'])) {
                    $custom_features[] = array(
                        'label' => sanitize_text_field($feature['label']),
                        'key' => sanitize_key($feature['key']),
                    );
                }
            }
        }

        $settings = array(
            'image_service_enabled' => isset($_POST['image_service_enabled']) ? true : false,
            'image_service_type' => sanitize_text_field($_POST['image_service_type']),
            'image_service_url' => esc_url_raw($_POST['image_service_url']),
            'image_service_api_key' => sanitize_text_field($_POST['image_service_api_key']),
            'custom_fields' => $custom_fields,
            'custom_features' => $custom_features,
            'require_login_to_submit' => isset($_POST['require_login_to_submit']) ? true : false,
            'auto_publish_submissions' => isset($_POST['auto_publish_submissions']) ? true : false,
            'send_admin_notifications' => isset($_POST['send_admin_notifications']) ? true : false,
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
