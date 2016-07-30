<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages
 */
abstract class CMSc_Cache_LocalPages
{
    protected static $aBlacklistedParams = [
        'stoken',
        'PHPSESSID',
        'force_sid',
    ];
    
    abstract protected function _getLocalPageCmsPages ($sCacheKey);
    abstract protected function _registerCmsPage ($sLocalPageCacheKey, $sCmsPageKey);
    abstract protected function _getList ();
    
    /**
     * Singleton instance.
     *
     * @var
     */
    protected static $_oInstance = null;
    
    /**
     * @var
     */
    protected $blInitialized = false;
    
    /**
     * Singleton instance getter
     */
    public static final function get ()
    {
        if ( !static::$_oInstance ) {
            $sEngine = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_LOCAL_PAGES_CACHE_ENGINE);
            
            if ( $sEngine === CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_AUTO ) {
                if ( extension_loaded('memcache') ) {
                    static::$_oInstance = new CMSc_Cache_LocalPages_memcache();
                } else {
                    static::$_oInstance = new CMSc_Cache_LocalPages_DB();
                }
            } else {
                switch ( $sEngine ) {
                    case CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DB:
                        static::$_oInstance = new CMSc_Cache_LocalPages_DB();
                        break;
                    case CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHE:
                        if ( extension_loaded('memcache') ) {
                            static::$_oInstance = new CMSc_Cache_LocalPages_memcache();
                            break;
                        }
                    case CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_OXIDFILECACHE:
                    default:
                        static::$_oInstance = new CMSc_Cache_LocalPages_OxidFileCache();
                        break;
                }
            }
            
        }
        
        return static::$_oInstance;
    }
    
    /**
     *
     */
    public function init ()
    {
        if ( $this->blInitialized ) {
            return;
        }
        
        $this->blInitialized = true;
    }
    
    /**
     * Register a CMS page as having been requested on the current local page.
     */
    public function registerCmsPage ($oCmsPage)
    {
        if ( !$oCmsPage->isCacheable() ) {
            return;
        }
        
        $sLocalPageKeyCache = $this->_getCurrentLocalPageCacheKey();
        $aKnownPages = $this->getLocalPageCmsPages($sLocalPageKeyCache);
        
        $blExists = false;
        foreach ( $aKnownPages as $oKnownPage ) {
            if ( $oKnownPage->getIdent() === $oCmsPage->getIdent() ) {
                $blExists = true;
                break;
            }
        }

        if ( !$blExists ) {
            $this->_registerCmsPage($sLocalPageKeyCache, $oCmsPage);
        }
    }
    
    /**
     * 
     */
    protected final function _getCurrentLocalPageCacheKey ()
    {
        $aPageData = $this->_getCurrentLocalPageData();
        
        return md5(serialize($aPageData));
    }
    
    /**
     * Return some data about the local page that is relevant for caching.
     */
    protected final function _getCurrentLocalPageData ()
    {
        $sCurrentUrl = oxRegistry::get('oxUtilsUrl')->getCurrentUrl();
        
        
        $aPost = $_POST;
        
        $aGet = [];
        parse_str($_SERVER['QUERY_STRING'], $aGet);
        
        ksort($aPost);
        ksort($aGet);
        
        foreach ( array_keys($aPost) as $key ) {
            if ( in_array($key, static::$aBlacklistedParams) ) {
                unset($aPost[$key]);
            }
        }
        foreach ( array_keys($aGet) as $key ) {
            if ( in_array($key, static::$aBlacklistedParams) ) {
                unset($aGet[$key]);
            }
        }
        
        if ( trim($_SERVER['QUERY_STRING'], '?&') ) {
            $iQueryPos = strrpos($sCurrentUrl, $_SERVER['QUERY_STRING']);
            $sCurrentUrl = rtrim(substr($sCurrentUrl, 0, $iQueryPos), '?&');
        }
        
        return[
            'url' => $sCurrentUrl,
            'queryData' => $aGet,
            'postData' => $aPost,
        ];
    }
    
    /**
     * Get the CMS page objects associated with the current local page
     */
    public function getCurrentLocalPageCmsPages ()
    {
        return $this->getLocalPageCmsPages( $this->_getCurrentLocalPageCacheKey() );
    }
    
    /**
     * Get the CMS page objects associated with the passed local page cache key
     */
    public function getLocalPageCmsPages ($sCacheKey)
    {
        return $this->_getLocalPageCmsPages($sCacheKey);
    }
    
    /**
     * Return a list of all known local pages
     */
    public function getList ()
    {
        return $this->_getList();
    }
}