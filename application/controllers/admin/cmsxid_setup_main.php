<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   William Hefter 2014
 */

/**
 * cmsxid_setup_main
 */
class cmsxid_setup_main extends oxAdminView
{
    /**
     * Configures the variables used by this module.
     *
     * @var mixed[]
     */
    protected $_aModuleSettings = array(
        array(  'name'      => 'aCmsxidBaseUrls',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aCmsxidBaseSslUrls',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aCmsxidPagePaths',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aCmsxidLangParams',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aCmsxidIdParams',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aCmsxidParams',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aCmsxidSeoIdents',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'blCmsxidLeaveUrls',
                'type'      => 'bool',
                'global'    => false,
            ),
        array(  'name'      => 'iCmsxidTtlDefault',
                'type'      => 'str',
                'global'    => false,
            ),
        array(  'name'      => 'iCmsxidTtlDefaultRnd',
                'type'      => 'str',
                'global'    => false,
            ),
        array(  'name'      => 'iCmsxidCurlConnectTimeout',
                'type'      => 'str',
                'global'    => false,
            ),
        array(  'name'      => 'iCmsxidCurlExecuteTimeout',
                'type'      => 'str',
                'global'    => false,
            ),
        array(  'name'      => 'blCmsxidEnableDummyContent',
                'type'      => 'bool',
                'global'    => false,
            ),
    );

    /**
     * Constant holding the module name for usage in saveShopConfVar
     *
     * @var string
     */
    const CONFIG_MODULE_NAME = 'cmsxid';
    
    /**
     * Constructor. Sets the template variable to the current class name with .tpl suffix
     *
     * @return string
     *
     * @author William Hefter <william@whefter.de>
     */
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = get_class($this) . '.tpl';
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
        
        foreach ( $this->_aModuleSettings as $aSetting ) {
            // For global settings, the associated shop id is the base shop id.
            $iTargetShopId = $aSetting['global'] ? $oxConfig->getBaseShopId() : $sShopId;
            $this->_aViewData[ $aSetting['name'] ] = $oxConfig->getShopConfVar( $aSetting['name'], $iTargetShopId, 'module:' . static::CONFIG_MODULE_NAME );
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
        
        $aParams = $oxConfig->getParameter('editval');
        
        if ( !is_array($aParams) ) {
            $aParams = array();
        }
        
        foreach ( $this->_aModuleSettings as $aSetting ) {
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
            $oxConfig->saveShopConfVar( $aSetting['type'], $aSetting['name'], $aParams[ $aSetting['name'] ], $iTargetShopId, 'module:' . static::CONFIG_MODULE_NAME );
        }
    }
}