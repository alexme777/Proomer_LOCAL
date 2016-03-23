(function ($, APP) {
    "use strict";

    /**
     * Контроллер мульти выбора
     */
    APP.Controls.SelectMulti = can.Control.extend(
        {
            defaults: {
                selectedHtml: '.js-selected-html',
                selectedValues: '.js-selected-values',
                delimiter: ','
            },

            initList: function ($list) {
                $list.each(function() {
                    var $select = $(this).find('.js-select-multiple');
                    var limit = $select.data('limit');

                    $select.select2({
                        minimumResultsForSearch: -1,
                        width: 'element',
                        maximumSelectionLength: limit,
                        language: 'ru'
                    });

                    new APP.Controls.SelectMulti(this, {
                        $select: $select,
                        $selectedHtml: $(this).closest('.js-form-block').find('.js-selected-html')
                    });
                });
            }
        },
        {

            init: function() {
                if (this.options.$selectedHtml) {
                    this.$selectedHtml = this.options.$selectedHtml;
                    this.options.selectedHtml = this.options.$selectedHtml;
                    this.on();
                } else {
                    this.$selectedHtml = this.element.find(this.options.selectedHtml);
                }

                if (this.options.$selectedValues) {
                    this.$selectedValues = this.options.$selectedValues;
                } else {
                    this.$selectedValues = this.element.find(this.options.selectedValues);
                }

                // прячем
                this.initCurrentSelection();
            },

            '.js-select-multiple change': function($el) {
                if (!$el.val()) return;

                var values = $el.val();

                for (var i = 0; i < values.length; i++) {
                    var $option = this.options.$select.find('option[value="' + values[i] + '"]');
                    if ($option.length) {
                        this.addOption($option);
                    }
                }
            },

            '{selectedHtml} .js-del click': function(el) {
                var parent = el.closest('.js-tag');
                var data = parent.data('value');
                var $option = this.options.$select.find('option[value="' + data + '"]');
                this.removeOption($option);
                parent.remove();
            },

            addOption: function($option) {
                var strResult = '';
                var arValue = this.$selectedValues.val() ? this.$selectedValues.val().split(this.options.delimiter) : [];
                var value = $option.attr('value');

                if (arValue.indexOf(value) == -1) {
                    arValue.push(value);
                    strResult = arValue.join(this.options.delimiter);
                    this.$selectedValues.val(strResult);

                    this.$selectedHtml.append('<div class="tag js-tag" data-value="' + value + '">' + $option.text() + '<span class="del-tag js-del"></span></div>');

                    return true;
                }

                return false;
            },

            initCurrentSelection: function() {
                var values = this.$selectedValues.val();
                values = values ? values.split(this.options.delimiter) : [];
                this.$selectedValues.val('');

                for (var i = 0; i < values.length; i++) {
                    var $option = this.options.$select.find('option[value="' + values[i] + '"]');
                    if ($option.length) {
                        this.addOption($option);
                    }
                }
            },

            removeOption: function($option) {
                var arValue = this.$selectedValues.val().split(this.options.delimiter);
                var value = $option.attr('value');

                var index = arValue.indexOf(value);
                if (index > -1) {
                    arValue.splice(index, 1);
                }

                this.$selectedValues.val(arValue.join(this.options.delimiter));
                this.options.$select.find('option[value="' + value + '"]').attr({'selected': false});
                this.options.$select.trigger('change');
            },

            'multiSelect.reset': function () {
                this.$selectedHtml.html('');
            }
        }
    );

})(jQuery, APP);
