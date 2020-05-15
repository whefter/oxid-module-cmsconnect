<?php
namespace wh\CmsConnect\Modules\Core;

use wh\CmsConnect\Application\Utils as CMSc_Utils;

/**
 * cmsconnect_oxconfig
 */
class Config extends Config_parent
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
        // the CMSconnect classes have been loaded, we don't need them before that
        // anyway.

        if (class_exists('CMSc_Utils')) {
            if ($sName == 'page') {
                if ($sCMScSeoPage = CMSc_Utils::getCurrentLocalPageSeoPath()) {
                    if ($sCMScSeoPage !== '') {
                        return urlencode($sCMScSeoPage);
                    }
                }
            }
        }

        return parent::getRequestParameter($sName, $blRaw);
    }
}
