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
            'property_features',
            __('Property Features', 'property-listings'),
            array($this, 'render_property_features'),
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
        $address = get_post_meta($post->ID, '_property_address', true);
        $price = get_post_meta($post->ID, '_property_price', true);
        $rooms = get_post_meta($post->ID, '_property_rooms', true);
        $bathrooms = get_post_meta($post->ID, '_property_bathrooms', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="property_address"><?php _e('Address', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_address" name="property_address"
                           value="<?php echo esc_attr($address); ?>" class="large-text" />
                    <p class="description"><?php _e('Full property address', 'property-listings'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="property_price"><?php _e('Price', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_price" name="property_price"
                           value="<?php echo esc_attr($price); ?>" class="regular-text" step="0.01" min="0" />
                    <p class="description"><?php _e('Enter the property price (without currency symbol)', 'property-listings'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="property_rooms"><?php _e('Rooms', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_rooms" name="property_rooms"
                           value="<?php echo esc_attr($rooms); ?>" class="small-text" min="0" />
                    <p class="description"><?php _e('Number of rooms', 'property-listings'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="property_bathrooms"><?php _e('Bathrooms', 'property-listings'); ?></label></th>
                <td>
                    <input type="number" id="property_bathrooms" name="property_bathrooms"
                           value="<?php echo esc_attr($bathrooms); ?>" class="small-text" step="0.5" min="0" />
                    <p class="description"><?php _e('Number of bathrooms', 'property-listings'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render property features meta box.
     *
     * @since 1.0.0
     * @param WP_Post $post The post object.
     */
    public function render_property_features($post) {
        // Add nonce for security
        wp_nonce_field('property_features_nonce', 'property_features_nonce_field');

        // Get current values
        $features = get_post_meta($post->ID, '_property_features', true);
        if (!is_array($features)) {
            $features = array();
        }

        // Define available features
        $available_features = array(
            'parking' => __('Parking', 'property-listings'),
            'garage' => __('Garage', 'property-listings'),
            'garden' => __('Garden', 'property-listings'),
            'pool' => __('Swimming Pool', 'property-listings'),
            'balcony' => __('Balcony', 'property-listings'),
            'terrace' => __('Terrace', 'property-listings'),
            'elevator' => __('Elevator', 'property-listings'),
            'air_conditioning' => __('Air Conditioning', 'property-listings'),
            'heating' => __('Central Heating', 'property-listings'),
            'fireplace' => __('Fireplace', 'property-listings'),
            'security' => __('Security System', 'property-listings'),
            'furnished' => __('Furnished', 'property-listings'),
            'pet_friendly' => __('Pet Friendly', 'property-listings'),
            'laundry' => __('Laundry Room', 'property-listings'),
            'storage' => __('Storage Space', 'property-listings'),
        );
        ?>
        <div class="property-features-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 15px;">
            <?php foreach ($available_features as $key => $label): ?>
                <label style="display: flex; align-items: center; margin: 0;">
                    <input type="checkbox"
                           name="property_features[]"
                           value="<?php echo esc_attr($key); ?>"
                           <?php checked(in_array($key, $features)); ?>
                           style="margin-right: 8px;" />
                    <?php echo esc_html($label); ?>
                </label>
            <?php endforeach; ?>
        </div>
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

            // Save address
            if (isset($_POST['property_address'])) {
                update_post_meta($post_id, '_property_address', sanitize_text_field($_POST['property_address']));
            }

            // Save price
            if (isset($_POST['property_price'])) {
                update_post_meta($post_id, '_property_price', sanitize_text_field($_POST['property_price']));
            }

            // Save rooms
            if (isset($_POST['property_rooms'])) {
                update_post_meta($post_id, '_property_rooms', intval($_POST['property_rooms']));
            }

            // Save bathrooms
            if (isset($_POST['property_bathrooms'])) {
                update_post_meta($post_id, '_property_bathrooms', sanitize_text_field($_POST['property_bathrooms']));
            }
        }

        // Save property features
        if (isset($_POST['property_features_nonce_field']) &&
            wp_verify_nonce($_POST['property_features_nonce_field'], 'property_features_nonce')) {

            // Get selected features or empty array if none selected
            $features = isset($_POST['property_features']) ? $_POST['property_features'] : array();

            // Sanitize each feature
            $features = array_map('sanitize_text_field', $features);

            // Save as serialized array
            update_post_meta($post_id, '_property_features', $features);
        } else {
            // If nonce is set but no features selected, save empty array
            if (isset($_POST['property_features_nonce_field'])) {
                update_post_meta($post_id, '_property_features', array());
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
