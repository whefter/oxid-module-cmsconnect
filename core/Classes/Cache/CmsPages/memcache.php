<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_CmsPages_memcache
 */
class CMSc_Cache_CmsPages_memcache extends CMSc_Cache_CmsPages
{
    const ENGINE_LABEL = 'memcache';
    
    protected $_oMemcache = null;
    
    protected function _getMemcache ()
    {
        if ( $this->_oMemcache === null ) {
            $this->_oMemcache = new Memcache();
            $this->_oMemcache->connect('127.0.0.1', 11211);
        }
        
        return $this->_oMemcache;
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
        
        $sCacheName = $this->_getMemcacheKeyFromCacheKey($sCacheKey);
        
        $blSuccess = $this->_getMemcache()->set($sCacheName, $oHttpResult);
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
        
        $sCacheName = $this->_getMemcacheKeyFromCacheKey($sCacheKey);
        
        $oHttpResult = $this->_getMemcache()->get($sCacheName);
        
        stopProfile(__METHOD__);
        
        return $oHttpResult;
    }
    
    /**
     * Override
     */
    protected function _deleteHttpResult ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getMemcacheKeyFromCacheKey($sCacheKey);
        
        // Memcache::delete() is broken in several versions of php-memcache
        // var_dump($this->_getMemcache()->delete($sCacheName, 0));
        $this->_getMemcache()->set($sCacheName, false);
        $this->_deletePageFromIndex($sCacheKey);
        
        stopProfile(__METHOD__);
    }
    
    /**
     * @param string    $sCacheKey       
     * 
     * @return string
     */
    protected function _getMemcacheKeyFromCacheKey ($sCacheKey)
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