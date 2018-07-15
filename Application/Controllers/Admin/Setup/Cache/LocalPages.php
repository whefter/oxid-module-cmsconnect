<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Controllers\Admin\Setup\Cache;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Controllers\Admin\Setup;
use \wh\CmsConnect\Application\Models\CmsPage;
use \wh\CmsConnect\Application\Models\Cache;

/**
 * cmsconnect_setup_cache_localpages
 */
class LocalPages extends Setup\Main
{
    /**
     * Constructor. Sets the template variable to the current class name with .tpl suffix
     *
     * @return string
     */
    function __construct()
    {
        parent::__construct();

        $this->_sThisTemplate = 'modules/wh/cmsconnect/admin/setup_cache_localpages.tpl';
    }

    /**
     * @return string
     */
    public function render()
    {
        $oxConfig = Registry::getConfig();
        
        $oLocalPagesCache = Cache\LocalPages::get();
        $oCmsPagesCache = Cache\CmsPages::get();
        
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
        $oxConfig = Registry::getConfig();
        
        $sKey = $oxConfig->getRequestParameter('key');

        Cache\LocalPages::get()->deleteLocalPageCache($sKey);
    }
    
    public function deleteAllLocalPages ()
    {
        $oxConfig = Registry::getConfig();
        $oCache = Cache\LocalPages::get();
        
        $aList = $oCache->getList();
        foreach( $aList as $sCacheKey => $oLocalPageCache ) {
            $oCache->deleteLocalPageCache($sCacheKey);
        }
    }
    
    public function deleteAllLocalPagesGlobal ()
    {
        $oxConfig = Registry::getConfig();
        $oCache = Cache\LocalPages::get();
        
        foreach ( $oxConfig->getShopIds() as $sShopId ) {
            $oCache->setShopId($sShopId);
            $aList = $oCache->getList();
            
            foreach( $aList as $sCacheKey => $oLocalPageCache ) {
                $oCache->deleteLocalPageCache($sCacheKey);
            }
        }
    }
}