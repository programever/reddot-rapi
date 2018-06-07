<?php

namespace Reddot\Rapi\Api;

interface CustomRepositoryInterface
{
  /**
   * Create custom Api.
   *
   * @return string
   * @throws \Magento\Framework\Exception\LocalizedException
   */
  public function rapiCallback();

  /**
   * Create custom Api.
   *
   * @return string
   * @throws \Magento\Framework\Exception\LocalizedException
   */
  public function rapiGetRedirectUrl();
}