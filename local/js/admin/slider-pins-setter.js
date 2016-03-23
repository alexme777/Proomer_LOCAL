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
			imageNum: 0,
            coordsPropId: 84,
            iblockName: "room"
        };
    }

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
			class: 'ADD_POINT',
			value: settings.imageNum,
            style: 'margin-left: 10px'
        });
		//el.coords.find('input[type="text"]').after('<a class="gallery" rel="group" title="это фото 1" href="/upload/iblock/cfd/cfd75f0329c8b8dc965085773085dace.jpg"><img width="240" height="190" src="/upload/iblock/cfd/cfd75f0329c8b8dc965085773085dace.jpg"></a><a class="gallery" rel="group" title="это фото 1" href="/upload/iblock/6a3/6a3fbaf9ed65bdfb903923b3aa94cffb.jpg"><img src="/upload/iblock/6a3/6a3fbaf9ed65bdfb903923b3aa94cffb.jpg"></a>');
        el.coords.find('input[type="text"]').after($button);
		$("a.gallery").fancybox();
        $("a.ADD_POINT").on('click', function() {
			var indx = $("a.ADD_POINT").index( this );
            showSlide(indx);
            return false;
        });
    }

    /**
     * Показать слайд
     */
    function showSlide(indx) {
        var $slideInput = el.slide.find('input[type="text"]');

        if($slideInput.val() == "") {
            $slideInput.focus();
            return;
        }

        $.fancybox({
            type: "ajax",
            ajax: {
                data: {id: $slideInput.val(), type: settings.iblockName, indx: indx},
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