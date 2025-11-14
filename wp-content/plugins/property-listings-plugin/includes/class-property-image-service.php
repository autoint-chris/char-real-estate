<?php
/**
 * Handle property image service integration.
 *
 * This class provides a framework for integrating with external image services.
 * It can be extended to support both API-based and server-based image retrieval.
 *
 * @package Property_Listings
 */

class Property_Image_Service {

    /**
     * The plugin settings.
     *
     * @since 1.0.0
     * @var array
     */
    private $settings;

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->settings = get_option('property_listings_settings', array());
    }

    /**
     * Check if image service is enabled.
     *
     * @since 1.0.0
     * @return bool
     */
    public function is_enabled() {
        return isset($this->settings['image_service_enabled']) && $this->settings['image_service_enabled'];
    }

    /**
     * Get the image service type (api or server).
     *
     * @since 1.0.0
     * @return string
     */
    public function get_service_type() {
        return isset($this->settings['image_service_type']) ? $this->settings['image_service_type'] : 'api';
    }

    /**
     * Sync images for a property from the image service.
     *
     * @since 1.0.0
     * @param int $post_id The property post ID.
     * @param string $service_property_id The property ID in the image service.
     * @return array|WP_Error Array of attachment IDs on success, WP_Error on failure.
     */
    public function sync_property_images($post_id, $service_property_id) {
        if (!$this->is_enabled()) {
            return new WP_Error('service_disabled', __('Image service is not enabled.', 'property-listings'));
        }

        if (empty($service_property_id)) {
            return new WP_Error('missing_id', __('Property ID is required.', 'property-listings'));
        }

        $service_type = $this->get_service_type();

        if ($service_type === 'api') {
            return $this->sync_from_api($post_id, $service_property_id);
        } elseif ($service_type === 'server') {
            return $this->sync_from_server($post_id, $service_property_id);
        }

        return new WP_Error('invalid_type', __('Invalid image service type.', 'property-listings'));
    }

    /**
     * Sync images from API.
     *
     * This is a placeholder method to be implemented when API details are available.
     *
     * @since 1.0.0
     * @param int $post_id The property post ID.
     * @param string $service_property_id The property ID in the image service.
     * @return array|WP_Error
     */
    private function sync_from_api($post_id, $service_property_id) {
        // Placeholder implementation
        // TODO: Implement API integration when service details are available

        $api_url = isset($this->settings['image_service_url']) ? $this->settings['image_service_url'] : '';
        $api_key = isset($this->settings['image_service_api_key']) ? $this->settings['image_service_api_key'] : '';

        if (empty($api_url)) {
            return new WP_Error('missing_config', __('API URL is not configured.', 'property-listings'));
        }

        /*
         * Example API integration structure:
         *
         * $response = wp_remote_get($api_url . '/properties/' . $service_property_id . '/images', array(
         *     'headers' => array(
         *         'Authorization' => 'Bearer ' . $api_key,
         *     ),
         * ));
         *
         * if (is_wp_error($response)) {
         *     return $response;
         * }
         *
         * $body = wp_remote_retrieve_body($response);
         * $data = json_decode($body, true);
         *
         * return $this->attach_images_to_post($post_id, $data['images']);
         */

        return new WP_Error('not_implemented', __('API integration is not yet configured. Please configure your image service settings.', 'property-listings'));
    }

    /**
     * Sync images from server location.
     *
     * This is a placeholder method to be implemented when server details are available.
     *
     * @since 1.0.0
     * @param int $post_id The property post ID.
     * @param string $service_property_id The property ID in the image service.
     * @return array|WP_Error
     */
    private function sync_from_server($post_id, $service_property_id) {
        // Placeholder implementation
        // TODO: Implement server integration when details are available

        $server_path = isset($this->settings['image_service_url']) ? $this->settings['image_service_url'] : '';

        if (empty($server_path)) {
            return new WP_Error('missing_config', __('Server path is not configured.', 'property-listings'));
        }

        /*
         * Example server integration structure:
         *
         * $image_directory = trailingslashit($server_path) . $service_property_id;
         *
         * if (!is_dir($image_directory)) {
         *     return new WP_Error('directory_not_found', __('Image directory not found.', 'property-listings'));
         * }
         *
         * $images = glob($image_directory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
         *
         * return $this->attach_images_to_post($post_id, $images);
         */

        return new WP_Error('not_implemented', __('Server integration is not yet configured. Please configure your image service settings.', 'property-listings'));
    }

    /**
     * Attach images to a property post.
     *
     * @since 1.0.0
     * @param int $post_id The property post ID.
     * @param array $image_urls Array of image URLs or file paths.
     * @return array Array of attachment IDs.
     */
    private function attach_images_to_post($post_id, $image_urls) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachment_ids = array();

        foreach ($image_urls as $image_url) {
            // Download or copy the image
            $tmp = download_url($image_url);

            if (is_wp_error($tmp)) {
                continue;
            }

            $file_array = array(
                'name' => basename($image_url),
                'tmp_name' => $tmp
            );

            // Upload the image to the media library
            $attachment_id = media_handle_sideload($file_array, $post_id);

            if (is_wp_error($attachment_id)) {
                @unlink($file_array['tmp_name']);
                continue;
            }

            $attachment_ids[] = $attachment_id;
        }

        // Update last sync timestamp
        update_post_meta($post_id, '_property_images_last_sync', time());

        // Set the first image as featured image if none exists
        if (!empty($attachment_ids) && !has_post_thumbnail($post_id)) {
            set_post_thumbnail($post_id, $attachment_ids[0]);
        }

        return $attachment_ids;
    }

    /**
     * Get all images for a property.
     *
     * @since 1.0.0
     * @param int $post_id The property post ID.
     * @return array Array of attachment data.
     */
    public function get_property_images($post_id) {
        $attachments = get_attached_media('image', $post_id);
        $images = array();

        foreach ($attachments as $attachment) {
            $images[] = array(
                'id' => $attachment->ID,
                'url' => wp_get_attachment_url($attachment->ID),
                'thumbnail' => wp_get_attachment_image_url($attachment->ID, 'thumbnail'),
                'medium' => wp_get_attachment_image_url($attachment->ID, 'medium'),
                'large' => wp_get_attachment_image_url($attachment->ID, 'large'),
                'title' => $attachment->post_title,
                'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
            );
        }

        return $images;
    }
}
