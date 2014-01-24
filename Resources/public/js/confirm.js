(function ($) {
    'use strict';

    //confirm message with class or data-attribute
    $('a[data-confirm], button[data-confirm], .data-confirm').on('click', function(e) {
        var self = $(this),
            message = self.attr('data-confirm') || 'Etes-vous sur de vouloir faire cette action ?';

        if (false === confirm(message)) {
            e.preventDefault();
        }
    });
}(jQuery));