<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Reddot\Rapi\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class PaymentAction
 */
class PaymentChannel implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {

        $channels = ['Visa / MasterCard', 'AMEX', 'CUP (Union Pay / UPOP)', 'Alipay', 'TenPay', '99Bill', 'eNETS', 'DBS PayLah!'];
        $channels_value = [];


        for ($i=1; $i < count($channels) + 1; $i++) { 
            $channels_value[] = ['value' => $i, 'label' => $channels[$i - 1]];

        }

        return $channels_value;
    }
}
