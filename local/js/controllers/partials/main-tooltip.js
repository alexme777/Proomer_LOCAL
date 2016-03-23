(function ($, APP) {
    "use strict";

    APP.Controls.MainTooltip = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'mainTooltip'
        },
        {
            init: function () {

            },
            'mouseover': function (el, e) {
                this.tooltip = $(can.view.render("tooltipDrop", {description: el.data("description")}));

                this.tooltip.addClass("dialog-open");
                this.options.pageObj.append(this.tooltip);

                var posTop = el.offset().top - this.tooltip.outerHeight() - 20,
                    posLeft = (el.offset().left);

                this.tooltip.css({
                    top: posTop,
                    left: (posLeft - (this.tooltip.innerWidth() / 2) + 10)
                });
            },
            'mouseout': function () {
                this.tooltip.removeClass("dialog-open");
                this.tooltip.addClass("dialog-close");

                setTimeout(this.proxy(function () {
                    this.options.pageObj.find(".dialog-close").remove();
                }), 250);
            }
        }
    );

})(jQuery, APP);