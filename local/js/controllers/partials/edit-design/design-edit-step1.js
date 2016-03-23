(function ($, APP) {
    "use strict";

    APP.Controls.DesignEditStep1 = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'designEditStep1'
        },
        {
            init: function () {
                new APP.Controls.SearchStepPlanForm(this.element.find(".js-search-service-plan-form"));
            },

            '.js-switcher .js-switcher-item click': function (el, e) {
                var type = el.data("type");
                var newClass = "";
                var $byPlanForm = this.element.find(".js-by-plan"),
                    $byComplexForm = this.element.find(".js-by-complex");

                switch (type) {
                    case "plan":
                        newClass = "first";
                        $byPlanForm.toggleClass("active", true);
                        $byComplexForm.toggleClass("active", false);
                        break;
                    case "complex":
                        newClass = "second";
                        $byPlanForm.toggleClass("active", false);
                        $byComplexForm.toggleClass("active", true);
                        var $searchService = this.element.find(".js-search-service");

                        if (!$searchService.data("SearchServiceController")) {
                            $searchService.data("SearchServiceController", true);
                            new APP.Controls.SearchService(this.element.find(".js-search-service"));
                        }
                        $byPlanForm.find('input[type="radio"]').prop("checked", false);
                        $byPlanForm.find('input[type="radio"]').trigger("change");

                        break;
                }

                el.closest(".js-switcher").removeClass("first second").addClass(newClass)
            },

            '.js-switcher .js-switcher-icon click': function (el) {
                if (el.closest(".js-switcher").hasClass("first")) {
                    this.element.find(".js-switcher .js-switcher-item").last().trigger("click");
                } else {
                    this.element.find(".js-switcher .js-switcher-item").first().trigger("click");
                }
            },

            '.js-next-add-step click': function (el) {
                if (el.hasClass("disabled")) return;
                this.saveStep();
            },

            saveStep: function () {
                //Сохранение первого шага
                var $stepBlock = this.element;

                var designId = this.options.designIdInput.val(),
                    planId = $stepBlock.find('.js-by-plan input[name="planId"]:checked').val(),
                    flatId = $stepBlock.find('.js-by-complex #flatId').val();

                $stepBlock.ajaxl({
                    url: APP.urls.designEdit.saveStepFirst,
                    data: {
                        designId: designId,
                        planId: planId,
                        flatId: flatId
                    },
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.result) {
                            if (data.newId > 0) {
                                this.options.designIdInput.val(data.newId);
                                $("title").html("Редактирование проекта");
                                history.pushState({}, 'Редактирование проекта', location.origin + "/profile/design/edit/" + data.newId + "/");
                            }

                            $stepBlock.find(".js-step-body").slideUp(500, function () {
                                $stepBlock.removeClass("active");
                                $stepBlock.addClass("success");
                                $stepBlock.removeAttr("style");
                                $stepBlock.find(".js-step-value").html(data.stepValue).fadeIn(500);
                            });
                        }

                        this.element.trigger("designEdit.saveStep1Complete");
                    })
                });
            }
        }
    );

})(jQuery, APP);