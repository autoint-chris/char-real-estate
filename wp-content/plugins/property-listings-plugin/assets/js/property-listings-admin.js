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

    });

})(jQuery);
