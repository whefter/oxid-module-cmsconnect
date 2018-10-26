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
 * cmsconnect_oxseodecoder
 */
class SeoDecoder extends SeoDecoder_parent
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
        
        $oxLang     = Registry::getLang();
        $oxConfig   = Registry::getConfig();
        
        $aSeoInfo = CMSc_Utils::getPageSeoInfoByUrl( $sSeoUrl );

        class_exists('t') && t::e(__METHOD__);
        
        if ( $aSeoInfo ) {
            $oxLang->setBaseLanguage( $aSeoInfo['lang'] );
            $oxConfig->setConfigParam( 'sCMScCurSeoPage', $aSeoInfo['pagePath'] );
            
            return $aSeoInfo;
        } else {
            return parent::decodeUrl($sSeoUrl);
        }
    }
}
