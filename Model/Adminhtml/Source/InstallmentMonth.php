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
class InstallmentMonth implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {

        $month = [];

        for ($i=1; $i < 13; $i++) { 
            array_push($month, ['value' => $i, 'label' => $i]);
        }

        return $month;
    }
}
