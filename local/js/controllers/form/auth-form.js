(function ($, APP) {
    'use strict';

    APP.Controls.AuthForm = APP.Controls.Form.extend(
        {
            pluginName: 'authForm'
        },
        {
            onSuccess: function () {
                window.location.href = APP.urls.profile;
            }
        }
    );

})(jQuery, window.APP);