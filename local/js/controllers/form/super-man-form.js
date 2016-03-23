(function ($, APP) {
    'use strict';

    APP.Controls.SupermanForm = APP.Controls.Form.extend(
        {
            pluginName: 'supermanForm'
        },
        {
            onSuccess: function () {
                var title = this.element.data('title');
                var text = this.element.data('text');
                APP.helpers.showFancyboxMessage(title, text, 3000);
            }
        }
    );

})(jQuery, window.APP);