<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */

/**
 * cmsxid_oxutilsview
 */
class cmsxid_oxutilsview extends cmsxid_oxutilsview_parent
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
            array( oxRegistry::getConfig()->getModulesDir() . '/wh/cmsxid/core/smarty/plugins' )
        );
    }
}
