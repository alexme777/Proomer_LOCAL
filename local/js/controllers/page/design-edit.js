(function($, APP) {
    'use strict';

    /**
     * Добавление дизайна
     **/
    APP.Controls.Page.DesignEdit = can.Control.extend({
        defaults: {
        },
        pluginName: 'designEdit',
        listenTo: ['designEdit.saveStep1Complete', 'designEdit.saveStep2Complete']
    }, {

        init: function() {
            this.runStepController(this.element.find(".js-first-step"), "DesignEditStep1");
            new APP.Controls.ProfileSidebar(this.element.find('.js-sidebar'));

            if (this.element.find("#designId").val().length == 0) {
                this.element.find(".js-second-step, .js-third-step, .js-fourth-step").addClass("disabled");
            }
        },

        '.js-step-title click': function (el, e) {
            if (!(el.closest(".js-step").hasClass("disabled"))) {
                this.runStep(el.closest(".js-step").data("step"));
            }
        },

        'designEdit.saveStep1Complete': function () {
            var $nextStepBlock = this.element.find(".js-second-step");

            this.runStepController($nextStepBlock, "DesignEditStep2");

            $nextStepBlock.removeClass("success").addClass("active");
            $nextStepBlock.find(".js-step-body").slideDown(500);
        },

        'designEdit.saveStep2Complete': function () {
            var $nextStepBlock = this.element.find(".js-third-step");

            this.runStepController($nextStepBlock, "DesignEditStep3");

            this["DesignEditStep3"].getCurrentRoomForm();
            $nextStepBlock.removeClass("success").addClass("active");
            $nextStepBlock.find(".js-step-body").slideDown(500);
        },

        'designEdit.saveStep3Complete': function () {
            var $nextStepBlock = this.element.find(".js-fourth-step");

            this.runStepController($nextStepBlock, "DesignEditStep4");

            $nextStepBlock.removeClass("success").addClass("active");
            $nextStepBlock.find(".js-step-body").slideDown(500);
        },

        runStepController: function ($element, controller) {
            if (!$element.data(controller)) {
                $element.data(controller, true);
                this[controller] = new APP.Controls[controller]($element, {
                    parent: this.element,
                    designIdInput: this.element.find('#designId')
                });
            }

            if (this.element.find("#designId").val().length > 0) {
                this.element.find(".js-second-step, .js-third-step, .js-fourth-step").removeClass("disabled");
            }
        },

        runStep: function (stepNumber) {
            var $activeStep = this.element.find(".js-step").filter(".active");
            if ($activeStep.data("step") == stepNumber) return;

            $activeStep.find(".js-step-body").slideUp(500, function () {
                $activeStep.removeClass("active");
                $activeStep.addClass("success");
                $activeStep.removeAttr("style");
            });

            switch (stepNumber) {
                case 1:
                    this.element.find(".js-first-step").removeClass("success").addClass("active");
                    this.element.find(".js-first-step").find(".js-step-body").slideDown(500);
                    break;
                case 2:
                    this.element.trigger("designEdit.saveStep1Complete");
                    break;
                case 3:
                    this.element.trigger("designEdit.saveStep2Complete");
                    break;
                case 4:
                    this.element.trigger("designEdit.saveStep3Complete");
                    break;
            }
        }
    });

})(jQuery, window.APP);