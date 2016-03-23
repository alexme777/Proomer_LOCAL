// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());



(function($) {
    /**
     *
     * @param param
     */
    $.fn.ajaxl = function (param) {
        var $el = this;
        if ($el.closest('.js-step-content').length > 0) {
            $el = $el.closest('.js-step-content')
        }

        var preloader = new APP.Controls.PreloaderController($el);
        var ajaxParam = $.extend({
            topPreloader: false,
            type: "POST",
            dataType:"html",
            beforeSend: function () {
                if (param.topPreloader) {
                    $el.addClass("ajax-loading top-preloader");
                } else {
                    $el.addClass("ajax-loading");
                }
                preloader.addPreloader();
            },
            complete: function () {
                setTimeout(function(){
                    if (param.topPreloader) {
                        $el.removeClass("ajax-loading top-preloader");
                    } else {
                        $el.removeClass("ajax-loading");
                    }
                    preloader.removePreloader();
                }, 300);
            }
        }, param);

        $.ajax(ajaxParam);
    };

    $.fn.slideRemove = function () {
        var $el = $(this);
        $el.slideUp(function () {
            $el.remove();
        });
    };

    $.fn.fadeRemove = function () {
        var $el = $(this);
        $el.fadeOut(function () {
            $el.remove();
        });
    };


    // Enter only numbers
    $.fn.numericOnly=function(){return this.each(function(){$(this).bind("change keyup input click",function(){this.value.match(/[^0-9.]/g)&&(this.value=this.value.replace(/[^0-9.]/g,""))})})};

    $.disabledDocumentScroll = function (disabled) {
        if (disabled == undefined) {
            disabled = true;
        }

        if (disabled) {
            document.onmousewheel = document.onwheel = function () {
                return false;
            };
            document.addEventListener("MozMousePixelScroll", function () {
                return false
            }, false);
            document.onkeydown = function (e) {
                if (e.keyCode >= 33 && e.keyCode <= 40) return false;
            }
        } else {
            document.onmousewheel = document.onwheel = function () {
                return true;
            };
            document.addEventListener("MozMousePixelScroll", function () {
                return true
            }, true);
            document.onkeydown = function (e) {
                if (e.keyCode >= 33 && e.keyCode <= 40) return true;
            }
        }
    };

    $.parseGet = function (val) {
        var result = false,
            tmp = [];
        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }

}(jQuery));

