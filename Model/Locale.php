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
 * Class Locale
 * @package BelVG\FacebookFree\Model
 */
class Locale
{

    /**
     *
     * @var \SimpleXMLElement|NULL
     */
    protected $locales = null;

    const FBFREE_DEFAULT_LOCALE = 'en_US';

    /**
     * Read locales
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $file = $moduleReader->getModuleDir('etc', 'BelVG_FacebookFree') . '/' . 'locale.xml';
        if (is_readable($file)) {
            $this->locales = simplexml_load_file($file);
        }
    }

    /**
     * Get current FB locale according to the selected store locale
     * @param string $systemLocale
     * @return string
     */
    public function getLocale($systemLocale)
    {
        $localeParams = array();

        if (isset($this->locales->$systemLocale)) {
            $localeParams = $this->locales->$systemLocale;
        }

        $locale = isset($localeParams->code) ? (string)$localeParams->code : self::FBFREE_DEFAULT_LOCALE;

        return $locale;
    }

}
