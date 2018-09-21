<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Modules\Core;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \t as t;

/**
 * cmsconnect_oxseoencoder
 */
class SeoEncoder extends SeoEncoder_parent
{
    /**
     * @var array
     */
    protected $_aStaticUrlCache = [];

    /**
     * Detect a request to a page URL that needs to be handled by CMSconnect
     *
     * @param string        $sStdUrl        See parent definition
     * @param int           $iLang          See parent definition
     * @param int           $iShopId        See parent definition
     *
     * @return string
     */
    public function getStaticUrl($sStdUrl, $iLang = null, $iShopId = null)
    {
        class_exists('t') && t::s(__METHOD__);
        
        $oxConfig   = Registry::getConfig();
        $oxLang     = Registry::getLang();
 
        if ( !isset($iShopId) ) {
            $iShopId = $oxConfig->getShopId();
        }
        if ( !isset($iLang) ) {
            $iLang = $oxLang->getEditLanguage();
        }
        
        if ( isset($this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId]) ) {
            class_exists('t') && t::e(__METHOD__);
            return $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId];
        }
        
        // Convert &amp; back into &
        $sQueryString = html_entity_decode( parse_url($sStdUrl, PHP_URL_QUERY) );
    
        $aQuery = array();
        parse_str($sQueryString, $aQuery);
        
        // Check if the requested URL is a call to CMSconnect
        if ( !empty($aQuery['cl']) && $aQuery['cl'] === 'cmsconnect_frontend') {
            // Construct an SEO URL that represents a call to CMSconnect from the passed page
            $sPage      = urldecode($aQuery['page']);
            $sSeoIdent  = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_SEO_IDENTIFIERS, $iLang);
            $sShopUrl   = (strpos($sStdUrl, 'https:') === 0) ? $oxConfig->getSslShopUrl($iLang) : $oxConfig->getShopUrl($iLang);
            
            $sSeoUrl = CMSc_Utils::sanitizeUrl( $sShopUrl . '/' . $sSeoIdent . '/' . $sPage );
            
            $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId] = $sSeoUrl;
        }
    
        class_exists('t') && t::e(__METHOD__);
        
        return parent::getStaticUrl($sStdUrl, $iLang, $iShopId);
    }
}