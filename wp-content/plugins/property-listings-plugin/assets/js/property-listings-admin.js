(function($) {
    'use strict';

    /**
     * Property Listings Admin JavaScript
     */
    $(document).ready(function() {

        /**
         * Sync property images button handler
         */
        $('#sync-property-images').on('click', function(e) {
            e.preventDefault();

            var button = $(this);
            var spinner = button.next('.spinner');
            var statusDiv = $('#property-images-status');
            var postId = $('#post_ID').val();
            var serviceId = $('#property_image_service_id').val();

            if (!serviceId) {
                statusDiv
                    .removeClass('success')
                    .addClass('error')
                    .html('<p>Please enter an Image Service Property ID first.</p>')
                    .show();
                return;
            }

            // Disable button and show spinner
            button.prop('disabled', true);
            spinner.addClass('is-active');
            statusDiv.hide();

            // Make AJAX request
            $.ajax({
                url: propertyListingsAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'sync_property_images',
                    nonce: propertyListingsAdmin.nonce,
                    post_id: postId,
                    service_id: serviceId
                },
                success: function(response) {
                    if (response.success) {
                        statusDiv
                            .removeClass('error')
                            .addClass('success')
                            .html('<p>' + response.data.message + '</p>')
                            .show();
                    } else {
                        statusDiv
                            .removeClass('success')
                            .addClass('error')
                            .html('<p>Error: ' + response.data.message + '</p>')
                            .show();
                    }
                },
                error: function(xhr, status, error) {
                    statusDiv
                        .removeClass('success')
                        .addClass('error')
                        .html('<p>Error: Failed to sync images. Please try again.</p>')
                        .show();
                },
                complete: function() {
                    // Re-enable button and hide spinner
                    button.prop('disabled', false);
                    spinner.removeClass('is-active');
                }
            });
        });

        /**
         * Toggle API key field visibility based on service type
         */
        $('#image_service_type').on('change', function() {
            var serviceType = $(this).val();
            var apiKeyRow = $('#image_service_api_key').closest('tr');

            if (serviceType === 'api') {
                apiKeyRow.show();
            } else {
                apiKeyRow.hide();
            }
        }).trigger('change');

        /**
         * Add custom field
         */
        var customFieldIndex = $('.custom-field-row').length;
        $('#add-custom-field').on('click', function(e) {
            e.preventDefault();
            var fieldHtml = '<div class="custom-field-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">' +
                '<p>' +
                    '<label>Field Label:</label>' +
                    '<input type="text" name="custom_fields[' + customFieldIndex + '][label]" value="" class="regular-text" />' +
                '</p>' +
                '<p>' +
                    '<label>Field Type:</label>' +
                    '<select name="custom_fields[' + customFieldIndex + '][type]" class="regular-text">' +
                        '<option value="text">Text</option>' +
                        '<option value="number">Number</option>' +
                        '<option value="textarea">Textarea</option>' +
                        '<option value="select">Select Dropdown</option>' +
                    '</select>' +
                '</p>' +
                '<p>' +
                    '<label>Field Key:</label>' +
                    '<input type="text" name="custom_fields[' + customFieldIndex + '][key]" value="" class="regular-text" placeholder="e.g., square_footage" />' +
                    '<span class="description">Used to store the value (no spaces, lowercase)</span>' +
                '</p>' +
                '<p>' +
                    '<label>Options (for select):</label>' +
                    '<input type="text" name="custom_fields[' + customFieldIndex + '][options]" value="" class="regular-text" placeholder="Option 1, Option 2, Option 3" />' +
                    '<span class="description">Comma-separated values for select dropdown</span>' +
                '</p>' +
                '<p>' +
                    '<button type="button" class="button remove-custom-field">Remove Field</button>' +
                '</p>' +
            '</div>';

            $('#custom-fields-container').append(fieldHtml);
            customFieldIndex++;
        });

        /**
         * Remove custom field
         */
        $(document).on('click', '.remove-custom-field', function(e) {
            e.preventDefault();
            $(this).closest('.custom-field-row').remove();
        });

        /**
         * Add custom feature
         */
        var customFeatureIndex = $('.custom-feature-row').length;
        $('#add-custom-feature').on('click', function(e) {
            e.preventDefault();
            var featureHtml = '<div class="custom-feature-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">' +
                '<input type="text" name="custom_features[' + customFeatureIndex + '][label]" value="" class="regular-text" placeholder="Feature name" />' +
                '<input type="text" name="custom_features[' + customFeatureIndex + '][key]" value="" class="regular-text" placeholder="feature_key" />' +
                '<button type="button" class="button remove-custom-feature">Remove</button>' +
            '</div>';

            $('#custom-features-container').append(featureHtml);
            customFeatureIndex++;
        });

        /**
         * Remove custom feature
         */
        $(document).on('click', '.remove-custom-feature', function(e) {
            e.preventDefault();
            $(this).closest('.custom-feature-row').remove();
        });

    });

})(jQuery);
