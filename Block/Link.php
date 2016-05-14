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
namespace BelVG\FacebookFree\Block;

use Magento\Customer\Model\Context;

/**
 * Class Link
 * @package BelVG\FacebookFree\Block
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Template name
     *
     * @var string
     */
    protected $_template = 'BelVG_FacebookFree::link.phtml';

    /**
     * @var \BelVG\FacebookFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \BelVG\FacebookFree\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \BelVG\FacebookFree\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->dataHelper->isActive()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Facebook Connect');
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getImage()
    {
        return $this->getViewFileUrl('BelVG_FacebookFree::images/fb.gif');
    }
}
