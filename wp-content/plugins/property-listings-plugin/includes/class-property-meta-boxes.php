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
        // Remove default taxonomy meta boxes
        remove_meta_box('property_typediv', 'property', 'side');
        remove_meta_box('property_statusdiv', 'property', 'side');

        // Remove featured image meta box
        remove_meta_box('postimagediv', 'property', 'side');

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
            'property_custom_fields',
            __('Additional Custom Fields', 'property-listings'),
            array($this, 'render_custom_fields'),
            'property',
            'normal',
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
        $street = get_post_meta($post->ID, '_property_street', true);
        $city = get_post_meta($post->ID, '_property_city', true);
        $state = get_post_meta($post->ID, '_property_state', true);
        $zip = get_post_meta($post->ID, '_property_zip', true);
        $price = get_post_meta($post->ID, '_property_price', true);
        $rooms = get_post_meta($post->ID, '_property_rooms', true);
        $bathrooms = get_post_meta($post->ID, '_property_bathrooms', true);

        // Get taxonomies
        $property_types = get_terms(array('taxonomy' => 'property_type', 'hide_empty' => false));
        $property_statuses = get_terms(array('taxonomy' => 'property_status', 'hide_empty' => false));
        $current_type = wp_get_object_terms($post->ID, 'property_type', array('fields' => 'ids'));
        $current_status = wp_get_object_terms($post->ID, 'property_status', array('fields' => 'ids'));
        ?>
        <table class="form-table">
            <tr>
                <th><label for="property_street"><?php _e('Street', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_street" name="property_street"
                           value="<?php echo esc_attr($street); ?>" class="large-text" />
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
                <th><label for="property_state"><?php _e('State', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_state" name="property_state"
                           value="<?php echo esc_attr($state); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="property_zip"><?php _e('ZIP', 'property-listings'); ?></label></th>
                <td>
                    <input type="text" id="property_zip" name="property_zip"
                           value="<?php echo esc_attr($zip); ?>" class="regular-text" />
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
                <th><label for="property_type"><?php _e('Property Type', 'property-listings'); ?></label></th>
                <td>
                    <select id="property_type" name="property_type" class="regular-text">
                        <option value=""><?php _e('Select Type', 'property-listings'); ?></option>
                        <?php if (!empty($property_types) && !is_wp_error($property_types)): ?>
                            <?php foreach ($property_types as $type): ?>
                                <option value="<?php echo esc_attr($type->term_id); ?>"
                                        <?php selected(in_array($type->term_id, $current_type)); ?>>
                                    <?php echo esc_html($type->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="property_status"><?php _e('Property Status', 'property-listings'); ?></label></th>
                <td>
                    <select id="property_status" name="property_status" class="regular-text">
                        <option value=""><?php _e('Select Status', 'property-listings'); ?></option>
                        <?php if (!empty($property_statuses) && !is_wp_error($property_statuses)): ?>
                            <?php foreach ($property_statuses as $status): ?>
                                <option value="<?php echo esc_attr($status->term_id); ?>"
                                        <?php selected(in_array($status->term_id, $current_status)); ?>>
                                    <?php echo esc_html($status->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="property_images"><?php _e('Property Images', 'property-listings'); ?></label></th>
                <td>
                    <input type="file" id="property_images" name="property_images[]" accept="image/*" multiple class="regular-text" />
                    <p class="description"><?php _e('Select one or more images to upload', 'property-listings'); ?></p>
                    <?php
                    // Display existing images
                    $attachments = get_attached_media('image', $post->ID);
                    if (!empty($attachments)):
                    ?>
                        <div id="property-images-list" style="margin-top: 15px;">
                            <strong><?php _e('Uploaded Images:', 'property-listings'); ?></strong>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                                <?php foreach ($attachments as $attachment): ?>
                                    <div style="position: relative; width: 100px; height: 100px;">
                                        <img src="<?php echo wp_get_attachment_image_url($attachment->ID, 'thumbnail'); ?>"
                                             style="width: 100%; height: 100%; object-fit: cover; border: 1px solid #ddd;" />
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
            // Render custom fields
            $settings = get_option('property_listings_settings', array());
            $custom_fields = isset($settings['custom_fields']) ? $settings['custom_fields'] : array();

            if (!empty($custom_fields)):
                foreach ($custom_fields as $field):
                    $field_key = '_property_custom_' . $field['key'];
                    $field_value = get_post_meta($post->ID, $field_key, true);
            ?>
            <tr>
                <th><label for="custom_<?php echo esc_attr($field['key']); ?>"><?php echo esc_html($field['label']); ?></label></th>
                <td>
                    <?php if ($field['type'] === 'textarea'): ?>
                        <textarea id="custom_<?php echo esc_attr($field['key']); ?>"
                                  name="custom_fields[<?php echo esc_attr($field['key']); ?>]"
                                  class="large-text" rows="4"><?php echo esc_textarea($field_value); ?></textarea>
                    <?php elseif ($field['type'] === 'select'): ?>
                        <select id="custom_<?php echo esc_attr($field['key']); ?>"
                                name="custom_fields[<?php echo esc_attr($field['key']); ?>]"
                                class="regular-text">
                            <option value="">Select...</option>
                            <?php
                            $options = !empty($field['options']) ? explode(',', $field['options']) : array();
                            foreach ($options as $option):
                                $option = trim($option);
                            ?>
                                <option value="<?php echo esc_attr($option); ?>"
                                        <?php selected($field_value, $option); ?>>
                                    <?php echo esc_html($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="<?php echo esc_attr($field['type']); ?>"
                               id="custom_<?php echo esc_attr($field['key']); ?>"
                               name="custom_fields[<?php echo esc_attr($field['key']); ?>]"
                               value="<?php echo esc_attr($field_value); ?>"
                               class="regular-text"
                               <?php if ($field['type'] === 'number'): ?>step="any"<?php endif; ?> />
                    <?php endif; ?>
                </td>
            </tr>
            <?php
                endforeach;
            endif;
            ?>
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

        // Define default features
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

        // Add custom features from settings
        $settings = get_option('property_listings_settings', array());
        $custom_features = isset($settings['custom_features']) ? $settings['custom_features'] : array();

        if (!empty($custom_features)) {
            foreach ($custom_features as $custom_feature) {
                $available_features[$custom_feature['key']] = $custom_feature['label'];
            }
        }
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
     * Render custom fields meta box for property-specific fields.
     *
     * @since 1.0.0
     * @param WP_Post $post The post object.
     */
    public function render_custom_fields($post) {
        // Add nonce for security
        wp_nonce_field('property_custom_fields_nonce', 'property_custom_fields_nonce_field');

        // Get saved local custom fields
        $local_fields = get_post_meta($post->ID, '_property_local_fields', true);
        if (!is_array($local_fields)) {
            $local_fields = array();
        }
        ?>
        <p class="description" style="margin-bottom: 15px;">
            <?php _e('Add custom fields specific to this property only. These fields will not appear on other properties.', 'property-listings'); ?>
        </p>

        <div id="local-custom-fields-container">
            <?php if (!empty($local_fields)): ?>
                <?php foreach ($local_fields as $index => $field): ?>
                    <div class="local-field-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
                        <p style="margin-bottom: 10px;">
                            <label style="display: inline-block; width: 150px; font-weight: 600;"><?php _e('Field Label:', 'property-listings'); ?></label>
                            <input type="text" name="local_fields[<?php echo $index; ?>][label]"
                                   value="<?php echo esc_attr($field['label']); ?>" class="regular-text"
                                   placeholder="<?php _e('e.g., HOA Fee', 'property-listings'); ?>" />
                        </p>
                        <p style="margin-bottom: 10px;">
                            <label style="display: inline-block; width: 150px; font-weight: 600;"><?php _e('Field Value:', 'property-listings'); ?></label>
                            <input type="text" name="local_fields[<?php echo $index; ?>][value]"
                                   value="<?php echo esc_attr($field['value']); ?>" class="regular-text"
                                   placeholder="<?php _e('Enter value', 'property-listings'); ?>" />
                        </p>
                        <p>
                            <button type="button" class="button remove-local-field"><?php _e('Remove Field', 'property-listings'); ?></button>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <p>
            <button type="button" id="add-local-field" class="button button-secondary">
                <?php _e('+ Add Field', 'property-listings'); ?>
            </button>
        </p>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var localFieldIndex = $('.local-field-row').length;

            $('#add-local-field').on('click', function(e) {
                e.preventDefault();
                var fieldHtml = '<div class="local-field-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">' +
                    '<p style="margin-bottom: 10px;">' +
                        '<label style="display: inline-block; width: 150px; font-weight: 600;">Field Label:</label>' +
                        '<input type="text" name="local_fields[' + localFieldIndex + '][label]" value="" class="regular-text" placeholder="e.g., HOA Fee" />' +
                    '</p>' +
                    '<p style="margin-bottom: 10px;">' +
                        '<label style="display: inline-block; width: 150px; font-weight: 600;">Field Value:</label>' +
                        '<input type="text" name="local_fields[' + localFieldIndex + '][value]" value="" class="regular-text" placeholder="Enter value" />' +
                    '</p>' +
                    '<p>' +
                        '<button type="button" class="button remove-local-field">Remove Field</button>' +
                    '</p>' +
                '</div>';

                $('#local-custom-fields-container').append(fieldHtml);
                localFieldIndex++;
            });

            $(document).on('click', '.remove-local-field', function(e) {
                e.preventDefault();
                $(this).closest('.local-field-row').remove();
            });
        });
        </script>
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

            // Save street
            if (isset($_POST['property_street'])) {
                update_post_meta($post_id, '_property_street', sanitize_text_field($_POST['property_street']));
            }

            // Save city
            if (isset($_POST['property_city'])) {
                update_post_meta($post_id, '_property_city', sanitize_text_field($_POST['property_city']));
            }

            // Save state
            if (isset($_POST['property_state'])) {
                update_post_meta($post_id, '_property_state', sanitize_text_field($_POST['property_state']));
            }

            // Save zip
            if (isset($_POST['property_zip'])) {
                update_post_meta($post_id, '_property_zip', sanitize_text_field($_POST['property_zip']));
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

            // Save property type taxonomy
            if (isset($_POST['property_type']) && !empty($_POST['property_type'])) {
                wp_set_object_terms($post_id, intval($_POST['property_type']), 'property_type');
            } else {
                wp_set_object_terms($post_id, array(), 'property_type');
            }

            // Save property status taxonomy
            if (isset($_POST['property_status']) && !empty($_POST['property_status'])) {
                wp_set_object_terms($post_id, intval($_POST['property_status']), 'property_status');
            } else {
                wp_set_object_terms($post_id, array(), 'property_status');
            }

            // Handle image uploads
            if (!empty($_FILES['property_images']['name'][0])) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $files = $_FILES['property_images'];
                $uploaded_images = array();

                foreach ($files['name'] as $key => $value) {
                    if ($files['name'][$key]) {
                        $file = array(
                            'name'     => $files['name'][$key],
                            'type'     => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error'    => $files['error'][$key],
                            'size'     => $files['size'][$key]
                        );

                        $_FILES = array('property_image' => $file);
                        $attachment_id = media_handle_upload('property_image', $post_id);

                        if (!is_wp_error($attachment_id)) {
                            $uploaded_images[] = $attachment_id;
                        }
                    }
                }
            }

            // Save custom field values
            if (isset($_POST['custom_fields']) && is_array($_POST['custom_fields'])) {
                foreach ($_POST['custom_fields'] as $field_key => $field_value) {
                    $meta_key = '_property_custom_' . sanitize_key($field_key);
                    update_post_meta($post_id, $meta_key, sanitize_text_field($field_value));
                }
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

        // Save local custom fields
        if (isset($_POST['property_custom_fields_nonce_field']) &&
            wp_verify_nonce($_POST['property_custom_fields_nonce_field'], 'property_custom_fields_nonce')) {

            $local_fields = array();
            if (isset($_POST['local_fields']) && is_array($_POST['local_fields'])) {
                foreach ($_POST['local_fields'] as $field) {
                    // Only save if both label and value are provided
                    if (!empty($field['label']) || !empty($field['value'])) {
                        $local_fields[] = array(
                            'label' => sanitize_text_field($field['label']),
                            'value' => sanitize_text_field($field['value']),
                        );
                    }
                }
            }

            update_post_meta($post_id, '_property_local_fields', $local_fields);
        }
    }
}
