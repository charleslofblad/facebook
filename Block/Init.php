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

/**
 * Class Init
 * @package BelVG\FacebookFree\Block
 */
class Init extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \BelVG\FacebookFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \BelVG\FacebookFree\Helper\Data $dataHelper
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Module\Dir\Reader $reader
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BelVG\FacebookFree\Helper\Data $dataHelper,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->localeResolver = $localeResolver;
        $this->reader = $reader;
        $this->urlEncoder = $urlEncoder;
    }

    /**
     * Return applicaton Id
     * @return string
     */
    public function getAppId()
    {
        return $this->dataHelper->getAppId();
    }

    /**
     * Return encoded current URL for after auth redirection
     * @return string
     */
    public function getLoginUrl()
    {
        $referer = $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl());
        return $this->getUrl('facebookfree/index/index', array('referer' => $referer));
    }

    /**
     * Get current FB locale according to the selected store locale
     * @return string
     */
    public function getLocale()
    {
        return (new \BelVG\FacebookFree\Model\Locale($this->reader))->getLocale($this->localeResolver->getLocale());
    }
}
