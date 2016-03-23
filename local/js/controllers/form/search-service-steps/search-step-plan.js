(function ($, APP) {
    'use strict';

    APP.Controls.SearchStepPlanForm = APP.Controls.SearchStepBaseForm.extend(
        {
            pluginName: 'searchStepPlanForm'
        },
        {
            init: function () {
                this._super();
                this.checkValid();
            },

            '.js-plan-list input change': function () {
                this.checkValid();
            },

            '.js-plan-name keyup': APP.helpers.debounce(200, function (el, e) {
                if (el.val().length > 0 && el.val().length < 3) return;

                this.element.find(".js-plan-list").ajaxl({
                    url: APP.urls.searchPlanByName,
                    data: {planName: el.val()},
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
                var checkedInput = this.element.find(".js-plan-list input:checked");
                this.element.find(".js-next-add-step").toggleClass("disabled", !(checkedInput.length > 0));
                this.element.find(".js-next-add-step").toggleClass("waves-effect", (checkedInput.length > 0));
            }
        }
    );

})(jQuery, window.APP);