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
        cmsxid_dbhandler::addColumns([
            'oxcontents' => array(
                'CMSXIDPAGE' => array(
                    'multilang' => true,
                    'schema'    => 'varchar(1024) NULL',
                ),
                'CMSXIDPAGEID' => array(
                    'multilang' => false,
                    'schema'    => 'varchar(32) NOT NULL',
                ),
            )
        ]);
    }

    /**
     * onDeactivate
     */
    public static function onDeactivate()
    {
    }
}
