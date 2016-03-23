(function ($, APP) {
    "use strict";

    APP.Controls.DesignEditStep2 = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'designEditStep2'
        },
        {
            init: function () {
                APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));

                new APP.Controls.DropzoneArea(this.element.find('.js-main-photo-dropzone'), {
                    url: APP.urls.designEdit.uploadFile,
                    maxFilesize: 5,
                    acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                    paramsQuery: {
                        designId: this.options.designIdInput.val(),
                        resizeType: "DROPZONE_MAIN_PHOTO"
                    }
                });

                this.setFormValidator(this.element.find('form'));
                this.element.find('.js-is-numeric').numericOnly();
                this.setFreeDesign();
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
                    url: APP.urls.designEdit.addRoom,
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
                    url: APP.urls.designEdit.deleteRoom,
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
                    url: APP.urls.designEdit.saveStepSecond,
                    data: $.merge($stepBlock.find("form").serializeArray(), [{name: "designId", value: designId}]),
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.result) {
                            $stepBlock.find(".js-step-body").slideUp(500, function () {
                                $stepBlock.removeClass("active");
                                $stepBlock.addClass("success");
                                $stepBlock.removeAttr("style");
                            });

                            this.element.trigger("designEdit.saveStep2Complete");
                        } else {
                            if (data.errorFields && data.errorFields.length > 0) {
                                for (var i = 0; i < data.errorFields.length; i++) {
                                    var input = this.element.find("#" + data.errorFields[i].fieldId);
                                    input.parent().removeClass("valid").addClass("error");
                                    input.siblings(".js-error").html(data.errorFields[i].message);
                                }
                            }
                        }
                    })
                });
            }

        }
    );

})(jQuery, APP);