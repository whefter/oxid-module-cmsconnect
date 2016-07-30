<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages_OxidFileCache
 */
class CMSc_Cache_LocalPages_DB extends CMSc_Cache_LocalPages_OxidFileCache
{
    /**
     * Override parent.
     */
    protected function _getLocalPageCmsPages ($sCacheKey)
    {
        return parent::_getLocalPageCmsPages($sCacheKey);
    }
    
    /**
     * Override parent.
     */
    protected function _registerCmsPage ($sLocalPageCacheKey, $oCmsPage)
    {
        return parent::_registerCmsPage($sLocalPageCacheKey, $oCmsPage);
    }
    
    /**
     * Override parent.
     */
    protected function _getList ()
    {
        return parent::_getList();
    }
    
    /**
     * Override parent.
     */
    protected function _setLocalPageCache ($sCacheKey, $aLocalPageCache)
    {
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
            INSERT INTO
                wh_cmsc_cache_localpages_data
            (
                `key`,
                `data`
            )
            VALUES
            (
                " . $oxDb->quote($sCacheKey) . ",
                " . $oxDb->quote(serialize($aLocalPageCache['data'])) . "
            );
        ";
        $oxDb->query($sSql1);
        $oxDb->query($sSql2);
        
        $sSql1 = "
            DELETE
            FROM
                wh_cmsc_cache_localpages_pages
            WHERE
                `localpagekey` = " . $oxDb->quote($sCacheKey) . "
            ;
        ";
        $sSql2 = "
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
        foreach ( $aLocalPageCache['pages'] as $oCmsPage ) {
            $aPageSqls[] = "
                (
                    MD5(CONCAT(RAND(), CURRENT_TIMESTAMP, " . $oxDb->quote($sCacheKey) . ")),
                    " . $oxDb->quote($sCacheKey) . ",
                    " . $oxDb->quote($oCmsPage->getIdent()) . ",
                    " . $oxDb->quote($oCmsPage->serialize()) . "
                )
            ";
        }
        $sSql2 .= implode(",\n", $aPageSqls);
        
        $oxDb->query($sSql1);
        $oxDb->query($sSql2);
        
        parent::_setLocalPageCache($sCacheKey, $aLocalPageCache);
    }
    
    /**
     * Override parent.
     */
    protected function _getLocalPageCache ($sCacheKey)
    {
        $this->_getLocalPageCacheFromFileCache($sCacheKey);
        
        if ( isset($this->_aPageCache[$sCacheKey]) ) {
            return parent::_getLocalPageCache($sCacheKey);
        }
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $sDataSql = "
            SELECT
                data.data
            FROM
                wh_cmsc_cache_localpages_data as data
            WHERE
                data.key = " . $oxDb->quote($sCacheKey) . "
            LIMIT 1
        ";

        $sData = $oxDb->getOne($sDataSql);
        
        if ( $sData === false ) {
            $aCache = parent::_getLocalPageCache($sCacheKey);
        } else {
            $sPagesSql = "
                SELECT
                    pages.cmspagekey,
                    pages.cmspagedata
                FROM
                    wh_cmsc_cache_localpages_data as data
                INNER JOIN
                    wh_cmsc_cache_localpages_pages as pages
                ON
                    pages.localpagekey = data.key
                WHERE
                    data.key = " . $oxDb->quote($sCacheKey) . "
            ";
            $aPages = $oxDb->getAll($sPagesSql);
            
            $aCache = [
                'pages' => [],
                'data' => unserialize($sData),
            ];
            
            foreach ( $aPages as $aPageRow ) {
                $aCache['pages'][] = CMSc_CmsPage::buildFromSerializedData($aPageRow['cmspagedata']);
            }
            
            // Write to OXID cache
            parent::_setLocalPageCache($sCacheKey, $aCache);
        }
        
        return $aCache;
    }
}