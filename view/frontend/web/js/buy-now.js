define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'underscore',
    'mage/cookies'
], function ($, $t, alert, _) {
    "use strict";

    $.widget('pmclain.oneClickButton', {
        options: {
            addToFormSelector: '#product_addtocart_form',
            isAvailableUrl: '',
            submitUrl: '',
            actionSelector: '.actions',
            buttonTemplateSelector: '#occ-template',
            buttonSelector: '#product-oneclick-button',
            confirmationSelector: '#one-click-confirmation'
        },

        cookie: 'occ_status',
        cookieEnabled: 'enabled',
        cookieDisabled: 'disabled',

        _create: function () {
            this._initButton();
        },

        _initButton: function () {
            var self = this;

            switch ($.mage.cookies.get(this.cookie)) {
                case this.cookieEnabled:
                    this._createButton();
                    break;
                case this.cookieDisabled:
                    break;
                default:
                    $.ajax({
                        url: self.options.isAvailableUrl
                    }).done(function(result) {
                        if (!result) {
                            $.mage.cookies.set(self.cookie, self.cookieDisabled);
                            return;
                        }
                        $.mage.cookies.set(self.cookie, self.cookieEnabled, {lifetime: -1});
                        self._createButton();
                    });
            }
        },

        _createButton: function () {
            var button = $(this.options.buttonTemplateSelector).html();
            this._parent().find(this.options.actionSelector).prepend(button);
            this._bind();
        },

        _bind: function () {
            var self = this;
            this._parent().find(self.options.buttonSelector).on('click touch', function() {
                if (self._parent().valid()) {
                    self._buyNow();
                }
            });
        },

        _parent: function () {
            return $(this.options.addToFormSelector);
        },

        _buyNow: function () {
            var self = this;
            self._disableButton();

            $.ajax({
                url: self.options.submitUrl,
                data: self._parent().serialize(),
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    self._orderSuccess(data);
                },
                error: function (request) {
                    self._orderError(request);
                }
            })
        },

        _orderSuccess: function (data) {
            if (data.status === 'success') {
                this._afterOrderButton();
                var orderHtml = this._getOrderTemplate(data);

                alert({
                    title: $t('Order Received'),
                    content: orderHtml
                });
            } else if (data.status === 'error') {
                this._enableButton();
                alert({
                    title: $t('Whoops...'),
                    content: data.message
                });
            }
        },

        _getOrderTemplate: function (order) {
            _.templateSettings.variable = 'order';

            var template = _.template($('script.order-template').html());
            var output = template(order);

            delete _.templateSettings.variable;

            return output;
        },

        _orderError: function (request) {
            console.log(request);
            this._enableButton();
        },

        _disableButton: function () {
            var button = this._parent().find(this.options.buttonSelector);
            button.addClass('disabled');
            button.find('span').text($t('Placing Order...'));
            button.attr('title', $t('Placing Order...'));
        },

        _afterOrderButton: function () {
            var button = this._parent().find(this.options.buttonSelector);
            button.find('span').text($t('Purchased'));
            button.attr('title', $t('Purchased'));
        },

        _enableButton: function () {
            var button = this._parent().find(this.options.buttonSelector);
            button.removeClass('disabled');
            button.find('span').text($t('One Click Checkout'));
            button.attr('title', $t('One Click Checkout'));
        }
    });

    return $.pmclain.oneClickButton;
});
