/**
 * Basic UX enhancements for BuddyActivist Passwordless forms.
 */

(function ($) {

    $(document).ready(function () {

        // Add a simple loading state to submit buttons
        $('form.bapl-form').on('submit', function () {
            const button = $(this).find('button[type="submit"]');
            if (button.length) {
                button.data('original-text', button.text());
                button.prop('disabled', true).text('Please wait...');
            }
        });

    });

})(jQuery);
