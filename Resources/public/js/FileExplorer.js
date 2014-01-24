/**
 * File Explorer on an input text
 */
var appCms = appCms || {};
appCms.FileExplorer = (function($, ife) {
    'use strict';

    /**
     * @type {String}
     * @private
     */
    var _popupContent = '\
        <div id="file-explorer" class="modal fade modal-file-explorer"> \
        <div class="modal-dialog"> \
        <div class="modal-content"> \
        <div class="modal-header"> \
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> \
        <h3>Explorateur de fichier</h3></div> \
        <div id="modal-body" class="modal-body"></div> \
        </div>\
        </div>\
        </div>';

    /**
     * @type {*|HTMLElement}
     * @private
     */
    var _modal = $(_popupContent);

    /**
     * @param {String} selector
     * @constructor
     * @todo clean for cms
     */
    var FileExplorer = function() {
        var self = this;

        $(document.body).append(_modal);
        this.container = $('#modal-body');
        this.viewer = new appCms.FilePreview('#file-explorer-preview');

        _modal.modal({show: false})
            .on('hide', function() {
                self.removeListenerKeyboard();
                self.viewer.stop();
            }).on('shown', function() {
                self.addListenerKeyboard();
            });

        this.listenHover()
            .listenDirectoriesClick()
            .listenFilesClick()
            .listenInvalidClick();

    };

    FileExplorer.prototype = {
        /**
         * Action when exploring folder
         *
         * @param {String} path
         */
        explore: function(path, callback, type) {
            var self = this;

            if (callback) this.callback = callback;
            if (type) this.type = type;

            $.ajax({
                url: ife.explorer,
                data: {path: path, type: this.type},
                success: function(data) {
                    self.container.html(data);
                    _modal.modal('show');
                    self.selectFirst();
                }
            });
        },

        /**
         * Listen click on directories
         *
         * @return {FileExplorer}
         */
        listenDirectoriesClick: function() {
            var self = this;
            $(document).on('click', 'a.explore-directory', function(e) {
                var el = $(this);
                self.explore(el.attr('href'));
                e.preventDefault();
            });

            return this;
        },

        /**
         * select a path
         *
         * @param {String} path
         * @return {FileExplorer}
         */
        selectPath: function(path) {
            if (this.callback) {
                this.callback(path, this);
            }
            _modal.modal('hide');

            return this;
        },

        /**
         * Listen click on files
         *
         * @return {FileExplorer}
         */
        listenFilesClick: function() {
            var self = this;
            $(document).on('click', 'a.select-file', function(e) {
                self.selectPath($(this).attr('data-return'));

                e.preventDefault();
            });

            return this;
        },

        /**
         * Listen click on files
         *
         * @return {FileExplorer}
         */
        listenInvalidClick: function() {
            $(document).on('click', 'a.invalid', function(e) {
                e.preventDefault();
            });

            return this;
        },

        /**
         * Listen hover on link
         *
         * @return {FileExplorer}
         */
        listenHover: function() {
            var self = this;
            _modal.on('mouseover', 'ul.dropdown-menu.file-explorer li a', function() {
                var item = $(this);
                $('a.focus-browser').removeClass('focus-browser');
                item.addClass('focus-browser').focus();
                self.viewer.show(item.attr('href'));
            });

            return this;
        },

        /**
         * add keyboard listener
         *
         * @return {FileExplorer}
         */
        addListenerKeyboard: function() {
            var self = this;
            self.selectFirst();

            _modal.on('keydown.focusBrowser', function(e) {
                var focus = $('a.focus-browser');

                if (0 ===focus.length) {
                    self.selectFirst();
                }

                switch (e.keyCode) {
                    case 33: //page up
                        focus.removeClass('focus-browser');
                        var item = self.selectFirst();
                        self.viewer.show(item.attr('href'));
                        break;
                    case 34: //page down
                        focus.removeClass('focus-browser');
                        var item = $('ul.dropdown-menu.file-explorer li a:last').addClass('focus-browser').focus();
                        self.viewer.show(item.attr('href'));
                        break;
                    case 38: //up
                        var prev = focus.parent().prev();
                        if (prev.length > 0) {
                            focus.removeClass('focus-browser');
                            var item = prev.children().first().focus().addClass('focus-browser');
                            self.viewer.show(item.attr('href'));
                        }
                        break;
                    case 40: //down
                        var next = focus.parent().next();
                        if (next.length > 0) {
                            focus.removeClass('focus-browser');
                            var item = next.children().first().focus().addClass('focus-browser');
                            self.viewer.show(item.attr('href'));
                        }
                        break;
                    case 13:
                        if (focus.hasClass('explore-directory')) {
                            self.explore(focus.attr('href'));
                        } else if (focus.hasClass('select-file')) {
                            self.selectPath(focus.attr('href'))
                                .removeListenerKeyboard();
                        }
                        break;
                }

                e.preventDefault();
            });

            return this;
        },

        /**
         * Select first element of current folder
         *
         * @return {FileExplorer}
         */
        selectFirst: function() {
            var selected = $('#file-selected');
            if (selected.length) {
                selected.addClass('focus-browser').focus();
            } else {
                selected = $('ul.dropdown-menu.file-explorer li a:first').addClass('focus-browser').focus();
            }

            return selected;
        },

        /**
         * remove keyboard listener
         *
         * @return {FileExplorer}
         */
        removeListenerKeyboard: function() {
            _modal.off('keydown.focusBrowser');

            return this;
        }
    };

    return FileExplorer;
}(jQuery, window.appCms));

