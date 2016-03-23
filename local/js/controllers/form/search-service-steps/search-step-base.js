(function ($, APP) {
    'use strict';

    APP.Controls.SearchStepBaseForm = can.Control.extend(
        {
            pluginName: 'searchStepBaseForm'
        },
        {
            init: function () {
                this._super();
                this.initSelect2();
                this.initScroller();
            },

            '.js-show-project click': function (el, e) {
                this.element.submit();
            },

            initScroller: function () {
                this.element.find('.js-scroller').each(function () {
                    $(this).jScrollPane({
                        autoReinitialise: true
                    }).hover(
                        function (ev) {
                            var $target = $(ev.target);
                            if ($(this).find(".jspVerticalBar").length > 0 && ($target.hasClass("js-scroller") || $target.closest(".js-scroller").length > 0)) {
                                $.disabledDocumentScroll();
                            }
                        },
                        function () {
                            $.disabledDocumentScroll(false);
                        }
                    );
                });


            },

            checkPin: function ($select) {
                var selected = $select.val();
                this.options.parent.find(".js-pin").removeClass("active");
                this.options.parent.find('#pin'+$select.val()).addClass("active");
                this.options.parent.siblings("#flatId").val(selected);
            },

            initSelect2: function () {
                this.element.find('.js-select').select2({
                    minimumResultsForSearch: 500,
                    width: 260
                });
            },

            'select2:open': function() {
                var self = this;
                var interval = setInterval(function() {
                    if ($('.jspScrollable').length > 0) {
                        clearInterval(interval);
                        return;
                    }
                    self.scrollPane = $('.select2-results').jScrollPane({
                        autoReinitialise: true
                    }).hover(
                        function () {
                            $.disabledDocumentScroll();
                        },
                        function () {
                            $.disabledDocumentScroll(false);
                        }
                    );
                }, 100);
            },

            'select2:select': function() {
                $.disabledDocumentScroll(false);
            },

            'select2:close': function() {
                $.disabledDocumentScroll(false);
                var api = this.scrollPane.data('jsp');
                api.destroy();
            }
        }
    );

})(jQuery, window.APP);