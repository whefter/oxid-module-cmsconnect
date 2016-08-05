<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
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
        startProfile(__METHOD__);
        
        $oxLang     = oxRegistry::getLang();
        $oxConfig   = oxRegistry::getConfig();
        
        $aSeoInfo = CMSc_Utils::getPageSeoInfoByUrl( $sSeoUrl );
        
        stopProfile(__METHOD__);
        
        if ( $aSeoInfo ) {
            $oxLang->setBaseLanguage( $aSeoInfo['lang'] );
            $oxConfig->setConfigParam( 'sCMScCurSeoPage', $aSeoInfo['page'] );
            
            return $aSeoInfo;
        } else {
            return parent::decodeUrl($sSeoUrl);
        }
    }
}