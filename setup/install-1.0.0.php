<?php


$this->addColumn('oxcontents', 'CMSXIDPAGE', [
    'multilang' => true,
    'schema'    => 'varchar(1024) NULL',
]);
$this->addColumn('oxcontents', 'CMSXIDPAGEID', [
    'multilang' => false,
    'schema'    => 'varchar(32) NOT NULL',
]);

$this->createTable(
    'wh_cmsc_cache_cmspages',
    [
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
    [
        'multishop' => false,
        'multilang' => false,
    ]
);
$this->createTable(
    'wh_cmsc_cache_localpages',
    [
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
    [
        'multishop' => false,
        'multilang' => false,
    ]
);
$this->createTable(
    'wh_cmsc_cache_localpage2cmspage',
    [
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
    [
        'multishop' => false,
        'multilang' => false,
    ]
);