(function ($, APP) {
    "use strict";
    APP.Controls.DropzoneMainArea = APP.BaseController.extend(
        {
            defaults: {
                thumbSize: [471, 357],
                url: "",
                multiple: false,
                files: false,
                maxFilesize: 20,
                acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                paramsQuery: {}
            },
            pluginName: 'dropzoneMainArea'
        },
        {
            init: function () {
                var params = $.extend({
					designId: $("#designId").val(),
                    fileType: "DETAIL_PICTURE",
                    multiple: this.options.multiple,
                    files: this.options.files
                }, this.options.paramsQuery);

                var self = this;

                this.element.dropzone({
                    url: this.options.url,
                    params: params,
                    thumbnailWidth: this.options.thumbSize[0],
                    thumbnailHeight: this.options.thumbSize[1],
                    previewTemplate: "<span></span>",
                    createImageThumbnails: false,
                    maxFilesize: this.options.maxFilesize,
                    acceptedFiles: this.options.acceptedFiles,
                    dictFileTooBig: "Файл слишком большой ({{filesize}}МБ). Максимальный размер файла: {{maxFilesize}}МБ.",
                    dictInvalidFileType: "Вы не можите загружать этот типа файла.<br /> Доступные типы: " + this.options.acceptedFiles,
                    init: function () {
                        //noinspection JSPotentiallyInvalidUsageOfThis this используется из контекста dropzone
                        var $dzElement = $(this.element);
                        var $errorBlock = $dzElement.next(".js-error");
                        var preloader = new APP.Controls.PreloaderController($dzElement);
		
                        this.on('sending', function (file) {
                            $dzElement.removeClass("dz-success");
                            $dzElement.addClass("ajax-loading");
                            preloader.addPreloader();
                            $dzElement.removeClass("error");
                            if ($errorBlock.length > 0) {
                                $errorBlock.hide();
                            }
                        });

                        this.on('complete', function (file) {
                            $dzElement.removeClass("ajax-loading");
                            preloader.removePreloader();
                        });

                        this.on('error', function (data, errorMessage) {
                            $dzElement.addClass("error");
                            if ($errorBlock.length > 0) {
                                $errorBlock.show();
                                $errorBlock.html(errorMessage);
                            }
                        });

                        $dzElement.find('.js-dz-text, .dz-image, .dz-default-img').click(function (ev) {
                            if (!$(ev.target).hasClass("js-del-item")) {
                                $dzElement.trigger("click");
                            }
                        });
                    },

                    success: function(data, responseDropzone) {
                        //noinspection JSPotentiallyInvalidUsageOfThis this используется из контекста dropzone
                        var $dzElement = $(this.element);

                        $dzElement.addClass("dz-success");
                        $dzElement.find('.dz-default-img, .upload-doc').remove();

                        if (!params.files) {
                            if (!params.multiple) {
                                self.appendSingleImg($dzElement, responseDropzone.response);
                            } else {
                                self.appendMultipleImg($dzElement, responseDropzone.response);
                            }
                        } else {
                            self.appendSingleFile($dzElement, responseDropzone.response);
                        }
                    }
                });
            },

            appendSingleImg: function ($dzElement, response) {
                var $newImage = $("<div>", {
                    "class": "dz-default-img",
                    "style": "background-image: url('" + response.imageSrc + "')"
                });
                $dzElement.append($newImage);
				$("#designId").val(response.designId);
            },

            appendMultipleImg: function ($dzElement, response) {
                response.forEach(function (val) {
                    var $newImage = $("<div>", {
                        "class": "dz-default-img",
                        "data-value-id": val.valueId,
                        "style": "background-image: url('" + val.imgSrc + "')"
                    });
                    var $delIcon = $("<div>", {
                        "class": "js-del-item del-icon"
                    });

                    $dzElement.append($newImage.append($delIcon));
                })
            },

            appendSingleFile: function ($dzElement, response) {
                var $newFile = $("<div>", {
                    "class":"upload-doc "+response.fileType,
                    "data-value-id": response.valueId
                });
                var $fileName = $("<span>", {
                    "class":"doc-title",
                    "html":response.fileName
                });
                var $fileSize = $("<span>", {
                    "class":"doc-size",
                    "html":response.fileSize
                });
                var $delIcon = $("<a>", {
                    "class": "js-file-delete delete ",
                    "href": "javascript:void(0);"
                });

                $dzElement.append($newFile.append($fileName).append($fileSize).append($delIcon));
            },

            '{window} dropzone.checkItems': function () {
                if (this.element.find('.dz-default-img, .upload-doc').length == 0) {
                    this.element.removeClass("dz-started dz-success");
                }
            }
        }
    );
	
	APP.Controls.DropzoneAnonsArea = APP.BaseController.extend(
        {
            defaults: {
                thumbSize: [471, 357],
                url: "",
                multiple: false,
                files: false,
                maxFilesize: 20,
                acceptedFiles: '.jpg, .png, .jpeg, .gif, .bmp',
                paramsQuery: {}
            },
            pluginName: 'dropzoneAnonsArea'
        },
        {
            init: function () {
                var params = $.extend({
					designId: $("#designId").val(),
                    fileType: "PREVIEW_PICTURE",
                    multiple: this.options.multiple,
                    files: this.options.files
                }, this.options.paramsQuery);

                var self = this;

                this.element.dropzone({
                    url: this.options.url,
                    params: params,
                    thumbnailWidth: this.options.thumbSize[0],
                    thumbnailHeight: this.options.thumbSize[1],
                    previewTemplate: "<span></span>",
                    createImageThumbnails: false,
                    maxFilesize: this.options.maxFilesize,
                    acceptedFiles: this.options.acceptedFiles,
                    dictFileTooBig: "Файл слишком большой ({{filesize}}МБ). Максимальный размер файла: {{maxFilesize}}МБ.",
                    dictInvalidFileType: "Вы не можите загружать этот типа файла.<br /> Доступные типы: " + this.options.acceptedFiles,
                    init: function () {
                        //noinspection JSPotentiallyInvalidUsageOfThis this используется из контекста dropzone
                        var $dzElement = $(this.element);
                        var $errorBlock = $dzElement.next(".js-error");
                        var preloader = new APP.Controls.PreloaderController($dzElement);
		
                        this.on('sending', function (file) {
                            $dzElement.removeClass("dz-success");
                            $dzElement.addClass("ajax-loading");
                            preloader.addPreloader();
                            $dzElement.removeClass("error");
                            if ($errorBlock.length > 0) {
                                $errorBlock.hide();
                            }
                        });

                        this.on('complete', function (file) {
                            $dzElement.removeClass("ajax-loading");
                            preloader.removePreloader();
                        });

                        this.on('error', function (data, errorMessage) {
                            $dzElement.addClass("error");
                            if ($errorBlock.length > 0) {
                                $errorBlock.show();
                                $errorBlock.html(errorMessage);
                            }
                        });

                        $dzElement.find('.js-dz-text, .dz-image, .dz-default-img').click(function (ev) {
                            if (!$(ev.target).hasClass("js-del-item")) {
                                $dzElement.trigger("click");
                            }
                        });
                    },

                    success: function(data, responseDropzone) {
                        //noinspection JSPotentiallyInvalidUsageOfThis this используется из контекста dropzone
                        var $dzElement = $(this.element);

                        $dzElement.addClass("dz-success");
                        $dzElement.find('.dz-default-img, .upload-doc').remove();

                        if (!params.files) {
                            if (!params.multiple) {
                                self.appendSingleImg($dzElement, responseDropzone.response);
                            } else {
                                self.appendMultipleImg($dzElement, responseDropzone.response);
                            }
                        } else {
                            self.appendSingleFile($dzElement, responseDropzone.response);
                        }
                    }
                });
            },

            appendSingleImg: function ($dzElement, response) {
                var $newImage = $("<div>", {
                    "class": "dz-default-img",
                    "style": "background-image: url('" + response.imageSrc + "')"
                });
                $dzElement.append($newImage);
				$("#designId").val(response.designId);
            },

            appendMultipleImg: function ($dzElement, response) {
                response.forEach(function (val) {
                    var $newImage = $("<div>", {
                        "class": "dz-default-img",
                        "data-value-id": val.valueId,
                        "style": "background-image: url('" + val.imgSrc + "')"
                    });
                    var $delIcon = $("<div>", {
                        "class": "js-del-item del-icon"
                    });

                    $dzElement.append($newImage.append($delIcon));
                })
            },

            appendSingleFile: function ($dzElement, response) {
                var $newFile = $("<div>", {
                    "class":"upload-doc "+response.fileType,
                    "data-value-id": response.valueId
                });
                var $fileName = $("<span>", {
                    "class":"doc-title",
                    "html":response.fileName
                });
                var $fileSize = $("<span>", {
                    "class":"doc-size",
                    "html":response.fileSize
                });
                var $delIcon = $("<a>", {
                    "class": "js-file-delete delete ",
                    "href": "javascript:void(0);"
                });

                $dzElement.append($newFile.append($fileName).append($fileSize).append($delIcon));
            },

            '{window} dropzone.checkItems': function () {
                if (this.element.find('.dz-default-img, .upload-doc').length == 0) {
                    this.element.removeClass("dz-started dz-success");
                }
            }
        }
    );

})(jQuery, APP);