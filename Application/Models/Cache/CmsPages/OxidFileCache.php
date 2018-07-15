<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models\Cache\CmsPages;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Models\Cache;
use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \t as t;

/**
 * CMSc_Cache_CmsPages_OxidFileCache
 */
class OxidFileCache extends Cache\CmsPages
{
    const ENGINE_LABEL = 'OXID file cache';
    
    /**
     * Override
     */
    protected function _saveHttpResult ($sCacheKey, $oHttpResult)
    {
        class_exists('t') && t::s(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        $blSuccess = Registry::get('oxUtils')->toFileCache($sCacheName, $oHttpResult);
        
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
        
        $oHttpResult = Registry::get('oxUtils')->fromFileCache($sCacheName);
        
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
        
        $sFilePath = Registry::get('oxUtils')->getCacheFilePath($sCacheName);
        
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
        
        $oxUtils = Registry::get('oxUtils');
        
        $sOxidCachePrefix = $this->_getCachePrefix();
        $aList = [];
        
        $iterator = new \DirectoryIterator($oxUtils->getCacheFilePath(null, true));
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