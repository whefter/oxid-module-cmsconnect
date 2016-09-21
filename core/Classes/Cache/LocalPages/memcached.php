<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages_memcached
 */
class CMSc_Cache_LocalPages_memcached extends CMSc_Cache_LocalPages
{
    const ENGINE_LABEL = 'memcached';
    
    protected $_oMemcached = null;
    
    protected $_aPageCache = [];
    
    protected function _getMemcached ()
    {
        if ( $this->_oMemcached === null ) {
            $this->_oMemcached = new Memcached();
            $this->_oMemcached->addServer('127.0.0.1', 11211);
        }
        
        return $this->_oMemcached;
    }
    
    protected function _getMemcacheddKey ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        return 'cmsconnect_localpage_' . $oxConfig->getShopId() . '_' . $sCacheKey;
    }
    
    protected function _addPageToIndex ($sCacheKey)
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( !in_array($sCacheKey, $aIndex) ) {
            $aIndex[] = $sCacheKey;
            $this->_getMemcached()->set('cmsconnect_localpages_' . $oxConfig->getShopId(), $aIndex);
        }
    }
    
    protected function _getIndex ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aIndex = $this->_getMemcached()->get('cmsconnect_localpages_' . $oxConfig->getShopId());
        
        if ( !$aIndex ) {
            $aIndex = [];
        }
        
        return $aIndex;
    }
    
    protected function _setLocalPageCache ($sCacheKey, $aLocalPageCache)
    {
        $this->_aPageCache[$sCacheKey] = $aLocalPageCache;
        
        $this->_getMemcached()->set( $this->_getMemcacheddKey($sCacheKey), $aLocalPageCache );
        
        $this->_addPageToIndex($sCacheKey);
    }
    
    protected function _getLocalPageCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $aCache = $this->_getMemcached()->get( $this->_getMemcacheddKey($sCacheKey) );
            
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