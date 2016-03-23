(function($, APP) {
    'use strict';
    
    /**
     * Контроллер главной страницы
     **/
    APP.Controls.Page.Main = can.Control.extend({
    
        init: function() {
            new APP.Controls.MovingSlider(this.element.find('.js-title-slider'));
			APP.Controls.AddGoodsToBasket.initList(this.element.find(".js-add-basket"));
			APP.Controls.AddGoodsToFavorites.initList(this.element.find(".js-add-favourite"));
            this.initRoundSlider();

            this.element.find('.js-select').select2({
                minimumResultsForSearch: 500,
                width: 'element'
            });

            this.element.find('.js-slider').slider({
                range: true,
                min: 0,
                max: 50000,
                values: [8500, 32000]
            });

            this.$seoText = this.element.find('.js-seo-text');
            this.$seoLink = this.element.find('.js-detail');
            this.initSeoBlock();

            this.initChangePasswordPopup();
			
			$('.js-open-win').on('mouseover', function(){
				$('.js-open-win').each(function(i, elem){
					$('.pin', elem).removeClass('hide');
					$('.window', elem).removeClass('show');
					$('.pin', elem).addClass('show');
					$('.window', elem).addClass('hide');
				});
				$('.pin', this).addClass('hide');
				$('.window', this).addClass('show');
			});
			$('.js-open-win').on('mouseout', function(){
				$('.js-open-win').each(function(i, elem){
					$('.pin', elem).addClass('hide');
					$('.window', elem).addClass('show');
					$('.pin', elem).removeClass('show');
					$('.window', elem).removeClass('hide');
				});
				$('.pin', this).removeClass('hide');
				$('.window', this).removeClass('show');
			});
        },

        initRoundSlider: function() {
            var self = this;
            var complexId;
            this.$sliderWrapper = this.element.find('.js-slider-wrapper');

            this.element.find('.js-round-slider').roundabout({
                childSelector: '.slide',
                minOpacity: 0.2,
                minScale: 0.1,
                clickToFocusCallback: function() {
                    complexId = self.element.find('.roundabout-in-focus').data('complex-id');
                    self.loadComplexDesigns(complexId);
                }
            }, function () {
                complexId = self.element.find('.roundabout-in-focus').data('complex-id');
                self.loadComplexDesigns(complexId);
            });
        },

        loadComplexDesigns: function(complexId) {
            this.$sliderWrapper.addClass('ajax-loading');

            $.ajax({
                url: APP.urls.sliderRefresh,
                data: {'complexId': complexId},
                type: 'POST',
                dataType: 'HTML',
                success: this.proxy(function (data) {
                    this.$sliderWrapper.html(data);

                    this.element.find('.js-design-slider').bxSlider({
                        minSlides: 1,
                        maxSlides: 1,
                        moveSlides: 1,
                        slideMargin: 0,
                        pager: true,
                        controls: false
                    });

                    APP.Controls.MainTooltip.initList(this.element.find(".js-tooltip"), {pageObj: this.element});
                    new APP.Controls.Likes.initList(this.element.find('.js-like'));
                    APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));
                }),
                complete: this.proxy(function () {
                    this.$sliderWrapper.removeClass('ajax-loading');
                })
            });
        },

        initSeoBlock: function() {
            this.hideSeoBlock();
        },

        '.js-detail click': function() {
            if (this.$seoText.hasClass('opened')) {
                this.hideSeoBlock();
            } else {
                this.showSeoBlock();
            }
        },
		
		'.js-select-address click': function(el, e) {
			window.location.href = '/project/?street='+el.attr('data-street')+'&number='+el.attr('data-number');
		},
		
		'.js-search-form click': function(el, e){
			window.location.href = '/project/?address='+$('#address').val();;
		},
		
		'#address input': function(el, e) {
            var address = el.val();
			var $ajaxContent = this.element.find('.result');
			$ajaxContent.ajaxl({
				topPreloader: false,
				url:'/search-service/step-plan-name/search',
				data: {'address': address},
				dataType: 'HTML',
				type: 'POST',
				success: this.proxy(function (data) {
					$ajaxContent.html(data);
				})
			});
        },

        hideSeoBlock: function() {
            this.$seoLink.html('Подробнее...');
            var height = this.$seoText.find('p').outerHeight();

            if (this.$seoText.height() <= height || !height) {
                this.$seoLink.html('');
            }
            if (height) {
                this.$seoText.height(height);
            } else {
                this.$seoText.height('auto');
            }

            this.$seoText.removeClass('opened');
        },

        showSeoBlock: function() {
            this.$seoLink.html('Скрыть подробности');
            this.$seoText.height(this.$seoText.find('.js-text-inner').outerHeight());
            this.$seoText.addClass('opened');
        },

        showFancybox: function(id, callback) {
            $.fancybox.open([{
                href: id,
                padding: 0,
                helpers: {
                    media: {},
                    overlay: {
                        locked: false
                    }
                },
                tpl: {
                    closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"></a>'
                },
                afterShow: callback
            }]);
        },

        initChangePasswordPopup: function() {
            var url=window.location.search;
            if (url.indexOf('change_password=yes') != -1) {
                var getParams = APP.helpers.parseGetParams();

                this.showFancybox('#change-pass-popup', function() {
                    $('#login').val(getParams.login);
                    $('#checkword').val(getParams.checkword);

                    var fancyboxOuter = $(".fancybox-outer");
                    setTimeout(function () {
                        $('.fancybox-outer input[type!="hidden"],.fancybox-outer  textarea,.fancybox-outer  select').first().focus();
                    }, 200);
                });
            }
        },

});
})(jQuery, window.APP);