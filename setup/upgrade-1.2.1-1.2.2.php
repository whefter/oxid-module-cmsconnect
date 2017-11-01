<?php

$this->modifyTable(
    'wh_cmsc_cache_httpresults',
    [
        "
        ALTER TABLE `wh_cmsc_cache_httpresults`
           DROP INDEX `KEY`;
        ",
        "
        ALTER TABLE `wh_cmsc_cache_httpresults`
           ADD INDEX `KEY` (`KEY`);
        ",
        "
        ALTER TABLE `wh_cmsc_cache_httpresults`
           ADD UNIQUE KEY `key_oxshopid` (`KEY`, `OXSHOPID`);
        ",
    ],
    [
        'multishop' => false,
        'multilang' => false,
    ]
);