<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models\Cache\LocalPages;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Models\Cache;
use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \t as t;

/**
 * CMSc_Cache_LocalPages_DB
 */
class DB extends Cache\LocalPages
{
    const ENGINE_LABEL = 'DB';
    
    /**
     * Override
     */
    protected function _deleteLocalPageCache ($sCacheKey)
    {
        class_exists('t') && t::s(__METHOD__);
        
        $oxDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb( \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC );
        $oxConfig = Registry::getConfig();
        
        $sSql1 = "
            DELETE
            FROM
                wh_cmsc_cache_localpages
            WHERE
                `key` = " . $oxDb->quote($sCacheKey) . "
                AND oxshopid = '" . $this->getShopId() . "'
            ;
        ";
        $sSql2 = "
            DELETE
            FROM
                wh_cmsc_cache_localpage2cmspage
            WHERE
                `localpagekey` = " . $oxDb->quote($sCacheKey) . "
                AND oxshopid = '" . $this->getShopId() . "'
            ;
        ";
        $oxDb->execute($sSql1);
        $oxDb->execute($sSql2);
        
        class_exists('t') && t::e(__METHOD__);
    }

    protected function _getCount ()
    {
        $oxDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb( \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC );
        $oxConfig = Registry::getConfig();
        
        $sSql = "
            SELECT
                COUNT(*)
            FROM
                wh_cmsc_cache_localpages
            WHERE
                oxshopid = '" . $this->getShopId() . "'
        ";
        
        return $oxDb->getOne($sSql);
    }
    
    /**
     * Override parent.
     */
    protected function _getList ($limit = null, $offset = null, $aFilters = [])
    {
        $oxDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb( \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC );
        $oxConfig = Registry::getConfig();
        
        $sSql = "
            SELECT
                localpages.key as localpagekey,
                localpages.data as localpagedata
            FROM
                wh_cmsc_cache_localpages as localpages
            WHERE
                localpages.oxshopid = '" . $this->getShopId() . "'
            " . ($offset !== null
                    ? ("LIMIT $offset" . ($limit !== null ? ",$limit" : ""))
                    : ""
                ) . "
        ";
        $aLocalPages = $oxDb->getAll($sSql);
        
        $aKeys = [];
        $aList = [];
        foreach ( $aLocalPages as $aRow ) {
            $sLocalPageCacheKey = $aRow['localpagekey'];
            
            $aKeys[] = "'$sLocalPageCacheKey'";
            
            if ( !isset($aList[$sLocalPageCacheKey]) ) {
                $aList[$sLocalPageCacheKey] = [
                    'pages' => [],
                    'data' => unserialize($aRow['localpagedata']),
                ];
            }
        }
        
        $sSql = "
            SELECT
                l2c.localpagekey as localpagekey,
                cmspages.key as cmspagekey,
                cmspages.data as cmspagedata
            FROM
                wh_cmsc_cache_localpage2cmspage as l2c
            INNER JOIN
                wh_cmsc_cache_cmspages as cmspages
                ON
                    cmspages.key = l2c.cmspagekey
            WHERE
                l2c.localpagekey IN (" . (count($aKeys) ? implode(',', $aKeys) : 'NULL') . ")
                AND l2c.oxshopid = '" . $this->getShopId() . "'
        ";
        $aCmsPages = $oxDb->getAll($sSql);
        
        // echo "<pre>";
        // var_dump($sSql);
        // var_dump($aCmsPages);
        // echo "</pre>";
        
        foreach ( $aCmsPages as $aRow ) {
            $sLocalPageCacheKey = $aRow['localpagekey'];
            $aList[$sLocalPageCacheKey]['pages'][] = unserialize($aRow['cmspagedata']);
        }
        
