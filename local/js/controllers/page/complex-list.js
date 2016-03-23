(function($, APP) {
    'use strict';

    /**
     * Контроллер страницы списка жилых комплексов
     **/
    APP.Controls.Page.ComplexList = can.Control.extend({
        listensTo: ['list.contentUpdate']
    },{

        init: function() {
            new APP.Controls.FilterForm(this.element.find(".js-filter-form"));

            new APP.Controls.Pagination(this.element.find(".js-pagination"));
            new APP.Controls.Sorting(this.element.find(".js-sort"));
            new APP.Controls.ViewCounter(this.element.find(".js-view-counter"));
        },

        '.js-checkbox-link click': function(el) {
            el.toggleClass('active');
        },

        'list.contentUpdate': function (el, e, param) {
            var $ajaxContent = this.element.find('.js-ajax-list-content');
            var data = [];

            if (param.page > 0) {
                data.push({
                    name: "page",
                    value: param.page
                })
            } else {
                data.push({
                    name: "page",
                    value: 1
                })
            }

            if (param.viewCounter > 0) {
                data.push({
                    name: "viewCounter",
                    value: param.viewCounter
                })
            }

            this.element.find(".js-sort a").each(function(){
                data.push({
                    name: "sort[" + $(this).data("type") + "]",
                    value: $(this).data("method")
                })
            });

            $.merge(data, this.element.find('.js-filter-form').serializeArray());

            var self = this;

            $ajaxContent.ajaxl({
                url: location.pathname,
                data: data,
                dataType: 'HTML',
                type: 'POST',
                success: this.proxy(function (data) {
                    $ajaxContent.html(data);
                    if ($ajaxContent.find('.not-found').length > 0) {
                        self.element.find('.filter').css({'visibility': 'hidden'});
                    } else {
                        self.element.find('.filter').css({'visibility': 'visible'});
                    }
                    new APP.Controls.Pagination(this.element.find(".js-pagination"));
                    $.scrollTo(this.element.find('.catalog-block'), 500);
                })
            });
        }
    });

})(jQuery, window.APP);