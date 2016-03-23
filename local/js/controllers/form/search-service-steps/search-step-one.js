(function ($, APP) {
    'use strict';

    APP.Controls.SearchStepOneForm = APP.Controls.SearchStepBaseForm.extend(
        {
            pluginName: 'searchStepOneForm'
        },
        {
            init: function () {
                this._super();
                this.checkValid();
            },

            '.js-complex-list input change': function () {
                this.checkValid();
            },

            '.js-complex-name keyup': APP.helpers.debounce(200, function (el, e) {
                if (el.val().length > 0 && el.val().length < 3) return;

                this.element.find(".js-complex-list").ajaxl({
                    url: APP.urls.searchComplexByName,
                    data: {complexName: el.val()},
                    dataType: 'HTML',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        this.element.find(".js-ajax-content").html(data);
                        this.checkValid();

                        var $formLabel = this.element.find(".js-form-label");
                        if (el.val().length > 0) {
                            $formLabel.html("Подходящие варианты");
                        } else {
                            $formLabel.html("или выберите из списка");
                        }
                    })
                });


            }),

            checkValid: function() {
                var checkedInput = this.element.find(".js-complex-list input:checked");
                this.element.find(".js-next-step").toggleClass("disabled", !(checkedInput.length > 0));
                this.element.find(".js-next-step").toggleClass("waves-effect", (checkedInput.length > 0));
            }
        }
    );

})(jQuery, window.APP);