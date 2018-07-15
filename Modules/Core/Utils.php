<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Modules\Core;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Models\CmsPage;
use \wh\CmsConnect\Application\Models\Cache;

/**
 * cmsconnect_oxutils
 */
class Utils extends Utils_parent
{
    public function commitFileCache()
    {
        $mRet = call_user_func_array('parent::commitFileCache', func_get_args());
        
        try {
            Cache\LocalPages::get()->commit();
            Cache\CmsPages::get()->commit();
        } catch (\Exception $ex) {
//            var_dump(__METHOD__, $ex->getMessage(), $ex->getTraceAsString());
            // Don't endanger the shop
        }
        
        return $mRet;
    }
}
