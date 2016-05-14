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

namespace BelVG\FacebookFree\Model\Resource;

/**
 * Class FacebookFree
 * @package BelVG\FacebookFree\Model\Resource
 */
class FacebookFree extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Set main entity table name and primary key field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('belvg_facebook', 'id');
    }
}
