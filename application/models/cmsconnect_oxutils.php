<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * cmsconnect_oxutils
 */
class cmsconnect_oxutils extends cmsconnect_oxutils_parent
{
    public function commitFileCache()
    {
        CMSc_Cache_LocalPages::get()->commit();
        
        return call_user_func_array('parent::commitFileCache', func_get_args());
    }
}