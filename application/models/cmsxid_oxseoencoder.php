<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */
 
/**
 * cmsxid_oxseoencoder
 */
class cmsxid_oxseoencoder extends cmsxid_oxseoencoder_parent
{
    /**
     * Detect a request to a page URL that needs to be handled by CMSxid
     *
     * @param string        $sStdUrl        See parent definition
     * @param int           $iLang          See parent definition
     * @param int           $iShopId        See parent definition
     *
     * @return string
     */
    public function getStaticUrl($sStdUrl, $iLang = null, $iShopId = null)
    {
        startProfile(__METHOD__);
        
        $oxConfig   = oxRegistry::getConfig();
        $oxLang     = oxRegistry::getLang();
        $oUtils     = CmsxidUtils::getInstance();
 
        if ( !isset($iShopId) ) {
            $iShopId = $oxConfig->getShopId();
        }
        if ( !isset($iLang) ) {
            $iLang = $oxLang->getEditLanguage();
        }
        
        if ( isset($this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId]) ) {
            return $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId];
        }
        
        // Convert &amp; back into &
        $sQueryString = html_entity_decode( parse_url($sStdUrl, PHP_URL_QUERY) );
    
        $aQuery = array();
        parse_str($sQueryString, $aQuery);
        
        // Check if the requested URL is a call to CMSxid
        if ( !empty($aQuery['cl']) && $aQuery['cl'] == 'cmsxid_fe' ) {
            // Construct an SEO URL that represents a call to CMSxid from the passed page
            $sPage      = urldecode($aQuery['page']);
            $sSeoIdent  = $oUtils->getLangConfigValue(CmsxidUtils::CONFIG_KEY_SEO_IDENTIFIERS, $iLang);
            $sShopUrl   = (strpos($sStdUrl, 'https:') === 0) ? $oxConfig->getSslShopUrl($iLang) : $oxConfig->getShopUrl($iLang);
            
            $sSeoUrl = $oUtils->sanitizeUrl( $sShopUrl . '/' . $sSeoIdent . '/' . $sPage );
            
            $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId] = $sSeoUrl;
        }
    
        stopProfile(__METHOD__);
        
        return parent::getStaticUrl($sStdUrl, $iLang, $iShopid);
    }
}
