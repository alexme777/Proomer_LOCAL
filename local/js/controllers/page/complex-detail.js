(function($, APP) {
    'use strict';
    /**
     * Контроллер детальной страницы жилого коплекса
     **/
    APP.Controls.Page.ComplexDetail = can.Control.extend({

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

})(jQuery, window.APP);