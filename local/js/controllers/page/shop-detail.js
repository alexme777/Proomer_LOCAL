/*(function($, APP) {
    'use strict';

    /**
     * внутренняя страница профиля - мои проекты
     **/
   /* APP.Controls.Page.ShopDetail = can.Control.extend({

        init: function() {
            APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));

            if ($('html').hasClass('ie9')) {
                this.element.find('.sidebar').height(this.element.find('.sidebar').parent().height());
            }

            this.element.find('.js-tooltip').each(function(){
                var $this = $(this);
              //  $this.css({'left': -($this.outerWidth()/2 - 10)});
            });

            new APP.Controls.ProfileSidebar(this.element.find('.js-sidebar'));

            new APP.Controls.FilterForm(this.element.find(".js-filter-form"));
            new APP.Controls.Pagination(this.element.find(".js-pagination"));
            //new APP.Controls.SortingItem(this.element.find(".js-sort"));
            //new APP.Controls.ViewCounterItem(this.element.find(".js-view-counter"));
			new APP.Controls.Sorting(this.element.find(".js-sort"));
            new APP.Controls.ViewCounter(this.element.find(".js-view-counter"));
			APP.Controls.AddGoodsToBasket.initList(this.element.find(".js-add-basket"));
            this.filterForm =  this.element.find('.js-profile-filter-form');
            new APP.Controls.Likes.initList(this.element.find('.js-like'));
            this.formHeight = this.filterForm.outerHeight();
        },

        '.show_yet click': function () {
            var $ajaxContent = this.element.find('.list-goods');
            var data = [];
            //кол-во
            var count = this.element.find('.js-view-counter > .active').attr('data-value');
            var categoryId = this.element.find('#categoryId').attr('data-value');
            var x = 1;
            if(count == 20){x = 1;}
            else if(count == 40){x = 2}
            else if(count == 60){x = 3};
            //отступ
            var offset = this.element.find('.js-view-offset').attr('data-value');
            var list_goods = this.element.find('.list-goods').html();
            $ajaxContent.ajaxl({
                topPreloader: true,
                url: '/shop/category',
                data: 'ajax=1&categoryid='+categoryId+'&getitem=1&count='+count+'&offset='+offset,
                dataType: 'HTML',
                type: 'POST',
                success: this.proxy(function (data) {
                    var attr = Number(this.element.find('.js-view-offset').attr('data-value'));
                    var new_attr = attr + x;
                    this.element.find('.js-view-offset').attr('data-value', new_attr);
                    //this.element.find('.js-view-offset').attr('data-value', attr);
                    $ajaxContent.html(list_goods+data);
                  /*  $ajaxContent.html(data);
                    if ($ajaxContent.find('.not-found').length > 0) {
                        self.element.find('.filter').css({'visibility': 'hidden'});
                    } else {
                        self.element.find('.filter').css({'visibility': 'visible'});
                    }
                    new APP.Controls.Pagination(this.element.find(".js-pagination"));
                    $.scrollTo(this.element.find('.profile-content'), 500);
                    new APP.Controls.Likes.initList(this.element.find('.js-like'));*/
          /*      })
            });
        },

        '.filt_item click': function () {
            var $ajaxContent = this.element.find('.list-goods');
            var filter_item = document.getElementById('filter-item');
            var priceMin = filter_item.elements.priceMin.value;
            var priceMax = filter_item.elements.priceMax.value;
            var primaryColor = filter_item.elements.primaryColor.selectedOptions;
            var madeIn = filter_item.elements.madeIn.selectedOptions;
            var style = filter_item.elements.styles.selectedOptions;
            var categoryid = filter_item.elements.categoryid.value;
            var selectColor = [];
            var selectMadeIn = [];
            var selectStyle = [];
          
            for(var i = 0; i < primaryColor.length; i++){
                selectColor.push(primaryColor[i].value);
            };
            for(var i = 0; i < madeIn.length; i++){
                selectMadeIn.push(madeIn[i].value);
            };
            for(var i = 0; i < style.length; i++){
                selectStyle.push(style[i].value);
            };
            
            var arr_val = {};
            arr_val.categoryId = categoryid;
            arr_val.priceMin = priceMin;
            arr_val.priceMax = priceMax;
            arr_val.selectColor = selectColor;
            arr_val.selectMadeIn = selectMadeIn;
            arr_val.selectStyle = selectStyle;
            
            var data = [];
            //кол-во
            $ajaxContent.ajaxl({
                topPreloader: true,
                url: '/shop/category',
                data: 'ajax=1&filteritem=1&val='+JSON.stringify(arr_val),
                dataType: 'HTML',
                type: 'POST',
                success: this.proxy(function (data) {
                    $ajaxContent.html(
                        data
                    );
                        


                })
            });
        },
		
		'.search_item click': function () {
			var $ajaxContent = this.element.find('.list-goods');
			var search_item = document.getElementById('search-item');
			var q = search_item.elements.search.value;
			$.ajax({
                topPreloader: true,
                url: '/shop/category',
                data: 'ajax=1&searchitem=1&val='+q,
                type: 'POST',
                success: this.proxy(function (res) {
                
                })
            });
        },

        '.js-delete-design click': function(el,ev) {
            APP.helpers.showFancyboxDelete('Вы уверены, что хотите удалить этот проект?', this.removeDesign, el, this);
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



        removeDesign: function(el, self) {
            $.ajax({
                url: APP.urls.designDelete + el.data('id') + '/',
                data: {
                    sessid: APP.sessid
                },
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    self.element.trigger('list.contentUpdate', {});
                },
                complete: function (data) {
                }
            });
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
                    $.scrollTo(this.element.find('.profile-content'), 500);
                    new APP.Controls.Likes.initList(this.element.find('.js-like'));
                })
            });
        },

        '.js-switcher-filter click': function(el) {
            el.toggleClass('active');
            if (el.hasClass('active')) {
                el.html('Свернуть фильтр');
                this.filterForm.animate({"height": this.formHeight});
                this.filterForm.find('.js-form-wrapper').fadeIn();

                var self = this;
                setTimeout(function () {
                    self.filterForm.css({"height": "auto"});
                }, 1000);
            } else {
                this.formHeight = this.filterForm.outerHeight();
                el.html('Развернуть фильтр');
                this.filterForm.animate({"height": '70'});
                this.filterForm.find('.js-form-wrapper').fadeOut();
            }
        }

    });

})(jQuery, window.APP);

















/*(function($, APP) {
    'use strict';
    /**
     * Контроллер детальной страницы жилого коплекса
     **/
	
   /* APP.Controls.Page.ShopDetail = can.Control.extend({

        init: function() {
            this.element.find('.js-select').select2({
                minimumResultsForSearch: 500,
                width: 'style'
            });

            this.houseId = [];

            if (this.element.find('.js-item').length > 0) {

                this.initMap();

                var self = this;
                var $slider = this.element.find('.js-building-slider');
                this.slider = $slider.bxSlider({
                    minSlides: 1,
                    maxSlides: 1,
                    moveSlides: 1,
                    slideMargin: 30,
                    pager: true,
                    controls: false,
                    onSliderLoad: function () {
                        $slider.parent().css({'width': '512'});
                    },
                    onSlideBefore: function () {
                        $slider.parent().css({'width': '482'});
                    },
                    onSlideAfter: function ($slideElement) {
                        $slider.parent().css({'width': '512'});
                        $slider.parent().css({'height': $slideElement.height()});
                    }
                });
            }

            this.element.find('.js-slider').bxSlider({
                minSlides: 1,
                maxSlides: 1,
                moveSlides: 1,
                slideMargin: 0,
                pager: true,
                controls: false
            });
            new APP.Controls.Likes.initList(this.element.find('.js-like'));
            APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));
			APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));
        },

        '.js-item click': function(el) {
            if (!el.hasClass('active')) {
                this.activateHouse(el);
            }
        },

        activateHouse: function(el) {
            var houseId = el.data('house-id');

            if (!this.houseId[houseId]) {
                el.find('.js-features li:not(:first-child)').remove();
                el.addClass('ajax-loading');

                $.ajax({
                    url: APP.urls.houseFlatCounter,
                    data: {'houseId': houseId},
                    type: 'POST',
                    dataType: 'HTML',
                    success: this.proxy(function (data) {
                        this.houseId[houseId] = data;
                        el.find('.js-features').append(data);
                        el.closest('.bx-viewport').css({'height': el.parent().height()});
                    }),
                    complete: this.proxy(function (data) {
                        el.removeClass('ajax-loading');
                    })
                });
            }

            this.element.find('.js-item.active').removeClass('active');

            if (this.element.find('.js-building-slider').length > 0) {
                var index;
                if (el.closest('.slide')) //если не на активном слайде
                {
                    index = el.closest('.slide').data('index');
                }
                this.slider.goToSlide(index);
            }
            el.addClass('active');
            el.closest('.bx-viewport').height(el.parent().height());

            this.moveMap();
        },

        initMap: function() {
            var centerCoords = this.element.find('.js-item.active').data('map-position').split(',');

            var mapOptions = {
                center: new google.maps.LatLng(centerCoords[0],centerCoords[1]),
                zoom: 17,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.LEFT_BOTTOM
                },
                disableDoubleClickZoom: true,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                },
                scaleControl: true,
                scrollwheel: false,
                panControl: true,
                streetViewControl: true,
                draggable : true,
                overviewMapControl: true,
                overviewMapControlOptions: {
                    opened: false
                },
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var mapElement = this.element.find('.js-map');
            this.map = new google.maps.Map(mapElement.get(0), mapOptions);

            this.initMarkers();
        },

        moveMap: function() {
            var centerCoords = this.element.find('.js-item.active').data('map-position').split(',');

            var pos = new google.maps.LatLng(centerCoords[0],centerCoords[1]);
            this.map.panTo(pos);

            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
            this.markers = [];
            this.activeMarker.setMap(null);

            this.initMarkers();
        },

        initMarkers: function() {
            var coords = [];
            var ids = [];
            this.element.find('.js-item:not(.active)').each(function(el) {
                coords.push($(this).data('map-position').split(','));
                ids.push($(this).data('house-id'));
            });

            var activeCoords = this.element.find('.js-item.active').data('map-position').split(',');
            var centerCoords = this.element.find('.js-item.active').data('map-position').split(',');

            this.markers = [];

            var locations = coords;
            for (var i = 0; i < locations.length; i++) {
                var marker = new google.maps.Marker({
                    icon: {
                        url: '/local/images/sprite.png',
                        size: new google.maps.Size(54, 39),
                        origin: new google.maps.Point(230, 240),
                        anchor: new google.maps.Point(14, 39)
                    },
                    position: new google.maps.LatLng(locations[i][0], locations[i][1]),
                    map: this.map
                });

                this.markers.push(marker);
                var self = this;

                (function (map, marker, id) {
                    google.maps.event.addListener(marker, 'click', function () {
                        //активировать дом с id
                        self.activateHouse(self.element.find('.js-item[data-house-id=' + id + ']'));
                        return;
                    });
                }(this.map, marker, ids[i]));
            }

            var activeLocation = activeCoords;
            this.activeMarker = new google.maps.Marker({
                icon: {
                    url: '/local/images/sprite.png',
                    size: new google.maps.Size(80, 79),
                    origin: new google.maps.Point(560, 290),
                    anchor: new google.maps.Point(25, 71)
                },
                position: new google.maps.LatLng(activeLocation[0], activeLocation[1]),
                map: this.map
            });
        }

    });

})(jQuery, window.APP);*/


