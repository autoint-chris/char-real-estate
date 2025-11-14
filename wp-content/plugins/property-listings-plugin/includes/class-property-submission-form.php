<?php
/**
 * Handle front-end property submission form.
 *
 * @package Property_Listings
 */

class Property_Submission_Form {

    /**
     * Form errors.
     *
     * @since 1.0.0
     * @var array
     */
    private $errors = array();

    /**
     * Form success message.
     *
     * @since 1.0.0
     * @var string
     */
    private $success_message = '';

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_shortcode('property_submission_form', array($this, 'render_form'));
        add_action('init', array($this, 'handle_form_submission'));
    }

    /**
     * Check if user can submit properties.
     *
     * @since 1.0.0
     * @return bool
     */
    private function can_submit_property() {
        $settings = get_option('property_listings_settings', array());
        $require_login = isset($settings['require_login_to_submit']) ? $settings['require_login_to_submit'] : true;

        if ($require_login && !is_user_logged_in()) {
            return false;
        }

        return true;
    }

    /**
     * Handle form submission.
     *
     * @since 1.0.0
     */
    public function handle_form_submission() {
        if (!isset($_POST['property_submission_nonce']) ||
            !wp_verify_nonce($_POST['property_submission_nonce'], 'submit_property')) {
            return;
        }

        // Check if user can submit
        if (!$this->can_submit_property()) {
            $this->errors[] = __('You must be logged in to submit a property.', 'property-listings');
            return;
        }

        // Validate and sanitize form data
        $property_data = $this->validate_form_data($_POST);

        if (empty($this->errors)) {
            // Create the property
            $property_id = $this->create_property($property_data);

            if ($property_id && !is_wp_error($property_id)) {
                $this->success_message = __('Your property has been submitted successfully and is pending review.', 'property-listings');

                // Send notification email to admin
                $this->send_admin_notification($property_id);

                // Optionally redirect or clear form
                // wp_redirect(add_query_arg('property_submitted', 'success', get_permalink()));
                // exit;
            } else {
                $this->errors[] = __('There was an error submitting your property. Please try again.', 'property-listings');
            }
        }
    }

    /**
     * Validate form data.
     *
     * @since 1.0.0
     * @param array $data Form data.
     * @return array Validated data.
     */
    private function validate_form_data($data) {
        $validated = array();

        // Required fields
        if (empty($data['property_title'])) {
            $this->errors[] = __('Property title is required.', 'property-listings');
        } else {
            $validated['title'] = sanitize_text_field($data['property_title']);
        }

        if (empty($data['property_description'])) {
            $this->errors[] = __('Property description is required.', 'property-listings');
        } else {
            $validated['description'] = wp_kses_post($data['property_description']);
        }

        // Property details
        $validated['price'] = isset($data['property_price']) ? floatval($data['property_price']) : '';
        $validated['bedrooms'] = isset($data['property_bedrooms']) ? intval($data['property_bedrooms']) : '';
        $validated['bathrooms'] = isset($data['property_bathrooms']) ? floatval($data['property_bathrooms']) : '';
        $validated['sqft'] = isset($data['property_sqft']) ? intval($data['property_sqft']) : '';
        $validated['year_built'] = isset($data['property_year_built']) ? intval($data['property_year_built']) : '';
        $validated['lot_size'] = isset($data['property_lot_size']) ? floatval($data['property_lot_size']) : '';

        // Location
        $validated['address'] = isset($data['property_address']) ? sanitize_text_field($data['property_address']) : '';
        $validated['city'] = isset($data['property_city']) ? sanitize_text_field($data['property_city']) : '';
        $validated['state'] = isset($data['property_state']) ? sanitize_text_field($data['property_state']) : '';
        $validated['zip'] = isset($data['property_zip']) ? sanitize_text_field($data['property_zip']) : '';
        $validated['country'] = isset($data['property_country']) ? sanitize_text_field($data['property_country']) : '';

        // Taxonomies
        $validated['property_type'] = isset($data['property_type']) ? intval($data['property_type']) : '';
        $validated['property_status'] = isset($data['property_status']) ? intval($data['property_status']) : '';

        // Contact information
        $validated['contact_name'] = isset($data['contact_name']) ? sanitize_text_field($data['contact_name']) : '';
        $validated['contact_email'] = isset($data['contact_email']) ? sanitize_email($data['contact_email']) : '';
        $validated['contact_phone'] = isset($data['contact_phone']) ? sanitize_text_field($data['contact_phone']) : '';

        // Validate email
        if (!empty($validated['contact_email']) && !is_email($validated['contact_email'])) {
            $this->errors[] = __('Please enter a valid email address.', 'property-listings');
        }

        return $validated;
    }

    /**
     * Create property post.
     *
     * @since 1.0.0
     * @param array $data Validated property data.
     * @return int|WP_Error Post ID on success, WP_Error on failure.
     */
    private function create_property($data) {
        // Determine post status
        $settings = get_option('property_listings_settings', array());
        $auto_publish = isset($settings['auto_publish_submissions']) ? $settings['auto_publish_submissions'] : false;
        $post_status = $auto_publish ? 'publish' : 'pending';

        // Create post
        $post_data = array(
            'post_title'   => $data['title'],
            'post_content' => $data['description'],
            'post_type'    => 'property',
            'post_status'  => $post_status,
            'post_author'  => is_user_logged_in() ? get_current_user_id() : 0,
        );

        $property_id = wp_insert_post($post_data);

        if (is_wp_error($property_id)) {
            return $property_id;
        }

        // Save meta data
        $meta_fields = array(
            'price', 'bedrooms', 'bathrooms', 'sqft', 'year_built', 'lot_size',
            'address', 'city', 'state', 'zip', 'country'
        );

        foreach ($meta_fields as $field) {
            if (!empty($data[$field])) {
                update_post_meta($property_id, '_property_' . $field, $data[$field]);
            }
        }

        // Save contact information
        if (!empty($data['contact_name'])) {
            update_post_meta($property_id, '_property_contact_name', $data['contact_name']);
        }
        if (!empty($data['contact_email'])) {
            update_post_meta($property_id, '_property_contact_email', $data['contact_email']);
        }
        if (!empty($data['contact_phone'])) {
            update_post_meta($property_id, '_property_contact_phone', $data['contact_phone']);
        }

        // Set taxonomies
        if (!empty($data['property_type'])) {
            wp_set_object_terms($property_id, intval($data['property_type']), 'property_type');
        }
        if (!empty($data['property_status'])) {
            wp_set_object_terms($property_id, intval($data['property_status']), 'property_status');
        }

        // Handle image uploads
        if (!empty($_FILES['property_images']['name'][0])) {
            $this->handle_image_uploads($property_id);
        }

        return $property_id;
    }

    /**
     * Handle image uploads.
     *
     * @since 1.0.0
     * @param int $property_id Property post ID.
     */
    private function handle_image_uploads($property_id) {
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

                $attachment_id = media_handle_upload('property_image', $property_id);

                if (!is_wp_error($attachment_id)) {
                    $uploaded_images[] = $attachment_id;
                }
            }
        }

        // Set first image as featured image
        if (!empty($uploaded_images)) {
            set_post_thumbnail($property_id, $uploaded_images[0]);
        }
    }

    /**
     * Send notification email to admin.
     *
     * @since 1.0.0
     * @param int $property_id Property post ID.
     */
    private function send_admin_notification($property_id) {
        $settings = get_option('property_listings_settings', array());
        $send_notifications = isset($settings['send_admin_notifications']) ? $settings['send_admin_notifications'] : true;

        if (!$send_notifications) {
            return;
        }

        $admin_email = get_option('admin_email');
        $property_title = get_the_title($property_id);
        $edit_link = admin_url('post.php?post=' . $property_id . '&action=edit');

        $subject = sprintf(__('New Property Submission: %s', 'property-listings'), $property_title);

        $message = sprintf(
            __("A new property has been submitted and is pending review.\n\nProperty: %s\n\nReview and publish: %s", 'property-listings'),
            $property_title,
            $edit_link
        );

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Render the submission form.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string Form HTML.
     */
    public function render_form($atts) {
        $atts = shortcode_atts(array(
            'redirect_url' => '',
        ), $atts);

        if (!$this->can_submit_property()) {
            return '<div class="property-form-notice error">' .
                   __('You must be logged in to submit a property.', 'property-listings') .
                   ' <a href="' . wp_login_url(get_permalink()) . '">' . __('Login here', 'property-listings') . '</a>' .
                   '</div>';
        }

        ob_start();
        ?>
        <div class="property-submission-form-wrapper">

            <?php if (!empty($this->success_message)): ?>
                <div class="property-form-notice success">
                    <?php echo esc_html($this->success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($this->errors)): ?>
                <div class="property-form-notice error">
                    <ul>
                        <?php foreach ($this->errors as $error): ?>
                            <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (empty($this->success_message)): ?>
                <form method="post" enctype="multipart/form-data" class="property-submission-form">
                    <?php wp_nonce_field('submit_property', 'property_submission_nonce'); ?>

                    <div class="form-section">
                        <h3><?php _e('Basic Information', 'property-listings'); ?></h3>

                        <div class="form-field required">
                            <label for="property_title"><?php _e('Property Title', 'property-listings'); ?> *</label>
                            <input type="text" id="property_title" name="property_title"
                                   value="<?php echo isset($_POST['property_title']) ? esc_attr($_POST['property_title']) : ''; ?>"
                                   required />
                            <span class="field-description"><?php _e('e.g., Beautiful 3BR Home in Downtown', 'property-listings'); ?></span>
                        </div>

                        <div class="form-field required">
                            <label for="property_description"><?php _e('Property Description', 'property-listings'); ?> *</label>
                            <textarea id="property_description" name="property_description" rows="8" required><?php
                                echo isset($_POST['property_description']) ? esc_textarea($_POST['property_description']) : '';
                            ?></textarea>
                            <span class="field-description"><?php _e('Detailed description of the property', 'property-listings'); ?></span>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><?php _e('Property Details', 'property-listings'); ?></h3>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="property_price"><?php _e('Price ($)', 'property-listings'); ?></label>
                                <input type="number" id="property_price" name="property_price"
                                       value="<?php echo isset($_POST['property_price']) ? esc_attr($_POST['property_price']) : ''; ?>"
                                       step="0.01" min="0" />
                            </div>

                            <div class="form-field">
                                <label for="property_sqft"><?php _e('Square Feet', 'property-listings'); ?></label>
                                <input type="number" id="property_sqft" name="property_sqft"
                                       value="<?php echo isset($_POST['property_sqft']) ? esc_attr($_POST['property_sqft']) : ''; ?>"
                                       min="0" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="property_bedrooms"><?php _e('Bedrooms', 'property-listings'); ?></label>
                                <input type="number" id="property_bedrooms" name="property_bedrooms"
                                       value="<?php echo isset($_POST['property_bedrooms']) ? esc_attr($_POST['property_bedrooms']) : ''; ?>"
                                       min="0" />
                            </div>

                            <div class="form-field">
                                <label for="property_bathrooms"><?php _e('Bathrooms', 'property-listings'); ?></label>
                                <input type="number" id="property_bathrooms" name="property_bathrooms"
                                       value="<?php echo isset($_POST['property_bathrooms']) ? esc_attr($_POST['property_bathrooms']) : ''; ?>"
                                       step="0.5" min="0" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="property_year_built"><?php _e('Year Built', 'property-listings'); ?></label>
                                <input type="number" id="property_year_built" name="property_year_built"
                                       value="<?php echo isset($_POST['property_year_built']) ? esc_attr($_POST['property_year_built']) : ''; ?>"
                                       min="1800" max="<?php echo date('Y'); ?>" />
                            </div>

                            <div class="form-field">
                                <label for="property_lot_size"><?php _e('Lot Size (acres)', 'property-listings'); ?></label>
                                <input type="number" id="property_lot_size" name="property_lot_size"
                                       value="<?php echo isset($_POST['property_lot_size']) ? esc_attr($_POST['property_lot_size']) : ''; ?>"
                                       step="0.01" min="0" />
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><?php _e('Location', 'property-listings'); ?></h3>

                        <div class="form-field">
                            <label for="property_address"><?php _e('Street Address', 'property-listings'); ?></label>
                            <input type="text" id="property_address" name="property_address"
                                   value="<?php echo isset($_POST['property_address']) ? esc_attr($_POST['property_address']) : ''; ?>" />
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="property_city"><?php _e('City', 'property-listings'); ?></label>
                                <input type="text" id="property_city" name="property_city"
                                       value="<?php echo isset($_POST['property_city']) ? esc_attr($_POST['property_city']) : ''; ?>" />
                            </div>

                            <div class="form-field">
                                <label for="property_state"><?php _e('State/Province', 'property-listings'); ?></label>
                                <input type="text" id="property_state" name="property_state"
                                       value="<?php echo isset($_POST['property_state']) ? esc_attr($_POST['property_state']) : ''; ?>" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="property_zip"><?php _e('ZIP/Postal Code', 'property-listings'); ?></label>
                                <input type="text" id="property_zip" name="property_zip"
                                       value="<?php echo isset($_POST['property_zip']) ? esc_attr($_POST['property_zip']) : ''; ?>" />
                            </div>

                            <div class="form-field">
                                <label for="property_country"><?php _e('Country', 'property-listings'); ?></label>
                                <input type="text" id="property_country" name="property_country"
                                       value="<?php echo isset($_POST['property_country']) ? esc_attr($_POST['property_country']) : ''; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><?php _e('Property Type & Status', 'property-listings'); ?></h3>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="property_type"><?php _e('Property Type', 'property-listings'); ?></label>
                                <?php
                                $property_types = get_terms(array(
                                    'taxonomy' => 'property_type',
                                    'hide_empty' => false,
                                ));
                                if (!empty($property_types) && !is_wp_error($property_types)):
                                ?>
                                    <select id="property_type" name="property_type">
                                        <option value=""><?php _e('Select Type', 'property-listings'); ?></option>
                                        <?php foreach ($property_types as $type): ?>
                                            <option value="<?php echo esc_attr($type->term_id); ?>"
                                                    <?php selected(isset($_POST['property_type']) ? $_POST['property_type'] : '', $type->term_id); ?>>
                                                <?php echo esc_html($type->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" id="property_type_text" name="property_type_text" placeholder="<?php _e('e.g., House, Apartment', 'property-listings'); ?>" />
                                <?php endif; ?>
                            </div>

                            <div class="form-field">
                                <label for="property_status"><?php _e('Property Status', 'property-listings'); ?></label>
                                <?php
                                $property_statuses = get_terms(array(
                                    'taxonomy' => 'property_status',
                                    'hide_empty' => false,
                                ));
                                if (!empty($property_statuses) && !is_wp_error($property_statuses)):
                                ?>
                                    <select id="property_status" name="property_status">
                                        <option value=""><?php _e('Select Status', 'property-listings'); ?></option>
                                        <?php foreach ($property_statuses as $status): ?>
                                            <option value="<?php echo esc_attr($status->term_id); ?>"
                                                    <?php selected(isset($_POST['property_status']) ? $_POST['property_status'] : '', $status->term_id); ?>>
                                                <?php echo esc_html($status->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" id="property_status_text" name="property_status_text" placeholder="<?php _e('e.g., For Sale, For Rent', 'property-listings'); ?>" />
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><?php _e('Property Images', 'property-listings'); ?></h3>

                        <div class="form-field">
                            <label for="property_images"><?php _e('Upload Images', 'property-listings'); ?></label>
                            <input type="file" id="property_images" name="property_images[]"
                                   accept="image/*" multiple />
                            <span class="field-description"><?php _e('You can select multiple images. First image will be used as featured image.', 'property-listings'); ?></span>
                        </div>

                        <div id="image-preview" class="image-preview-container"></div>
                    </div>

                    <div class="form-section">
                        <h3><?php _e('Contact Information', 'property-listings'); ?></h3>

                        <div class="form-field">
                            <label for="contact_name"><?php _e('Contact Name', 'property-listings'); ?></label>
                            <input type="text" id="contact_name" name="contact_name"
                                   value="<?php echo isset($_POST['contact_name']) ? esc_attr($_POST['contact_name']) : (is_user_logged_in() ? wp_get_current_user()->display_name : ''); ?>" />
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="contact_email"><?php _e('Contact Email', 'property-listings'); ?></label>
                                <input type="email" id="contact_email" name="contact_email"
                                       value="<?php echo isset($_POST['contact_email']) ? esc_attr($_POST['contact_email']) : (is_user_logged_in() ? wp_get_current_user()->user_email : ''); ?>" />
                            </div>

                            <div class="form-field">
                                <label for="contact_phone"><?php _e('Contact Phone', 'property-listings'); ?></label>
                                <input type="tel" id="contact_phone" name="contact_phone"
                                       value="<?php echo isset($_POST['contact_phone']) ? esc_attr($_POST['contact_phone']) : ''; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="button button-primary button-large">
                            <?php _e('Submit Property', 'property-listings'); ?>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
