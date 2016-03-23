(function ($, APP) {
    "use strict";

    APP.Controls.SearchService = can.Control.extend({
            pluginName: 'searchService',
            listenTo: 'searchService.stepChange'
        },
        {
            init: function () {
                this.currentStep = 1;
                new APP.Controls.SearchStepOneForm(this.element.find(".js-search-service-form"));

                var defaultParams = {
                    complexId: parseInt($.parseGet("complexId")),
                    complexName: $.parseGet("complexName"),
                    house: parseInt($.parseGet("house")),
                    entrance: parseInt($.parseGet("entrance")),
                    floor: parseInt($.parseGet("floor")),
                    flat: parseInt($.parseGet("flat"))
                };

                if (defaultParams.flat > 0) {
                    this.element.trigger("searchService.stepChange", {
                        step: 3,
                        stepData: [
                            {name: "complexId", value: defaultParams.complexId},
                            {name: "house", value: defaultParams.house},
                            {name: "entrance", value: defaultParams.entrance},
                            {name: "floor", value: defaultParams.floor},
                            {name: "flat", value: defaultParams.flat}
                        ]
                    });
                } else if (defaultParams.house > 0) {
                    this.element.trigger("searchService.stepChange", {
                        step: 2,
                        stepData: [
                            {name: "complexId", value: defaultParams.complexId},
                            {name: "house", value: defaultParams.house},
                            {name: "entrance", value: defaultParams.entrance},
                            {name: "floor", value: defaultParams.floor}
                        ]
                    });
                } else if (defaultParams.complexId > 0) {
                    this.element.trigger("searchService.stepChange", {
                        step: 1,
                        stepData: [
                            {name: "complexName", value: defaultParams.complexName},
                            {name: "complexId", value: defaultParams.complexId}
                        ]
                    });
                }
            },

            '.js-step-link click': function (el, e) {
                if (el.hasClass("disabled")) return;
                this.element.trigger("searchService.stepChange", {
                    step: el.data("step"),
                    stepData: false
                });
            },

            '.js-next-step click': function (el, e) {
                if (el.hasClass("disabled")) return;
                this.element.trigger("searchService.stepChange", {
                    step: (this.currentStep + 1),
                    stepData: false
                });
            },

            'searchService.stepChange': function (el, e, eventData) {
                this.currentStep = eventData.step;
                this.loadStep(eventData.stepData);
                this.showStep();
            },

            showStep: function () {
                var step = this.currentStep;
                var self = this;
                this.element.find(".js-progress-tab, .js-progress-number").removeClass("active1 active2 active3").addClass("active" + step);

                var $tabList = this.element.find(".js-tab");
                $tabList.removeClass("active");
                $tabList.removeClass("success");
                $tabList.each(function (index, el) {
                    var $addDesignLink = $(el).find(".js-add-design");

                    if ((index + 1) < step) {
                        $(el).addClass("success");
                        var currentCaption;
                        switch (index + 1) {
                            case 1:
                                currentCaption = self.element.find('input[type="radio"]:checked + label').html();
                                break;
                            case 2:
                                var house = self.element.find('.js-house-select option:selected').html();
                                var entrance = self.element.find('.js-entrance-select option:selected').html();
                                var floor = self.element.find('.js-floor-select option:selected').html();
                                currentCaption = house + "/" + entrance + "/" + floor;
                                break;
                            case 3:
                                currentCaption = "кв. " + self.element.find('.js-flat-select option:selected').html();
                                break;
                        }

                        $addDesignLink.html(currentCaption);

                    } else if ((index + 1) == step) {
                        $(el).addClass("active");
                    }

                    if ((index + 1) < step) {
                        $(el).find(".js-step-link").removeClass("disabled");
                    } else {
                        $(el).find(".js-step-link").addClass("disabled");
                        $addDesignLink.html($addDesignLink.data("default-caption"));
                    }
                })
            },

            loadStep: function (stepData) {
                $.disabledDocumentScroll(false);
                var step = this.currentStep;
                var $stepContent = this.element.find(".js-step-content");

                var ajaxData;
                if (stepData == false) {
                    ajaxData = this.element.find(".js-search-service-form").serializeArray();
                } else {
                    ajaxData = stepData;
                }

                $stepContent.ajaxl({
                    url: "/search-service/step" + step,
                    data: ajaxData,
                    dataType: 'HTML',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        var stepController;
                        switch (this.currentStep) {
                            case 1:
                                stepController = "SearchStepOneForm";
                                break;
                            case 2:
                                stepController = "SearchStepTwoForm";
                                break;
                            case 3:
                                stepController = "SearchStepThreeForm";
                                break;
                        }

                        var self = this;
                        $stepContent.children().fadeOut(function () {
                            var $newData = $(data);
                            $newData.hide();
                            $stepContent.html($newData);
                            new APP.Controls[stepController](self.element.find(".js-search-service-form"), {parent: self.element});
                            $newData.fadeIn();
                        });
                    })
                });
            }
        }
    );

})(jQuery, APP);