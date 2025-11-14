<?php
/**
 * Handle AJAX requests for the plugin.
 *
 * @package Property_Listings
 */

class Property_Ajax_Handler {

    /**
     * Initialize the AJAX handler.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action('wp_ajax_sync_property_images', array($this, 'sync_property_images'));
    }

    /**
     * Handle AJAX request to sync property images.
     *
     * @since 1.0.0
     */
    public function sync_property_images() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'property_listings_ajax')) {
            wp_send_json_error(array(
                'message' => __('Security check failed.', 'property-listings')
            ));
        }

        // Check user capabilities
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to perform this action.', 'property-listings')
            ));
        }

        // Get POST data
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $service_id = isset($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : '';

        if (!$post_id || !$service_id) {
            wp_send_json_error(array(
                'message' => __('Missing required parameters.', 'property-listings')
            ));
        }

        // Verify post exists and is a property
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'property') {
            wp_send_json_error(array(
                'message' => __('Invalid property post.', 'property-listings')
            ));
        }

        // Sync images
        $image_service = new Property_Image_Service();
        $result = $image_service->sync_property_images($post_id, $service_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ));
        }

        wp_send_json_success(array(
            'message' => sprintf(
                __('Successfully synced %d images.', 'property-listings'),
                count($result)
            ),
            'image_count' => count($result),
            'attachment_ids' => $result
        ));
    }
}

// Initialize AJAX handler
new Property_Ajax_Handler();
