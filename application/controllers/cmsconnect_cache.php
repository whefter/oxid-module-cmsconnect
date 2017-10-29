<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */
 
/**
 * cmsconnect_cache
 */
class cmsconnect_cache extends oxUBase
{
    /**
     * Constructor. Sets the $_sThisTemplate property.
     
     * @return void
     */   
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = 'modules/wh/cmsconnect/cache.tpl';
    }
    
    public function clearCmsPageCaches ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $oCmsPagesCache = CMSc_Cache_CmsPages::get();
        
        $oCmsPagesCache->synchronizeIndex();
        
        $sPageId = $oxConfig->getRequestParameter('pageId');
        $sPagePath = $oxConfig->getRequestParameter('pagePath');
        
        if ($sPagePath && ($sPageId || $sPageId === '0' || $sPageId === 0)) {
            echo 'ERROR: Cannot specify both pageId and pagePath.';
            die;
        }
        
        if ($sPagePath) {
            $aList = $oCmsPagesCache->getList(0, 0, ['pagepath' => $sPagePath]);
            
        } else if ($sPageId || $sPageId === '0' || $sPageId === 0) {
            $aList = $oCmsPagesCache->getList(0, 0, ['pageid' => $sPageId]);
            
            foreach ($aList as $sCacheKey => $oHttpResult) {
                $oCmsPagesCache->deleteHttpResultByCacheKey($sCacheKey);
            }
        }
        
        if ($aList && is_array($aList)) {
            foreach ($aList as $sCacheKey => $oHttpResult) {
                $oCmsPagesCache->deleteHttpResultByCacheKey($sCacheKey);
            }

            echo count($aList);
        } else {
            echo "0";
        }
        
        oxRegistry::get('oxUtils')->commitFileCache();
        
        die;
    }
}
