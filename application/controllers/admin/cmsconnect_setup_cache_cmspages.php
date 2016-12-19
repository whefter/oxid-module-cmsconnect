<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * cmsconnect_setup_cache_cmspages
 */
class cmsconnect_setup_cache_cmspages extends cmsconnect_setup_main
{
    /**
     * @return string
     */
    public function render()
    {
        $oxConfig = oxRegistry::getConfig();
        $sShopId  = $oxConfig->getShopId();
        
        $this->_aViewData['oLocalPagesCache'] = CMSc_Cache_LocalPages::get();
        $this->_aViewData['oCmsPagesCache'] = CMSc_Cache_CmsPages::get();
        
        $iPage = (int)$oxConfig->getRequestParameter('pgNr') ?: 1;
        $iLimit = 100;
        $iOffset = ($iPage - 1) * $iLimit;
        
        $this->_aViewData['iPage'] = $iPage;
        $this->_aViewData['iLimit'] = $iLimit;
        $this->_aViewData['iOffset'] = $iOffset;

        // return get_called_class() . '.tpl';
        return parent::render();
    }
    
    public function deleteCmsPage ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $sKey = $oxConfig->getRequestParameter('key');
        
        CMSc_Cache_CmsPages::get()->deleteHttpResultByIdent($sKey);
    }
    
    public function deleteAllCmsPages ()
    {
        $oxConfig = oxRegistry::getConfig();
        $oCache = CMSc_Cache_CmsPages::get();
        
        $aList = $oCache->getList();
        
        foreach( $aList as $sCacheKey => $oHttpResult ) {
            $oCache->deleteHttpResultByIdent($sCacheKey);
        }
    }
    
    public function deleteAllCmsPagesGlobal ()
    {
        $oxConfig = oxRegistry::getConfig();
        $oCache = CMSc_Cache_CmsPages::get();
        
        foreach ( $oxConfig->getShopIds() as $sShopId ) {
            $oCache->setShopId($sShopId);
            $aList = $oCache->getList();
            
            foreach( $aList as $sCacheKey => $oHttpResult ) {
                $oCache->deleteHttpResultByIdent($sCacheKey);
            }
        }
    }
}