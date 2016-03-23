(function ($, APP) {
    'use strict';

    APP.Controls.SearchStepThreeForm = APP.Controls.SearchStepBaseForm.extend(
        {
            pluginName: 'searchStepThreeForm'
        },
        {
            init: function () {
                this._super();
                this.setSquare();
                this.checkValid();
                this.checkPin(this.element.find('.js-flat-select'));
            },

            '.js-flat-select change': function (el, e) {
                this.setSquare();
                this.checkValid();
                this.checkPin(el);
            },

            setSquare: function() {
                var $squareBlock = this.element.find('.js-flat-square');
                var flatSquareData = $squareBlock.find(".js-flat-square-value").data("flats");

                var currentTab = this.options.parent.find('.js-tab').last();

                if (this.element.find(".js-flat-select").val() > 0) {
                    $squareBlock.show();
                    $squareBlock.find(".js-flat-square-value").html(flatSquareData[parseInt(this.element.find(".js-flat-select").val())]);

                    var currentCaption = "кв. " + this.element.find('.js-flat-select option:selected').html();
                    currentTab.removeClass("active").addClass("success");
                    currentTab.find('.js-add-design').html(currentCaption);
                } else {
                    $squareBlock.hide();
                    currentTab.removeClass("success").addClass("active");
                    currentTab.find('.js-add-design').html(currentTab.find('.js-add-design').data("default-caption"));

                }
            },

            '{parent} .js-pin click': function (el, e) {
                this.options.parent.find(".js-pin").removeClass("active");
                el.addClass("active");

                var flatId = el.data("flat");
                this.element.find('.js-flat-select').val(flatId);
                this.element.find(".js-flat-select").trigger("change");
            },

            checkValid: function() {
                this.element.find(".js-next-add-step").toggleClass("disabled", !(this.element.find(".js-flat-select").val() > 0));
                this.element.find(".js-next-add-step").toggleClass("waves-effect", (this.element.find(".js-flat-select").val() > 0));

            }
        }
    );

})(jQuery, window.APP);