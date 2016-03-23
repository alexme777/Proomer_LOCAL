(function ($, APP) {
    "use strict";

    /**
     * Контроллер заказов
     */
    APP.Controls.Order = can.Control.extend({
        pluginName: 'order'
    }, {


        init: function () {
            this.$fader = this.element.find('.order-fader');
            this.$panel = this.element.find('.js-order-panel');
            this.$counter = this.element.find('.js-counter');
            this.$title = this.element.find('.js-title');
            this.$total = this.element.find('.js-total');
            this.$empty = this.element.find('.js-empty');
            this.$full = this.element.find('.js-full');
            this.$titleWrapper = this.element.find('.js-title-wrapper');
            this.$bottom = this.element.find('.js-bottom');
            this.$scroller = this.element.find('.js-sidebar-scroller');
            this.$basketInner = this.element.find('.js-sidebar-basket-list');
            this.maxHeight = 0;
            this.scrollerInited = false;
        },

        '{window} resize': function() {
            this.maxHeight = $(window).height() - this.$titleWrapper.outerHeight() - this.$bottom.outerHeight();
            if (this.scrollerInited) {
                this.$scroller.height(this.maxHeight);
            } else {
                if (this.$basketInner.height() < this.maxHeight) {
                    this.$scroller.height(this.$basketInner.height());
                }
            }
        },

        '{window} openOrder': function ($el, ev) {
			console.log(this.$fader)
            this.$fader.toggle();
            this.$panel.toggle();
            this.maxHeight = $(window).height() - this.$titleWrapper.outerHeight() - this.$bottom.outerHeight();
            this.reinitScroll();

        },

        '.sidebar-fader click': function() {
            this.hidePanel();
        },

        '.basket-panel .close click': function() {
            this.hidePanel();
        },

        hidePanel: function() {
            this.$fader.hide();
            this.$panel.hide();
            this.$scroller.height(this.element.find('.js-sidebar-basket-list'));
        },

        '.side-cart-item .js-remove click': function($el, ev) {
            var $item = $el.closest('.side-cart-item');

            if ($item.hasClass('ajax-loading')) {
                return;
            }
            $item.addClass("ajax-loading");

            var elementId = $el.data('element-id');
            var productId = $el.data('product-id');

            $.ajax({
                url: APP.urls.basket.deleteDesign,
                data: {designId: elementId, sessid: APP.sessid},
                dataType: 'JSON',
                type: 'POST',
                success: this.proxy(function(data) {
                    if (!data.success) return;

                    if (data.basketCnt > 0) {

                        this.$counter.html(data.basketCnt);
                        this.$title.html('В корзине ' + data.basketCnt + ' проект' + APP.helpers.getWordForm(data.basketCnt, []));
                        this.$total.html(APP.helpers.price(data.totalPrice, true));

                        $item.animate({
                            'margin-left': '100%',
                            'height': 0
                        }, 300, function() {
                            $item.remove();
                            if ($('.side-cart-item').length == 0) {
                                this.$panel.toggle();
                            }
                            this.element.trigger("basketCountChanged", data.basketCnt);
                            this.element.trigger("removeItemFromBasket", productId);
                            this.reinitScroll();
                        }.bind(this));
                    } else { //пустая корзина
                        this.$counter.hide();
                        this.$full.hide();
                        this.$empty.show();
                        this.$title.html('В корзине пусто');
                    }

                    this.element.trigger("basketCountChanged", data.basketCnt);
                })
            });
        },

        '{window} addElement': function($el, ev, data) {
            var $basketItem = $(can.view.render('sidebarBasketItem', {item: data.item}));
            $basketItem.hide();
            this.element.find(".js-sidebar-basket-list").append($basketItem);
            $basketItem.slideDown();

            this.$counter.html(data.cnt);
            this.$title.html('В корзине ' + data.cnt + ' проект' + APP.helpers.getWordForm(data.cnt, []));
            this.$total.html(APP.helpers.price(data.price, true));
            if (data.cnt == 1) { //корзина была пустой
                this.$full.show();
                this.$empty.hide();
                this.$counter.show();
            }
        },

        reinitScroll: function() {
            var height = this.$basketInner.height();
            if (this.maxHeight <= height) {
                this.$scroller.height(this.maxHeight);
                this.initScroller();
            } else {
                if (this.scrollerInited) {
                    this.destroyScroller();
                    this.$scroller = this.element.find('.js-sidebar-scroller');
                    this.$scroller.height('auto');
                }
            }
        },

        initScroller: function () {
            this.$scroller.jScrollPane();
            this.scrollerInited = true;
        },

        destroyScroller: function () {
            this.$scroller.jScrollPane().data('jsp').destroy();
            this.scrollerInited = false;
        }

    });
})(jQuery, APP);
