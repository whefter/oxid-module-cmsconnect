<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
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
        $sShopId  = $oxConfig->getShopId();
        
        $this->_aViewData['oLocalPagesCache'] = CMSc_Cache_LocalPages::get();
        $this->_aViewData['oCmsPagesCache'] = CMSc_Cache_CmsPages::get();

        // return get_called_class() . '.tpl';
        return parent::render();
    }
}