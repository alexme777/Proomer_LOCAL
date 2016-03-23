(function ($, APP) {
    "use strict";

    APP.Controls.ProviderEditStep3 = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'providerEditStep3'
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
                    url: APP.urls.providerEdit.roomFormStep3,
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