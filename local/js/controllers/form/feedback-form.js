(function ($, APP) {
    'use strict';

    APP.Controls.FeedbackForm = APP.Controls.Form.extend(
        {
            pluginName: 'feedbackForm'
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