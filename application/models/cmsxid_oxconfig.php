<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */

/**
 * cmsxid_oxconfig
 */
class cmsxid_oxconfig extends cmsxid_oxconfig_parent
{
    /**
     * When asked for the 'page' request parameter, the native OXID function returns the last part
     * of the current SEO path only.
     *
     * @param   string      $sName      See parent definition
     * @param   bool        $blRaw      See parent definition
     *
     * @return bool
     */
    public function getRequestParameter($sName, $blRaw = false)
    {
        // This method is called at such an early point in the execution that
        // not all module classes have been loaded. Skip our checks until after
        // the CMSxid classes have been loaded, we don't need them before that 
        // anyway.
        
        if ( class_exists('CmsxidUtils') ) {
            $oUtils = CmsxidUtils::getInstance();
            
            if ( $sName == 'page' ) {
                if ( $sCmsxidSeoPage = $oUtils->getCurrentSeoPage() ) {
                    if ( $sCmsxidSeoPage !== '' ) {
                        return urlencode($sCmsxidSeoPage);
                    }
                }
            }
        }
        
        return parent::getRequestParameter($sName, $blRaw);
    }
}