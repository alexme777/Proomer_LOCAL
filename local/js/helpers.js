(function($, APP) {
    'use strict';

    APP.helpers = {};

    APP.urls = {
        sliderRefresh: '/slider/refresh/',
        houseFlatCounter: '/complex/flat-count/',
        searchComplexByName: '/search-service/step1/search/',
        searchPlanByName: '/search-service/step-plan/search/',
        profile: '/profile/',
        removeAvatar: '/profile/remove-photo/',
        entranceByHouse: '/search-service/step2/entrance/',
        floorByEntrance: '/search-service/step2/floor/',
        designEdit: {
            saveStepFirst: '/profile/design/save/first/',
            saveStepSecond: '/profile/design/save/second/',
            uploadFile: '/profile/design/upload-file/',
            deleteFile: '/profile/design/delete-file/',
            uploadRoomImage: '/room/upload-image/',
            deleteRoomImage: '/room/delete-image/',
            addRoom: '/room/add-room/',
            deleteRoom: '/room/delete-room/',
            roomFormStep3: '/room/room-form/',
            publishDesign: '/profile/design/publish-design/',
            uploadPlanImage: '/profile/design/add-plan/',
            deletePlanImage: '/profile/design/delete-plan/'
        },
        designDelete: '/profile/design/delete/',
		providerEdit: {
            saveStepFirst: '/profile/provider/save/first/',
            saveStepSecond: '/profile/provider/save/second/',
            uploadFile: '/profile/design/upload-file/',
			uploadFile: '/profile/provider/upload-file/',
            deleteFile: '/profile/provider/delete-file/',
            uploadRoomImage: '/room/upload-image/',
            deleteRoomImage: '/room/delete-image/',
            addRoom: '/room/add-room/',
            deleteRoom: '/room/delete-room/',
            roomFormStep3: '/room/room-form/',
            publishDesign: '/profile/provider/publish-design/',
            uploadPlanImage: '/profile/provider/add-plan/',
            deletePlanImage: '/profile/provider/delete-plan/'
        },
        providerDelete: '/profile/provider/delete/',
        likes: {
            add: '/design/like/add/',
            remove: '/design/like/remove/'
        },
		likesShop: {
            add: '/shop/like/add/',
            remove: '/shop/like/remove/'
        },
        basket: {
            index: '/basket/',
            addDesign: '/basket/design/add/',
            deleteDesign: '/basket/design/delete/',
			addGoods: '/basket/goods/add/',
			addProject: '/basket/project/add/',
            deleteGoods: '/basket/goods/delete/'
        },
        order: {
            success: '/order/success/'
        }
    };


    /**
     *
     */
    APP.helpers.isTouch = (function () {
        var isIpad    = (navigator.platform.indexOf("iPad")    != -1) || (navigator.userAgent.match(/iPad/i)     != null);
        var isIphone  = (navigator.platform.indexOf("iPhone")  != -1) || (navigator.userAgent.indexOf("iPhone")  != -1);
        var isIpod    = (navigator.platform.indexOf("iPod")    != -1) || (navigator.userAgent.indexOf("iPod")    != -1);
        var isAndroid = (navigator.platform.indexOf("Android") != -1) || (navigator.userAgent.indexOf("Android") != -1);
        return isIpad || isIphone || isIpod || isAndroid || navigator.msMaxTouchPoints;
    }());

    APP.helpers.getCookie = function (name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    };

    APP.helpers.setCookie = function (name, value, options) {
        options = options || {};

        var expires = options.expires;

        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        var updatedCookie = name + "=" + value;

        for (var propName in options) {
            updatedCookie += "; " + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }
        document.cookie = updatedCookie;
    };

    APP.helpers.showFancyboxMessage = function (title, subtitle, timeout, location) {
        var $successFancybox = $('#success');
        if (title) {
            $successFancybox.find('h2').html(title);
        }

        if (subtitle) {
            $successFancybox.find('.descr').html(subtitle);
        }

        $.fancybox.open([{
            href: '#success',
            padding: 0,
            helpers: {
                media: {},
                overlay: {
                    locked: false
                }
            },
            tpl: {
                closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"></a>'
            },
            beforeClose: function() {
                if (location !== undefined) {
                    window.location.href = location;
                }
            }
        }]);

        if (timeout) {
            setTimeout(function () {
                $.fancybox.close();
            }, timeout);
        }
    };

    APP.helpers.showFancyboxDelete = function (text, func, param, self) {
        var $deleteFancybox = $('#delete-popup');
        if (text) {
            $deleteFancybox.find('.popup-inner').html(text);
        }

        $.fancybox.open([{
            href: '#delete-popup',
            padding: 0,
            helpers: {
                media: {},
                overlay: {
                    locked: false
                }
            },
            tpl: {
                closeBtn: '<a title="Закрыть" class="fancybox-item fancybox-close" href="javascript:;"></a>'
            },
            beforeShow: function () {
                $('#ok').unbind('click');
                $('#cancel').unbind('click');
                $('#ok').bind('click', function() {
                    func(param, self);
                    $.fancybox.close();
                });
                $('#cancel').bind('click',function() {
                    $.fancybox.close();
                });
            }
        }]);
    };

    APP.helpers.callMethod = function (method, $element) {
        if (APP.Controls[method] && $element && !$element.data("method-" + method)) {
            $element.data("method-" + method, true);
            new APP.Controls[method]($element);
        }
    };

    APP.helpers.debounce = function(wait, callback, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) callback.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) callback.apply(context, args);
        };
    };

    APP.helpers.parseGetParams = function () {
        var getParams = {};
        var getString = window.location.search.substring(1).split("&");
        for (var i=0; i<getString.length; i++) {
            var getVar = getString[i].split("=");
            getParams[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1];
        }
        return getParams;
    };

    APP.helpers.getWordForm = function(count, forms) {
        if (!forms[0]) forms[0] = "";
        if (!forms[1]) forms[1] = "а";
        if (!forms[2]) forms[2] = "ов";

        var n100 = count % 100;
        var n10 = count % 10;

        if ((n100 > 10) && (n100 < 21)) {
            return forms[2];
        } else if ((!n10) || (n10 >= 5)) {
            return forms[2];
        } else if (n10 == 1) {
            return forms[0];
        }

        return forms[1];
    };

    /**
     *
     * @param price
     * @returns {string}
     * @param addRouble
     */
    APP.helpers.price = function(price, addRouble) {
        if (typeof addRouble === 'undefined') addRouble = true;
        return APP.helpers.number_format(price, 0, '', ' ') + (addRouble ? ' <i class="ruble"></i>' : '');
    };

    /**
     * PHP number_format
     */
    APP.helpers.number_format = function(e,n,t,i){e=(e+"").replace(/[^0-9+\-Ee.]/g,"");//noinspection JSUnusedAssignment
        var r=isFinite(+e)?+e:0,a=isFinite(+n)?Math.abs(n):0,o="undefined"==typeof i?",":i,d="undefined"==typeof t?".":t,u="",f=function(e,n){var t=Math.pow(10,n);return""+(Math.round(e*t)/t).toFixed(n)};//noinspection CommaExpressionJS
        return u=(a?f(r,a):""+Math.round(r)).split("."),u[0].length>3&&(u[0]=u[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,o)),(u[1]||"").length<a&&(u[1]=u[1]||"",u[1]+=new Array(a-u[1].length+1).join("0")),u.join(d)};

})(jQuery, window.APP);