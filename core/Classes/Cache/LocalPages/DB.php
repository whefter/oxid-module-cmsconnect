<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages_OxidFileCache
 */
class CMSc_Cache_LocalPages_DB extends CMSc_Cache_LocalPages
{
    const ENGINE_LABEL = 'DB';
    
    /**
     * Override
     */
    protected function _deleteLocalPageCache ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $sSql1 = "
            DELETE
            FROM
                wh_cmsc_cache_localpages_data
            WHERE
                `key` = " . $oxDb->quote($sCacheKey) . "
            ;
        ";
        $sSql2 = "
            DELETE
            FROM
                wh_cmsc_cache_localpages_pages
            WHERE
                `localpagekey` = " . $oxDb->quote($sCacheKey) . "
            ;
        ";
        $oxDb->query($sSql1);
        $oxDb->query($sSql2);
        
        stopProfile(__METHOD__);
    }
    
    public function _getCount ()
    {
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $sSql = "
            SELECT
                COUNT(*)
            FROM
                wh_cmsc_cache_localpages_data as data
        ";
        
        return $oxDb->getOne($sSql);
    }
    
    /**
     * Override parent.
     */
    protected function _getList ($limit = null, $offset = null)
    {
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sSql = "
            SELECT
                data.key as localpagekey,
                data.data as localpagedata
            FROM
                wh_cmsc_cache_localpages_data as data
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
                pages.localpagekey as localpagekey,
                pages.cmspagekey as cmspagekey,
                pages.cmspagedata as cmspagedata
            FROM
                wh_cmsc_cache_localpages_pages as pages
            WHERE
                pages.localpagekey IN (" . (count($aKeys) ? implode(',', $aKeys) : 'NULL') . ")
        ";
        $aCmsPages = $oxDb->getAll($sSql);
        
        // echo "<pre>";
        // var_dump($aData);
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
            $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
            
            $sPagesSql = "
                SELECT
                    pages.cmspagekey,
                    pages.cmspagedata
                FROM
                    wh_cmsc_cache_localpages_data as data
                LEFT JOIN
                    wh_cmsc_cache_localpages_pages as pages
                ON
                    pages.localpagekey = data.key
                WHERE
                    data.key = " . $oxDb->quote($sCacheKey) . "
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
                    // $sCmsPageCacheKey = $oCmsPage->getIdent();
                    
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
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        // echo "<pre>";
        // var_dump(__METHOD__, $this->_aPageCache);
        // echo "</pre>";
        
        foreach ( $this->_aPageCache as $sCacheKey => $aCache ) {
            if ( !$aCache ) {
                continue;
            }
            
            $sSql = "
                SELECT
                    1
                FROM
                    wh_cmsc_cache_localpages_data
                WHERE
                    `key` = " . $oxDb->quote($sCacheKey) . "
                ;
            ";
            $blExists = (bool)$oxDb->getOne($sSql);
            
            if ( !$blExists ) {
                $sSql = "
                    INSERT INTO
                        wh_cmsc_cache_localpages_data
                    (
                        `key`,
                        `data`
                    )
                    VALUES
                    (
                        " . $oxDb->quote($sCacheKey) . ",
                        " . $oxDb->quote(serialize( $this->_getCurrentLocalPageData() )) . "
                    );
                ";
                
                $oxDb->query($sSql);
            }
            
            $sPagesSql = "
                SELECT
                    pages.cmspagekey
                FROM
                    wh_cmsc_cache_localpages_data as data
                INNER JOIN
                    wh_cmsc_cache_localpages_pages as pages
                    ON
                        pages.localpagekey = data.key
                WHERE
                    data.key = " . $oxDb->quote($sCacheKey) . "
            ";
            $aDbPageKeys = $oxDb->getCol($sPagesSql);
            
            $aMissingPages = [];
            foreach ( $aCache['pages'] as $sCmsPageCacheKey => $oCmsPage ) {
                if ( !in_array($sCmsPageCacheKey, $aDbPageKeys) ) {
                    $aMissingPages[$sCmsPageCacheKey] = $oCmsPage;
                }
            }
            
            if ( count($aMissingPages) ) {
                $sSql = "
                    INSERT INTO
                        wh_cmsc_cache_localpages_pages
                    (
                        `id`,
                        `localpagekey`,
                        `cmspagekey`,
                        `cmspagedata`
                    )
                    VALUES
                ";
                $aPageSqls = [];
                foreach ( $aMissingPages as $sCmsPageCacheKey => $oCmsPage ) {
                    $aPageSqls[] = "
                        (
                            MD5(CONCAT(RAND(), CURRENT_TIMESTAMP, " . $oxDb->quote($sCacheKey) . ")),
                            " . $oxDb->quote($sCacheKey) . ",
                            " . $oxDb->quote($oCmsPage->getIdent()) . ",
                            " . $oxDb->quote(serialize($oCmsPage)) . "
                        )
                    ";
                }
                $sSql .= implode(",\n", $aPageSqls);
                
                $oxDb->query($sSql);
            }
        }
    }
}