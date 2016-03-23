(function ($, APP) {
    'use strict';

    APP.Controls.RegistrationForm = APP.Controls.Form.extend(
        {
            pluginName: 'registrationForm'
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