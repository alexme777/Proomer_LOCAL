(function($, APP) {
    'use strict';
    
    /**
     * Контроллер детальной страницы дизайна
     **/
    APP.Controls.Page.ProviderDetail = can.Control.extend({
    
        init: function() {
            new APP.Controls.Likes.initList(this.element.find('.js-like'));
            APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));

            this.element.find('.js-gallery-slider').bxSlider({
                mode: 'fade',
                minSlides: 1,
                moveSlides: 1,
                slideMargin: 0,
                auto: false,
                pager: true,
                controls: false
            });

            this.element.find('.js-slider').bxSlider({
                minSlides: 1,
                maxSlides: 1,
                moveSlides: 1,
                slideMargin: 0,
                pager: true,
                controls: false
            });

            this.element.find('.js-fancybox-thumb').fancybox({
                prevEffect: 'none',
                nextEffect: 'none',
                padding: 0,
                margin: 50,
                wrapCSS: 'fancy-image',
                helpers: {
                    title: {
                        type: 'outside',
                        position : 'top'
                    },
                    thumbs: {
                        width: 50,
                        height: 50
                    },
                    overlay: {
                        locked: false
                    }
            },
                tpl: {
                    closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close white" href="javascript:;"></a>',
                    prev: '<a title="" class="fancybox-nav fancybox-prev" href="javascript:;"></a>',
                    next: '<a title="" class="fancybox-nav fancybox-next" href="javascript:;"></a>'
                }
            });
        },

        '.js-scroll-to click': function(el) {
            $.scrollTo($(el.attr('href')), 500);
        }

    });

})(jQuery, window.APP);