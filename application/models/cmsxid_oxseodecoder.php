<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */
 
/**
 * cmsxid_oxseodecoder
 */
class cmsxid_oxseodecoder extends cmsxid_oxseodecoder_parent
{
    /**
     * Detect a request to a page URL that needs to be handled by CMSxid
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
        $oUtils     = CmsxidUtils::getInstance();
        
        $aSeoInfo = $oUtils->getPageSeoInfoByUrl( $sSeoUrl );
        
        stopProfile(__METHOD__);
        
        if ( $aSeoInfo ) {
            $oxLang->setBaseLanguage( $aSeoInfo['lang'] );
            $oxConfig->setConfigParam( 'sCmsxidPage', $aSeoInfo['page'] );
            
            return $aSeoInfo;
        } else {
            return parent::decodeUrl($sSeoUrl);
        }
    }
}
