<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * cmsconnect_events
 */
class cmsconnect_events
{
    protected static $aColumns = array(
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
    );
    
    protected static $aTables = array(
        'wh_cmsc_cache_localpages_data' => array(
            'multishop'     => false,
            'multilang'     => false,
            'sql'           => [
                "
                CREATE TABLE IF NOT EXISTS `wh_cmsc_cache_localpages_data` (
                  `KEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Unique local cache key',
                  `DATA` varchar(60000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Page data'
                ) DEFAULT CHARSET=utf8 COMMENT='CMSconnect local pages cache data.';
                ",
                "
                ALTER TABLE `wh_cmsc_cache_localpages_data`
                  ADD PRIMARY KEY (`KEY`);
                ",
            ],
        ),
        'wh_cmsc_cache_localpages_pages' => array(
            'multishop'     => false,
            'multilang'     => false,
            'sql'           => [
                "
                CREATE TABLE IF NOT EXISTS `wh_cmsc_cache_localpages_pages` (
                  `ID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Record ID',
                  `LOCALPAGEKEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Local page cache key',
                  `CMSPAGEKEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'CMS page cache key',
                  `CMSPAGEDATA` varchar(60000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'CMS page serialized data'
                ) DEFAULT CHARSET=utf8 COMMENT='CMSconnect local pages cache pages.';
                ",
                "
                ALTER TABLE `wh_cmsc_cache_localpages_pages`
                  ADD PRIMARY KEY (`ID`),
                  ADD KEY (`LOCALPAGEKEY`);
                ",
            ],
        ),
    );
    
    /**
     * onActivate
     */
    public static function onActivate()
    {
        cmsconnect_dbhandler::addModuleTables(static::$aTables);
        cmsconnect_dbhandler::addColumns(static::$aColumns);
    }

    /**
     * onDeactivate
     */
    public static function onDeactivate()
    {
    }
}
