(function($) {
    'use strict';

    var ImagePreview = function (element, options) {
        this.options   = options;
        this.element  = $(element);
        this.markup = $(options.markup);

        this.init();
    };

    ImagePreview.DEFAULTS = {
        selector: false,
        trigger: 'hover focus',
        markup: '<div class="popover right popover-xl"><div class="popover-content">--</div></div>'
    };

    ImagePreview.prototype = {
        constructor: ImagePreview,

        /**
         *
         */
        init: function() {
            var triggers = this.options.trigger.split(' ');
            for (var i = triggers.length; i--;) {
                var eventIn = triggers[i] == 'hover' ? 'mouseenter' : 'focusin';
                var eventOut = triggers[i] == 'hover' ? 'mouseleave' : 'focusout';
                this.element.on(eventIn + '.bs.imagepreview.data-api', this.options.selector, $.proxy(this.show, this));
                this.element.on(eventOut + '.bs.imagepreview.data-api', this.options.selector, $.proxy(this.hide, this));
            }
        },

        /**
         *
         */
        show: function() {
            var image = new Image();
            var that = this;

            $(document.body).append(this.markup);
            image.onload = function() {
                that.markup.find('.popover-content').html(image);
                var pos = that.element.position();
                pos.left = pos.left - that.markup.width() - 30;
                pos.top = pos.top - that.markup.height() / 2;
                that.markup.css(pos);
                that.markup.show();
            };0
            image.src = this.element.attr('data-image-preview');
        },

        /**
         *
         */
        hide: function() {
            this.markup.hide();
            this.markup.remove();
        }
    };


    // PREVIEW IMAGE PLUGIN DEFINITION
    // ==========================

    $.fn.imagePreview = function (option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('ds.imagepreview');
            var options = $.extend({}, ImagePreview.DEFAULTS, $this.data(), typeof option == 'object' && option);

            if (!data) {
                $this.data('bs.imagepreview', (data = new ImagePreview(this, options)))
            }

            if (!data) $this.data('ds.imagepreview', (data = new ImagePreview(this, options)));
            if (typeof option == 'string') data[option]()
        })
    };

    $.fn.imagePreview.Constructor = ImagePreview;

    $('[data-image-preview]').imagePreview();
}(jQuery));