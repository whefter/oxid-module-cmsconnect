<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
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
        'wh_cmsc_cache_cmspages' => array(
            'multishop'     => false,
            'multilang'     => false,
            'sql'           => [
                "
                CREATE TABLE IF NOT EXISTS `wh_cmsc_cache_cmspages` (
                  `KEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'CMS page cache key',
                  `DATA` varchar(10240) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'CMS page serialized data'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMSconnect cache CMS pages.';
                ",
                "
                ALTER TABLE `wh_cmsc_cache_cmspages`
                  ADD KEY `KEY` (`KEY`);
                ",
            ],
        ),
        'wh_cmsc_cache_localpages' => array(
            'multishop'     => false,
            'multilang'     => false,
            'sql'           => [
                "
                CREATE TABLE IF NOT EXISTS `wh_cmsc_cache_localpages` (
                  `OXSHOPID` int(11) NOT NULL,
                  `KEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Local page cache key',
                  `DATA` varchar(10240) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Local page serialized data'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMSconnect cache local pages.';
                ",
                "
                ALTER TABLE `wh_cmsc_cache_localpages`
                  ADD KEY `KEY` (`KEY`),
                  ADD KEY `OXSHOPID` (`OXSHOPID`);
                ",
            ],
        ),
        'wh_cmsc_cache_localpage2cmspage' => array(
            'multishop'     => false,
            'multilang'     => false,
            'sql'           => [
                "
                CREATE TABLE IF NOT EXISTS `wh_cmsc_cache_localpage2cmspage` (
                  `OXSHOPID` int(11) NOT NULL,
                  `LOCALPAGEKEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Local page cache key',
                  `CMSPAGEKEY` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'CMS page cache key'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMSconnect local page to CMS page relation table.';
                ",
                "
                ALTER TABLE `wh_cmsc_cache_localpage2cmspage`
                  ADD KEY `OXSHOPID` (`OXSHOPID`),
                  ADD KEY `LOCALPAGEKEY` (`LOCALPAGEKEY`),
                  ADD KEY `CMSPAGEKEY` (`CMSPAGEKEY`);
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
