(function ($, APP) {
    "use strict";

    /**
     * Контроллер сайдбара в профиле
     * @type {void|*}
     */
    APP.Controls.ProfileSidebar = can.Control.extend(
        {

            init: function () {
                if ($('html').hasClass('ie9')) {
                    this.element.find('.sidebar').height(this.element.find('.sidebar').parent().height());
                }
            },

            '.sidebar .profile-menu a click': function($el) {
                $el.closest('ul').find('.active').removeClass('active');
                $el.addClass('active');
            },

            '.js-upload-avatar click': function (el) {
                el.closest('.panel').find('input[type=file]').click();
            },

            '.js-image change': function (el, ev) {
                var $form = el.closest('#upload-image');

                var options = {
                    beforeSend: function()
                    {
                        el.closest('.avatar').addClass('ajax-loading');
                    },
                    success: function(data)
                    {
                        if (data.success) {
                            el.closest('.avatar').css({'background-image': 'url("' + data.avatar + '")'});
                            $('.js-profile-open').css({'background-image': 'url("' + data.headerAvatar + '")'});
                            el.closest('.avatar').removeClass('ajax-loading');
                            if (el.closest('.avatar').find('.js-remove-avatar').length == 0) {
                                el.closest('.avatar').find('.js-upload-avatar').after('<div><span class="delete under-link js-remove-avatar">удалить фото</span></div>');
                                el.closest('.avatar').find('.js-upload-avatar').html('загрузить новое фото');
                            }
                        } else {
                            APP.helpers.showFancyboxMessage(data.messageTitle, data.messageText, 3000);
                            el.val('');
                            el.closest('.avatar').removeClass('ajax-loading');
                        }
                    }
                };

                $form.ajaxForm(options);
                $form.submit();
            },

            '.js-remove-avatar click': function(el,ev) {
                APP.helpers.showFancyboxDelete('Вы уверены, что хотите удалить эту фотографию?', this.removePhoto, el, this);
            },

            removePhoto: function(el) {
                el.closest('.avatar').addClass('ajax-loading');
                $.ajax({
                    url: APP.urls.removeAvatar,
                    data: {sessid: APP.sessid},
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            el.closest('.avatar').css({'background-image': 'url("' + data.avatar + '")'});
                            $('.js-profile-open').css({'background-image': 'url("' + data.headerAvatar + '")'});
                        }
                    },
                    complete: function (data) {
                        if (data.success) {
                            el.closest('.avatar').removeClass('ajax-loading');
                            el.closest('.avatar').find('.js-upload-avatar').html('загрузить фото');
                            el.remove();
                        }
                    }
                });
            }
        }
    );

})(jQuery, APP);
