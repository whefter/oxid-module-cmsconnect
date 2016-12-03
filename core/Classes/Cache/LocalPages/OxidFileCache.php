<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages_OxidFileCache
 */
class CMSc_Cache_LocalPages_OxidFileCache extends CMSc_Cache_LocalPages
{
    const ENGINE_LABEL = 'OXID file cache';
    
    protected $_aPageCache = [];
    
    protected function _getCacheFilename ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $this->_getCachePrefix() . $sCacheKey;
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
        
        $blSuccess = oxRegistry::get('oxUtils')->toFileCache( $this->_getCacheFilename($sCacheKey), $aLocalPageCache );
    }
    
    
    protected function _getLocalPageCacheFromFileCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $oxUtils = oxRegistry::get('oxUtils');
            
            $sCacheFilename = $this->_getCacheFilename($sCacheKey);
            $sOxidCacheFilepath = $oxUtils->getCacheFilePath($sCacheFilename);
            
            if ( file_exists($sOxidCacheFilepath) ) {
                $aCache = oxRegistry::get('oxUtils')->fromFileCache($sCacheFilename);
                
                if ( $aCache ) {
                    $aCache['pages'] = array_map(function ($v)
                    {
                        return unserialize($v);
                    }, $aCache['pages']);
                }
                
                $this->_aPageCache[$sCacheKey] = $aCache;
            }
        }
    }
    
    protected function _getLocalPageCache ($sCacheKey)
    {
        $this->_getLocalPageCacheFromFileCache($sCacheKey);
        
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

        $sCacheName = $this->_getCacheFilename($sCacheKey);
        
        $sFilePath = oxRegistry::get('oxUtils')->getCacheFilePath($sCacheName);
        unlink($sFilePath);
        
        stopProfile(__METHOD__);
    }
    
    /**
     * Override
     */
    protected function _getList ()
    {
        $oxUtils = oxRegistry::get('oxUtils');
        $oxConfig = oxRegistry::getConfig();
        
        $sOxidCachePrefix = $this->_getCachePrefix();;
        
        $aFiles = glob($oxUtils->getCacheFilePath(null, true) . '*' . $sOxidCachePrefix . '*');
        
        $aList = [];
        if ( is_array($aFiles) ) {
            foreach ( $aFiles as $sFilePath ) {
                $sOxidCacheKey = substr($sFilePath, strrpos($sFilePath, $sOxidCachePrefix));
                $sOxidCacheKey = substr($sOxidCacheKey, 0, strrpos($sOxidCacheKey, '.'));
                
                $sCacheKey = substr($sOxidCacheKey, strlen($sOxidCachePrefix));
                
                $aList[$sCacheKey] = $this->_getLocalPageCache($sCacheKey);
            }
        }
        
        return $aList;
    }
}