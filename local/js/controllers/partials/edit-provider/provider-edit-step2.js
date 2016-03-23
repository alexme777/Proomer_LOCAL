(function ($, APP) {
    "use strict";

    APP.Controls.ProviderEditStep2 = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'providerEditStep2'
        },
        {
            init: function () {
                APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));
				this.initRoomDropzone();
                new APP.Controls.DropzoneMainArea(this.element.find('.js-main-photo-dropzone'), {
                    url: APP.urls.providerEdit.uploadFile,
                    maxFilesize: 5,
                    acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                    paramsQuery: {
                        designId: $("#designId").val(),
                        resizeType: "DROPZONE_MAIN_PHOTO"
                    }
                });
				new APP.Controls.DropzoneAnonsArea(this.element.find('.js-anons-photo-dropzone'), {
                    url: APP.urls.providerEdit.uploadFile,
                    maxFilesize: 5,
                    acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                    paramsQuery: {
                        designId: $("#designId").val(),
                        resizeType: "DROPZONE_ANONS_PHOTO"
                    }
                });
                this.setFormValidator(this.element.find('form'));
                this.element.find('.js-is-numeric').numericOnly();
                this.setFreeDesign();
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
                    url = APP.urls.providerEdit.deleteRoomImage;
                } else {
                    data = {imageId: valueId, designId: self.options.designIdInput.val()};
                    url = APP.urls.providerEdit.deletePlanImage;
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

            initRoomDropzone: function () {
                var self = this;
                var $planImage = this.element.find(".js-plan-dropzone");
                if (!$planImage.data("planDropzone")) {
                    $planImage.data("planDropzone", true);
                    new APP.Controls.DropzoneArea($planImage, {
                        url: APP.urls.providerEdit.uploadPlanImage,
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
                            url: APP.urls.providerEdit.uploadRoomImage,
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
                    url: APP.urls.providerEdit.roomFormStep2,
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
            },
			
            '.js-design-free change': function (el, e) {
                this.setFreeDesign();
            },

            '.js-add-room-panel keyup': function (el, e) {
                if (e.keyCode == 13) {
                    el.find(".js-add-room-button").trigger("click");
                }
            },

            '.js-add-room-button click': function (el, e) {
                var $addRoomPanel = this.element.find(".js-add-room-panel");
                this.validAddRoomFields();
                if (el.hasClass("disabled")) return;

                var designId = this.options.designIdInput.val(),
                    roomName = $addRoomPanel.find('input[name="addRoomName"]').val(),
                    roomSquare = $addRoomPanel.find('input[name="addRoomSquare"]').val();

                var postData = [
                    {name: "designId", value: designId},
                    {name: "addRoomName", value: roomName},
                    {name: "addRoomSquare", value: roomSquare}
                ];

                $addRoomPanel.ajaxl({
                    url: APP.urls.providerEdit.addRoom,
                    data: postData,
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.result) {
                            var newRoomData = {
                                id: data.newId,
                                name: roomName,
                                square: roomSquare
                            };
                            var newRoomBlock = can.view.render("newRoomBlock", {newRoom: newRoomData});
                            $addRoomPanel.find('input[name="addRoomName"], input[name="addRoomSquare"]').val("");
                            this.element.find('.js-room-list').append(newRoomBlock);
                        }
                    })
                });
            },

            '.js-room-list .js-delete click': function (el, e) {
                if (el.data("has-img") == 1) {
                    APP.helpers.showFancyboxDelete("К комнате добавлены фотографии. Вы действительно хотите удалить комнату?", this.deleteRoom, el, this);
                } else {
                    this.deleteRoom(el, this);
                }

            },

            '.js-add-room-panel input keyup': function () {
                this.validAddRoomFields();
            },

            deleteRoom: function (el, self) {
                var roomId = el.parent().data("room-id");

                var postData = [
                    {name: "roomId", value: roomId}
                ];

                self.element.find(".js-room-list").ajaxl({
                    url: APP.urls.providerEdit.deleteRoom,
                    data: postData,
                    dataType: 'JSON',
                    type: 'POST',
                    success: self.proxy(function (data) {
                        if (data.result) {
                            el.parent().fadeRemove();
                        }
                    })
                });
            },

            validAddRoomFields: function () {
                var $addRoomPanel = this.element.find(".js-add-room-panel");
                var valid = true;
                $addRoomPanel.find("input").each(function () {
                    if ($(this).val().length == 0) {
                        $(this).parent().addClass("error");
                        $(this).siblings(".js-error").html("Обязательное поле");
                        valid = false;
                    } else {
                        $(this).parent().removeClass("error");
                    }
                });

                this.element.find('.js-add-room-button').toggleClass("disabled", !valid);
                this.element.find('.js-add-room-button').toggleClass("waves-effect", valid);
            },

            setFreeDesign: function () {
                var $priceInput = this.element.find('.js-design-price');
                var isChecked = this.element.find('.js-design-free').prop("checked");
                $priceInput.toggleClass("required", !isChecked);
                $priceInput.prop("disabled", isChecked);
                if (isChecked) {
                    $priceInput.closest('.input-row').removeClass("error");
                }
            },

            setFormValidator: function ($form) {
                this.validator = $form.validate({
                    highlight: function (element, errorClass, validClass) {
                        $(element).closest(".input-row").removeClass(validClass).addClass(errorClass);
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).closest(".input-row").removeClass(errorClass).addClass(validClass);
                    },
                    errorPlacement: function (errorEl, input) {
                        input.closest(".input-row").find('.js-error').html(errorEl.text());
                    }
                });
            },

            '.js-next-add-step click': function (el) {
                if (el.hasClass("disabled")) return;
                this.saveStep();
            },
			'.js-clear-filter click': function () {
                this.element.trigger("reset");
                this.element.find("select").each(function () {
                    $(this).find("option").first().prop("selected", true);
                    $(this).trigger("change");
                });

                this.element.find(".js-range-slider").trigger("rangeSlider.reset");
                this.element.find(".js-select-multiple").trigger("multiSelect.reset");
                this.element.submit();
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

            initSelect2: function () {
                this.element.find('.js-select').select2({
                    minimumResultsForSearch: 500,
                    width: 'element',
                    dropdownAutoWidth: true
                });
            },

            'select2:open': function() {
                var self = this;
                var interval = setInterval(function() {
                    if ($('.jspScrollable').length > 0) {
                        clearInterval(interval);
                        return;
                    }
                    self.scrollPane = $('.select2-results').jScrollPane({
                        autoReinitialise: true
                    });
                }, 100);
            },

            'select2:close': function() {
                var api = this.scrollPane.data('jsp');
                api.destroy();
            },

            saveStep: function () {
                //Сохранение второго шага
                var $stepBlock = this.element;
                var designId = this.options.designIdInput.val();

                var isValid = $stepBlock.find("form").valid();
                if (!isValid) {
                    var $errorFields = $stepBlock.find("form .error");
                    $errorFields.find("input, textarea").first().focus();
                    return;
                }

                $stepBlock.ajaxl({
                    url: APP.urls.providerEdit.saveStepSecond,
                    data: $.merge($stepBlock.find("form").serializeArray(), [{name: "designId", value: designId}]),
                    dataType: 'JSON',
                    type: 'POST',
					complete: function (data) {
						if(data.status == 200){
							APP.helpers.showFancyboxMessage("Отправлен на модерацию", "Ваш товар отправлен на модерацию. Администратор проверит его на соответствие нашим требованиям и опубликует на сайте. Или вернет на доработку — проверяйте email", 5000, "/profile/provider/");    						
						};
					},
                    success: this.proxy(function (data) {
						if (data.result) {
                            APP.helpers.showFancyboxMessage("Отправлен на модерацию", "Ваш товар отправлен на модерацию. Администратор проверит его на соответствие нашим требованиям и опубликует на сайте. Или вернет на доработку — проверяйте email", 5000, "/profile/provider/");
                        }						
                    })
                });
            }

        }
    );

})(jQuery, APP);