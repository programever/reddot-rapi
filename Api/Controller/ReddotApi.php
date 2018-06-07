<?php
namespace Reddot\Rapi\Api\Controller;

use Reddot\Rapi\Api\CustomRepositoryInterface;
use Reddot\Rapi\Helper\LogHelper;
use Reddot\Rapi\Helper\Data;
use Magento\Framework\Webapi\Rest\Request;

class ReddotApi implements CustomRepositoryInterface
{ 

    protected $request;
    protected $dataHelper;
    protected $store;

    public function __construct(
        Request $request,
        \Magento\Store\Api\Data\StoreInterface $storeManager,
        Data $dataHelper
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->store = $storeManager->getStore();
    }

    public function rapiCallback(){
        $responseData = json_decode(file_get_contents('php://input'), true);
        (new LogHelper())->debug('------------------CallBackData------------');
        (new LogHelper())->debug($responseData);
        (new LogHelper())->debug('1');

        $signature = $responseData['signature'];
        (new LogHelper())->debug('2');

        $createResponseSignature = $this->dataHelper->createResponseSignature($responseData);
        (new LogHelper())->debug('3');

        if ($createResponseSignature != $responseData['signature']) {
            (new LogHelper())->debug('4');
            return [[
                'code' => 40,
                'message' => 'Invalid signature'
            ]];
        }
        (new LogHelper())->debug('5');

        if ($responseData['response_msg'] == 'successful' || $responseData['response_code'] == 0){
            (new LogHelper())->debug('6');
            $this->dataHelper->updateOrderStatus($responseData['order_id'], 'complete');
        } else {
            (new LogHelper())->debug('7');
            $this->dataHelper->updateOrderStatus($responseData['order_id'], 'canceled');
        }
        (new LogHelper())->debug('8');

        return [[
          'code' => 200,
          'message' => 'Success'
        ]];
    }

    public function rapiGetRedirectUrl(){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $session = $om->get('Magento\Customer\Model\Session');

        $redirectUrl = $session->getPaymentUrl();
        $session->setPaymentUrl(null);

        if ($redirectUrl == null){
            return [[
              'code' => 40,
              'url' => $this->store->getUrl('customer/account')
            ]];
        }

        return [[
          'code' => 200,
          'url' => $redirectUrl
        ]];
    }
}