<?php

/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Belvg
 * @package    BelVG_FacebookFree
 * @author Pavel Novitsky <pavel@belvg.com>
 * @copyright  Copyright (c) 2010 - 2015 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

namespace BelVG\FacebookFree\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config;

/**
 * Class Data
 * @package BelVG\Facebookfree\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH_FBFREE_ENABLED = 'facebookfree/general/enable';
    const CONFIG_PATH_FBFREE_APP_ID = 'facebookfree/general/appid';
    const CONFIG_PATH_FBFREE_APP_SECRET = 'facebookfree/general/secret';

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     * @param string $scopeType
     * @param null|mixed $scopeCode
     * @return bool
     */
    public function isActive($scopeType = Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_FBFREE_ENABLED, $scopeType, $scopeCode);
    }

    /**
     * Get application id
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getAppId($scopeType = Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_FBFREE_APP_ID, $scopeType, $scopeCode);
    }

    /**
     * Get application secret key
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getAppSecret($scopeType = Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_FBFREE_APP_SECRET, $scopeType, $scopeCode);
    }
}
