<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Modules\Core;

use \OxidEsales\Eshop\Core\Registry as Registry;

/**
 * cmsconnect_oxutilsview
 */
class UtilsView extends UtilsView_parent
{
    /**
     * Overwrite parent function to add our custom plugin.
     *
     * @param object    $oSmarty        Smarty object
     *
     * @return void
     */
    protected function _fillCommonSmartyProperties( $oSmarty )
    {
        parent::_fillCommonSmartyProperties( $oSmarty );

        $oSmarty->plugins_dir = array_merge(
            $oSmarty->plugins_dir,
            array( Registry::getConfig()->getModulesDir() . '/wh/cmsconnect/Application/smarty/plugins/' )
        );
    }
}
