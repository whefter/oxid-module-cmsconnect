<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Controllers\Admin\Setup;

/**
 * cmsconnect_setup_list
 */
class ListController extends \OxidEsales\Eshop\Application\Controller\Admin\ShopList
{
    function render ()
    {
        $sParentTemplate = parent::render();
        
//        return 'modules/wh/cmsconnect/admin/setup_list.tpl';
        return 'shop_list.tpl';
    }
}
