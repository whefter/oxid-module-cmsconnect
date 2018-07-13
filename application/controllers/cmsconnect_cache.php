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
        
        $sPageId = $oxConfig->getRequestParameter('pageId');
        $sPagePath = $oxConfig->getRequestParameter('pagePath');
        if ($sPagePath && ($sPageId || $sPageId === '0' || $sPageId === 0)) {
            echo 'ERROR: cannot specify both pageId and pagePath.';
            die;
        }
        if (!$sPagePath && (!$sPageId && $sPageId !== '0' && $sPageId !== 0)) {
            echo 'ERROR: must specify either pageId or pagePath.';
            die;
        }
        
        $sShopId = $oxConfig->getRequestParameter('shopId');
        if ($sShopId === 'current') {
            $aShopIds = [$oxConfig->getShopId()];
        } else {
            $aShopIds = $oxConfig->getShopIds();
        }

        class_exists('t') && t::s(__METHOD__);

        $iCount = 0;
        foreach ( $aShopIds as $sShopId ) {
//            var_dump_pre(__METHOD__, '$sShopId', $sShopId);

            class_exists('t') && t::s("shop $sShopId");

            $oCmsPagesCache->setShopId($sShopId);
            $oCmsPagesCache->synchronizeIndex();

            if ($sPagePath) {
                $aList = $oCmsPagesCache->getList(0, 0, ['pagepath' => $sPagePath]);
            } else if ($sPageId || $sPageId === '0' || $sPageId === 0) {
                $aList = $oCmsPagesCache->getList(0, 0, ['pageid' => $sPageId]);
            }

            if ($aList && is_array($aList)) {
                foreach ($aList as $sCacheKey => $oHttpResult) {
//                    echo "Deleting: $sCacheKey " . $oHttpResult->info['url'] . "\n<br>";

                    $oCmsPagesCache->deleteHttpResultByCacheKey($sCacheKey);
                }

                $iCount += count($aList);
            }

            class_exists('t') && t::e("shop $sShopId");
        }

        $oCmsPagesCache->setShopId(null);

        oxRegistry::get('oxUtils')->commitFileCache();
        
        echo $iCount;
        
        class_exists('t') && t::e(__METHOD__);
        
        class_exists('t') && t::dAll();
        
        die;
    }
}
