<?php

$this->createTable(
    'wh_cmsc_cache_httpresults',
    [
        "
        CREATE TABLE IF NOT EXISTS `wh_cmsc_cache_httpresults` (
          `KEY` char(32) COMMENT 'Corresponding CmsPage cache key',
          `OXSHOPID` int(11),
          `PAGEID` varchar(255),
          `PAGEPATH` varchar(1024),
          `URL` varchar(8192),
          `HTTP_CODE` int(3),
          `HTTP_METHOD` varchar(32),
          `POST_PARAMS` text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMSconnect HTTP results cache.';
        ",
        "
        ALTER TABLE `wh_cmsc_cache_httpresults`
           ADD UNIQUE KEY `KEY` (`KEY`);
        ",
        "
        ALTER TABLE `wh_cmsc_cache_httpresults`
           ADD INDEX `OXSHOPID` (`OXSHOPID`);
        ",
    ],
    [
        'multishop' => false,
        'multilang' => false,
    ]
);