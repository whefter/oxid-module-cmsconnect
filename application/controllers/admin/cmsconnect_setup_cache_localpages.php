<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * cmsconnect_setup_cache_localpages
 */
class cmsconnect_setup_cache_localpages extends cmsconnect_setup_main
{
    /**
     * @return string
     */
    public function render()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $oLocalPagesCache = CMSc_Cache_LocalPages::get();
        $oCmsPagesCache = CMSc_Cache_CmsPages::get();
        
        $oCmsPagesCache->synchronizeIndex();
        
        $this->_aViewData['oLocalPagesCache'] = $oLocalPagesCache;
        $this->_aViewData['oCmsPagesCache'] = $oCmsPagesCache;
        
        $iPage = (int)$oxConfig->getRequestParameter('pgNr') ?: 1;
        $iLimit = 50;
        $iOffset = ($iPage - 1) * $iLimit;
        
        $this->_aViewData['iPage'] = $iPage;
        $this->_aViewData['iLimit'] = $iLimit;
        $this->_aViewData['iOffset'] = $iOffset;
        $this->_aViewData['aList'] = $oLocalPagesCache->getList($iLimit, $iOffset);
        $this->_aViewData['iCount'] = $oLocalPagesCache->getCount();

        // return get_called_class() . '.tpl';
        return parent::render();
    }
    
    public function deleteLocalPage ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $sKey = $oxConfig->getRequestParameter('key');
        
        CMSc_Cache_LocalPages::get()->deleteLocalPageCache($sKey);
    }
    
    public function deleteAllLocalPages ()
    {
        $oxConfig = oxRegistry::getConfig();
        $oCache = CMSc_Cache_LocalPages::get();
        
        $aList = $oCache->getList();
        foreach( $aList as $sCacheKey => $oLocalPageCache ) {
            $oCache->deleteLocalPageCache($sCacheKey);
        }
    }
    
    public function deleteAllLocalPagesGlobal ()
    {
        $oxConfig = oxRegistry::getConfig();
        $oCache = CMSc_Cache_LocalPages::get();
        
        foreach ( $oxConfig->getShopIds() as $sShopId ) {
            $oCache->setShopId($sShopId);
            $aList = $oCache->getList();
            
            foreach( $aList as $sCacheKey => $oLocalPageCache ) {
                $oCache->deleteLocalPageCache($sCacheKey);
            }
        }
    }
}