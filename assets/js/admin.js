/**
 * Admin scripts for Media Restriction plugin
 */
(function( $ ) {
    'use strict';

    $(function() {
        // Initialize select2 for better user selection
        if ($.fn.select2) {
            $('.media-restriction-multiselect').select2({
                allowClear: true, // Ensure allowClear is set to true
                width: 'resolve',
                closeOnSelect: false
            });
        }

        // Show success toast on form submission
        $('form').on('submit', function(event) {
            event.preventDefault();

            var form = $(this);
            var submitButton = form.find('input[type="submit"]');
            var originalButtonText = submitButton.val();

            submitButton.prop('disabled', true).val('Saving Changes..');

            // Submit the form via AJAX
            $.post(form.attr('action'), form.serialize())
                .done(function(response) {
                    $('#success-toast').fadeIn(400).delay(3000).fadeOut(400);
                })
                .fail(function() {
                    console.error('Media Restriction: Settings failed to save.');
                })
                .always(function() {
                    submitButton.prop('disabled', false).val(originalButtonText);
                });
        });
    });

})( jQuery );