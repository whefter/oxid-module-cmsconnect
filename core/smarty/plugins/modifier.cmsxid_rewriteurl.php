<?php
function smarty_modifier_cmsxid_rewriteurl ($string)
{
    $oUtils = CmsxidUtils::getInstance();
        
    return $oUtils->rewriteContentUrls($string);
}