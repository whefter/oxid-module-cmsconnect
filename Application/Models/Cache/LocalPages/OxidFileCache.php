<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models\Cache\LocalPages;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Models\Cache;
use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \t as t;

/**
 * CMSc_Cache_LocalPages_OxidFileCache
 */
class OxidFileCache extends Cache\LocalPages
{
    const ENGINE_LABEL = 'OXID file cache';
    
    protected function _getCacheFilename ($sCacheKey)
    {
        $oxConfig = Registry::getConfig();
        
        return $this->_getCachePrefix() . $sCacheKey;
    }
    
    protected function _getLocalPageCacheFromFileCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $oxUtils = Registry::get('oxUtils');
            
            $sCacheFilename = $this->_getCacheFilename($sCacheKey);
            $sOxidCacheFilepath = $oxUtils->getCacheFilePath($sCacheFilename);
            
            if ( file_exists($sOxidCacheFilepath) ) {
                $aCache = Registry::get('oxUtils')->fromFileCache($sCacheFilename);
                
                if ( $aCache ) {
                    $aCache['pages'] = array_map(function ($v)
                    {
                        return unserialize($v);
                    }, $aCache['pages']);
                }
                
                $this->_aPageCache[$sCacheKey] = $aCache ?: false;
            }
        }
    }
    
    protected function _getLocalPageCache ($sCacheKey)
    {
        $this->_getLocalPageCacheFromFileCache($sCacheKey);

        if (isset($this->_aPageCache[$sCacheKey])) {
            return $this->_aPageCache[$sCacheKey];
        } else {
            return null;
        }
    }
    
    /**
     * Override
     */
    protected function _deleteLocalPageCache ($sCacheKey)
    {
        class_exists('t') && t::s(__METHOD__);

        $sCacheName = $this->_getCacheFilename($sCacheKey);
        
        $sFilePath = Registry::get('oxUtils')->getCacheFilePath($sCacheName);
        unlink($sFilePath);
        
        class_exists('t') && t::e(__METHOD__);
    }
    
    /**
     * Override
     */
    protected function _getCount ()
    {
        $oxUtils = Registry::get('oxUtils');
        $oxConfig = Registry::getConfig();
        
        $sOxidCachePrefix = $this->_getCachePrefix();;
        
        $aFiles = glob($oxUtils->getCacheFilePath(null, true) . '*' . $sOxidCachePrefix . '*');
        
        return count($aFiles);
    }
    
    /**
     * Override
     */
    protected function _getList ($limit = null, $offset = null, $aFilters = [])
    {
        $oxUtils = Registry::get('oxUtils');
        $oxConfig = Registry::getConfig();
        
        $sOxidCachePrefix = $this->_getCachePrefix();;
        
        $aFiles = glob($oxUtils->getCacheFilePath(null, true) . '*' . $sOxidCachePrefix . '*');
        
        $iCnt = 0;
        $aList = [];
        if ( is_array($aFiles) ) {
            foreach ( $aFiles as $sFilePath ) {
                $iCnt++;
                if ( $offset !== null && $iCnt <= $offset ) {
                    continue;
                }
                if ( $limit !== null && $iCnt > ((int)$offset + $limit) ) {
                    continue;
                }
                
                $sOxidCacheKey = substr($sFilePath, strrpos($sFilePath, $sOxidCachePrefix));
                $sOxidCacheKey = substr($sOxidCacheKey, 0, strrpos($sOxidCacheKey, '.'));
                
                $sCacheKey = substr($sOxidCacheKey, strlen($sOxidCachePrefix));
                
                $aCache = $this->_getLocalPageCache($sCacheKey);
                
                if ( $aCache ) {
                    $aList[$sCacheKey] = $aCache;
                }
            }
        }
        
        return $aList;
    }
    
    /**
     * Override parent.
     */
    public function commit ()
    {
        foreach ( $this->_aPageCache as $sCacheKey => $aCache ) {
            $aCache['pages'] = array_map(function ($v)
            {
                return serialize($v);
            }, $aCache['pages']);
            
            $blSuccess = Registry::get('oxUtils')->toFileCache( $this->_getCacheFilename($sCacheKey), $aCache );
        }
    }
}