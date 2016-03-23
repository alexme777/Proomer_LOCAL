(function ($, APP) {
    'use strict';

    APP.Controls.TypeForm = APP.Controls.Form.extend(
        {
            pluginName: 'typeForm'
        },
        {
            onSuccess: function () {
                window.location.href = APP.urls.profile;
            }
        }
    );

})(jQuery, window.APP);