        return $aList;
    }
    
    /**
     * Override parent.
     */
    protected function _getLocalPageCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $oxDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb( \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC );
            $oxConfig = Registry::getConfig();
            
            $sPagesSql = "
                SELECT
                    cmspages.key as cmspagekey,
                    cmspages.data as cmspagedata
                FROM
                    wh_cmsc_cache_localpage2cmspage as l2c
                INNER JOIN
                    wh_cmsc_cache_cmspages as cmspages
                    ON
                        cmspages.key = l2c.cmspagekey
                        AND l2c.oxshopid = '" . $this->getShopId() . "'
                WHERE
                    l2c.localpagekey = " . $oxDb->quote($sCacheKey) . "
            ";
            $aPages = $oxDb->getAll($sPagesSql);
            
            $aCache = false;
            
            // This will be 0 for pages that have no DATA (means not present
            // in database) and at least 1 for pages that have DATA, but no
            // PAGES (there will be one row will NULL values)
            if ( count($aPages) ) {
                $aCache = [
                    'pages' => [],
                    // 'data' => unserialize($sData),
                ];
                
                foreach ( $aPages as $aPageRow ) {
                    // Skip NULL rows from LEFT JOIN
                    if ( !$aPageRow['cmspagekey'] ) {
                        continue;
                    }
                    
                    $oCmsPage = unserialize($aPageRow['cmspagedata']);
                    // $sCmsPageCacheKey = $oCmsPage->getCacheKey();
                    
                    $aCache['pages'][$aPageRow['cmspagekey']] = $oCmsPage;
                }
            }
            
            $this->_aPageCache[$sCacheKey] = $aCache;
        }
        
        return $this->_aPageCache[$sCacheKey];
    }
    
    /**
     * Override parent.
     */
    public function commit ()
    {
        $oxDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb( \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC );
        $oxConfig = Registry::getConfig();
        
        // echo "<pre>";
        // var_dump(__METHOD__, $this->_aPageCache);
        // echo "</pre>";
        
        foreach ( $this->_aPageCache as $sCacheKey => $aCache ) {
            if ( !$aCache ) {
                continue;
            }
            
            
            // Local page
            $sSql = "
                SELECT
                    1
                FROM
                    wh_cmsc_cache_localpages
                WHERE
                    `key` = " . $oxDb->quote($sCacheKey) . "
                    AND oxshopid = '" . $this->getShopId() . "'
                ;
            ";
            $blExists = (bool)$oxDb->getOne($sSql);
            
            if ( !$blExists ) {
                $sSql = "
                    INSERT INTO
                        wh_cmsc_cache_localpages
                    (
                        `oxshopid`,
                        `key`,
                        `data`
                    )
                    VALUES
                    (
                        '" . $this->getShopId() . "',
                        " . $oxDb->quote($sCacheKey) . ",
                        " . $oxDb->quote(serialize( $this->_getCurrentLocalPageData() )) . "
                    );
                ";
                
                $oxDb->execute($sSql);
            }
            
            
            // CMS pages
            if ( count($aCache['pages']) ) {
                $aCmsPagesKeys = array_keys($aCache['pages']);
                
                $sCmsPagesSql = "
                    SELECT
                        `key`
                    FROM
                        wh_cmsc_cache_cmspages
                    WHERE
                        `key` IN (" . implode(', ', array_map([$oxDb, 'quote'], $aCmsPagesKeys)) . ")
                ";
                $aDbPageKeys = $oxDb->getCol($sCmsPagesSql);
                
                $aMissingPages = array_diff($aCmsPagesKeys, $aDbPageKeys);
                
                if ( count($aMissingPages) ) {
                    $sSql = "
                        INSERT INTO
                            wh_cmsc_cache_cmspages
                        (
                            `key`,
                            `data`
                        )
                        VALUES
                    ";
                    $aSqls = [];
                    
                    foreach ( $aMissingPages as $sCmsPageCacheKey ) {
                        $oCmsPage = $aCache['pages'][$sCmsPageCacheKey];
                        
                        $aSqls[] = "
                            (
                                " . $oxDb->quote($oCmsPage->getCacheKey()) . ",
                                " . $oxDb->quote(serialize($oCmsPage)) . "
                            )
                        ";
                    }
                    
                    $sSql .= implode(",\n", $aSqls);
                    
                    $oxDb->execute($sSql);
                }
                
                // Links
                $sLinkSql = "
                    SELECT
                        cmspagekey
                    FROM
                        wh_cmsc_cache_localpage2cmspage
                    WHERE
                        localpagekey = " . $oxDb->quote($sCacheKey) . "
                        AND oxshopid = '" . $this->getShopId() . "'
                ";
                $aDbPageKeys = $oxDb->getCol($sLinkSql);
                
                $aMissingPages = array_diff($aCmsPagesKeys, $aDbPageKeys);
                
                if ( count($aMissingPages) ) {
                    $sSql = "
                        INSERT INTO
                            wh_cmsc_cache_localpage2cmspage
                        (
                            `oxshopid`,
                            `localpagekey`,
                            `cmspagekey`
                        )
                        VALUES
                    ";
                    $aSqls = [];
                    
                    foreach ( $aMissingPages as $sCmsPageCacheKey ) {
                        $oCmsPage = $aCache['pages'][$sCmsPageCacheKey];
                        
                        $aSqls[] = "
                            (
                                '" . $this->getShopId() . "',
                                " . $oxDb->quote($sCacheKey) . ",
                                " . $oxDb->quote($oCmsPage->getCacheKey()) . "
                            )
                        ";
                    }
                    
                    $sSql .= implode(",\n", $aSqls);
                    
                    $oxDb->execute($sSql);
                }
            }
        }
    }
}