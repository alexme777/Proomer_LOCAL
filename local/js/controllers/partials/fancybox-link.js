(function ($, APP) {
    "use strict";

    APP.Controls.FancyboxLink = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'fancyboxLink'
        },
        {
            init: function () {
                var whiteClass = "";
                if (this.element.data("white-close") == 1) {
                    whiteClass = " white";
                }

                var method = false,
                    element = false;

                if (this.element.data("user-call") != undefined) {
                    var userCall = this.element.data("user-call");
                    method  = userCall.method;
                    element = userCall.element;
                }

                var test = "SearchService";
                var el = ".js-search-service";

                this.element.fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    padding: 0,
                    helpers : {
                        media : {},
                        overlay: {
                            locked: false
                        }
                    },
                    tpl: {
                        closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close'+whiteClass+'" href="javascript:;"></a>'
                    },

                    afterShow: function() {
                        var fancyboxOuter = $(".fancybox-outer");
                        setTimeout(function () {
                            $('.fancybox-outer input[type!="hidden"],.fancybox-outer  textarea,.fancybox-outer  select').first().focus();
                        }, 200);

                        APP.helpers.callMethod(method, fancyboxOuter.find(element));
                    }
                });
            }
        }
    );

})(jQuery, APP);