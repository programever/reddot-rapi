<?php

namespace Reddot\Rapi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{

  const DEFAULT_PATH = 'payment/reddot_rapi/';

  public function getConfigValue($field, $storeId = null)
  {
    return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
  }

  public function getGeneralConfig($code, $storeId = null)
  {
    return $this->getConfigValue(self::DEFAULT_PATH.$code, $storeId);
  }

}