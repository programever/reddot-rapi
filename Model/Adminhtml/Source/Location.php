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
class Location implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'en',
                'label' => 'English'
            ],
            [
                'value' => 'id',
                'label' => 'Bahasa'
            ],
            [
                'value' => 'es',
                'label' => 'Spanish'
            ],
            [
                'value' => 'fr',
                'label' => 'French'
            ],
            [
                'value' => 'de',
                'label' => 'Germany'
            ]
        ];
    }
}
