<?php
function smarty_modifier_cmsxid_rewriteurl ($string)
{
    return CmsxidUtils::rewriteContentUrls($string);
}