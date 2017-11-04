<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * cmsconnect_setup_main
 */
class cmsconnect_setup_main extends oxAdminView
{
    /**
     * Constructor. Sets the template variable to the current class name with .tpl suffix
     *
     * @return string
     */
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = 'modules/wh/cmsconnect/admin/' . str_replace('cmsconnect_', '', get_class($this)) . '.tpl';
    }
    
    /**
     * Reads all configured settings and sets them as template variables.
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        $oxConfig   = oxRegistry::getConfig();
        $sShopId    = $oxConfig->getShopId();
        
        foreach ( CMSc_Utils::getMetadataSettings() as $aSetting ) {
            // For global settings, the associated shop id is the base shop id.
            $iTargetShopId = $aSetting['global'] ? $oxConfig->getBaseShopId() : $sShopId;
            $this->_aViewData[ $aSetting['name'] ] = $oxConfig->getShopConfVar( $aSetting['name'], $iTargetShopId, 'module:' . CMSc_Utils::CONFIG_MODULE_NAME );
        }

        return parent::render();
    }

    /**
     * Saves the all configured settings.
     *
     * @return void
     */
    public function save()
    {
        $oxConfig   = oxRegistry::getConfig();
        $sShopId    = $oxConfig->getShopId();
        
        $aParams = $oxConfig->getRequestParameter('editval');
        
        if ( !is_array($aParams) ) {
            $aParams = array();
        }
        
        foreach ( CMSc_Utils::getMetadataSettings() as $aSetting ) {
            // Don't overwrite params just because they're not implemented in the current page
            if ( !array_key_exists($aSetting['name'], $aParams) ) {
                continue;
            }
            
            // Handle checkboxes
            if ( $aSetting['type'] == 'bool' ) {
                if ( !array_key_exists( $aSetting['name'], $aParams ) ) {
                    $aParams[ $aSetting['name'] ] = '0';
                }
            }
            
            // For global settings, the associated shop id is the base shop id.
            $iTargetShopId = $aSetting['global'] ? $oxConfig->getBaseShopId() : $sShopId;
            $oxConfig->saveShopConfVar( $aSetting['type'], $aSetting['name'], $aParams[ $aSetting['name'] ], $iTargetShopId, 'module:' . CMSc_Utils::CONFIG_MODULE_NAME );
        }
    }
}