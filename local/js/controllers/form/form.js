(function ($, APP) {
    "use strict";

    $.extend($.validator.messages, {
        required: "Это поле необходимо заполнить.",
        remote: "Пожалуйста, введите правильное значение.",
        email: "Пожалуйста, введите корректный E-mail.",
        url: "Пожалуйста, введите корректный URL.",
        date: "Пожалуйста, введите корректную дату.",
        dateISO: "Пожалуйста, введите корректную дату в формате ISO.",
        number: "Пожалуйста, введите число.",
        digits: "Пожалуйста, вводите только цифры.",
        creditcard: "Пожалуйста, введите правильный номер кредитной карты.",
        equalTo: "Введенные пароли не совпадают.",
        accept: "Пожалуйста, выберите файл с правильным расширением.",
        maxlength: $.validator.format("Пожалуйста, введите не больше {0} символов."),
        minlength: $.validator.format("Пожалуйста, введите не меньше {0} символов."),
        rangelength: $.validator.format("Пожалуйста, введите значение длиной от {0} до {1} символов."),
        range: $.validator.format("Пожалуйста, введите число от {0} до {1}."),
        max: $.validator.format("Пожалуйста, введите число, меньшее или равное {0}."),
        min: $.validator.format("Пожалуйста, введите число, большее или равное {0}.")
    });
    $.validator.addClassRules("required", {required: true});
    $.validator.addClassRules("is-email", {email: true});

    APP.Controls.Form = can.Control.extend(
        {
            pluginName: 'Form',
            defaults: {
                focusInvalid: true
            },
            inProcess: false
        },
        {
            init: function() {
                this.element.find(".js-protect").val("proomer-i-am-not-bot");

                this.validator = this.element.validate({
                    focusInvalid: this.options.focusInvalid,
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

                this.element.formTabber();
            },

            '.btn click': function (el, e) {
                this.submit(this.element, e);
            },

            'submit': function (el, e) {
                e.preventDefault();

                //Если форма не валидна, то ничего не делаем
                if (!this.element.valid()) {
                    this.onInvalid();
                    return;
                }

                if (this.inProcess) {
                    return;
                }
                this.inProcess = true;

                this.onSubmit();

                this.element.ajaxl({
                    url: this.element.attr('action'),
                    data: this.element.serializeArray(),
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.success) {
                            this.onSuccess(data);
                        } else {
                            if (data.errorFields && data.errorFields.length > 0) {
                                for (var i = 0; i < data.errorFields.length; i++) {
                                    var input = this.element.find("#" + data.errorFields[i].fieldId);
                                    if (input.length == 0) {
                                        input = this.element.find("input[name=" + data.errorFields[i].fieldId + "]");
                                    }
                                    input.closest('.input-row').removeClass("valid").addClass("error");
                                    input.closest('.input-row').find(".js-error").html(data.errorFields[i].message);
                                }
                            }
                            this.onError(data);
                        }
                        this.inProcess = false;
                    })
                });
            },

            'keyup': function (el, e) {
                if (e.keyCode == 13) {
                    if (e.target.tagName == 'TEXTAREA') {
                        return;
                    }
                    this.element.submit();
                }

                if (e.keyCode == 27) {
                    $.fancybox.close();
                }
            },

            '.js-show-password click':function (el) {
                if (el.hasClass('show-password')) {
                    el.removeClass('show-password').addClass('hide-password').attr('title', 'Скрыть пароль');
                    el.closest('.input-row').find('input[type=password]').attr('type', 'text');
                } else {
                    el.removeClass('hide-password').addClass('show-password').attr('title', 'Показать пароль');
                    el.closest('.input-row').find('input[type=text]').attr('type', 'password');
                }
            },

            onSubmit: function (){

            },

            onSuccess: function () {
                var title = this.element.data('title');
                var text = this.element.data('text');
                if (title || text) {
                    APP.helpers.showFancyboxMessage(title, text, 3000);
                }
            },

            onError: function (el) {
                this.element.find('.input-row.error input[type!="hidden"],.input-row.error  textarea,.input-row.error  select').first().focus();
            },

            onInvalid: function() {
                this.element.find('.input-row.error input[type!="hidden"],.input-row.error  textarea,.input-row.error  select').first().focus();
            }
        }
    );

    can.Control.extend(
        {
            pluginName: 'formTabber',
            defaults: {
                findQuery: 'input[type!="hidden"], textarea, select'
            }
        },
        {
            init: function () {
                this.formInputs = this.element.find(this.options.findQuery);

                this.formInputs.last().keydown(this.proxy(function (e) {
                    if (e.keyCode == 9 && !e.shiftKey) {
                        this.formInputs.first().focus();
                        return false;
                    }
                }));

                this.formInputs.first().keydown(this.proxy(function (e) {
                    if (e.keyCode == 9 && e.shiftKey) {
                        this.formInputs.last().focus();
                        return false;
                    }
                }));
            }
        }
    );

})(jQuery, APP);