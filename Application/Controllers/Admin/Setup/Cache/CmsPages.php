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
 * cmsconnect_setup_cache_cmspages
 */
class CmsPages extends Setup\Main
{
    /**
     * Constructor. Sets the template variable to the current class name with .tpl suffix
     *
     * @return string
     */
    function __construct()
    {
        parent::__construct();

        $this->_sThisTemplate = 'modules/wh/cmsconnect/admin/setup_cache_cmspages.tpl';
    }

    /**
     * @return string
     */
    public function render()
    {
        $oxConfig = Registry::getConfig();
        
        $aFilters = $oxConfig->getRequestParameter('filters');
        
        $oLocalPagesCache = Cache\LocalPages::get();
        $oCmsPagesCache = Cache\CmsPages::get();
        
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
        $this->_aViewData['iCount'] = $oCmsPagesCache->getCount($aFulltextFilters);
        $this->_aViewData['aFilters'] = $aFilters;

        // return get_called_class() . '.tpl';
        return parent::render();
    }
    
    public function deleteCmsPage ()
    {
        $oxConfig = Registry::getConfig();
        
        $sKey = $oxConfig->getRequestParameter('key');

        Cache\CmsPages::get()->deleteHttpResultByCacheKey($sKey);
    }
    
    public function deleteCmsPages ()
    {
        $oxConfig = Registry::getConfig();
        
        try {
            $aKeys = json_decode($oxConfig->getRequestParameter('selectedCacheKeysList'), true);
        } catch (\Exception $ex) {
            $aKeys = [];
        }
        
        if (!is_array($aKeys)) {
            return;
        }
        
        foreach ($aKeys as $aCacheKey) {
            Cache\CmsPages::get()->deleteHttpResultByCacheKey($aCacheKey);
        }
    }
    
    public function deleteAllCmsPages ()
    {
        $oxConfig = Registry::getConfig();
        $oCache = Cache\CmsPages::get();
        
        $aList = $oCache->getList();
        
        foreach( $aList as $sCacheKey => $oHttpResult ) {
            $oCache->deleteHttpResultByCacheKey($sCacheKey);
        }
        
        Registry::get('oxUtils')->commitFileCache();
    }
    
    public function deleteAllCmsPagesGlobal ()
    {
        $oxConfig = Registry::getConfig();
        $oCache = Cache\CmsPages::get();
        
        foreach ( $oxConfig->getShopIds() as $sShopId ) {
            $oCache->setShopId($sShopId);
            $aList = $oCache->getList();
            
            foreach( $aList as $sCacheKey => $oHttpResult ) {
                $oCache->deleteHttpResultByCacheKey($sCacheKey);
            }
        }
        
        Registry::get('oxUtils')->commitFileCache();
    }
    
    public function showCacheEntryContent ()
    {
        $oxConfig = Registry::getConfig();
        
        $sCacheKey = $oxConfig->getRequestParameter('cacheKey');
        
        $oHttpResult = Cache\CmsPages::get()->fetchHttpResultByCacheKey($sCacheKey);
        $sContent = $oHttpResult->content;
        
        echo '<h1>Content</h1>';
        if ($sContent) {
            try {
                $previousValue = libxml_use_internal_errors(true);
                libxml_clear_errors();

                // Format nicely
                $dom = new \DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($sContent, LIBXML_NOWARNING);

                if (count(libxml_get_errors())) {
                    throw new \Exception('Error loading XML');
                }

                echo '<pre>';
                echo htmlentities($dom->saveXML());
                echo '</pre>';
            } catch (\Exception $ex) {
                // Nothing
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previousValue);
            }
            
        }
        
        echo '<br /><br />';
        echo '<h1>cURL info</h1>';
        echo '<pre>';
        print_r($oHttpResult->info);
        echo '</pre>';
        
        die;
    }
}