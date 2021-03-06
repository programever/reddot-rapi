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
class PaymentType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'S',
                'label' => 'Sale Transaction'
            ],
            [
                'value' => 'A',
                'label' => '(Pre) Authorisation'
            ],
            [
                'value' => 'I',
                'label' => 'Installment'
            ]
        ];
    }
}
