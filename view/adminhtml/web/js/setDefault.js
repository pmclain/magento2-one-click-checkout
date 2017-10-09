define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui'
], function ($, alert) {
    'use strict';

    $.widget('pmclain.setDefault', {
        options: {
            url: '',
            elementId: '',
            successText: '',
            failedText: '',
            fieldMapping: ''
        },

        /**
         * Bind handlers to events
         */
        _create: function () {
            this._on({
                'click': $.proxy(this._setDefault, this)
            });
        },

        /**
         * Method triggers an AJAX request to check search engine connection
         * @private
         */
        _setDefault: function () {
            var result = this.options.failedText,
                element =  $('#' + this.options.elementId),
                self = this,
                params = {},
                msg = '';

            element.removeClass('success').addClass('fail');

            params['form_key'] = FORM_KEY;

            $.ajax({
                url: this.options.url,
                showLoader: true,
                data: params
            }).done(function (response) {
                if (response.success) {
                    element.removeClass('fail').addClass('success');
                    result = self.options.successText;
                } else {
                    msg = response.errorMessage;

                    if (msg) {
                        alert({
                            content: $.mage.__(msg)
                        });
                    }
                }
            }).always(function () {
                $('#' + self.options.elementId + '_result').text(result);
            });
        }
    });

    return $.pmclain.setDefault;
});
