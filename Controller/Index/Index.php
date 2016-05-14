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

namespace BelVG\FacebookFree\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Registration;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use BelVG\FacebookFree\Controller\IndexInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Index
 * @package BelVG\FacebookFree\Controller\Index
 */
class Index extends Action implements IndexInterface
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;

    /** @var Session */
    protected $session;

    /** @var Registration */
    protected $registration;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var AccountManagementInterface */
    protected $accountManagement;

    /** @var CustomerUrl */
    protected $customerUrl;

    /** @var CustomerExtractor */
    protected $customerExtractor;

    /** @var \Magento\Framework\UrlInterface */
    protected $urlModel;

    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    /** @var Logger */
    protected $logger;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registration $registration,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        CustomerExtractor $customerExtractor,
        AccountRedirect $accountRedirect,
        UrlFactory $urlFactory,
        Logger $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->registration = $registration;
        $this->session = $customerSession;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;
        $this->customerExtractor = $customerExtractor;
        $this->accountRedirect = $accountRedirect;
        $this->urlModel = $urlFactory->create();
        $this->logger = $logger;
    }

    private function getSession()
    {
        return $this->session;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        // redirect logged in user to the dashboard
        if ($this->getSession()->isLoggedIn() || !$this->registration->isAllowed()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        }

        try {
            /* @var $requestData \BelVG\FacebookFree\Model\Request */
            $requestData = $this->_objectManager->create('BelVG\FacebookFree\Model\Request');
            $fbData = $requestData->load();
            
            if (!isset($fbData['email'])) {
                throw new \Exception('You should allow application to access Email field from your Facebook account.');
            }
            
            $fbData['store_id'] = $this->storeManager->getStore()->getId();
            $fbData['website_id'] = $this->storeManager->getStore()->getWebsiteId();

            /* @var $customer \BelVG\FacebookFree\Model\Customer */
            $customer = $this->_objectManager->create('BelVG\FacebookFree\Model\Customer')->setFbData($fbData);

            /* @var $facebook \BelVG\FacebookFree\Model\FacebookFree */
            $facebook = $this->_objectManager->create('BelVG\FacebookFree\Model\FacebookFree');
            if (!$customerId = $facebook->checkFbCustomer($customer->getFbData())) {
                if (!$customerId = $customer->checkCustomer()) {
                    // create customer
                    $customerModel = $this->createCustomer($customer);
                    $customerId = $customerModel->getId();
                    $this->messageManager->addSuccess($this->getSuccessMessage());
                }

                if (!isset($customerModel)) {
                    $customerModel = $customer->load($customerId);
                }

                $fbData['customer_id'] = $customerId;

                // create record about FB user
                $facebook->prepareData($fbData)->save();
                $this->getSession()->setCustomerDataAsLoggedIn($customerModel);
            } else {
                $customerModel = $customer->load($customerId);
                $this->getSession()->setCustomerAsLoggedIn($customerModel);
            }

            $resultRedirect = $this->accountRedirect->getRedirect();

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addError($e->getMessage());
            $url = $this->urlModel->getUrl('customer/account/login', ['_secure' => true]);
            $resultRedirect->setUrl($url);
            return $resultRedirect;
        }

        return $resultRedirect;
    }

    public function createCustomer(\BelVG\FacebookFree\Model\Customer $customerFb)
    {
        $this->_request->setParams($customerFb->prepareData());
        $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
        $customer->setAddresses(null);

        $customer = $this->accountManagement
            ->createAccount($customer, $customerFb->generatePassword(), '');

        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => $this, 'customer' => $customer]
        );

        $this->getSession()->setCustomerDataAsLoggedIn($customer);
        $this->messageManager->addSuccess($this->getSuccessMessage());
        return $customer;
    }

    /**
     * Retrieve success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        return __('Thank you for registering with %1.', $this->storeManager->getStore()->getFrontendName());
    }
}
