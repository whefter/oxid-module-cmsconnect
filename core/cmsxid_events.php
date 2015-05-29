<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */

/**
 * cmsxid_events
 */
class cmsxid_events
{
    /**
     * onActivate
     */
    public static function onActivate()
    {
        cmsxid_dbhandler::addCustomColumns();
    }

    /**
     * onDeactivate
     */
    public static function onDeactivate()
    {
    }
}
