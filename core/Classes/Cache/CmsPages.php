<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_CmsPages
 */
abstract class CMSc_Cache_CmsPages extends CMSc_Cache
{
    abstract protected function _saveHttpResult ($sCacheKey, $oHttpResult);
    abstract protected function _fetchHttpResult ($sCacheKey);
    abstract protected function _deleteHttpResult ($sCacheKey);
    
    /**
     * Singleton instance.
     *
     * @var
     */
    protected static $_oInstance = null;
    
    /**
     * Singleton instance getter
     */
    public static function get ()
    {
        if ( !static::$_oInstance ) {
            $sEngine = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_CMS_PAGES_CACHE_ENGINE);
            
            if ( $sEngine === CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_AUTO ) {
                if ( extension_loaded('memcached') ) {
                    static::$_oInstance = new CMSc_Cache_CmsPages_memcached();
                } else if ( extension_loaded('memcache') ) {
                    static::$_oInstance = new CMSc_Cache_CmsPages_memcache();
                } else {
                    static::$_oInstance = new CMSc_Cache_CmsPages_OxidFileCache();
                }
            } else {
                switch ( $sEngine ) {
                    case CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHED:
                        if ( extension_loaded('memcached') ) {
                            static::$_oInstance = new CMSc_Cache_CmsPages_memcached();
                            break;
                        }
                    case CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHE:
                        if ( extension_loaded('memcache') ) {
                            static::$_oInstance = new CMSc_Cache_CmsPages_memcache();
                            break;
                        }
                    case CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_OXIDFILECACHE:
                    default:
                        static::$_oInstance = new CMSc_Cache_CmsPages_OxidFileCache();
                        break;
                }
            }
        }
        
        return static::$_oInstance;
    }
    
    /**
     * @return string
     */
    protected function _getCachePrefix ()
    {
        return 'CMSc_CmsPage_' . $this->getShopId() . '_';
    }
    
    /**
     * @param CMSc_CmsPage   $oCmsPage       
     * @param object        $oHttpResult    Result object
     * @param int           $iTtl           Cache TTL
     * 
     * @return bool
     */
    public function saveHttpResult ($oCmsPage, $oHttpResult, $iTtl = null)
    {
        startProfile(__METHOD__);
        
        // Figure out cache TTL
        if ( $iTtl === null ) {
            if ( !($iTtl = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_TTL_DEFAULT)) ) {
                $iTtl = CMSc_Utils::CONFIG_DEFAULTVALUE_TTL;
            }
            if ( !($iTtlRnd = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_TTL_DEFAULT_RND)) ) {
                $iTtlRnd = CMSc_Utils::CONFIG_DEFAULTVALUE_TTL_RND;
            }
        }
        
        // Randomize by $iCacheRandomize percentage
        $iTtl = mt_rand( floor( $iTtl * (1 - $iTtlRnd/100) ), ceil( $iTtl * (1 + $iTtlRnd/100) ) );
        
        $oHttpResult->ttl = oxRegistry::get('oxUtilsDate')->getTime() + $iTtl;
        $oHttpResult->oCmsPage = $oCmsPage;
        
        $blSuccess = $this->_saveHttpResult($oCmsPage->getIdent(), $oHttpResult);
        
        stopProfile(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Returns a cached HTTP result
     *
     * @param CMSc_CmsPage    $oCmsPage
     * 
     * @return object
     */
    public function fetchHttpResult ($oCmsPage)
    {
        return $this->fetchHttpResultByIdent($oCmsPage->getIdent());
    }
    
    /**
     * Returns a cached HTTP result
     *
     * @param string    $sCacheKey
     * 
     * @return object
     */
    public function fetchHttpResultByIdent ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $oResult = $this->_fetchHttpResult($sCacheKey);
        
        if ( !$oResult  ) {
            $oResult = false;
        } else {
            if ( $oResult->ttl <= oxRegistry::get('oxUtilsDate')->getTime() ) {
                $oResult = false;
            }
        }
        
        stopProfile(__METHOD__);
        
        return $oResult;
    }
    
    /**
     * Delete a cached HTTP result
     *
     * @param object CMSc_CmsPage    $oCmsPage
     * 
     * @return object
     */
    public function deleteHttpResult ($oCmsPage)
    {
        startProfile(__METHOD__);
        
        $mReturn = $this->_deleteHttpResult($oCmsPage->getIdent());
        
        stopProfile(__METHOD__);
        
        return $mReturn;
    }
    
    /**
     * Delete a cached HTTP result
     *
     * @param string    $sCacheKey
     * 
     * @return object
     */
    public function deleteHttpResultByIdent ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $mReturn = $this->_deleteHttpResult($sCacheKey);
        
        stopProfile(__METHOD__);
        
        return $mReturn;
    }
}