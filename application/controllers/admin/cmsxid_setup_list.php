<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   William Hefter 2014-2016
 */

/**
 * cmsxid_setup
 */
class cmsxid_setup_list extends Shop_List
{
    function render ()
    {
        $sParentTemplate = parent::render();
        
        return get_called_class() . '.tpl';
    }
}