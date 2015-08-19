<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   William Hefter 2014
 */

/**
 * cmsxid_setup
 */
class cmsxid_setup_list extends Shop_List
{
    /**
     * Constructor. Sets the template variable to the current class name with .tpl suffix
     *
     * @return string
     */
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = get_class($this) . '.tpl';
    }
}