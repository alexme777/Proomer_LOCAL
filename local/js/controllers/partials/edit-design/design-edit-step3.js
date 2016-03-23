(function ($, APP) {
    "use strict";

    APP.Controls.DesignEditStep3 = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'designEditStep3'
        },
        {
            init: function () {
                this.initRoomDropzone();
            },

            '.js-del-item click': function (el, e) {
                var self = this;
                var $photoBlock = el.closest(".js-photo-dropzone"),
                    roomId = $photoBlock.data("element-id"),
                    valueId = el.parent().data("value-id");
                var data = {};
                var url = '';

                if ($photoBlock.hasClass('js-room-photo-dropzone')) {
                    data = {roomId:roomId, imageId: valueId};
                    url = APP.urls.designEdit.deleteRoomImage;
                } else {
                    data = {imageId: valueId, designId: self.options.designIdInput.val()};
                    url = APP.urls.designEdit.deletePlanImage;
                }

                $photoBlock.ajaxl({
                    url: url,
                    data: data,
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.result) {
                            $photoBlock.find(".dz-default-img").remove();
                            data.response.forEach(function(val){
                                var $newImage = $("<div>", {
                                    "class": "dz-default-img",
                                    "data-value-id": val.valueId,
                                    "style": "background-image: url('" + val.imgSrc + "')"
                                });
                                var $delIcon = $("<div>", {
                                    "class": "js-del-item del-icon"
                                });
                                $photoBlock.append($newImage.append($delIcon));
                            });

                            $(window).trigger("dropzone.checkItems");
                        }
                    })
                });
            },
			
			'.js-pin-item click': function (el, e) {
                var self = this;
                var $photoBlock = el.closest(".js-photo-dropzone"),
				roomId = $photoBlock.data("element-id"),
				valueId = el.parent().data("value-id");
				var indx = $('.dz-default-img').index( el.parent() ) - 1;
                var data = {};
                var url = '';
					
				/**
					* Показать слайд
				*/
				var el;
				function showSlide(indx, roomId) {
					$.fancybox({
						padding:0,
						minWidth:464,
						maxWidth:464,
						type: "ajax",
						href: "/profile/design/get-slides-img/",
						ajax: {
							data: {id: roomId, type: 'room', indx: indx},
							type: "POST"
						},

						openEffect: 'none',
						closeEffect: 'none',
						afterShow: function() {
							var $img = $('.js-slide-image');
							var $delete_pin = $('.js-delete-pin');
							var coordsInput = $('.js-slide-input');
							var coordsInputVal = coordsInput.attr('value');
							//var pinName = el.name.find('input[type="text"]').val();
							if(coordsInputVal != ""){
								var oldCoords = coordsInputVal.split(',');
								var oldPinPos = "left: " + (e.offsetX - 12) + "px; top:" +  (e.offsetY - 12) + "px";
								var $imgWrapper = $img.parent();
								var $pin = $('<div>', {
									class: 'point',
									style: oldPinPos
									//html: pinName
								});
								$imgWrapper.append($pin);
							}
							$img.on('click', function(e) {
								setCoords(e.offsetX, e.offsetY, indx);
							});
							$delete_pin.on('click', function (e) {
								var pinId = this.parentNode.getAttribute('data-pin-id');
								var pin = this.parentNode.parentNode.remove();
								var data = {};
								data = {pinId: pinId};
								$photoBlock.ajaxl({
									url: '/profile/design/delpin',
									data: data,
									dataType: 'JSON',
									type: 'POST',
									success: function (data) {
										
										
										/*if (data.result) {
											$photoBlock.find(".dz-default-img").remove();
											data.response.forEach(function(val){
												var $newImage = $("<div>", {
													"class": "dz-default-img",
													"data-value-id": val.valueId,
													"style": "background-image: url('" + val.imgSrc + "')"
												});
												var $delIcon = $("<div>", {
													"class": "js-del-item del-icon"
												});
												$photoBlock.append($newImage.append($delIcon));
											});

											$(window).trigger("dropzone.checkItems");
										}*/
									}
								});
							});
							//Drag and drop
							$(function() {
								$('.pin').draggable({containment: 'parent'});
							});
							$('.pin').bind('dragstop', function(event, ui) {
								var pinId = this.getAttribute('data-pin-id');
								var pos = (ui.position.left + 19) + ',' + (ui.position.top + 19);
								//var pos = ui.position.left + ',' + ui.position.left;
								var data = {pinId: pinId, pos:pos, indx:indx};
								$photoBlock.ajaxl({
									url: '/profile/design/changepospin',
									data: data,
									dataType: 'JSON',
									type: 'POST',
									success: function (data) {

									}
								});
							});

						},
					});
				}

				/**
				* Запись координат в поле
				* @param x
				* @param y
				*/
				function setCoords(x, y, indx) {
					var pinName = $('#pinName'); var pinNameVal = pinName.val();
					var pinURL = $('#pinURL'); var pinURLVal = pinURL.val();
					var pinCoords = $('#pinCoords'); var pinCoordsVal = pinCoords.val();
					var coordsInput = $('.js-slide-input');
					pinCoords.attr('value', x + ', ' + y);
					
					var $js_add_pin = $('.js-add-pin');
					$js_add_pin.on('click', function(e) {
						addPin(pinNameVal, pinURLVal, pinCoordsVal, roomId, indx);
					});
					/*$.fancybox({
						type: "ajax",
						href: "/profile/design/add-pin-img/",
						ajax: {
							data: {roomId: roomId, type: 'room', indx: indx, coords: x + ', ' + y},
							type: "POST"
						},

						minWidth: 350,
						openEffect: 'none',
						closeEffect: 'none',
						afterShow: function() {
							var $js_add_pin = $('.js-add-pin');
							var pinName = $('#pinName'); var pinNameVal = pinName.val();
							var pinURL = $('#pinURL'); var pinURLVal = pinURL.val();
							var pinCoords = $('#pinCoords'); var pinCoordsVal = pinCoords.val();
							/*var coordsInput = $('.js-slide-input');
							var coordsInputVal = coordsInput.attr('value');
							//var pinName = el.name.find('input[type="text"]').val();
							if(coordsInputVal != ""){
								var oldCoords = coordsInputVal.split(',');
								var oldPinPos = "left: " + (e.offsetX - 12) + "px; top:" +  (e.offsetY - 12) + "px";
								var $imgWrapper = $img.parent();
								var $pin = $('<div>', {
									class: 'point',
									style: oldPinPos
									//html: pinName
								});
								$imgWrapper.append($pin);
							}*/
				
						/*	$js_add_pin.on('click', function(e) {
								addPin(pinNameVal, pinURLVal, pinCoordsVal, roomId, indx);
							});
						}
					});*/
				}
				
				function addPin(pinNameVal, pinURLVal, pinCoordsVal, roomId, indx) {
					var pinName = $('#pinName'); var pinNameVal = pinName.val();
					var pinURL = $('#pinURL'); var pinURLVal = pinURL.val();
					var pinCoords = $('#pinCoords'); var pinCoordsVal = pinCoords.val();
					$.ajax({
                            type: "POST",
                            url: "/profile/design/add-pin-img",
							data: "name="+pinNameVal+"&url="+pinURLVal+"&coords="+pinCoordsVal+"&roomId="+roomId+"&indx="+indx,
                            success: function(data){
								$.fancybox.close();
							}
					 });
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
				
				showSlide(indx, roomId);
  

				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
            },
			
            initRoomDropzone: function () {
                var self = this;
                var $planImage = this.element.find(".js-plan-dropzone");
                if (!$planImage.data("planDropzone")) {
                    $planImage.data("planDropzone", true);
                    new APP.Controls.DropzoneArea($planImage, {
                        url: APP.urls.designEdit.uploadPlanImage,
                        maxFilesize: 5,
                        acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                        multiple: true,
                        paramsQuery: {
                            designId: self.options.designIdInput.val(),
                            fileType: "PROPERTY_PLAN_FLAT",
                            resizeType: "DROPZONE_MAIN_PHOTO"
                        }
                    });
                }

                this.element.find(".js-room-photo-dropzone").each(function () {
                    if (!$(this).data("roomDropzone")) {
                        $(this).data("roomDropzone", true);

                        new APP.Controls.DropzoneArea($(this), {
                            url: APP.urls.designEdit.uploadRoomImage,
                            maxFilesize: 5,
                            acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                            multiple: true,
                            paramsQuery: {
                                designId: self.options.designIdInput.val(),
                                fileType: "PROPERTY_IMAGES",
                                roomId: $(this).data("element-id")
                            }
                        });
                    }
                });
            },

            getCurrentRoomForm: function () {
                var $roomListForm = this.element.find(".js-room-list-form");

                $.ajax({
                    url: APP.urls.designEdit.roomFormStep3,
                    data: {designId: this.options.designIdInput.val()},
                    dataType: 'HTML',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        this.element.find(".js-step-body").html($(data));
                        this.initRoomDropzone();
                        APP.Controls.MainTooltip.initList(this.element.find(".js-tooltip"), {pageObj: this.options.parent});
                    })
                });
            },

            '.js-next-add-step click': function (el) {
                if (el.hasClass("disabled")) return;
                this.saveStep();
            },
						
            saveStep: function () {
                //Переход с третьего шага на четвертый
                var $stepBlock = this.element;

                $stepBlock.find(".js-step-body").slideUp(500, function () {
                    $stepBlock.removeClass("active");
                    $stepBlock.addClass("success");
                    $stepBlock.removeAttr("style");
                });

                this.element.trigger("designEdit.saveStep3Complete");
            }
        }
    );

})(jQuery, APP);