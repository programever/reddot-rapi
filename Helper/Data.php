<?php

namespace Reddot\Rapi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Sales\Model\Order;

class Data extends AbstractHelper
{
    protected $store;
    protected $configHelper;
    protected $orderRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Reddot\Rapi\Helper\Config $configHelper,
        \Magento\Sales\Api\Data\OrderInterface $orderRepository
    ) {
        $this->store = $storeManager->getStore();
        $this->configHelper = $configHelper;
        $this->orderRepository = $orderRepository;
    }

    public function makeRequestData($order, $payment){
        $requestData = [];

        $signatureData = [
            'mid' => $this->configHelper->getGeneralConfig('merchant_id'),
            'payment_type' => (string) $this->configHelper->getGeneralConfig('payment_type'),
            'order_id' => (string) $order->getOrderIncrementId(),
            'ccy' => $this->configHelper->getGeneralConfig('currency'),
            'amount' => round($order->getGrandTotalAmount(), 2)
        ];

        $requestData = [
            'api_mode' => 'redirection_hosted',
            // 'payment_channel' => $payment->getAdditionalInformation('paymentChannel'),
            'back_url' => $this->store->getUrl('customer/account'),
            'redirect_url' => $this->store->getUrl('checkout/onepage/success/'),
            'notify_url' => $this->store->getBaseUrl(). 'index.php/rest/V1/rapiCallback',
            'signature' => $this->createSignature($signatureData),
            'merchant_reference' => $payment->getAdditionalInformation('note'),
            'locale' => $this->configHelper->getGeneralConfig('location')
        ];

        $requestData = array_merge($signatureData, $requestData);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
         
        $billingAddress = $cart->getQuote()->getBillingAddress();
        $shippingAddress = $cart->getQuote()->getShippingAddress();

        $billingAddressData = $this->makeBillingAddress($billingAddress);
        $shippingAddressData = $this->makeShippingAddress($shippingAddress);

        $requestData = array_merge($requestData, $billingAddressData);
        $requestData = array_merge($requestData, $shippingAddressData);


        if ($this->configHelper->getGeneralConfig('payment_type') == 'I') {
            $requestData = array_merge($requestData, ['installment_tenor_month' => $this->configHelper->getGeneralConfig('installment_month')]);
        }

        return $requestData;
    }

    private function makeBillingAddress($billingAddress){
        $returnData = [];

        if (isset($billingAddress)){
            $returnData = [
                'bill_to_forename' => $billingAddress['firstname'],
                'bill_to_surname' => $billingAddress['lastname'],
                'bill_to_address_city' => $billingAddress['city'],
                'bill_to_address_line1' => $billingAddress['street'],
                'bill_to_address_line2' => $billingAddress['region'],
                'bill_to_address_country' => $billingAddress['region_code'],
                'bill_to_address_state' => $billingAddress['country_id'],
                'bill_to_address_postal_code' => $billingAddress['postcode'],
                'bill_to_phone' => $billingAddress['telephone']
            ];
        }

        return $returnData;
    }

    private function makeShippingAddress($shippingAddress){
        $returnData = [];

        if (isset($shippingAddress)){
            $returnData = [
                'ship_to_forename' => $shippingAddress['firstname'],
                'ship_to_surname' => $shippingAddress['lastname'],
                'ship_to_address_city' => $shippingAddress['city'],
                'ship_to_address_line1' => $shippingAddress['street'],
                'ship_to_address_line2' => $shippingAddress['region'],
                'ship_to_address_country' => $shippingAddress['region_code'],
                'ship_to_address_state' => $shippingAddress['country_id'],
                'ship_to_address_postal_code' => $shippingAddress['postcode'],
                'ship_to_phone' => $shippingAddress['telephone']
            ];
        }

        return $returnData;
    }

    public function updateOrderStatus($orderId, $status) {
        $order = $this->orderRepository->loadByIncrementId($orderId);

        if ($status == 'complete'){
            $orderState = Order::STATE_COMPLETE;
            $order->setState($orderState)->setStatus(Order::STATE_COMPLETE);
        }else if($status == 'pending'){
            $order->setStatus('pending');
        }else{
            $order->setStatus('canceled');
        }

        $order->save();
    }

    public function createSignature($signatureInfo){
        $secretKey = $this->configHelper->getGeneralConfig('secret_key');

        $fieldsForSign = array('mid', 'order_id', 'payment_type', 'amount', 'ccy');

        $aggregatedFieldStr = '';
        foreach ($fieldsForSign as $f) {
            $aggregatedFieldStr .= trim($signatureInfo[$f]);
        }

        $aggregatedFieldStr .= $secretKey;

        $signature = hash('sha512', $aggregatedFieldStr);

        return $signature;
    }

    public function createResponseSignature($response) {
        $secret_key = $this->configHelper->getGeneralConfig('secret_key');

        $response = json_decode(json_encode($response), true);
        unset($response['signature']);

        $data_to_sign = '';
        $this->recursiveGenericArraySign($response, $data_to_sign);
        $data_to_sign .= $secret_key;

        return hash('sha512', $data_to_sign);
    }

    private function recursiveGenericArraySign(&$params, &$data_to_sign){
        ksort($params);

        foreach ($params as $v) {
            if (is_array($v)) {
                recursiveGenericArraySign($v, $data_to_sign);
            } else {
                $data_to_sign .= $v;
            }
        }
    }
}