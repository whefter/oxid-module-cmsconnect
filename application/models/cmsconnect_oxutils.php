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
        $mRet = call_user_func_array('parent::commitFileCache', func_get_args());
        
        try {
            CMSc_Cache_LocalPages::get()->commit();
            CMSc_Cache_CmsPages::get()->commit();
        } catch (Exception $ex) {
//            var_dump(__METHOD__, $ex->getMessage(), $ex->getTraceAsString());
            // Don't endanger the shop
        }
        
        return $mRet;
    }
}