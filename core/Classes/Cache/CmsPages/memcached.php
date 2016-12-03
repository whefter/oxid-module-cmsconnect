<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_CmsPages_memcached
 */
class CMSc_Cache_CmsPages_memcached extends CMSc_Cache_CmsPages
{
    const ENGINE_LABEL = 'memcached';
    
    protected $_oMemcached = null;
    
    protected function _getMemcached ()
    {
        if ( $this->_oMemcached === null ) {
            $this->_oMemcached = new Memcached();
            $this->_oMemcached->addServer('127.0.0.1', 11211);
        }
        
        return $this->_oMemcached;
    }
    
    protected function _addPageToIndex ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( !in_array($sCacheKey, $aIndex) ) {
            $aIndex[] = $sCacheKey;
            $this->_getMemcached()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _deletePageFromIndex ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( in_array($sCacheKey, $aIndex) ) {
            unset( $aIndex[array_search($sCacheKey, $aIndex)] );
            $this->_getMemcached()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _getIndex ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getMemcached()->get($this->_getCachePrefix());
        
        if ( !$aIndex ) {
            $aIndex = [];
        }
        
        return $aIndex;
    }
    
    /**
     * Override
     */
    protected function _saveHttpResult ($sCacheKey, $oHttpResult)
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getMemcachedKeyFromCacheKey($sCacheKey);
        
        $blSuccess = $this->_getMemcached()->set($sCacheName, $oHttpResult);
        $this->_addPageToIndex($sCacheKey);
        
        stopProfile(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Override
     */
    protected function _fetchHttpResult ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getMemcachedKeyFromCacheKey($sCacheKey);
        
        $oHttpResult = $this->_getMemcached()->get($sCacheName);
        
        stopProfile(__METHOD__);
        
        return $oHttpResult;
    }
    
    /**
     * Override
     */
    protected function _deleteHttpResult ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getMemcachedKeyFromCacheKey($sCacheKey);
        
        // Memcache::delete() is broken in several versions of php-memcache
        // var_dump($this->_getMemcached()->delete($sCacheName, 0));
        $this->_getMemcached()->set($sCacheName, false);
        $this->_deletePageFromIndex($sCacheKey);
        
        stopProfile(__METHOD__);
    }
    
    /**
     * @param string    $sCacheKey       
     * 
     * @return string
     */
    protected function _getMemcachedKeyFromCacheKey ($sCacheKey)
    {
        return 'CMSc_CmsPage__' . $sCacheKey;
    }
    
    /**
     * Orverride
     */
    protected function _getList ()
    {
        startProfile(__METHOD__);
        
        $aIndex = $this->_getIndex();
        
        $aList = [];
        foreach ( $aIndex as $sCacheKey ) {
            $aList[$sCacheKey] = $this->_fetchHttpResult($sCacheKey);
        }
        
        stopProfile(__METHOD__);
        
        return $aList;
    }
}