<?php
use \wh\CmsConnect\Application\Utils as CMSc_Utils;

function smarty_modifier_cmsc_rewriteurl ($string)
{
    return CMSc_Utils::rewriteUrl($string);
}