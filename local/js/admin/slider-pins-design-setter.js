(function() {
    var el;
    /**
     * Показать слайд
     */
    function showSlide(indx) {

        $.fancybox({
            type: "ajax",
            ajax: {
                data: {id: 730, type: 15, indx: indx},
                type: "POST"
            },
            href: "/admin/get-slides-img/",
            minWidth: 200,
            openEffect: 'none',
            closeEffect: 'none',
            afterShow: function() {
                var $img = $('.js-slide-image');
				var coordsInput = el.coords.find('input[type="text"]').eq( indx );
                var coordsInputVal = coordsInput.val();
                var pinName = el.name.find('input[type="text"]').val();
                if(coordsInputVal != ""){
                    var oldCoords = coordsInputVal.split(',');
                    var oldPinPos = "left: " + (oldCoords[0] - 12) + "px; top:" +  (oldCoords[1] - 12) + "px";
                    var $imgWrapper = $img.parent();
                    var $pin = $('<div>', {
                        class: 'point',
                        style: oldPinPos,
                        html: pinName
                    });
                    $imgWrapper.append($pin);
                }
                $img.on('click', function(e) {
                    setCoords(e.offsetX, e.offsetY, indx);
                });
            }
        });
    }

    /**
    * Запись координат в поле
    * @param x
    * @param y
    */
    function setCoords(x, y, indx) {
        var coordsInput = el.coords.find('input[type="text"]').eq( indx );
        coordsInput.val(x + ', ' + y);
        $.fancybox.close();
    }

    function parse(val) {
        var result = false,
            tmp = [];
        location.search
            //.replace ( "?", "" )
            // this is better, there might be a question mark inside
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }

})();