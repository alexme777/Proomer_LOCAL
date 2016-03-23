(function ($, APP) {
    'use strict';

    APP.Controls.SearchStepTwoForm = APP.Controls.SearchStepBaseForm.extend(
        {
            pluginName: 'searchStepTwoForm'
        },
        {
            init: function () {
                this._super();
                this.checkValid();
                this.$entranceSelect = this.element.find(".js-entrance-select");
                this.$floorSelect = this.element.find(".js-floor-select");
                this.checkPin(this.element.find('.js-house-select'));
            },

            '.js-house-select change': function (el, e) {
                this.element.find(".js-entrance-floor").ajaxl({
                    url: APP.urls.entranceByHouse,
                    data: {house:el.val()},
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        this.updateSelectValues(this.$entranceSelect, data.entranceList);
                        this.updateSelectValues(this.$floorSelect, [{value:"", html:"Любой"}]);
                    })
                });

                this.checkPin(el);
            },

            '.js-entrance-select change': function (el, e) {
                this.element.find(".js-floor-row").ajaxl({
                    url: APP.urls.floorByEntrance,
                    data: {entrance:el.val()},
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        this.updateSelectValues(this.$floorSelect, data.floorList);
                    })
                });
            },

            '.js-floor-select change': function (el, e) {
                this.checkValid();
            },

            '{parent} .js-pin click': function (el, e) {
                this.options.parent.find(".js-pin").removeClass("active");
                el.addClass("active");

                var houseId = el.data("house");
                this.element.find('.js-house-select').val(houseId);
                this.element.find(".js-house-select").trigger("change");
            },

            updateSelectValues: function(select, values) {
               select.select2("destroy");
               select.empty();
                for (var key in values) {
                    select.append($("<option>", {
                        "value":values[key].value,
                        "html":values[key].html
                    }))
                }
                select.select2({
                    minimumResultsForSearch: 500,
                    width: 260
                });
                this.checkValid();
            },

            checkValid: function() {
                this.element.find(".js-next-step").toggleClass("disabled", !(this.element.find(".js-floor-select").val() > 0));
                this.element.find(".js-next-step").toggleClass("waves-effect", (this.element.find(".js-floor-select").val() > 0));

            }
        }
    );

})(jQuery, window.APP);