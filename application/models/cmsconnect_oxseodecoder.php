<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */
 
/**
 * cmsconnect_oxseodecoder
 */
class cmsconnect_oxseodecoder extends cmsconnect_oxseodecoder_parent
{
    /**
     * Detect a request to a page URL that needs to be handled by CMSconnect
     *
     * @param string        $sSeoUrl        Detection URL
     *
     * @return string[]
     */
    public function decodeUrl( $sSeoUrl )
    {   
       class_exists('t') && t::s(__METHOD__);
        
        $oxLang     = oxRegistry::getLang();
        $oxConfig   = oxRegistry::getConfig();
        
        $aSeoInfo = CMSc_Utils::getPageSeoInfoByUrl( $sSeoUrl );
        
       class_exists('t') && t::e(__METHOD__);
        
        if ( $aSeoInfo ) {
            $oxLang->setBaseLanguage( $aSeoInfo['lang'] );
            $oxConfig->setConfigParam( 'sCMScCurSeoPage', $aSeoInfo['page'] );
            
            return $aSeoInfo;
        } else {
            return parent::decodeUrl($sSeoUrl);
        }
    }
}
