<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Modules\Core;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \wh\CmsConnect\Application\Models\CmsPage;
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
        // Performance optimization
        if (strpos($sStdUrl, 'cmsconnect_frontend') === false || strpos($sStdUrl, 'pagePath') === false) {
            return call_user_func_array('parent::getStaticUrl', func_get_args());
        }

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
        if ($aQuery['cl'] === 'cmsconnect_frontend' && !empty($aQuery['pagePath'])) {
            // Construct an SEO URL that represents a call to CMSconnect from the passed page
            $sPagePath = urldecode($aQuery['pagePath']);
            unset($aQuery['cl']);
            unset($aQuery['pagePath']);

            // This causes trouble, since it also encodes &lang=, leave out for now [wh]
//            $sNewQueryString = count($aQuery) ? ('?' . http_build_query($aQuery)) : '';
            $sNewQueryString = '';

            $oPage = new CmsPage\Path($sPagePath, $iLang);
            $oXml = $oPage->getXml();

            if ($oXml && $oXml->{'language-urls'} && count($oXml->{'language-urls'}) && $oXml->{'language-urls'}[0]->url && count($oXml->{'language-urls'}[0]->url)) {
                foreach ($oxLang->getLanguageArray() as $oLang) {
                    foreach ($oXml->{'language-urls'}[0]->url as $oUrlXml) {
                        if ($oLang->abbr !== (string)$oUrlXml['lang']) {
                            continue;
                        }

                        $sUrl = CMSc_Utils::unwrapCDATA((string)$oUrlXml);

                        $sSeoUrl = CMSc_Utils::rewriteUrl($sUrl) . $sNewQueryString;
                        $this->_aStaticUrlCache[$sStdUrl][$oLang->id][$iShopId] = $sSeoUrl;
                    }
                }
            } else {
                // No language navigation. "Best effort": construct URLs based on the requested
                // page path for our known languages (and return the one for the
                // requested language). [wh]
                foreach ($oxLang->getLanguageArray() as $oLang) {
                    $sShopUrl = (strpos($sStdUrl, 'https:') === 0) ? $oxConfig->getSslShopUrl($oLang->id) : $oxConfig->getShopUrl($oLang->id);
                    $sSeoIdent = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_SEO_IDENTIFIERS, $oLang->id);
                    $sSeoUrl = CMSc_Utils::sanitizeUrl( $sShopUrl . '/' . $sSeoIdent . '/' . $sPagePath ) . $sNewQueryString;

                    $this->_aStaticUrlCache[$sStdUrl][$oLang->id][$iShopId] = $sSeoUrl;
                }

                if ( isset($this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId]) ) {
                    class_exists('t') && t::e(__METHOD__);
                    return $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId];
                }
            }
        }

        class_exists('t') && t::e(__METHOD__);

        return parent::getStaticUrl($sStdUrl, $iLang, $iShopId);
    }
}
