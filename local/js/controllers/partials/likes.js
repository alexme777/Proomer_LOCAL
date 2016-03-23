(function ($, APP) {
    "use strict";

	
    /**
     * Контроллер лайков
     * @type {void|*}
     */

    APP.Controls.Likes = can.Control.extend(
        {
            initList: function($list) {
                $list.each(function() {
                    if (!$(this).data('like-button')) {
                        new APP.Controls.Likes($(this));
                    }
                });
            }
        }, {

            init: function () {
				
                this.element.data('like-button', true);
                this.elementId = this.element.data("id");
            },

            'click': function (el, e) {
                e.preventDefault();
				
                if (this.element.hasClass("disabled")) {
                    return;
                }
                if (!this.element.hasClass("active")) {
                    this.like(this.elementId, APP.urls.likes.add);
                } else {
                    this.like(this.elementId, APP.urls.likes.remove);
                }
            },

            like: function(itemId, url) {
                $.ajax({
                    url: url,
                    data: {itemId: itemId, sessid: APP.sessid},
                    dataType: 'JSON',
                    type: 'POST',
                    success: this.proxy(function (data) {
                        if (data.success) {
                            this.element.toggleClass("active");
                            this.element.find('.js-value').html(data.likeCnt);
                        }
                    })
                });
            }
        }
    );

})(jQuery, APP);
