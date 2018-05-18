<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * CMSc_Cache_LocalPages
 */
abstract class CMSc_Cache_LocalPages extends CMSc_Cache
{
    // These will be removed from the GET and POST arrays
    // before cache key computation, that is, they do not affect
    // the cache key
    protected static $aRequestParamsIgnoreList = [
        'stoken',
        'PHPSESSID',
        'force_sid',
        'sid',
        'gclid',
        'utm_campaign',
        'utm_medium',
        'utm_source',
        'ldtype',
        'listtype',
        'redirected',
        'lgn_pwd',
        'lgn_usr',
        '_', // jQuery AJAX callbacks
        'pgNr', // alist
        '_artperpage', // alist
        'listorder', // alist
        'listorderby', // alist
        'attrfilter',
        'searchparam',
        'maxpriceselected', // multifilter
        'minpriceselected', // multifilter
        'multifilter_reset', // multifilter
    ];
    
    // Any of these request params being present and (if it's an array)
    // having one of the specified values will disable the local page
    // cache for that page
    protected static $aRequestParamsBlacklist = [
        'anid',
        'aid',
        'force_admin_sid',
        'fnc' => [
            'tobasket',
        ],
        'cl' => [
            'contact',
            'suggest',
            'basket',
            'user',
            'payment',
            'thankyou',
            
            'oxwreview',
            'oxwrating',
            'oxwarticledetails',
            
            'account_newsletter',
            'account_recommlist',
            'account_wishlist',
            
            'br_deliverycostlist',
        ],
    ];
    
    abstract protected function _getLocalPageCache ($sCacheKey);
    abstract protected function _deleteLocalPageCache ($sCacheKey);
    abstract public function commit ();
    
    /**
     * Singleton instance.
     *
     * @var
     */
    protected static $_oInstance = null;
    
    /**
     * Cache for current local page's data
     *
     * @var
     */
    protected $_aCurrentLocalPageData = null;
    
    /**
     * @var
     */
    protected $_blIsCachable = null;
    
    /**
     * @var
     */
    protected $_aPageCache = [];
    
