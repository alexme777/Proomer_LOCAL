(function ($, APP) {
    "use strict";

    APP.Controls.RangeSlider = APP.BaseController.extend(
        {
            defaults: {},
            pluginName: 'rangeSlider',
            listenTo: 'rangeSlider.reset'
        },
        {
            init: function () {
                var $slider = this.element.find('.js-slider');
				var $slider_price = this.element.find('.js-slider-price');
                var $minValue = this.element.find('.js-min-value');
                var $maxValue = this.element.find('.js-max-value');
                this.rangeSlider = $slider.slider({
                    range: true,
					step: 50,
                    min: $slider.data("min"),
                    max: $slider.data("max"),
                    values: [$minValue.val(), $maxValue.val()],
                    slide: function (event, ui) {
                        $minValue.val(ui.values[0]);
                        $maxValue.val(ui.values[1]);
                    }
                });
				this.rangeSlider = $slider_price.slider({
                    range: true,
					step: 10,
                    min: $slider.data("min"),
                    max: $slider.data("max"),
                    values: [$minValue.val(), $maxValue.val()],
                    slide: function (event, ui) {
                        $minValue.val(ui.values[0]);
                        $maxValue.val(ui.values[1]);
                    }
                });
            },

            '.js-min-value, .js-max-value change': function (el, e) {
                var $minValue = this.element.find('.js-min-value');
                var $maxValue = this.element.find('.js-max-value');

                var minValue = parseInt($minValue.val());
                var maxValue = parseInt($maxValue.val());

                var sliderValues = [
                    this.rangeSlider.slider( "option", "min" ),
                    this.rangeSlider.slider( "option", "max" )
                ];

                if (minValue > maxValue) {
                    minValue = maxValue;
                }
                if (maxValue < minValue) {
                    maxValue = minValue;
                }

                if (minValue < sliderValues[0]) {
                    minValue = sliderValues[0];
                }

                if (maxValue > sliderValues[1]) {
                    maxValue = sliderValues[1];
                }

                if (!(minValue > 0)) {
                    minValue = sliderValues[0];
                }

                if (!maxValue > 0) {
                    maxValue = sliderValues[1];
                }

                this.rangeSlider.slider("values", [minValue, maxValue]);
                $minValue.val(minValue);
                $maxValue.val(maxValue);
            },

            'rangeSlider.reset': function () {
                var sliderValues = [
                    this.rangeSlider.slider( "option", "min" ),
                    this.rangeSlider.slider( "option", "max" )
                ];
                this.rangeSlider.slider("values", [sliderValues[0], sliderValues[1]]);
                this.element.find('.js-min-value').val(sliderValues[0]);
                this.element.find('.js-max-value').val(sliderValues[1]);
            }
        }
    );

})(jQuery, APP);