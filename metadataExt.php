<?php
/**
 * OXID >= 6.2 has strict validation of the metadata schema.
 * To work around this, extended information is stored in here, then merged.
 * This is a pretty rudimentary workaround and will be reconsidered at a later point.
 */

$aModuleExt = array(
    'settings' => array(
        array(
            'name'      => 'sCMScLocalPageCacheEngine',
            'global'    => true,
        ),
        array(
            'name'      => 'sCMScCmsPageCacheEngine',
            'global'    => true,
        ),
    ),
);


return $aModuleExt;
