<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Reddot\Rapi\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Reddot\Rapi\Gateway\Http\Client\ClientMock;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'reddot_rapi';

    protected $configHelper;

    public function __construct(\Reddot\Rapi\Helper\Config $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                ]
            ]
        ];
    }

    // public function getPaymentChannels($channelIds){
    //     $arrayIds = explode(',', $channelIds);
    //     $channels = ['Visa / MasterCard', 'AMEX', 'CUP (Union Pay / UPOP)', 'Alipay', 'TenPay', '99Bill', 'eNETS', 'DBS PayLah!'];
    //     $returnData = [];

    //     for ($i=0; $i < count($arrayIds); $i++) { 
    //         $returnData[] = ['value' => ((int) $arrayIds[$i]), 'channel' => $channels[((int) $arrayIds[$i]) - 1]];
    //     }

    //     return $returnData;
    // }
}
