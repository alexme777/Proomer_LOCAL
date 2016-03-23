(function ($, APP) {
    'use strict';

    APP.Controls.ChangePassForm = APP.Controls.Form.extend(
        {
            pluginName: 'changePassForm'
        },
        {
            onSuccess: function () {
                var title = this.element.data('title');
                var text = this.element.data('text');
                APP.helpers.showFancyboxMessage(title, text, 3000, APP.urls.profile);
            }
        }
    );

})(jQuery, window.APP);