(function ($, APP) {
    "use strict";

    APP.Controls.Pagination = can.Control.extend({},
        {
            init: function () {
	
            },

            'a click': function (el, e) {
                e.preventDefault();
                var page = el.data("page");

                if (page != undefined) {
                    this.element.trigger("list.contentUpdate", {page: page});
                }
            }
        }
    );

})(jQuery, APP);