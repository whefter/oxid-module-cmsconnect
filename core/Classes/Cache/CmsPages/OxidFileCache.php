<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * CMSc_Cache_CmsPages_OxidFileCache
 */
class CMSc_Cache_CmsPages_OxidFileCache extends CMSc_Cache_CmsPages
{
    const ENGINE_LABEL = 'OXID file cache';
    
    /**
     * Override
     */
    protected function _saveHttpResult ($sCacheKey, $oHttpResult)
    {
        class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        $blSuccess = oxRegistry::get('oxUtils')->toFileCache($sCacheName, $oHttpResult);
        
        class_exists('t') && t::e(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Override
     */
    protected function _fetchHttpResult ($sCacheKey)
    {
        class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        
        $oHttpResult = oxRegistry::get('oxUtils')->fromFileCache($sCacheName);
        
        class_exists('t') && t::e(__METHOD__);
        
        return $oHttpResult;
    }
    
    /**
     * Override
     */
    protected function _deleteHttpResult ($sCacheKey)
    {
        class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        
        $sFilePath = oxRegistry::get('oxUtils')->getCacheFilePath($sCacheName);
        
        if (file_exists($sFilePath)) {
            unlink($sFilePath);
        }
        
        class_exists('t') && t::e(__METHOD__);
    }
    
    /**
     * @param string    $sCacheKey       
     * 
     * @return string
     */
    protected function _getCacheFilenameFromKey ($sCacheKey)
    {
        return $this->_getCachePrefix() . $sCacheKey;
    }
    
    /**
     * Override
     */
    protected function _getStorageKeysList ()
    {
        class_exists('t') && t::s('OxidFileCache::_getStorageKeysList');
        
        $oxUtils = oxRegistry::get('oxUtils');
        
        $sOxidCachePrefix = $this->_getCachePrefix();
        $aList = [];
        
        $iterator = new DirectoryIterator($oxUtils->getCacheFilePath(null, true));
        foreach ( $iterator as $oFileInfo ) {
            if (strpos($oFileInfo->getFilename(), $sOxidCachePrefix) === false) {
                continue;
            }
            
            $sCacheKey = substr($oFileInfo->getFilename(), strrpos($oFileInfo->getFilename(), $sOxidCachePrefix));
            $sCacheKey = substr($sCacheKey, 0, strrpos($sCacheKey, '.'));
            $sCacheKey = substr($sCacheKey, strlen($sOxidCachePrefix));

            $aList[] = $sCacheKey;
        }
        
        class_exists('t') && t::e('OxidFileCache::_getStorageKeysList');
        
        return $aList;
    }
}