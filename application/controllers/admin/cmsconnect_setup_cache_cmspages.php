<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
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
//        t::enable();
        
        $oxConfig = oxRegistry::getConfig();
        $sShopId  = $oxConfig->getShopId();
        
        $aFilters = $oxConfig->getRequestParameter('filters');
        
        $oLocalPagesCache = CMSc_Cache_LocalPages::get();
        $oCmsPagesCache = CMSc_Cache_CmsPages::get();
        
        $oCmsPagesCache->synchronizeIndex();
        
        $this->_aViewData['oLocalPagesCache'] = $oLocalPagesCache;
        $this->_aViewData['oCmsPagesCache'] = $oCmsPagesCache;
        
        $iPage = (int)$oxConfig->getRequestParameter('pgNr') ?: 1;
        $iLimit = 100;
        $iOffset = ($iPage - 1) * $iLimit;
        
        $aFulltextFilters = is_array($aFilters) ? array_map( function ($filter) {
            if ($filter || $filter === 0 || $filter === '0') {
                return '%' . $filter . '%';
            } else {
                return '';
            }
        }, $aFilters) : [];
        
        $this->_aViewData['iPage'] = $iPage;
        $this->_aViewData['iLimit'] = $iLimit;
        $this->_aViewData['iOffset'] = $iOffset;
        $this->_aViewData['aList'] = $oCmsPagesCache->getList($iLimit, $iOffset, $aFulltextFilters);
        $this->_aViewData['aFilters'] = $aFilters;

        // return get_called_class() . '.tpl';
        return parent::render();
    }
    
    public function deleteCmsPage ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $sKey = $oxConfig->getRequestParameter('key');
        
        CMSc_Cache_CmsPages::get()->deleteHttpResultByCacheKey($sKey);
    }
    
    public function deleteCmsPages ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        try {
            $aKeys = json_decode($oxConfig->getRequestParameter('selectedCacheKeysList'), true);
        } catch (Exception $ex) {
            $aKeys = [];
        }
        
        if (!is_array($aKeys)) {
            return;
        }
        
        foreach ($aKeys as $aCacheKey) {
            CMSc_Cache_CmsPages::get()->deleteHttpResultByCacheKey($aCacheKey);
        }
    }
    
    public function deleteAllCmsPages ()
    {
        $oxConfig = oxRegistry::getConfig();
        $oCache = CMSc_Cache_CmsPages::get();
        
        $aList = $oCache->getList();
        
        foreach( $aList as $sCacheKey => $oHttpResult ) {
            $oCache->deleteHttpResultByCacheKey($sCacheKey);
        }
        
        oxRegistry::get('oxUtils')->commitFileCache();
    }
    
    public function deleteAllCmsPagesGlobal ()
    {
        $oxConfig = oxRegistry::getConfig();
        $oCache = CMSc_Cache_CmsPages::get();
        
        foreach ( $oxConfig->getShopIds() as $sShopId ) {
            $oCache->setShopId($sShopId);
            $aList = $oCache->getList();
            
            foreach( $aList as $sCacheKey => $oHttpResult ) {
                $oCache->deleteHttpResultByCacheKey($sCacheKey);
            }
        }
        
        oxRegistry::get('oxUtils')->commitFileCache();
    }
    
    public function showCacheEntryContent ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $sCacheKey = $oxConfig->getRequestParameter('cacheKey');
        
        $oHttpResult = CMSc_Cache_CmsPages::get()->fetchHttpResultByCacheKey($sCacheKey);
        $sContent = $oHttpResult->content;
        
        if ($sContent) {
            try {
                $previousValue = libxml_use_internal_errors(true);
                libxml_clear_errors();

                // Format nicely
                $dom = new DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($sContent, LIBXML_NOWARNING);

                if (count(libxml_get_errors())) {
                    throw new Exception('Error loading XML');
                }

                echo "<pre>";
                echo htmlentities($dom->saveXML());
                echo "</pre>";
            } catch (Exception $ex) {
                // Nothing
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previousValue);
            }
        }
        
        die;
    }
}