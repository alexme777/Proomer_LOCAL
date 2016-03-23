(function() {

    var iblockId = parse("IBLOCK_ID");
    var settings;
    if (iblockId == 6) {
        settings = {
            pinName: 60,
            imagePropId: 14,
            coordsPropId: 59,
            iblockName: "complex"
        };
    }

    if (iblockId == 9) {
        settings = {
            pinName: 62,
            imagePropId: 18,
            coordsPropId: 61,
            iblockName: "floor"
        };
    }
	
	if (iblockId == 21) {
        settings = {
            pinName: 85,
            imagePropId: 83,
            coordsPropId: 84,
            iblockName: "room"
        };
    }
	
	if (iblockId == 10) {
        settings = {
            pinName: 130,
            imagePropId: 132,
			imageNum: 0,
            coordsPropId: 129,
            iblockName: "plan"
        };
    };

    var el;

    $(function() {
        el = {
            name: $("#tr_PROPERTY_" + settings.pinName),
            slide: $("#tr_PROPERTY_" + settings.imagePropId),
            coords: $("#tr_PROPERTY_" + settings.coordsPropId)
        };

        addButton();

    });

    /**
    * Добавить кнопку
    */
    function addButton() {
        var $button = $('<a>', {
            href: 'javascript:void(0);',
            text: '[Задать координаты метки]',
            style: 'margin-left: 10px'
        });
        el.coords.find('input[type="text"]').after($button);

        $button.on('click', function() {
            showSlide();
            return false;
        });

    }

    /**
     * Показать слайд
     */
    function showSlide() {
        var $slideInput = el.slide.find('input[type="text"]');

        if($slideInput.val() == "") {
            $slideInput.focus();
            return;
        }

        $.fancybox({
            type: "ajax",
            ajax: {
                data: {id: $slideInput.val(), type: settings.iblockName},
                type: "POST"
            },
            href: "/admin/get-slides-img/",
            minWidth: 200,
            openEffect: 'none',
            closeEffect: 'none',
            afterShow: function() {
                var $img = $('.js-slide-image');
                var coordsInputVal = el.coords.find('input[type="text"]').val();
                var pinName = el.name.find('input[type="text"]').val();

                if(coordsInputVal != "") {
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
                    setCoords(e.offsetX, e.offsetY);
                });
            }
        });
    }

    /**
    * Запись координат в поле
    * @param x
    * @param y
    */
    function setCoords(x, y) {
        var coordsInput = el.coords.find('input[type="text"]');
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