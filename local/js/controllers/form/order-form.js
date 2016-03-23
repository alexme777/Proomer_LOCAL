(function ($, APP) {
    'use strict';

    APP.Controls.OrderForm = APP.Controls.Form.extend(
        {
            pluginName: 'orderForm'
        },
        {
            onSuccess: function (data) {
                /*var title = this.element.data('title');
                var text = this.element.data('text');
                APP.helpers.showFancyboxMessage(title, text, 3000);*/
				window.location.href = APP.urls.order.success + data.orderId;
            }
        }
    );

})(jQuery, window.APP);