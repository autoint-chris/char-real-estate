<?php
/**
 * Register and handle property meta boxes.
 *
 * @package Property_Listings
 */

class Property_Meta_Boxes {

    /**
     * Add meta boxes for property post type.
     *
     * @since 1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'property_details',
            __('Property Details', 'property-listings'),
            array($this, 'render_property_details'),
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'property_location',
            __('Property Location', 'property-listings'),
            array($this, 'render_property_location'),
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'property_images',
            __('Property Images', 'property-listings'),
            array($this, 'render_property_images'),
            'property',
            'side',
            'default'
        );
    }

    /**
     * Render property details meta box.
     *
     * @since 1.0.0
     * @param WP_Post $post The post object.
     */
    public function render_property_details($post) {
        // Add nonce for security
        wp_nonce_field('property_details_nonce', 'property_details_nonce_field');

        // Get current values
        $price = get_post_meta($post->ID, '_property_price', true);
        $bedrooms = get_post_meta($post->ID, '_property_bedrooms', true);
        $bathrooms = get_post_meta($post->ID, '_property_bathrooms', true);
        $sqft = get_post_meta($post->ID, '_property_sqft', true);
        $year_built = get_post_meta($post->ID, '_property_year_built', true);
        $lot_size = get_post_meta($post->ID, '_property_lot_size', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="property_price"><?php _e('Price', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_price" name="property_price"
                           value="<?php echo esc_attr($price); ?>" class="regular-text" step="0.01" />
                    <p class="description"><?php _e('Enter the property price (without currency symbol)', 'property-listings'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="property_bedrooms"><?php _e('Bedrooms', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_bedrooms" name="property_bedrooms"
                           value="<?php echo esc_attr($bedrooms); ?>" class="small-text" min="0" />
                </td>
            </tr>
            <tr>
                <th><label for="property_bathrooms"><?php _e('Bathrooms', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_bathrooms" name="property_bathrooms"
                           value="<?php echo esc_attr($bathrooms); ?>" class="small-text" step="0.5" min="0" />
                </td>
            </tr>
            <tr>
                <th><label for="property_sqft"><?php _e('Square Feet', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_sqft" name="property_sqft"
                           value="<?php echo esc_attr($sqft); ?>" class="regular-text" min="0" />
                </td>
            </tr>
            <tr>
                <th><label for="property_year_built"><?php _e('Year Built', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_year_built" name="property_year_built"
                           value="<?php echo esc_attr($year_built); ?>" class="small-text"
                           min="1800" max="<?php echo date('Y'); ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="property_lot_size"><?php _e('Lot Size (acres)', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_lot_size" name="property_lot_size"
                           value="<?php echo esc_attr($lot_size); ?>" class="regular-text" step="0.01" min="0" />
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render property location meta box.
     *
     * @since 1.0.0
     * @param WP_Post $post The post object.
     */
    public function render_property_location($post) {
        // Add nonce for security
        wp_nonce_field('property_location_nonce', 'property_location_nonce_field');

        // Get current values
        $address = get_post_meta($post->ID, '_property_address', true);
        $city = get_post_meta($post->ID, '_property_city', true);
        $state = get_post_meta($post->ID, '_property_state', true);
        $zip = get_post_meta($post->ID, '_property_zip', true);
        $country = get_post_meta($post->ID, '_property_country', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="property_address"><?php _e('Street Address', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_address" name="property_address"
                           value="<?php echo esc_attr($address); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th><label for="property_city"><?php _e('City', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_city" name="property_city"
                           value="<?php echo esc_attr($city); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="property_state"><?php _e('State/Province', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_state" name="property_state"
                           value="<?php echo esc_attr($state); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="property_zip"><?php _e('ZIP/Postal Code', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_zip" name="property_zip"
                           value="<?php echo esc_attr($zip); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="property_country"><?php _e('Country', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_country" name="property_country"
                           value="<?php echo esc_attr($country); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render property images meta box.
     *
     * @since 1.0.0
     * @param WP_Post $post The post object.
     */
    public function render_property_images($post) {
        // Add nonce for security
        wp_nonce_field('property_images_nonce', 'property_images_nonce_field');

        $image_service_id = get_post_meta($post->ID, '_property_image_service_id', true);
        $last_sync = get_post_meta($post->ID, '_property_images_last_sync', true);
        ?>
        <div class="property-images-meta">
            <p>
                <label for="property_image_service_id">
                    <?php _e('Image Service Property ID', 'property-listings'); ?>
                </label>
                <input type="text" id="property_image_service_id" name="property_image_service_id"
                       value="<?php echo esc_attr($image_service_id); ?>" class="widefat" />
                <span class="description">
                    <?php _e('Enter the property ID from your image service to sync images.', 'property-listings'); ?>
                </span>
            </p>

            <?php if ($last_sync): ?>
                <p class="description">
                    <?php
                    printf(
                        __('Last synced: %s', 'property-listings'),
                        date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_sync)
                    );
                    ?>
                </p>
            <?php endif; ?>

            <p>
                <button type="button" class="button button-secondary" id="sync-property-images">
                    <?php _e('Sync Images Now', 'property-listings'); ?>
                </button>
                <span class="spinner" style="float: none;"></span>
            </p>

            <div id="property-images-status"></div>
        </div>

        <style>
            .property-images-meta .description {
                display: block;
                margin-top: 5px;
            }
        </style>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @since 1.0.0
     * @param int $post_id The post ID.
     * @param WP_Post $post The post object.
     */
    public function save_meta_boxes($post_id, $post) {
        // Check if it's a property post type
        if ('property' !== $post->post_type) {
            return;
        }

        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save property details
        if (isset($_POST['property_details_nonce_field']) &&
            wp_verify_nonce($_POST['property_details_nonce_field'], 'property_details_nonce')) {

            $fields = array('price', 'bedrooms', 'bathrooms', 'sqft', 'year_built', 'lot_size');
            foreach ($fields as $field) {
                $key = "property_{$field}";
                if (isset($_POST[$key])) {
                    update_post_meta($post_id, "_{$key}", sanitize_text_field($_POST[$key]));
                }
            }
        }

        // Save property location
        if (isset($_POST['property_location_nonce_field']) &&
            wp_verify_nonce($_POST['property_location_nonce_field'], 'property_location_nonce')) {

            $fields = array('address', 'city', 'state', 'zip', 'country');
            foreach ($fields as $field) {
                $key = "property_{$field}";
                if (isset($_POST[$key])) {
                    update_post_meta($post_id, "_{$key}", sanitize_text_field($_POST[$key]));
                }
            }
        }

        // Save property images
        if (isset($_POST['property_images_nonce_field']) &&
            wp_verify_nonce($_POST['property_images_nonce_field'], 'property_images_nonce')) {

            if (isset($_POST['property_image_service_id'])) {
                update_post_meta($post_id, '_property_image_service_id', sanitize_text_field($_POST['property_image_service_id']));
            }
        }
    }
}
