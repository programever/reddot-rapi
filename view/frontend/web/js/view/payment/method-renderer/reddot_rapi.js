/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Reddot_Rapi/js/action/set-payment-method-action',
        'mage/url'
    ],
    function (ko, $, Component, setPaymentMethodAction) {
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'Reddot_Rapi/payment/form',
                note: ''
            },

            afterPlaceOrder: function () {
                setPaymentMethodAction(this.messageContainer);
                return false;
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'note'
                    ]);
                return this;
            },

            getCode: function() {
                return 'reddot_rapi';
            },

            getData: function () {
                var data = {
                    'method': this.item.method,
                    'additional_data': {
                        'note': this.note()
                    }
                };

                return data;
            }
        });
    }
);