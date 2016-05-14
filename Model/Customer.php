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

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Indexer\StateInterface;


/**
 * Class Customer
 * @package BelVG\FacebookFree\Model
 */
class Customer extends \Magento\Customer\Model\Customer
{
    /**
     * Array of the FB user profile information
     * @var array
     */
    private $fbData = array();
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\ResourceModel\Customer $resource,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressesFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\CustomerMetadataInterface $metadataService,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $scopeConfig,
            $resource,
            $configShare,
            $addressFactory,
            $addressesFactory,
            $transportBuilder,
            $groupRepository,
            $encryptor,
            $dateTime,
            $customerDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $metadataService,
            $indexerRegistry,
            $resourceCollection,
            $data
        );
    }

    /**
     * Assign FB data to the entity
     *
     * @param array $fbData
     * @return Customer
     */
    public function setFbData(array $fbData)
    {
        $this->fbData = $fbData;

        if (isset($data['website_id'])) {
            $this->setWebsiteId((int)$data['website_id']);
        }

        return $this;
    }

    /**
     * Get data from the entity
     *
     * @param string|NULL $key
     * @return array
     */
    public function getFbData($key = null)
    {
        $data = $this->fbData;
        if (!is_null($key) && isset($data[$key])) {
            $data = $data[$key];
        }
        return $data;
    }

    /**
     * Check if customer exists
     *
     * @return boolean
     */
    public function checkCustomer()
    {
        $this->setWebsiteId($this->getFbData('website_id'));
        $this->loadByEmail($this->getFbData('email'));

        if ($this->getId()) {
            return $this->getId();
        }

        return false;
    }

    /**
     * Map FB data to the entity
     *
     * @return array
     */
    public function prepareData()
    {
        $data = [
            'firstname' => $this->getFbData('first_name'),
            'lastname' => $this->getFbData('last_name'),
            'email' => $this->getFbData('email'),
            'password' => $this->generatePassword(),
            'confirmation' => null,
            'is_active' => true,
        ];

        return $data;
    }


    public function generatePassword()
    {
        return md5(date('r').rand(200, 20000).$this->getFbData('first_name'));
    }
}
