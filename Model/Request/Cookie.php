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

namespace BelVG\FacebookFree\Model\Request;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Class Cookie
 * @package BelVG\FacebookFree\Model\Request
 */
class Cookie
{
    const FB_COOKIE_PREFIX = 'fbsr_';
    /**
     * Original FB cookie
     *
     * @var string
     */
    private $fbCookie = null;

    /**
     * Cookie Manager
     *
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \BelVG\FacebookFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * get FB cookie
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param \BelVG\FacebookFree\Helper\Data $dataHelper
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        \BelVG\FacebookFree\Helper\Data $dataHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->dataHelper = $dataHelper;

        $cookieName = self::FB_COOKIE_PREFIX . $this->dataHelper->getAppId();
        $this->fbCookie = $this->cookieManager->getCookie($cookieName);
    }

    /**
     * get FB cookie
     *
     * @return string
     */
    public function getFbCookie()
    {
        return $this->fbCookie;
    }

    /**
     * Decode and parce FB cookie
     *
     * @return array|NULL
     * @throws \RuntimeException
     */
    private function parseCookie()
    {
        if (!empty($this->fbCookie)) {
            if (list($encoded_sig, $payload) = explode('.', $this->fbCookie, 2)) {

                // decode the data
                $sig = $this->base64UrlDecode($encoded_sig);
                $data = json_decode($this->base64UrlDecode($payload), true);

                if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
                    throw new \RuntimeException('Unknown algorithm. Expected HMAC-SHA256');
                }

                $secret = $this->dataHelper->getAppSecret();
                // Adding the verification of the signed_request below
                $expected_sig = hash_hmac('sha256', $payload, $secret, true);
                if ($sig !== $expected_sig) {
                    throw new \RuntimeException('Bad Signed JSON signature!');
                }

                return $data;
            }
        }

        return null;
    }

    /**
     * Getter for parseCookie() method
     *
     * @return array
     */
    public function getParsedCookie()
    {
        return $this->parseCookie();
    }

    /**
     * See http://developers.facebook.com/docs/authentication/signed_request/ for more info
     *
     * @param string $input
     * @return string
     */
    private function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
