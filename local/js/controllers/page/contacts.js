(function($, APP) {
    'use strict';

    /**
     * Контроллер страницы контактов
     **/
    APP.Controls.Page.Contacts = can.Control.extend({

        init: function() {
            this.element.find('.js-select').select2({
                minimumResultsForSearch: 500,
                width: 'style'
            });

            this.initMap();
        },

        initMap: function() {
            var centerCoord = APP.centerCoords.split(',');
            var pinCoord = APP.pinCoords.split(',');


            var mapOptions = {
                center: new google.maps.LatLng(centerCoord[0], centerCoord[1]),
                zoom: 14,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE
                },
                disableDoubleClickZoom: true,
                mapTypeControl: false,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                },
                scaleControl: true,
                scrollwheel: true,
                panControl: false,
                streetViewControl: true,
                draggable : true,
                overviewMapControl: false,
                overviewMapControlOptions: {
                    opened: false
                },
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [
                    { "stylers": [
                        { "saturation": -100 }
                    ] },{
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [ { "color": "#3bb3dc" } ]
                    },{
                        "elementType": "labels",
                        "stylers": [ {
                            "visibility": "off"
                        } ] },{
                        "featureType": "poi.park",
                        "elementType": "geometry.fill",
                        "stylers": [ {
                            "color": "#aadd55"
                        } ] },{
                        "featureType": "road.highway",
                        "elementType": "labels",
                        "stylers": [ {
                            "visibility": "on"
                        } ] },{
                        "featureType": "road.arterial",
                        "elementType": "labels.text",
                        "stylers": [ {
                            "visibility": "on"
                        } ] },{
                        "featureType": "road.local",
                        "elementType": "labels.text",
                        "stylers": [ {
                            "visibility": "on"
                        } ] },
                    { }
                ]

            };
            var mapElement = this.element.find('.js-map');
            var map = new google.maps.Map(mapElement.get(0), mapOptions);
            var locations = [
                [[pinCoord[0]],[pinCoord[1]]]
            ];
            for (var i = 0; i < locations.length; i++) {
                var marker = new google.maps.Marker({
                    icon: {
                        url: '/local/images/sprite.png',
                        size: new google.maps.Size(92, 79),
                        origin: new google.maps.Point(0, 290),
                        anchor: new google.maps.Point(25, 71)
                    },
                    position: new google.maps.LatLng(locations[i][0], locations[i][1]),
                    map: map
                });
            }
        }


    });

})(jQuery, window.APP);