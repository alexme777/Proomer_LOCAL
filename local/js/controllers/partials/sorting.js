(function ($, APP) {
    "use strict";

    APP.Controls.Sorting = can.Control.extend({},
        {
            init: function () {
            },

            'a click': function (el, e) {
                e.preventDefault();
                var type = el.data("type"),
                    method = el.data("method"),
                    methodExe;

                switch(method) {
                    case "asc":
                        methodExe = "desc";
                        break;
                    case "desc":
                        methodExe = "";
                        break;
                    default:
                        methodExe = "asc";
                        break;
                }
                el.data("method", methodExe);
                el.removeClass("asc desc").addClass(methodExe);
			
                this.element.trigger("list.contentUpdate", {});
            }
        }
    );

})(jQuery, APP);

(function ($, APP) {
    "use strict";

    APP.Controls.SortingItem = can.Control.extend({},
        {
            init: function () {
            },
            'a click': function (el, e) {
                e.preventDefault();
                var type = el.data("type"),
                    method = el.data("method"),
                    methodExe;
                switch(method) {
                    case "asc":
                        methodExe = "desc";
                        break;
                    case "desc":
                        methodExe = "";
                        break;
                    default:
                        methodExe = "asc";
                        break;
                }

                el.data("method", methodExe);
                window.location.href = location.pathname+'?p_sort='+methodExe;
                el.removeClass("asc desc").addClass(methodExe);

              //  this.element.trigger("list.contentUpdate", {});
            }
        }
    );

})(jQuery, APP);
