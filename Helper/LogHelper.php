<?php

namespace Reddot\Rapi\Helper;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogHelper
{
  public function debug($message)
  {
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/reddotRApi.log');
	$logger = new \Zend\Log\Logger();
	$logger->addWriter($writer);
	$logger->info(print_r($message, true));
    return;
  }
}