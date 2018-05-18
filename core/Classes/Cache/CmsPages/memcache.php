<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
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
        $aIndex = $this->_getIndex();
        
        if ( !in_array($sCacheKey, $aIndex) ) {
            $aIndex[] = $sCacheKey;
            $this->_getMemcache()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _deletePageFromIndex ($sCacheKey)
    {
        $aIndex = $this->_getIndex();
        
        if ( in_array($sCacheKey, $aIndex) ) {
            unset( $aIndex[array_search($sCacheKey, $aIndex)] );
            $this->_getMemcache()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _getIndex ()
    {
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
       class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getMemcacheKeyFromCacheKey($sCacheKey);
        
        $blSuccess = $this->_getMemcache()->set($sCacheName, $oHttpResult);
        $this->_addPageToIndex($sCacheKey);
        
       class_exists('t') && t::e(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Override
     */
    protected function _fetchHttpResult ($sCacheKey)
    {
       class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getMemcacheKeyFromCacheKey($sCacheKey);
        
        $oHttpResult = $this->_getMemcache()->get($sCacheName);
        
       class_exists('t') && t::e(__METHOD__);
        
        return $oHttpResult;
    }
    
    /**
     * Override
     */
    protected function _deleteHttpResult ($sCacheKey)
    {
       class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getMemcacheKeyFromCacheKey($sCacheKey);
        
        // Memcache::delete() is broken in several versions of php-memcache
        // var_dump($this->_getMemcache()->delete($sCacheName, 0));
        $this->_getMemcache()->set($sCacheName, false);
        $this->_deletePageFromIndex($sCacheKey);
        
       class_exists('t') && t::e(__METHOD__);
    }
    
    /**
     * @param string    $sCacheKey       
     * 
     * @return string
     */
    protected function _getMemcacheKeyFromCacheKey ($sCacheKey)
    {
        return 'CMSc_CmsPage__' . $this->getShopId() . '__' . $sCacheKey;
    }
    
    /**
     * Override
     */
    public function _getCount ()
    {
        $aIndex = $this->_getIndex();

        return count($aIndex);
    }
    
    /**
     * Override
     */
    protected function _getStorageKeysList ()
    {
       class_exists('t') && t::s(__METHOD__);
        
        $aList = $this->_getIndex();
        
        var_dump_pre(__METHOD__, $aList);
        
       class_exists('t') && t::e(__METHOD__);
        
        return $aList;
    }
}