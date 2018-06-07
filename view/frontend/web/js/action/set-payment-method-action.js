define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer, fullScreenLoader) {
        'use strict';
        return function (messageContainer) {
            var serviceUrlCreate,serviceUrl,payload;
             /**
              * Save  values .
              */
            var body = $('body').loader();
            body.loader('show'); 
            serviceUrl = 'rest/V1/rapiGetRedirectUrl';
            payload = {};
            storage.post(
                serviceUrl,
                JSON.stringify(payload)
            ).done(function (response) {
                var data = response[0];
                $.mage.redirect(data.url);
            }).fail(function (response) {
            });
        };
    }
);