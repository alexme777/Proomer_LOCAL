(function ($, APP) {
    'use strict';

    APP.Controls.FilterForm = APP.Controls.Form.extend(
        {
            pluginName: 'filterForm'
        },
        {
            init: function () {
                this._super();
                this.initSelect2();
                this.element.find('.js-min-value, .js-max-value').numericOnly();
                APP.Controls.RangeSlider.initList(this.element.find(".js-range-slider"))
            },

            '.js-clear-filter click': function () {
                this.element.trigger("reset");
                this.element.find("select").each(function () {
                    $(this).find("option").first().prop("selected", true);
                    $(this).trigger("change");
                });

                this.element.find(".js-range-slider").trigger("rangeSlider.reset");
                this.element.find(".js-select-multiple").trigger("multiSelect.reset");
                this.element.submit();
            },

            'submit': function (el, e) {
                e.preventDefault();

                var filterParams = this.element.serializeArray();

                var validFilterParams = $.map(filterParams, function (el) {
                    return el.value.length > 0 ? el : null;
                });

                var getStr = $.map(validFilterParams, function (el) {
                    return el.name + "=" + el.value;
                }).join("&");

                var newUrl = location.origin + location.pathname + "?" + getStr;
                history.pushState({}, '', newUrl);
                
                this.element.trigger("list.contentUpdate", {});
            },
			
			'change': function(el, e){
				e.preventDefault();

                var filterParams = this.element.serializeArray();

                var validFilterParams = $.map(filterParams, function (el) {
                    return el.value.length > 0 ? el : null;
                });

                var getStr = $.map(validFilterParams, function (el) {
                    return el.name + "=" + el.value;
                }).join("&");

                var newUrl = location.origin + location.pathname + "?" + getStr;
                history.pushState({}, '', newUrl);
                
                this.element.trigger("list.contentUpdate", {});
			},

            initSelect2: function () {
                this.element.find('.js-select').select2({
                    minimumResultsForSearch: 500,
                    width: 'element',
                    dropdownAutoWidth: true
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
                    });
                }, 100);
            },

            'select2:close': function() {
                var api = this.scrollPane.data('jsp');
                api.destroy();
            }
        }
    );

})(jQuery, window.APP);