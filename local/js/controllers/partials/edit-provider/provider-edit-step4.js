(function ($, APP) {
    "use strict";

    APP.Controls.ProviderEditStep4 = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'providerEditStep4'
        },
        {
            init: function () {
                var self = this;
                new APP.Controls.DropzoneArea(this.element.find(".js-docs-dropzone"), {
                    url: APP.urls.providerEdit.uploadFile,
                    maxFilesize: 20,
                    acceptedFiles: '.doc, .docx, .xls, .xlsx, .pdf, .rar, .tar, .zip',
                    files: true,
                    paramsQuery: {
                        designId: self.options.designIdInput.val(),
                        fileType: "PROPERTY_DOCUMENTS"
                    }
                });
            },

            '.js-finish-step click': function (el) {
                if (el.hasClass("disabled")) return;
                this.saveStep();
            },

            '.js-file-delete click': function (el) {
                this.element.ajaxl({
                    url: APP.urls.providerEdit.deleteFile,
                    data: {designId: this.options.designIdInput.val(), fileType: "PROPERTY_DOCUMENTS"},
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        el.closest(".upload-doc").remove();
                        $(window).trigger("dropzone.checkItems");
                    })
                });
            },

            saveStep: function () {
                //Перевод проекта на модерацию
                var $stepBlock = this.element;

                $stepBlock.ajaxl({
                    url: APP.urls.designEdit.publishDesign,
                    data: {designId: this.options.designIdInput.val()},
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.result) {
                            APP.helpers.showFancyboxMessage("Отправлен на модерацию", "Ваш проект отправлен на модерацию. Администратор проверит его на соответствие нашим требованиям и опубликует на сайте. Или вернет на доработку — проверяйте email", 5000, "/profile/design/");
                        }
                    })
                });
            }
        }
    );

})(jQuery, APP);