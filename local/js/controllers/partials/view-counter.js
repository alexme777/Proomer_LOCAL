(function ($, APP) {
    "use strict";

    APP.Controls.ViewCounter = can.Control.extend({},
        {
            init: function () {
            },

            'a click': function (el, e) {
                e.preventDefault();
                var value = el.data("value");

                if (value != undefined) {
                    this.element.find("a").removeClass("active");
                    el.addClass("active");
                    this.element.trigger("list.contentUpdate", {viewCounter: value});
                }
            }
        }
    );

    APP.Controls.ViewCounterItem = can.Control.extend({},
        {
            init: function () {
            },

            'a click': function (el, e) {
                e.preventDefault();
                var value = el.data("value");

                if (value != undefined) {
                    this.element.find("a").removeClass("active");
                    window.location.href = location.pathname+'?view='+value;
                    el.addClass("active");
                }
            }
        }
    );

})(jQuery, APP);