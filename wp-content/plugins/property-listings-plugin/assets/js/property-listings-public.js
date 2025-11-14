(function($) {
    'use strict';

    /**
     * Property Listings Public JavaScript
     */
    $(document).ready(function() {

        /**
         * Initialize property gallery lightbox
         * (Can be extended with a lightbox library like Fancybox or PhotoSwipe)
         */
        $('.property-gallery-item').on('click', function(e) {
            // Placeholder for lightbox functionality
            // Add your preferred lightbox implementation here
        });

        /**
         * Property search and filter functionality
         * (Can be extended based on requirements)
         */
        // Add your custom JavaScript for property filtering, AJAX loading, etc.

        /**
         * Property Submission Form - Image Preview
         */
        var selectedFiles = [];

        $('#property_images').on('change', function(e) {
            var files = e.target.files;
            var previewContainer = $('#image-preview');

            // Add new files to selectedFiles array
            for (var i = 0; i < files.length; i++) {
                selectedFiles.push(files[i]);
            }

            // Update preview
            updateImagePreview();
        });

        function updateImagePreview() {
            var previewContainer = $('#image-preview');
            previewContainer.empty();

            selectedFiles.forEach(function(file, index) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var previewItem = $('<div class="image-preview-item"></div>');
                    var img = $('<img src="' + e.target.result + '" alt="Preview">');
                    var removeBtn = $('<button type="button" class="remove-image" data-index="' + index + '">&times;</button>');

                    previewItem.append(img);
                    previewItem.append(removeBtn);
                    previewContainer.append(previewItem);
                };

                reader.readAsDataURL(file);
            });
        }

        // Remove image from preview
        $(document).on('click', '.remove-image', function() {
            var index = $(this).data('index');
            selectedFiles.splice(index, 1);
            updateImagePreview();
            updateFileInput();
        });

        function updateFileInput() {
            // Create a new DataTransfer to update the file input
            var dataTransfer = new DataTransfer();

            selectedFiles.forEach(function(file) {
                dataTransfer.items.add(file);
            });

            document.getElementById('property_images').files = dataTransfer.files;
        }

        /**
         * Form validation and user feedback
         */
        $('.property-submission-form').on('submit', function(e) {
            var title = $('#property_title').val().trim();
            var description = $('#property_description').val().trim();

            if (!title) {
                alert('Please enter a property title.');
                $('#property_title').focus();
                return false;
            }

            if (!description) {
                alert('Please enter a property description.');
                $('#property_description').focus();
                return false;
            }

            // Show loading state
            var submitBtn = $(this).find('button[type="submit"]');
            var originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Submitting...');

            // The form will submit normally, but we've disabled the button to prevent double submission
        });

        /**
         * Auto-fill contact info for logged-in users (if not already filled)
         */
        if ($('#contact_name').length && !$('#contact_name').val()) {
            // Contact fields will be pre-filled by PHP if user is logged in
        }

    });

})(jQuery);
