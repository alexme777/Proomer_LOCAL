(function ($, APP) {
    "use strict";

    APP.Controls.PreloaderController = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'preloaderController'
        },
        {
            init: function () {},

            addPreloader: function () {
                var tooltip = can.view.render("preloaderSvg");
                if (this.element.find(".preloaderContent").length > 0) {
                    this.element.find(".preloaderContent").remove();
                }
                this.element.append(tooltip);

                var drop = document.getElementById('drop');
                var drop2 = document.getElementById('drop2');

                var tl = new TimelineMax({
                    repeat: -1,
                    paused: false,
                    repeatDelay: 0,
                    immediateRender: false
                });

                tl.timeScale(3);

                tl.to(drop, 4, {
                    attr: {
                        cx: 170,
                        rx: '+=7',
                        ry: '+=7'
                    },
                    ease: Back.easeInOut.config(2)
                })
                    .to(drop2, 4, {
                        attr: {
                            cx: 170
                        },
                        ease: Power1.easeInOut
                    }, '-=4')
                    .to(drop, 4, {
                        attr: {
                            cx: 50,
                            rx: '-=7',
                            ry: '-=7'
                        },
                        ease: Back.easeInOut.config(3)
                    })
                    .to(drop2, 4, {
                        attr: {
                            cx: 50,
                            rx: '-=7',
                            ry: '-=7'
                        },
                        ease: Power1.easeInOut
                    }, '-=4')

            },

            removePreloader: function () {
                this.element.find(".preloaderContent").remove();
            }
        }
    );

})(jQuery, APP);