<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Reddot\Rapi\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Reddot\Rapi\Helper\LogHelper;
use Reddot\Rapi\Helper\Data;
use Reddot\Rapi\Helper\Config;

class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    private $dataHelper;
    private $configHelper;
    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        Data $dataHelper,
        \Reddot\Rapi\Helper\Config $configHelper,
        ConfigInterface $config
    ) {
        $this->config = $config;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new CouldNotSaveException(__('Payment data object should be provided'));
        }

        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();

        $requestParams = $this->dataHelper->makeRequestData($order, $payment);

        $response = $this->callApi('POST', $requestParams);        

        if ($response->response_code != '0') {
            throw new CouldNotSaveException(__($response->response_msg));
        }

        $createResponseSignature = $this->dataHelper->createResponseSignature($response);

        if ($response->signature != $createResponseSignature) {
            throw new CouldNotSaveException(__('Invalid signature'));
        }

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $session = $om->get('Magento\Customer\Model\Session');
        $session->setPaymentUrl($response->payment_url);

        return [
            'TXN_TYPE' => 'A',
            'INVOICE' => $requestParams['order_id'],
            'AMOUNT' => $requestParams['amount'],
            'CURRENCY' => $requestParams['ccy'],
            'EMAIL' => '',
            'MERCHANT_KEY' => $this->config->getValue(
                'merchant_gateway_key',
                $order->getStoreId()
            )
        ];
    }

    private function callApi($method, $param) {
        $url = 'https://secure-dev.reddotpayment.com/service/payment-api';

        if ($this->configHelper->getGeneralConfig('environment') == 'production') {
            $url = 'https://secure.reddotpayment.com/service/payment-api';
        }

        $chSign = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER =>  array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        ];

        if ($method !== 'GET' && $param) {
            $options[CURLOPT_POSTFIELDS] = json_encode($param);
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }

        curl_setopt_array($chSign, $options);

        $res = curl_exec($chSign);
        $error = curl_error($chSign);

        if ($error) {
            (new LogHelper())->debug('error:'.json_encode($error));
            throw new CouldNotSaveException(__($error->getMessage()));
        }

        $resStatus = curl_getinfo($chSign, CURLINFO_HTTP_CODE);

        if ($resStatus < 200 || $resStatus >= 300) {
            (new LogHelper())->debug('resStatus:'.json_encode($res));
            throw new CouldNotSaveException(__($res->getMessage()));
        }
        
        $resJson = json_decode($res);

        curl_close($chSign);
        return $resJson;
    }
}
