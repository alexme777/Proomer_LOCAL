(function($, APP) {
    'use strict';

    /**
     * Корзина и оформление заказа
     **/
    APP.Controls.Page.Basket = can.Control.extend({
        defaults: {

        }
    }, {

        init: function() {
            new APP.Controls.OrderForm(this.element.find(".js-order-form"));
        },

        '.js-remove click': function($el, e) {
            e.preventDefault();

            var $item = $el.closest('.js-basket-item');
            if ($item.hasClass('ajax-loading')) {
                return;
            }
            $item.addClass("ajax-loading");

            var elementId = $el.data('element-id');

            $.ajax({
                url: APP.urls.basket.deleteDesign,
                data: {designId: elementId, sessid: APP.sessid},
                dataType: 'JSON',
                type: 'POST',
                success: this.proxy(function(data) {
                    if (!data.success) return;

                    if (data.basketCnt > 0) {
                        this.element.find('.js-total').each(function () {
                            $(this).html(
                                '<small>' + data.basketCnt + ' ' + 'проект' + APP.helpers.getWordForm(data.basketCnt, []) +
                                ',</small> ' + APP.helpers.price(data.totalPrice, true)
                            );
                        });

                        $item.animate({
                            'margin-left': '-100%',
                            'margin-bottom': '0',
                            'opacity': 0,
                            'height': 0
                        }, 300, function() {
                            $item.remove();
                        }.bind(this));
                    } else {
                        var self= this;
                        this.element.find('.js-basket-full').slideUp("slow", function() {
                            self.element.find('.js-basket-empty').slideDown();
                        });

                    }

                    this.element.trigger("basketCountChanged", data.basketCnt);
                })
            });
        },

        '.js-submit-btn click': function (el, e) {
            el.closest('form').submit();
        }
    });

})(jQuery, window.APP);