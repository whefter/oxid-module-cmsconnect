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
    protected $_aPageCache = [];
    
    protected function _getCacheFilename ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        return 'cmsconnect_localpage_' . $oxConfig->getShopId() . '_' . $sCacheKey;
    }
    
    protected function _setLocalPageCache ($sCacheKey, $aLocalPageCache)
    {
        $this->_aPageCache[$sCacheKey] = $aLocalPageCache;
        
        $blSuccess = oxRegistry::get('oxUtils')->toFileCache( $this->_getCacheFilename($sCacheKey), $aLocalPageCache );
    }
    
    
    protected function _getLocalPageCacheFromFileCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $aCache = oxRegistry::get('oxUtils')->fromFileCache( $this->_getCacheFilename($sCacheKey) );
            
            $this->_aPageCache[$sCacheKey] = $aCache;
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
    protected function _getList ()
    {
        $oxUtils = oxRegistry::get('oxUtils');
        $oxConfig = oxRegistry::getConfig();
        
        $aFiles = glob($oxUtils->getCacheFilePath(null, true) . '*cmsconnect_localpage_' . $oxConfig->getShopId() . '_*');
        
        $aList = [];
        if ( is_array($aFiles) ) {
            foreach ( $aFiles as $sFilePath ) {
                $sOxidCacheKey = substr($sFilePath, strrpos($sFilePath, 'cmsconnect_localpage_' . $oxConfig->getShopId()));
                $sOxidCacheKey = substr($sOxidCacheKey, 0, strrpos($sOxidCacheKey, '.'));
                
                $aList[$sOxidCacheKey] = $this->_getLocalPageCache($sOxidCacheKey);
            }
        }
        
        return $aList;
    }
}