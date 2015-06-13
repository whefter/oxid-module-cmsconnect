<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */

/**
 * cmsxid_oxcontent
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
        if ( $sName == 'page' ) {
            if ( $sCmsxidSeoPage = CmsxidUtils::getCurrentSeoPage() ) {
                if ( $sCmsxidSeoPage !== '' ) {
                    return urlencode($sCmsxidSeoPage);
                }
            }
        }
        
        return parent::getRequestParameter($sName, $blRaw);
    }
}