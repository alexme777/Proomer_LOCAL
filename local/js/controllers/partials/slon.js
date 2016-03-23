(function ($, APP, undefined) {
    "use strict";

    APP.Controls.Slon = can.Control.extend(
        {
            defaults: {
            }
        },
        {
            init: function () {
                this.animatedSlon = this.element.find('.js-slon-animation img');
                this.counter = 1;
                this.src = this.animatedSlon.attr('src');
            },

            '.slon mouseover': function ($el) {
                if ($el.hasClass('animated')) {
                    return false;
                }
                $el.addClass('animated');

                this.animatedSlon.attr({'src': this.src + '?' + this.counter});
                this.counter++;

                setTimeout(function() {
                    $el.removeClass('animated');
                }, 5000);
            }
        }

    );

})(jQuery, APP);