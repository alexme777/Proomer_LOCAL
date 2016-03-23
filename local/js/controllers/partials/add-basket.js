(function ($, APP) {
    "use strict";

    /**
     * Контроллер добавления товара в корзину
     * @type {void|*}
     */
    APP.Controls.AddBasket = APP.BaseController.extend({

        init: function () {
            this.elementId = this.element.data("element-id");
            this.addProcess = false;
        },

        'click': function(el, e){
            e.preventDefault();
            if (this.element.hasClass('disabled') || this.element.hasClass('in-basket') || this.element.hasClass('basket-adding')) return false;
            this.addToBasket(this.elementId);
        },

        /**
         * @param itemId
         */
        addToBasket: function(elementId) {
            if (this.addProcess) return;
            this.addProcess = true;
            this.element.addClass("basket-adding");

            $.ajax({
                url: APP.urls.basket.addDesign,
                data: {designId: elementId, delay:'N', sessid: APP.sessid},
                dataType: 'JSON',
                type: 'POST',
                success: this.proxy(function(data) {
                    if (!data.success) return;

                    this.element.removeClass('to-basket').addClass('in-basket');

                    if (this.element.hasClass('btn')) {
                        this.element.html('Проект в корзине');
                        this.element.attr({'href': APP.urls.basket.index});
                    }

                    this.element.trigger("basketCountChanged", data.basketCnt);
                    this.element.trigger("addElement", {cnt: data.basketCnt, price: data.totalPrice, item: data.item});
                }),
                complete: this.proxy(function () {
                    this.addProcess = false;
                    this.element.removeClass("basket-adding");
                })
            });
        },

        '{window} removeItemFromBasket': function ($el, ev, id) {
            if (this.elementId == id && this.element.hasClass('in-basket')) {
                this.element.removeClass('in-basket').addClass('to-basket');
            }
        }
    });
	
	
	APP.Controls.AddProjectToBasket = APP.BaseController.extend({

        init: function () {
            this.elementId = this.element.data("element-id");
            this.addProcess = false;
        },

        'click': function(el, e){
			if(!this.element.hasClass('in-basket') && this.element.data("offer") == 'Y'){
				var section = this.element.data("section-id");
				var item = this.element.data("element-id");
				location.href = '/shop/' + section + '/' + item;
				return;
			}
            e.preventDefault();
            if (this.element.hasClass('disabled') || this.element.hasClass('in-basket') || this.element.hasClass('basket-adding')) return false;
            this.addToBasket(this.elementId);
        },

        /**
         * @param itemId
         */
        addToBasket: function(elementId) {
            if (this.addProcess) return;
            this.addProcess = true;
            this.element.addClass("basket-adding");

            $.ajax({
                url: APP.urls.basket.addProject,
                data: {designId: elementId, delay:'N', sessid: APP.sessid},
                dataType: 'JSON',
                type: 'POST',
                success: this.proxy(function(data) {
                    if (!data.success) return;

                    this.element.removeClass('to-basket').addClass('in-basket');

                    if (this.element.hasClass('btn')) {
                        this.element.html('Проект в корзине');
                        this.element.attr({'href': APP.urls.basket.index});
                    }

                    this.element.trigger("basketCountChanged", data.basketCnt);
                    this.element.trigger("addElement", {cnt: data.basketCnt, price: data.totalPrice, item: data.item});
                }),
                complete: this.proxy(function () {
                    this.addProcess = false;
                    this.element.removeClass("basket-adding");
                })
            });
        },

        '{window} removeItemFromBasket': function ($el, ev, id) {
            if (this.elementId == id && this.element.hasClass('in-basket')) {
                this.element.removeClass('in-basket').addClass('to-basket');
            }
        }
    });
	
	
	APP.Controls.AddGoodsToBasket = APP.BaseController.extend({

        init: function () {
            this.elementId = this.element.data("element-id");
            this.addProcess = false;
        },

        'click': function(el, e){
			if(!this.element.hasClass('in-basket') && this.element.data("offer") == 'Y'){
				var section = this.element.data("section-id");
				var item = this.element.data("element-id");
				location.href = '/shop/' + section + '/' + item;
				return;
			}
            e.preventDefault();
            if (this.element.hasClass('disabled') || this.element.hasClass('in-basket') || this.element.hasClass('basket-adding')) return false;
            this.addToBasket(this.elementId);
        },

        /**
         * @param itemId
         */
        addToBasket: function(elementId) {
            if (this.addProcess) return;
            this.addProcess = true;
            this.element.addClass("basket-adding");

            $.ajax({
                url: APP.urls.basket.addGoods,
                data: {designId: elementId, delay:'N', sessid: APP.sessid},
                dataType: 'JSON',
                type: 'POST',
                success: this.proxy(function(data) {
                    if (!data.success) return;

                    this.element.removeClass('to-basket').addClass('in-basket');

                    if (this.element.hasClass('btn')) {
                        this.element.html('Проект в корзине');
                        this.element.attr({'href': APP.urls.basket.index});
                    }

                    this.element.trigger("basketCountChanged", data.basketCnt);
                    this.element.trigger("addElement", {cnt: data.basketCnt, price: data.totalPrice, item: data.item});
                }),
                complete: this.proxy(function () {
                    this.addProcess = false;
                    this.element.removeClass("basket-adding");
                })
            });
        },

        '{window} removeItemFromBasket': function ($el, ev, id) {
            if (this.elementId == id && this.element.hasClass('in-basket')) {
                this.element.removeClass('in-basket').addClass('to-basket');
            }
        }
    });

})(jQuery, APP);