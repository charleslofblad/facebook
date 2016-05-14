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

/**
 * Class Request
 * @package BelVG\FacebookFree\Model
 */
class Request extends \Magento\Framework\DataObject
{

    const FB_REQUEST_URL = 'https://graph.facebook.com/oauth/access_token?client_id=%s&redirect_uri=&client_secret=%s&code=%s';
    const FB_USER_URL = 'https://graph.facebook.com/%s?access_token=%s&fields=name,first_name,last_name,email,id';

    /**
     * @var \BelVG\FacebookFree\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \BelVG\FacebookFree\Model\Request\Cookie
     */
    protected $cookie;

    public function __construct(
        \BelVG\FacebookFree\Helper\Data $dataHelper,
        \BelVG\FacebookFree\Model\Request\Cookie $cookie
    ) {
        $this->dataHelper = $dataHelper;
        $this->cookie = $cookie;
        parent::__construct();
    }

    /**
     * Send request to FB
     *
     * @param string $url
     * @return string
     * @throws \RuntimeException
     */
    private function getFbData($url)
    {
        $data = null;

        if (extension_loaded('curl')) {
            $request = curl_init();
            curl_setopt($request, CURLOPT_URL, $url);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($request);
        } else {
            throw new \RuntimeException('Curl extension should be loaded');
        }

        return $data;
    }

    /**
     * Get Application Token from FB cookie
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getToken()
    {
        $app_id = $this->dataHelper->getAppId();
        $secret = $this->dataHelper->getAppSecret();

        if ($data = $this->cookie->getParsedCookie()) {
            if (isset($data['code'])) {
                $url = sprintf(self::FB_REQUEST_URL, $app_id, $secret, $data['code']);
                $tokenResponse = $this->getFbData($url);
                parse_str($tokenResponse, $signedRequest);

                if (isset($signedRequest['access_token'])) {
                    return $signedRequest['access_token'];
                }

                throw new \RuntimeException('Access Token not found');
            }

            throw new \RuntimeException('Request code not found');
        } else {
            throw new \RuntimeException('False Signed Request');
        }
    }

    /**
     * Load object data
     * @param string $fbId
     * @return array
     * @throws \RuntimeException
     */
    public function load($fbId = 'me')
    {
        $url = sprintf(self::FB_USER_URL, $fbId, $this->getToken());
        $userDataJson = $this->getFbData($url);

        $userData = json_decode($userDataJson);
        if (is_null($userData)) {
            throw new \RuntimeException('FB-connect user data parse error: ' . json_last_error());
        }

        return (array)$userData;
    }
}
