<?php
function smarty_modifier_cmsc_rewriteurl ($string)
{
    return CMSc_Utils::rewriteUrl($string);
}