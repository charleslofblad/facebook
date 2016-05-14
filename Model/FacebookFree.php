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

namespace BelVG\FacebookFree\Model;

use BelVG\FacebookFree\Model\Resource\FacebookFree as ResourceFacebookFree;
use BelVG\FacebookFree\Model\Resource\FacebookFree\Collection;

/**
 * Class FacebookFree
 * @package BelVG\FacebookFree\Model
 */
class FacebookFree extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'facebookfree';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'facebookfree';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceFacebookFree $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getId()) {
            $identities = [self::CACHE_TAG . '_' . $this->getId()];
        }
        return $identities;
    }

    /**
     * Check if customer was already logged in with FB before
     *
     * @param array $fbData
     * @return boolean
     */
    public function checkFbCustomer(array $fbData)
    {
        $this->setWebsiteId($fbData['website_id']);
        $collection = $this->getCollection();
        $collection->addFieldToFilter('fb_id', $fbData['id'])
            ->addFieldToFilter('website_id', $this->getWebsiteId());
        if ($collection->count() && $customer_id = $collection->getFirstItem()->getCustomerId()) {
            return $customer_id;
        }

        return false;
    }

    /**
     * Load data to the entity
     * @param array $fbData
     * @return $this
     */
    public function prepareData($fbData)
    {
        $data = array(
            'customer_id' => (int)$fbData['customer_id'],
            'website_id' => (int)$fbData['website_id'],
            'store_id' => (int)$fbData['store_id'],
            'fb_id' => (int)$fbData['id'],
        );

        $this->setData($data);
        return $this;
    }
}
