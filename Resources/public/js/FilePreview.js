/**
 * File Preview
 */
var appCms = appCms || {};
appCms.FilePreview = (function($) {
    'use strict';

    var _extToMines = {
        jpg: 'Image',
        jpeg: 'Image',
        png: 'Image',
        mp4: 'Video',
        mp3: 'Audio'
    };

    /**
     * @param {String} selector
     * @constructor
     */
    var FilePreview = function(selector) {
        this.selector = selector;
        this.container = false;
        this.src = false;
        this.timer = null;
    };

    FilePreview.prototype = {
        /**
         * @param {String} filename
         */
        show: function(filename) {
            var ext = this.getExtension(filename),
                mime =  this.getMimeByExt(ext);

            this.container = $(this.selector);
            if (mime && this['show'+mime]) {
                var self = this;
                clearTimeout(this.timer);
                this.timer = setTimeout(function() {
                    var path = appCms.workingUrl + filename.replace(/\\/g,'/');

                    if (self.src !== path) {
                        self.stop();
                        self.src = path;
                        self['show'+mime]();
                    }
                }, 200);
            } else {
                this.container
                    .removeClass('feed')
                    .removeClass('image')
                    .removeClass('video')
                    .removeClass('audio')
                ;
            }
        },

        /**
         * Get extension
         *
         * @param {String} filename
         * @return {Null|String}
         */
        getExtension: function(filename) {
            var ext = null;
            if (/\./.test(filename)) {
                ext = filename.split('.').pop();
            }

            return ext;
        },

        /**
         *
         * @param {String} ext
         * @returns {}
         */
        getMimeByExt: function(ext) {
            if (_extToMines.hasOwnProperty(ext)) {
                return _extToMines[ext];
            }

            return false;
        },

        /**
         * @param {String} text
         */
        setMetadata: function(text) {
            var filename = this.src.replace(/^.*[\\\/]/, '');
            this.container.find('#metadata').html(filename+'<br/>'+text);
        },

        /**
         * Show image
         */
        showImage: function() {
            var self = this,
                img = new Image();

            img.src = this.src;
            img.onload = function() {
                self.container
                    .addClass('feed image')
                    .find('img').attr('src', img.src);
                self.setMetadata('Dimensions: '+img.naturalWidth+'x'+img.naturalHeight)
            };
        },

        /**
         * Show video
         */
        showVideo: function() {
            this.container
                .addClass('feed video')
                .find('video').attr('src', this.src);
            this.setMetadata('');
        },

        /**
         * Show audio
         */
        showAudio: function() {
            this.container
                .addClass('feed audio')
                .find('audio').attr('src', this.src);
            this.setMetadata('');
        },

        /**
         * Stop preview
         */
        stop: function() {
            if (this.container) {
                this.container
                    .removeClass('feed')
                    .removeClass('image')
                    .removeClass('video')
                    .removeClass('audio')
                ;

                this.container.find('video')[0].pause();
                this.container.find('audio')[0].pause();
            }
        }
    };

    return FilePreview;
}(jQuery));

