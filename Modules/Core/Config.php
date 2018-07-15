<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Modules\Core;

use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \wh\CmsConnect\Application\Models\Cache;
use \wh\CmsConnect\Application\Models\SessionCache;
use \wh\CmsConnect\Application\Models\CmsPage;

/**
 * cmsconnect_oxconfig
 */
class Config extends Config_parent
{
    protected static $_blCMScInitialized = false;
    
    public function init ()
    {
        if ( !static::$_blCMScInitialized ) {
            static::$_blCMScInitialized = true;
            
            Cache\LocalPages::get()->init();
            Cache\CmsPages::get()->init();
            
            $start = microtime(true);
            
            $aCmsPages = Cache\LocalPages::get()->getCurrentLocalPageCmsPages();
            
            $aPagesToFetch =  [];
            
            if ( count($aCmsPages) ) {
                foreach ( $aCmsPages as $oCmsPage ) {
                    $oHttpResult = Cache\CmsPages::get()->fetchHttpResult($oCmsPage);

                    if ( $oHttpResult ) {
                        SessionCache::set('results', $oCmsPage->getSessionCacheKey(), $oHttpResult);
                    } else {
                        $aPagesToFetch[] = $oCmsPage;
                    }
                }
            }
            
            // echo "<pre>Pages to fetch";
            // var_dump($aPagesToFetch);
            // echo "</pre>";
            
            if ( count($aPagesToFetch) ) {
                $aRequests = [];
                foreach ( $aPagesToFetch as $i => $oCmsPage ) {
                    $aRequests[] = $oCmsPage->getHttpRequest();
                }
                
                $aResults = CMSc_Utils::httpMultiRequest($aRequests);
                
                // $aResults = [];
                // foreach ( $aRequests as $i => $aRequest ) {
                    // list($aResult) = CMSc_Utils::httpMultiRequest([$aRequest]);
                    // $aResults[] = $aResult;
                // }
                
                $time = microtime(true) - $start;
                
                // echo "<br>Time: " . $time . " s<br>";
                
                foreach ( $aPagesToFetch as $i => $oCmsPage ) {
                    SessionCache::set('results', $oCmsPage->getSessionCacheKey(), $aResults[$i]);
                    Cache\CmsPages::get()->saveHttpResult($oCmsPage, $aResults[$i]);
                }
            }
        }
        
        return parent::init();
    }
    
    /**
     * When asked for the 'page' request parameter, the native OXID function returns the last part
     * of the current SEO path only.
     *
     * @param   string      $sName      See parent definition
     * @param   bool        $blRaw      See parent definition
     *
     * @return bool
     */
    public function getRequestParameter($sName, $blRaw = false)
    {
        // This method is called at such an early point in the execution that
        // not all module classes have been loaded. Skip our checks until after
        // the CMSconnect classes have been loaded, we don't need them before that 
        // anyway.
        
        if ( class_exists('CMSc_Utils') ) {
            if ( $sName == 'page' ) {
                if ( $sCMScSeoPage = CMSc_Utils::getCurrentLocalPageSeoPath() ) {
                    if ( $sCMScSeoPage !== '' ) {
                        return urlencode($sCMScSeoPage);
                    }
                }
            }
        }
        
        return parent::getRequestParameter($sName, $blRaw);
    }
}