(function ($, APP, undefined) {

    /**
     * Скрипты для меню в headere
     **/
    APP.Controls.HeaderMenu = can.Control.extend({

        init: function () {
          
			
			this.element.find('.js-select-city:eq(0)').select2({
                minimumResultsForSearch: 500,
                width: 'element',
               // dropdownParent: $('.select-city-container2')
            });
			this.element.find('.js-select-city:eq(1)').select2({
                minimumResultsForSearch: 500,
                width: 'element',
                //dropdownParent: $('.select-city-container')
            });
        },

        '.js-open click': function(el) {
            if (el.parent().hasClass('opened')) {
				el.removeClass('_opened');
				$("body").css("overflow-x","auto");
				el.parent().find('.dropdown').removeClass('open');
				el.parent().find('.b-sidebar').removeClass('_opened');
                el.parent().removeClass('opened');
            } else {
				el.addClass('_opened');
				$("body").css("overflow-x","hidden");
				el.parent().find('.dropdown').addClass('open');
				el.parent().find('.b-sidebar').addClass('_opened');
                el.parent().addClass('opened');
            }
        },

        '.js-basket click': function() {
            this.element.trigger("openSidebar");
        },

        '.js-select-city change': function (el, e) {
            $.ajax({
                url: "/user/change-location/",
                data: {userLocation: el.val()},
                dataType: 'JSON',
                type: 'POST',
                success: this.proxy(function (data) {
                    if (data.success) {
                        window.location.href = window.location.href.split("?")[0]; //очищаем get-параметры для complex-list
                    }
                })
            });
        },

        '.js-profile-open click': function(el) {
            el.parent().addClass('opened');
        },

        '.js-close click': function(el) {
            el.closest('.login').removeClass('opened');
        },

        '{window} click': function(el, ev) {
            this.closeMenu(el,ev);
        },

        '{window} touchstart': function(el, ev) {
            this.closeMenu(el,ev);
        },

        '{window} basketCountChanged': function ($el, ev, count) {
            this.$basketCounter = this.element.find('.js-basket-count');
            if (count > 0) {
                this.$basketCounter.css({'display': 'block'});
                this.$basketCounter.html(count);
            } else {
                this.$basketCounter.css({'display': 'none'});
            }

        },

        'closeMenu': function (el,ev) {
            /*var $target = $(ev.target);
            if ($target.closest(".js-dropdown-wrapper.opened").length) return;

            var $openedMenu = this.element.find(".js-dropdown-wrapper.opened");
				
            if ($openedMenu.length) {
                $openedMenu.removeClass("opened");
                $openedMenu.find('.js-dropdown').fadeOut("fast");

                ev.stopPropagation();
                return false;
            }*/
        }

    });

})(jQuery, window.APP);