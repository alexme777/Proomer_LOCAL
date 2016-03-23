(function($, APP) {
    'use strict';

    /**
     * Базовый контроллер для элементов, которые надо инициализировать списком
     */
    APP.BaseController = can.Control.extend({
        initList: function($list, options) {
            var classObject = this;

            if (!classObject.controllerName) {
                classObject.controllerName = 'controller-' + Math.random();
            }

            $list.each(function() {
                if ($(this).data(classObject.controllerName) !== true) {
                    if (options != undefined) {
                        new classObject($(this), options);
                    } else {
                        new classObject($(this));
                    }
                }
            });
        }
    }, {
        setup: function(element, options) {
            element.data(this.constructor.controllerName, true);
            return can.Control.prototype.setup.apply(this, [element, options]);
        },
        init: function() { }
    });

})(jQuery, window.APP);