(function($, APP) {
    'use strict';

    /**
     * Контроллер страницы дизайнера
     **/
    APP.Controls.Page.Designer = can.Control.extend({

        init: function() {
            new APP.Controls.Pagination(this.element.find(".js-pagination"));
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

            $ajaxContent.ajaxl({
                url: location.pathname,
                data: data,
                dataType: 'HTML',
                type: 'POST',
                success: this.proxy(function (data) {
                    $ajaxContent.html(data);
                    new APP.Controls.Pagination(this.element.find(".js-pagination"));
                    $.scrollTo(this.element.find('.profile-content'), 500);
                })
            });
        }
    });

})(jQuery, window.APP);