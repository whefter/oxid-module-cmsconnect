<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages_memcache
 */
class CMSc_Cache_LocalPages_memcache extends CMSc_Cache_LocalPages
{
    const ENGINE_LABEL = 'memcache';
    
    protected $_oMemcache = null;
    
    protected $_aPageCache = [];
    
    protected function _getMemcache ()
    {
        if ( $this->_oMemcache === null ) {
            $this->_oMemcache = new Memcache();
            $this->_oMemcache->connect('127.0.0.1', 11211);
        }
        
        return $this->_oMemcache;
    }
    
    protected function _getMemcacheKey ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $this->_getCachePrefix() . $sCacheKey;
    }
    
    protected function _addPageToIndex ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( !in_array($sCacheKey, $aIndex) ) {
            $aIndex[] = $sCacheKey;
            $this->_getMemcache()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _deletePageFromIndex ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( in_array($sCacheKey, $aIndex) ) {
            unset( $aIndex[array_search($sCacheKey, $aIndex)] );
            $this->_getMemcache()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _getIndex ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getMemcache()->get($this->_getCachePrefix());
        
        if ( !is_array($aIndex) ) {
            $aIndex = [];
        }
        
        return $aIndex;
    }
    
    protected function _setLocalPageCache ($sCacheKey, $aLocalPageCache)
    {
        $this->_aPageCache[$sCacheKey] = $aLocalPageCache;
        
        if ( $aLocalPageCache ) {
            $aLocalPageCache['pages'] = array_map(function ($v)
            {
                return serialize($v);
            }, $aLocalPageCache['pages']);
        }
        
        $this->_getMemcache()->set( $this->_getMemcacheKey($sCacheKey), $aLocalPageCache );
        
        $this->_addPageToIndex($sCacheKey);
    }
    
    protected function _getLocalPageCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $aCache = $this->_getMemcache()->get( $this->_getMemcacheKey($sCacheKey) );
            
            if ( $aCache ) {
                $aCache['pages'] = array_map(function ($v)
                {
                    return unserialize($v);
                }, $aCache['pages']);
            }
            
            $this->_aPageCache[$sCacheKey] = $aCache;
        }
        
        // This evaluates to true if there was no OXID file cache or it was
        // invalid
        if ( !$this->_aPageCache[$sCacheKey] ) {
            $this->_aPageCache[$sCacheKey] = [
                'pages' => [],
                'data' => $this->_getCurrentLocalPageData(),
            ];
        }
        
        return $this->_aPageCache[$sCacheKey];
    }
    
    /**
     * Override parent
     */
    protected function _registerCmsPage ($sLocalPageCacheKey, $oCmsPage)
    {
        $aLocalPageCache = $this->_getLocalPageCache($sLocalPageCacheKey);
        
        $aLocalPageCache['pages'][] = $oCmsPage;

        $this->_setLocalPageCache($sLocalPageCacheKey, $aLocalPageCache);
    }
    
    /**
     * Override
     */
    protected function _getLocalPageCmsPages ($sCacheKey)
    {
        $_aPageCache = $this->_getLocalPageCache($sCacheKey);
        
        if ( !$_aPageCache ) {
            return [];
        } else {
            return $_aPageCache['pages'];
        }
    }
    
    /**
     * Override
     */
    protected function _deleteLocalPageCache ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        // Memcache::delete() is broken in several versions of php-memcache
        // $this->_getMemcache()->delete( $this->_getMemcacheKey($sCacheKey) );e
        $this->_getMemcache()->set($this->_getMemcacheKey($sCacheKey), false);
        $this->_deletePageFromIndex($sCacheKey);
        
        stopProfile(__METHOD__);
    }
    
    /**
     * Override
     */
    protected function _getList ()
    {
        $aIndex = $this->_getIndex();
        
        $aList = [];
        foreach ( $aIndex as $sCacheKey ) {
            $aList[$sCacheKey] = $this->_getLocalPageCache($sCacheKey);
        }
        
        return $aList;
    }
}