    /**
     * Singleton instance getter
     */
    public static final function get ()
    {
        if ( !static::$_oInstance ) {
            $sEngine = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_LOCAL_PAGES_CACHE_ENGINE);
            
            if ( $sEngine === CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_AUTO ) {
                static::$_oInstance = new CMSc_Cache_LocalPages_Disabled();
                
//                if ( extension_loaded('memcached') ) {
//                    static::$_oInstance = new CMSc_Cache_LocalPages_memcached();
//                } else if ( extension_loaded('memcache') ) {
//                    static::$_oInstance = new CMSc_Cache_LocalPages_memcache();
//                } else {
//                    static::$_oInstance = new CMSc_Cache_LocalPages_DB();
//                }
            } else {
                switch ( $sEngine ) {
                    case CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DISABLED:
                        static::$_oInstance = new CMSc_Cache_LocalPages_Disabled();
                        break;
                    case CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DB:
                        static::$_oInstance = new CMSc_Cache_LocalPages_DB();
                        break;
                    case CMSc_Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHED:
                        if ( extension_loaded('memcached') ) {
                            static::$_oInstance = new CMSc_Cache_LocalPages_memcached();
                            break;
                        }
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
     * Override parent
     */
    public function init()
    {
        parent::init();
        
        $this->_loadOrCreateCurrentLocalPageCache();
    }
    
    /**
     * @return string
     */
    protected function _getCachePrefix ()
    {
        return 'CMSc_LocalPage_' . $this->getShopId() . '_';
    }
    
    /**
     * Register a CMS page as having been requested on the current local page.
     */
    public function registerCmsPage ($oCmsPage)
    {
        if ( !$this->isCurrentLocalPagePageCachable() ) {
            return;
        }
        
        if ( !$oCmsPage->isCacheable() ) {
            return;
        }
        
        $sLocalPageCacheKey = $this->_getCurrentLocalPageCacheKey();
        $aKnownPages = $this->getLocalPageCmsPages($sLocalPageCacheKey);
        
        // Not yet compatible with engines besides "DB"
        // $blExists = in_array($oCmsPage->getCacheKey(), $aKnownPages);
        $blExists = false;
        foreach ( $aKnownPages as $oKnownPage ) {
            if ( $oKnownPage->getCacheKey() === $oCmsPage->getCacheKey() ) {
                $blExists = true;
                break;
            }
        }

        if ( !$blExists ) {
            $this->_registerCmsPage($sLocalPageCacheKey, $oCmsPage);
        }
    }
    
    /**
     * Override parent.
     */
    protected function _registerCmsPage ($sLocalPageCacheKey, $oCmsPage)
    {
        $this->_getLocalPageCache($sLocalPageCacheKey);
        
        if ( $this->_aPageCache[$sLocalPageCacheKey] ) {
            $this->_aPageCache[$sLocalPageCacheKey]['pages'][$oCmsPage->getCacheKey()] = $oCmsPage;
        }
    }
    
    /**
     * 
     */
    protected function _loadOrCreateCurrentLocalPageCache ()
    {
        $sCacheKey = $this->_getCurrentLocalPageCacheKey();
        
        $this->_getLocalPageCache($sCacheKey);
        
        // This evaluates to true if there was no OXID file cache
        // or it was invalid
        if ( !$this->_aPageCache[$sCacheKey] ) {
            // Cache for current page doesn't exist - create IF
            // current page is cachable
            if ( $this->isCurrentLocalPagePageCachable() ) {
                $this->_aPageCache[$sCacheKey] = [
                    'pages' => [],
                    'data' => $this->_getCurrentLocalPageData(),
                ];
            }
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
        if ( !$this->_aCurrentLocalPageData ) {
            $sCurrentUrl = oxRegistry::get('oxUtilsUrl')->getCurrentUrl();
            
            $aPost = $_POST;
            
            $aGet = [];
            parse_str($_SERVER['QUERY_STRING'], $aGet);
            
            ksort($aPost);
            ksort($aGet);
            
            foreach ( static::$aRequestParamsIgnoreList as $key ) {
                unset($aPost[$key]);
                unset($aGet[$key]);
            }
            
            if ( trim($_SERVER['QUERY_STRING'], '?&') ) {
                $iQueryPos = strrpos($sCurrentUrl, $_SERVER['QUERY_STRING']);
                $sCurrentUrl = rtrim(substr($sCurrentUrl, 0, $iQueryPos), '?&');
            }
            
            $this->_aCurrentLocalPageData = [
                'url' => $sCurrentUrl,
                'queryData' => $aGet,
                'postData' => $aPost,
            ];
        }
        
        return $this->_aCurrentLocalPageData;
    }
    
    /**
     *
     */
    protected function isCurrentLocalPagePageCachable ()
    {
        if ( $this->_blIsCachable === null ) {
            $this->_blIsCachable = true;
            
            $aData = $this->_getCurrentLocalPageData();
            
            foreach ( static::$aRequestParamsBlacklist as $key => $mVal ) {
                if ( is_string($mVal) ) {
                    if ( isset($aData['queryData'][$mVal]) || isset($aData['postData'][$mVal]) ) {
                        $this->_blIsCachable = false;
                        
                        break;
                    }
                } elseif ( is_array($mVal) ) {
                    if ( isset($aData['queryData'][$key]) || isset($aData['postData'][$key]) ) {
                        foreach ( $mVal as $sVal ) {
                            if ( $aData['queryData'][$key] === $sVal || $aData['postData'][$key] === $sVal ) {
                                $this->_blIsCachable = false;
                                
                                break 2;
                            }
                        }
                    }
                }
            }
        }
        
        return $this->_blIsCachable;
    }
    
    /**
     * Override parent.
     */
    protected function _setLocalPageCache ($sCacheKey, $aLocalPageCache)
    {
        $this->_aPageCache[$sCacheKey] = $aLocalPageCache;
        
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
        $this->_getLocalPageCache($sCacheKey);
        
        // echo "<pre>";
        // var_dump("aaa", $sCacheKey, in_array($sCacheKey, $this->_aPageCache), $this->_aPageCache);
        // echo "</pre>";
        
        if ( $this->_aPageCache[$sCacheKey] ) {
            return $this->_aPageCache[$sCacheKey]['pages'];
        } else {
            return [];
        }
    }
    
    /**
     * Delete a local page's cache
     *
     * @param string    $sCacheKey
     * 
     * @return object
     */
    public function deleteLocalPageCache ($sCacheKey)
    {
       class_exists('t') && t::s(__METHOD__);
        
        unset($this->_aPageCache[$sCacheKey]);
        $mReturn = $this->_deleteLocalPageCache($sCacheKey);
        
       class_exists('t') && t::e(__METHOD__);
        
        return $mReturn;
    }
}