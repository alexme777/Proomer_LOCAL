(function ($, APP) {
    'use strict';

    APP.Controls.ProfileForm = APP.Controls.Form.extend(
        {
            pluginName: 'profileForm'
        },
        {
            onSuccess: function () {
                var title = this.element.data('title');
                var text = this.element.data('text');
                if (title && text) {
                    APP.helpers.showFancyboxMessage(title, text, 3000);
                }

                this.element.removeClass('editing');
                this.element.find('input[type="password"]').val('');
            },
            'keyup': function (el, e) {
                if (e.keyCode == 13) {
                    if (e.target.tagName == 'TEXTAREA') {
                        return;
                    }
                    this.element.submit();
                }

                if (e.keyCode == 27) {
                    $.fancybox.close();
                }

                if (!this.element.hasClass('editing')) {
                    this.element.addClass('editing');
                }
            },

            'select change': function (el, e) {
                /*if (this.element.hasClass('editing')) {
                    return;
                } else {
                    this.element.addClass('editing');
                }*/
            }
        }
    );

})(jQuery, window.APP);