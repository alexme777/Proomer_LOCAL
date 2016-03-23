(function($, APP) {
    'use strict';

    /**
     * Контроллер приложения, запускает контроллеры страниц
     **/
    APP.Controls.Application = can.Control.extend({

        /**
         *
         **/
        init: function() {
			
            this.initPageController();
            this.initForms();
            new APP.Controls.HeaderMenu(this.element.find('.js-header-menu'));
			new APP.Controls.HeaderMenu(this.element.find('.js-header2-menu'));
            APP.Controls.FancyboxLink.initList(this.element.find(".js-fancybox"));
            APP.Controls.MainTooltip.initList(this.element.find(".js-tooltip"), {pageObj: this.element});
            new APP.Controls.Slon(this.element.find('footer'));
            new APP.Controls.BasketSidebar(this.element.find(".js-sidebar-basket"));
        },

        '.js-scroll-top click': function () {
            $.scrollTo(0, 500);
        },

        /**
         *
         **/
        initPageController: function() {
            var pagePlugin = can.capitalize(can.camelize(this.element.data('page-type')));
            if (APP.Controls.Page[pagePlugin]) {
				
                new APP.Controls.Page[pagePlugin](this.element);
            }
        },

        initForms: function() {
            new APP.Controls.FeedbackForm(this.element.find(".js-feedback-form"));
			new APP.Controls.OrderForm(this.element.find(".js-order-form"));
            new APP.Controls.RegistrationForm(this.element.find(".js-registration-form"));
            new APP.Controls.AuthForm(this.element.find(".js-auth-form"));
            new APP.Controls.Form(this.element.find(".js-remind-form"));
            new APP.Controls.ChangePassForm(this.element.find(".js-change-form"));
            new APP.Controls.TypeForm(this.element.find(".js-type-form"));
            new APP.Controls.Form(this.element.find(".js-form"));
        }

    });

    /**
     * Bootstrap
     */
    $(function() {
        new APP.Controls.Application($('body'));
    });

})(jQuery, window.APP);