<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * cmsconnect_setup_list
 */
class cmsconnect_setup_list extends Shop_List
{
    function render ()
    {
        $sParentTemplate = parent::render();
        
        return 'modules/wh/cmsconnect/admin/' . str_replace('cmsconnect_', '', get_class($this)) . '.tpl';
    }
}