(function($, APP) {
    'use strict';
    
    /**
     * Контроллер каталога дизайнов
     **/
    APP.Controls.Page.ShopDetail = can.Control.extend({

        init: function() {
            APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));
            // new APP.Controls.MovingSlider(this.element.find('.js-title-slider'));
            new APP.Controls.FilterForm(this.element.find(".js-filter-form"));
            new APP.Controls.Pagination(this.element.find(".js-pagination"));
            new APP.Controls.Sorting(this.element.find(".js-sort"));
            new APP.Controls.ViewCounter(this.element.find(".js-view-counter"));
			new APP.Controls.OrderForm(this.element.find(".js-form-order"));
           //new APP.Controls.Likes.initList(this.element.find('.js-like'));
           // APP.Controls.AddBasket.initList(this.element.find(".js-add-basket"));
			APP.Controls.AddGoodsToBasket.initList(this.element.find(".js-add-basket"));
			APP.Controls.AddGoodsToFavorites.initList(this.element.find(".js-add-favourite"));
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
                url: '/shop/shopdetail/',
                data: data,
                dataType: 'HTML',
                type: 'POST',
                success: this.proxy(function (data) {
					/*var res = JSON.parse(data);
					console.log(res)
					var html = '';
					var i = 0;
					for(i; i < res.items.length; i++){
						if(!res.items[i].PREVIEW_PICTURE) res.items[i].PREVIEW_PICTURE = '/local/images/proomer.png';
						html+='<div class="item goods-item">';
						html+='	<a href="javascript:void(0)" class="favourite js-tooltip mainTooltip" data-description="Добавить в избранное"></a>';
						html+='	<div class="image" style="background:url('+res.items[i].PREVIEW_PICTURE+') no-repeat 50%;background-size:100%;">';
						html+='	<!--$this->Like()->button($item->ID, $item->PROPERTY_LIKE_CNT_VALUE, $item->IS_LIKED)	-->	';
						html+='	</div>';
						html+='	<div class="content">';
						html+='		<a href="javascript:void(0)" class="to-basket basket js-add-basket" data-element-id="'+res.items[i].ID+'"></a>';
						html+='		<a href="/shop/'+res.items[i].IBLOCK_SECTION_ID+'/'+res.items[i].ID+'" class="title">'+res.items[i].NAME+'</a>';
						html+='<div class="description">';
						html+='	Цена';
						html+='	<span class="price">'+res.items[i].PROPERTY_PRICE_VALUE+'<span class="ruble">⃏</span></span>';
						html+='</div></div></div>';
					}
					*/
					$ajaxContent.html(data);
                    if ($ajaxContent.find('.not-found').length > 0) {
                        self.element.find('.filter').css({'visibility': 'hidden'});
                    } else {
                        self.element.find('.filter').css({'visibility': 'visible'});
                    }
				
                    new APP.Controls.Pagination(this.element.find(".js-pagination"));
                    //$.scrollTo(this.element.find('.catalog-block'), 500);
                   // new APP.Controls.Likes.initList(this.element.find('.js-like'));
                    APP.Controls.AddGoodsToBasket.initList(this.element.find(".js-add-basket"));
                })
            });
        },
		'.js-buy click': function() {
            this.element.trigger("openOrder");
        },
		/*'.js-step-1 mouseup': function(e) {
		// получить объект событие.
			// вместо event лучше писать window.event
			event = event || window.event
			// кросс-браузерно получить target
			var target = event.target || event.srcElement
			var json_trade_offer = JSON.parse(this.element.find('#json_trade_offer').html());

				if($('.js-step-2').children().hasClass("disabled")){
					$('.js-step-2').children().removeClass('disabled');
				}
				else{
				  // $('.js-step-2').children().addClass('disabled');
				};
				// цикл двигается вверх от target к родителям до form
				while (target.tagName != 'FORM') {
					if (target.tagName == 'LABEL') {
						var val = target.getAttribute('value');
						var name = target.getAttribute('data-title');
						$('#colorId').val(val);
						$('#colorName').val(name);
						$('.js-step-2 .tag label').each(function(i, elem){
								elem.parentNode.classList.add('N');
						});
						//пройдемся по св-вам объкта для того чтобы понять какие предложения есть у данного товара
						for(var key in json_trade_offer){
							//нужно выбрать значения св-в объекта у которых совпадает св-во цвета 
							if(val == key){
								//рассчитаем цену
								var standart_price = 10000;
								var bases_price = Number($('.price > span.price').html());
								var price_property = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;
								var diff = price_property - bases_price;
								if(diff){
									var new_price = bases_price + diff;
									var discount = (new_price/100) * $('#discount').html();
									$('.price .price').html(new_price);
									$('.price .new_price').html(new_price - discount);
								};
								var size = json_trade_offer[key].PROPERTY_WIDTH_VALUE +
								'x' + json_trade_offer[key].PROPERTY_HEIGHT_VALUE +
								'x' + json_trade_offer[key].PROPERTY_LENGTH_VALUE
								$('.js-step-2 .tag input, .js-step-3 .tag input').removeAttr("checked"); 
								$('.js-step-2 .tag label').each(function(i, elem){
									//if(elem.getAttribute('value') == size){
										var i = 0;
										for(i; i < json_trade_offer[key].PROPERTY_LINK_VALUE.length; i++){
											var id = elem.parentNode.getAttribute('data-element-id');
											if(json_trade_offer[key].PROPERTY_LINK_VALUE[i] == id){
												elem.parentNode.classList.remove('N');
											}
										};
								//	}
									//else{
										//elem.parentNode.classList.add('N');
									//}
								});
								$('.js-step-3 .input-cell').each(function(i, elem){
										elem.parentNode.classList.add('disabled');
								});
								$('.js-step-3 .tag label').each(function(i, elem){
										elem.parentNode.classList.add('N');
								});
							}
						}
						
						return;
					}
					else{
						
					}
					target = target.parentNode;
				}
			
		},
		
		'.js-step-2 mouseup': function(e) {
				// получить объект событие.
				// вместо event лучше писать window.event
				event = event || window.event;

				// кросс-браузерно получить target
				var target = event.target || event.srcElement;
				var json_trade_offer = JSON.parse(this.element.find('#json_trade_offer').html());
				if($('.js-step-3').children().hasClass("disabled")){
					$('.js-step-3').children().removeClass('disabled');
				}
				else{
				   //$('.js-step-3').children().addClass('disabled');
				};
				// цикл двигается вверх от target к родителям до form
				while (target.tagName != 'FORM') {
					if (target.tagName == 'LABEL') {
						var val = target.getAttribute('value');
						var name = target.getAttribute('data-title');
						$('#sizeId').val(val);
						$('#sizeName').val(name);
						
						if (target.parentNode.classList.contains("N")) {
							return false;
						}
						else{
							var val = target.getAttribute('value');
							
							$('.js-step-3 .tag label').each(function(i, elem){
								elem.parentNode.classList.add('N');
							});
							$('.js-step-2 .tag').each(function(i, elem){
								elem.classList.remove('active');
							});
							if(target.parentNode.classList.contains('active')){
								return false;
							}
							target.parentNode.classList.add('active');
							//пройдемся по св-вам объкта для того чтобы понять какие предложения есть у данного товара
							
							for(var key in json_trade_offer){							
								//нужно выбрать значения св-в объекта у которых совпадает св-во размера							
								if(val == key){								
									//рассчитаем цену
									var standart_price = 10000;
									var bases_price = Number($('.price > span.price').html());
									var price_property = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;
									
									var diff = price_property - standart_price;
									if(diff){
										var new_price = bases_price + diff;
										var discount = (new_price/100) * $('#discount').html();
										$('.price .price').html(new_price);
										$('.price .new_price').html(new_price - discount);
									};
									var material = json_trade_offer[key].PROPERTY_MATHERIAL_VALUE;
									$('.js-step-3 .tag > input:checked').each(function(i, elem){
										
										console.log(json_trade_offer[elem.value].PROPERTY_PRICE_VALUE.PRICE);
									});
									
									$('.js-step-3 .tag input').removeAttr("checked");
									$('.js-step-3 .tag label').each(function(i, elem){
										//if(elem.getAttribute('value') == material){
											var i = 0;
											for(i; i < json_trade_offer[key].PROPERTY_LINK_VALUE.length; i++){
												var id = elem.parentNode.getAttribute('data-element-id');
												if(json_trade_offer[key].PROPERTY_LINK_VALUE[i] == id){
													elem.parentNode.classList.remove('N');
												}
											};
										//}
										//else{
											//elem.parentNode.classList.add('N');
										//}
									});
								}
							}
							
							return;
						}
					}
					else{
						
					}
					target = target.parentNode;
				}
		},
		
		'.js-step-3 mouseup': function(e) {
					// получить объект событие.
				// вместо event лучше писать window.event
				event = event || window.event;

				// кросс-браузерно получить target
				var target = event.target || event.srcElement;
				var json_trade_offer = JSON.parse(this.element.find('#json_trade_offer').html());
				if($('.js-step-4').children().hasClass("disabled")){
					$('.js-step-4').children().removeClass('disabled');
				}
				else{
				   //$('.js-step-3').children().addClass('disabled');
				};
								// цикл двигается вверх от target к родителям до form
				while (target.tagName != 'FORM') {
					if (target.tagName == 'LABEL') {
						var val = target.getAttribute('value');
						var name = target.getAttribute('data-title');
						$('#materialId').val(val);
						$('#materialName').val(name);
						if ($(target).parent().hasClass("N")) {
							return false;
						}
						else{
							var val = target.getAttribute('value');

							//пройдемся по св-вам объкта для того чтобы понять какие предложения есть у данного товара
							for(var key in json_trade_offer){
								//нужно выбрать значения св-в объекта у которых совпадает св-во размера 
								if(val == key){
									//рассчитаем цену
									var standart_price = 10000;
									var bases_price = Number($('.price > span.price').html());
									var price_property = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;
									var diff = price_property - standart_price;
									if(diff){
										var new_price = bases_price + diff;
										var discount = (new_price/100) * $('#discount').html();
										$('.price .price').html(new_price);
										$('.price .new_price').html(new_price - discount);
									};
								}
							}
							
							return;
						}
					}
					else{
						
					}
					target = target.parentNode;
				}
		},*/
		
			
				////////////Калькулятор
		//получаем значения параметров
		'.js-filter-form click': function(e) {
			var json_trade_offer = JSON.parse(this.element.find('#json_trade_offer').html());
			var Price = Number($('.price > span.price').html()); //получаем стоимость товара.
			var base_price = Number($('#price_item').html());
			var colorPrice = 0;
			var sizePrice = 0;
			var materialPrice = 0;
			var colorValue = $('input.color:checked').val();
			var sizeValue = $('input.size:checked').val();
			var materialValue = $('input.material:checked').val();
			
			var colorName = $('input.color:checked').attr('data-name');
			var sizeName = $('input.size:checked').attr('data-name');
			var materialName = $('input.material:checked').attr('data-name');
			
			$('#materialName').val(materialName);
			$('#sizeName').val(sizeName);
			$('#colorName').val(colorName);
			
			$('#materialId').val(materialValue);
			$('#sizeId').val(sizeValue);
			$('#colorId').val(colorValue);
			$('#count').val($('.vote').html());
			
			for(var key in json_trade_offer){													
				if(colorValue == key){		
					colorPrice = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;						
				}
				else if(sizeValue == key){
					sizePrice = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;	
				}
				else if(materialValue == key){
					materialPrice = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;	
				}
			}
			
			
				var count = Number($('#voter .vote').html())
				var total = base_price + (Number(colorPrice) + Number(sizePrice) + Number(materialPrice)) * count;//формула расчета общей стоимости
				var discount = (total/100) * Number($('#discount').html());
				var newTotal = Math.round(total);//округляем
				var newTotalDics = Math.round(total) - discount;//округляем
				
				Number($('.price > span.price').html(newTotal));
				Number($('.price > span.new_price').html(newTotalDics));
		
		
		},

		
		/*'.js-filter-form click': function(e) {
			// получить объект событие.
			// вместо event лучше писать window.event
			event = event || window.event
			// кросс-браузерно получить target
			var target = event.target || event.srcElement
			var json_trade_offer = JSON.parse(this.element.find('#json_trade_offer').html());
			$(".js-step-1").on('click', function() {
				if($('.js-step-2').children().hasClass("disabled")){
					$('.js-step-2').children().removeClass('disabled');
				}
				else{
				  // $('.js-step-2').children().addClass('disabled');
				};
				// цикл двигается вверх от target к родителям до form
				while (target.tagName != 'FORM') {
					if (target.tagName == 'LABEL') {
						var val = target.getAttribute('value');
						var name = target.getAttribute('data-title');
						$('#colorId').val(val);
						$('#colorName').val(name);
						$('.js-step-2 .tag label').each(function(i, elem){
								elem.parentNode.classList.add('N');
						});
						//пройдемся по св-вам объкта для того чтобы понять какие предложения есть у данного товара
						for(var key in json_trade_offer){
							//нужно выбрать значения св-в объекта у которых совпадает св-во цвета 
							if(val == key){
								//рассчитаем цену
								var bases_price = Number($('.price > span.price').html());
								var price_property = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;
								var diff = price_property - bases_price;
								if(diff != 0){
									var new_price = bases_price + diff;
									var discount = (new_price/100) * $('#discount').html();
									$('.price .price').html(new_price);
									$('.price .new_price').html(new_price - discount);
								};
								var size = json_trade_offer[key].PROPERTY_WIDTH_VALUE +
								'x' + json_trade_offer[key].PROPERTY_HEIGHT_VALUE +
								'x' + json_trade_offer[key].PROPERTY_LENGTH_VALUE
								$('.js-step-2 .tag input, .js-step-3 .tag input').removeAttr("checked"); 
								$('.js-step-2 .tag label').each(function(i, elem){
									//if(elem.getAttribute('value') == size){
										var i = 0;
										for(i; i < json_trade_offer[key].PROPERTY_LINK_VALUE.length; i++){
											var id = elem.parentNode.getAttribute('data-element-id');
											if(json_trade_offer[key].PROPERTY_LINK_VALUE[i] == id){
												elem.parentNode.classList.remove('N');
											}
										};
								//	}
									//else{
										//elem.parentNode.classList.add('N');
									//}
								});
								$('.js-step-3 .input-cell').each(function(i, elem){
										elem.parentNode.classList.add('disabled');
								});
								$('.js-step-3 .tag label').each(function(i, elem){
										elem.parentNode.classList.add('N');
								});
							}
						}
						
						return;
					}
					else{
						
					}
					target = target.parentNode;
				}
			})
			$(".js-step-2").on('click', function(e) {
				// получить объект событие.
				// вместо event лучше писать window.event
				event = event || window.event;

				// кросс-браузерно получить target
				var target = event.target || event.srcElement;
				if($('.js-step-3').children().hasClass("disabled")){
					$('.js-step-3').children().removeClass('disabled');
				}
				else{
				   //$('.js-step-3').children().addClass('disabled');
				};
				// цикл двигается вверх от target к родителям до form
				while (target.tagName != 'FORM') {
					if (target.tagName == 'LABEL') {
						var val = target.getAttribute('value');
						var name = target.getAttribute('data-title');
						$('#sizeId').val(val);
						$('#sizeName').val(name);
						if ($(target).parent().hasClass("N")) {
							console.log(target)
							console.log($(target).parent().attr('class'))
							return false;
						}
						else{
							var val = target.getAttribute('value');
							
							$('.js-step-3 .tag label').each(function(i, elem){
								elem.parentNode.classList.add('N');
							});
							//пройдемся по св-вам объкта для того чтобы понять какие предложения есть у данного товара
							for(var key in json_trade_offer){
								//нужно выбрать значения св-в объекта у которых совпадает св-во размера							
								if(val == key){
									//рассчитаем цену
									var bases_price = Number($('.price > span.price').html());
									var price_property = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;
									var diff = price_property - bases_price;
									if(diff != 0){
										var new_price = bases_price + diff;
										var discount = (new_price/100) * $('#discount').html();
										$('.price .price').html(new_price);
										$('.price .new_price').html(new_price - discount);
									};
										var material = json_trade_offer[key].PROPERTY_MATHERIAL_VALUE;
										$('.js-step-3 .tag input').removeAttr("checked");
										$('.js-step-3 .tag label').each(function(i, elem){
											//if(elem.getAttribute('value') == material){
												var i = 0;
												for(i; i < json_trade_offer[key].PROPERTY_LINK_VALUE.length; i++){
													var id = elem.parentNode.getAttribute('data-element-id');
													if(json_trade_offer[key].PROPERTY_LINK_VALUE[i] == id){
														elem.parentNode.classList.remove('N');
													}
												};
											//}
											//else{
												//elem.parentNode.classList.add('N');
											//}
										});
								}
							}
							
							return;
						}
					}
					else{
						
					}
					target = target.parentNode;
				}
			})
			$(".js-step-3").on('click', function(e) {
				// получить объект событие.
				// вместо event лучше писать window.event
				event = event || window.event;

				// кросс-браузерно получить target
				var target = event.target || event.srcElement;
				if($('.js-step-4').children().hasClass("disabled")){
					$('.js-step-4').children().removeClass('disabled');
				}
				else{
				   //$('.js-step-3').children().addClass('disabled');
				};
								// цикл двигается вверх от target к родителям до form
				while (target.tagName != 'FORM') {
					if (target.tagName == 'LABEL') {
						var val = target.getAttribute('value');
						var name = target.getAttribute('data-title');
						$('#materialId').val(val);
						$('#materialName').val(name);
						if ($(target).parent().hasClass("N")) {
							return false;
						}
						else{
							var val = target.getAttribute('value');

							//пройдемся по св-вам объкта для того чтобы понять какие предложения есть у данного товара
							for(var key in json_trade_offer){
								//нужно выбрать значения св-в объекта у которых совпадает св-во размера 
								if(val == key){
									//рассчитаем цену
									var bases_price = Number($('.price > span.price').html());
									var price_property = json_trade_offer[key].PROPERTY_PRICE_VALUE.PRICE;
									var diff = price_property - bases_price;
									if(diff != 0){
										var new_price = bases_price + diff;
										var discount = (new_price/100) * $('#discount').html();
										$('.price .price').html(new_price);
										$('.price .new_price').html(new_price - discount);
									};
								}
							}
							
							return;
						}
					}
					else{
						
					}
					target = target.parentNode;
				}
			})
		
		  /* if(this.element.find('.js-step-2').children().hasClass("disabled")){
				this.element.find('.js-step-2').children().removeClass('disabled');
		   }
		   else{
			   this.element.find('.js-step-2').children().addClass('disabled');
		   };*/
        //},*/
		/*'.js-step-2 click': function() {
            if(this.element.find('.js-step-3').children().hasClass("disabled")){
				this.element.find('.js-step-3').children().removeClass('disabled');
		   }
		   else{
			   this.element.find('.js-step-3').children().addClass('disabled');
		   };
        },
		'.js-step-3 click': function() {
		
        },*/
		

    });

})(jQuery, window.APP);




