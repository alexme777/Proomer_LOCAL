(function($, APP) {
    'use strict';

    /**
     * внутренняя страница профиля - мои проекты
     **/
    APP.Controls.Page.ProfileFavouriteGoods = can.Control.extend({

        init: function() {
			alert('awdawd')
            APP.Controls.SelectMulti.initList(this.element.find('.js-select-multi'));
            if ($('html').hasClass('ie9')) {
                this.element.find('.sidebar').height(this.element.find('.sidebar').parent().height());
            }

            this.element.find('.js-tooltip').each(function(){
                var $this = $(this);
                $this.css({'left': -($this.outerWidth()/2 - 10)});
            });

            new APP.Controls.ProfileSidebar(this.element.find('.js-sidebar'));

            new APP.Controls.FilterForm(this.element.find(".js-filter-form"));
            new APP.Controls.Pagination(this.element.find(".js-pagination"));
            new APP.Controls.Sorting(this.element.find(".js-sort"));
            new APP.Controls.ViewCounter(this.element.find(".js-view-counter"));

            this.filterForm =  this.element.find('.js-profile-filter-form');
            new APP.Controls.Likes.initList(this.element.find('.js-like'));
            this.formHeight = this.filterForm.outerHeight();
        },

        '.js-delete-design click': function(el,ev) {
            APP.helpers.showFancyboxDelete('Вы уверены, что хотите удалить этот проект?', this.removeDesign, el, this);
        },

        removeDesign: function(el, self) {
            $.ajax({
                url: APP.urls.designDelete + el.data('id') + '/',
                data: {
                    sessid: APP.sessid
                },
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    self.element.trigger('list.contentUpdate', {});
                },
                complete: function (data) {
                }
            });
        },

        'list.contentUpdate': function (el, e, param) {
            var $ajaxContent = this.element.find('.js-ajax-list-content');
            var data = [];

            if (param.page > 0) {
                data.push({
                    name: "page",
                    value: param.page
                })
            } else {
                data.push({
                    name: "page",
                    value: 1
                })
            }

            if (param.viewCounter > 0) {
                data.push({
                    name: "viewCounter",
                    value: param.viewCounter
                })
            }

            this.element.find(".js-sort a").each(function(){
                data.push({
                    name: "sort[" + $(this).data("type") + "]",
                    value: $(this).data("method")
                })
            });

            $.merge(data, this.element.find('.js-filter-form').serializeArray());

            var self = this;

            $ajaxContent.ajaxl({
                topPreloader: true,
                url: location.pathname,
                data: data,
                dataType: 'HTML',
                type: 'POST',
                success: this.proxy(function (data) {
                    $ajaxContent.html(data);
                    if ($ajaxContent.find('.not-found').length > 0) {
                        self.element.find('.filter').css({'visibility': 'hidden'});
                    } else {
                        self.element.find('.filter').css({'visibility': 'visible'});
                    }
                    new APP.Controls.Pagination(this.element.find(".js-pagination"));
                    $.scrollTo(this.element.find('.profile-content'), 500);
                    new APP.Controls.Likes.initList(this.element.find('.js-like'));
                })
            });
        },

        '.js-switcher-filter click': function(el) {
            el.toggleClass('active');
            if (el.hasClass('active')) {
                el.html('Свернуть фильтр');
                this.filterForm.animate({"height": this.formHeight});
                this.filterForm.find('.js-form-wrapper').fadeIn();

                var self = this;
                setTimeout(function () {
                    self.filterForm.css({"height": "auto"});
                }, 1000);
            } else {
                this.formHeight = this.filterForm.outerHeight();
                el.html('Развернуть фильтр');
                this.filterForm.animate({"height": '70'});
                this.filterForm.find('.js-form-wrapper').fadeOut();
            }
        }

    });

})(jQuery, window.APP);