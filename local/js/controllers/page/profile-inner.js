(function($, APP) {
    'use strict';

    /**
     * внутренняя страница профиля
     **/
    APP.Controls.Page.ProfileInner = can.Control.extend({

        init: function() {
			
			this.$fader = this.element.find('.sidebar-fader');
            this.$panel = this.element.find('.js-basket-panel');
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

			
            APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));

            this.element.find('.js-tooltip').each(function(){
                var $this = $(this);
                $this.css({'left': -($this.outerWidth()/2 - 10)});
            });

            this.element.find('.js-profile-form').each(function() {
                new APP.Controls.ProfileForm(this);
            });

            new APP.Controls.ProfileSidebar(this.element.find('.js-sidebar'));

            if (this.element.find('.js-profile-content').data('type') == 0) {
                $.fancybox.open([{
                    href: '#type-popup',
                    padding: 0,
                    helpers: {
                        media: {},
                        overlay: {
                            locked: false
                        }
                    },
                    tpl: {
                        closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"></a>'
                    }
                }]);
            }
        },
		
		'.js-delete-design click': function(el,ev) {
			APP.helpers.showFancyboxDelete('Вы уверены, что хотите удалить этот проект?', this.removeDesign, el, this);
		},
		
		removeDesign: function(el, self) {
			var $item = el.closest('.design-item');

			if ($item.hasClass('ajax-loading')) {
				return;
			}
			$item.addClass("ajax-loading");
			var elementId = el.data('element-id');
			var productId = el.data('product-id');
			
            $.ajax({
                url: APP.urls.basket.deleteDesign,
                data: {designId: elementId, sessid: APP.sessid},
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                        if (!data.success) return;

						if (data.basketCnt >= 0) {

							//this.$counter.html(data.basketCnt);
							//this.$title.html('В корзине ' + data.basketCnt + ' проект' + APP.helpers.getWordForm(data.basketCnt, []));
							//this.$total.html(APP.helpers.price(data.totalPrice, true));

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
							//this.$counter.hide();
							//this.$full.hide();
							//this.$empty.show();
							//this.$title.html('В корзине пусто');
						}

                    this.element.trigger("basketCountChanged", data.basketCnt);
                },
                complete: function (data) {
                }
            });
        },

    });

})(jQuery, window.APP);