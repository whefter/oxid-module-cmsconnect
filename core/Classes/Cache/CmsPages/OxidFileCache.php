<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
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
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        
        $blSuccess = oxRegistry::get('oxUtils')->toFileCache($sCacheName, $oHttpResult);
        
        stopProfile(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Override
     */
    protected function _fetchHttpResult ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        
        $oHttpResult = oxRegistry::get('oxUtils')->fromFileCache($sCacheName);
        
        stopProfile(__METHOD__);
        
        return $oHttpResult;
    }
    
    /**
     * Override
     */
    protected function _deleteHttpResult ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->_getCacheFilenameFromKey($sCacheKey);
        
        $sFilePath = oxRegistry::get('oxUtils')->getCacheFilePath($sCacheName);
        unlink($sFilePath);
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
    public function _getCount ()
    {
        $oxUtils = oxRegistry::get('oxUtils');
        $oxConfig = oxRegistry::getConfig();
        
        $sOxidCachePrefix = $this->_getCachePrefix();;
        
        $aFiles = glob($oxUtils->getCacheFilePath(null, true) . '*' . $sOxidCachePrefix . '*');
        
        return count($aFiles);
    }
    
    /**
     * Override
     */
    protected function _getList ($limit = null, $offset = null)
    {
        startProfile(__METHOD__);
        
        $oxUtils = oxRegistry::get('oxUtils');
        
        $sOxidCachePrefix = $this->_getCachePrefix();
        
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

                $aList[$sCacheKey] = oxRegistry::get('oxUtils')->fromFileCache( $sOxidCacheKey );
            }
        }
        
        stopProfile(__METHOD__);
        
        return $aList;
    }
}