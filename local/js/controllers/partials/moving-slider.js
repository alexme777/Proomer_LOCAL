(function ($, APP, undefined) {

    /**
     * Слайдер на главной странице
     **/
    APP.Controls.MovingSlider = can.Control.extend({

        init: function () {
            this.pause = this.element.data('pause') * 1000;
            this.element.bxSlider({
                speed: 500,
                pause: this.pause,
                mode: 'fade',
                minSlides: 1,
                moveSlides: 1,
                slideMargin: 0,
                auto: false,
                pager: true,
                controls: false,
                nextText: '',
                prevText: '',
                onSlideAfter: this.proxy(this.onSlideAfter),
                onSlideBefore: this.proxy(this.onSlideBefore)
            });

            this.onSlideAfter(this.element.find('.slide').first());
        },

        onSlideAfter: function($slide) {
            if (this.interval != undefined) {
                clearInterval(this.interval)
            }

            $slide.find('.slide-bg').addClass('scaling');

            this.element.startAuto();
        },

        onSlideBefore: function($slide) {
            this.element.stopAuto();
            $slide.find('.slide-bg').removeClass('scaling');

            this.element.startAuto();
        }
    });

})(jQuery, window.APP);