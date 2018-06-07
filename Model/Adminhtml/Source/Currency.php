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
class Currency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'SGD',
                'label' => 'SGD'
            ],
            [
                'value' => 'USD',
                'label' => 'USD'
            ],
            [
                'value' => 'IDR',
                'label' => 'IDR'
            ]
        ];
    }
}
