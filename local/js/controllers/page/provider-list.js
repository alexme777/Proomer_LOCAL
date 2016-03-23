(function($, APP) {
    'use strict';
    
    /**
     * Контроллер каталога дизайнов
     **/
    APP.Controls.Page.ProviderList = can.Control.extend({

        init: function() {
            APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));
            new APP.Controls.MovingSlider(this.element.find('.js-title-slider'));
            new APP.Controls.FilterForm(this.element.find(".js-filter-form"));

            new APP.Controls.Pagination(this.element.find(".js-pagination"));
            new APP.Controls.Sorting(this.element.find(".js-sort"));
            new APP.Controls.ViewCounter(this.element.find(".js-view-counter"));

            new APP.Controls.Likes.initList(this.element.find('.js-like'));
            APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));
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
                topPreloader: true,
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
                    new APP.Controls.Likes.initList(this.element.find('.js-like'));
                    APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));
                })
            });
        }
    });

})(jQuery, window.APP);