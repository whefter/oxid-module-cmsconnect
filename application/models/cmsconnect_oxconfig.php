<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * cmsconnect_oxconfig
 */
class cmsconnect_oxconfig extends cmsconnect_oxconfig_parent
{
    protected static $_blCMScInitialized = false;
    
    public function init ()
    {
        if ( !static::$_blCMScInitialized ) {
            static::$_blCMScInitialized = true;
            
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/CmsPage.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/CmsPage/Path.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/CmsPage/Path/Implicit.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/CmsPage/Id.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Utils.php');
            
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/LocalPages.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/LocalPages/OxidFileCache.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/LocalPages/DB.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/LocalPages/memcache.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/CmsPages.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/CmsPages/OxidFileCache.php');
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/Cache/CmsPages/memcache.php');
            
            require_once(OX_BASE_PATH . '/modules/wh/cmsconnect/core/Classes/SessionCache.php');
            
            CMSc_Cache_LocalPages::get()->init();
            CMSc_Cache_CmsPages::get()->init();
            
            $start = microtime(true);
            
            $aCmsPages = CMSc_Cache_LocalPages::get()->getCurrentLocalPageCmsPages();
            
            $aPagesToFetch =  [];
            
            foreach ( $aCmsPages as $oCmsPage ) {
                $oHttpResult = CMSc_Cache_CmsPages::get()->fetchHttpResult($oCmsPage);

                if ( $oHttpResult ) {
                    CMSc_SessionCache::set('results', $oCmsPage->getSessionCacheKey(), $oHttpResult);
                } else {
                    $aPagesToFetch[] = $oCmsPage;
                }
            }
            
            $aRequests = [];
            foreach ( $aPagesToFetch as $i => $oCmsPage ) {
                $aRequests[] = $oCmsPage->getHttpRequest();
            }
            $aResults = CMSc_Utils::httpMultiRequest($aRequests);
            
            // $aResults = [];
            // foreach ( $aUrlsToFetch as $i => $sUrl ) {
                // $aResults[] = CMSc_Utils::fetchUrl($sUrl);
            // }
            
            $time = microtime(true) - $start;
            
            // echo "<br>Time: " . $time . " s<br>";
            
            foreach ( $aPagesToFetch as $i => $oCmsPage ) {
                CMSc_SessionCache::set('results', $oCmsPage->getSessionCacheKey(), $aResults[$i]);
                CMSc_Cache_CmsPages::get()->saveHttpResult($oCmsPage, $aResults[$i]);
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