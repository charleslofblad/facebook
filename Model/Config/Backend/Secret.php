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
namespace BelVG\FacebookFree\Model\Config\Backend;

/**
 * Config Cookie Restriction mode backend
 * @package BelVG\FacebookFree\Model\Config\Backend
 */
class Secret extends \Magento\Framework\App\Config\Value
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'adminhtml_system_config_backend_secret';
}
