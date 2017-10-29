<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * CMSc_Cache_CmsPages
 */
abstract class CMSc_Cache_CmsPages extends CMSc_Cache
{
    abstract protected function _saveHttpResult ($sCacheKey, $oHttpResult);
    abstract protected function _fetchHttpResult ($sCacheKey);
    abstract protected function _deleteHttpResult ($sCacheKey);
    abstract protected function _getStorageKeysList ();
    
    /**
     * Singleton instance.
     *
     * @var
     */
    protected static $_oInstance = null;
    
    /**
     * Cache for changes to commit to the index (DB) at page shutdown
     *
     * @var
     */
    protected $_aIndexCommitCache = [
        'add' => [],
        'delete' => [],
    ];
    
    /**
     * Singleton instance getter
     */
    public static function get ()
    {
        if ( !static::$_oInstance ) {
            $sEngine = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_CMS_PAGES_CACHE_ENGINE);
            
            if ( $sEngine === CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_AUTO ) {
                if ( extension_loaded('memcached') ) {
                    static::$_oInstance = new CMSc_Cache_CmsPages_memcached();
                } else if ( extension_loaded('memcache') ) {
                    static::$_oInstance = new CMSc_Cache_CmsPages_memcache();
                } else {
                    static::$_oInstance = new CMSc_Cache_CmsPages_OxidFileCache();
                }
            } else {
                switch ( $sEngine ) {
                    case CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHED:
                        if ( extension_loaded('memcached') ) {
                            static::$_oInstance = new CMSc_Cache_CmsPages_memcached();
                            break;
                        }
                    case CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHE:
                        if ( extension_loaded('memcache') ) {
                            static::$_oInstance = new CMSc_Cache_CmsPages_memcache();
                            break;
                        }
                    case CMSc_Utils::VALUE_CMS_PAGES_CACHE_ENGINE_OXIDFILECACHE:
                    default:
                        static::$_oInstance = new CMSc_Cache_CmsPages_OxidFileCache();
                        break;
                }
            }
        }
        
        return static::$_oInstance;
    }
    
    /**
     * @return string
     */
    protected function _getCachePrefix ()
    {
        // It is necessary to differenciate between shops to only show those cached
        // pages that actually belong to that shop in the backend
        return 'CMSc_CmsPage_' . $this->getShopId() . '_';
//        return 'CMSc_CmsPage_';
    }
    
    /**
     * @param CMSc_CmsPage   $oCmsPage       
     * @param object        $oHttpResult    Result object
     * @param int           $iTtl           Cache TTL
     * 
     * @return bool
     */
    public function saveHttpResult ($oCmsPage, $oHttpResult, $iTtl = null)
    {
        startProfile(__METHOD__);
        
        // Figure out cache TTL
        if ( $iTtl === null ) {
            if ( !($iTtl = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_TTL_DEFAULT)) ) {
                $iTtl = CMSc_Utils::CONFIG_DEFAULTVALUE_TTL;
            }
            if ( !($iTtlRnd = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_TTL_DEFAULT_RND)) ) {
                $iTtlRnd = CMSc_Utils::CONFIG_DEFAULTVALUE_TTL_RND;
            }
        }
        
        // Randomize by $iCacheRandomize percentage. This prevents hundreds of CMS pages from expiring
        // at the same time and causing huge numbers of parallel requests.
        $iTtl = mt_rand( floor( $iTtl * (1 - $iTtlRnd/100) ), ceil( $iTtl * (1 + $iTtlRnd/100) ) );
        
        $oHttpResult->ttl = oxRegistry::get('oxUtilsDate')->getTime() + $iTtl;
        // This will get serialized
        $oHttpResult->oCmsPage = $oCmsPage;
        $oHttpResult->sCacheKey = $oCmsPage->getCacheKey();
        
        $blSuccess = $this->_saveHttpResult($oCmsPage->getCacheKey(), $oHttpResult);
        
        if ($blSuccess) {
            $this->_storePageIndexEntry($oCmsPage, $oHttpResult);
        }
        
        stopProfile(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Returns a cached HTTP result
     *
     * @param CMSc_CmsPage    $oCmsPage
     * 
     * @return object
     */
    public function fetchHttpResult ($oCmsPage)
    {
        return $this->fetchHttpResultByCacheKey($oCmsPage->getCacheKey());
    }
    
    /**
     * Returns a cached HTTP result
     *
     * @param string    $sCacheKey
     * 
     * @return object
     */
    public function fetchHttpResultByCacheKey ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $oResult = $this->_fetchHttpResult($sCacheKey);
        
        if ( !$oResult  ) {
            $oResult = false;
        } else {
            if ( $oResult->ttl <= oxRegistry::get('oxUtilsDate')->getTime() ) {
                $oResult = false;
            }
        }
        
        stopProfile(__METHOD__);
        
        return $oResult;
    }
    
    /**
     * Delete a cached HTTP result
     *
     * @param object CMSc_CmsPage    $oCmsPage
     * 
     * @return object
     */
    public function deleteHttpResult ($oCmsPage)
    {
        return $this->deleteHttpResultByCacheKey($oCmsPage->getCacheKey());
    }
    
    /**
     * Delete a cached HTTP result
     *
     * @param string    $sCacheKey
     * 
     * @return object
     */
    public function deleteHttpResultByCacheKey ($sCacheKey)
    {
        startProfile(__METHOD__);
        
        $mReturn = $this->_deleteHttpResult($sCacheKey);
        
        $this->_deletePageIndexEntryByCacheKey($sCacheKey);
        
        stopProfile(__METHOD__);
        
        return $mReturn;
    }
    
    /**
     * Internal function that adds a CMS page to the list of pages to store in the
     * index on commit.
     * 
     * @param type $oCmsPage
     */
    protected function _storePageIndexEntry ($oCmsPage)
    {
        $this->_aIndexCommitCache['add'][] = $oCmsPage;
    }
    
    /**
     * Internal function that adds a cache key to the list of pages to delete
     * from the index on commit
     * 
     * @param type $sCacheKey
     */
    protected function _deletePageIndexEntryByCacheKey ($sCacheKey)
    {
        $this->_aIndexCommitCache['deleteCacheKeys'][] = $sCacheKey;
    }
    
    /**
     * Gets a filtered and limited/offseted list of cached HTTP results.
     * NOTE: This does NOT automatically call synchronize(). Results might be
     * out of sync with the storage.
     * 
     * @param type $limit
     * @param type $offset
     * @param type $aFilters
     * @return type
     */
    protected function _getList ($limit = null, $offset = null, $aFilters = [])
    {
        t::s('Cache_CmsPages::_getList');
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $aHttpResults = [];
        $sSelectSql = $this->_getSqlBaseQuery($aFilters);
            
        if ($limit) {
            $sSelectSql .= "
                LIMIT $limit
            ";
        }
        if ($offset) {
            $sSelectSql .= "
                OFFSET $offset
            ";
        }
            
        t::s('getCol');
        $aCacheKeys = $oxDb->getCol($sSelectSql);
        t::e('getCol');

        foreach ($aCacheKeys as $sCacheKey) {
            t::s('fetchHttpResult');
            $oHttpResult = $this->_fetchHttpResult($sCacheKey);
            t::e('fetchHttpResult');

            if ($oHttpResult) {
                $aHttpResults[$sCacheKey] = $oHttpResult;
            } else {
//                    echo "ORPHANED DB CACHE ENTRY: $sCacheKey\n";
                $this->_deletePageIndexEntryByCacheKey($sCacheKey);
            }
        }
//        echo "</pre>";
        
        t::e('Cache_CmsPages::_getList');
        
        return $aHttpResults;
    }
    
    /**
     * Returns the count of all cached HTTP results which correspond to the passed
     * filters
     * 
     * @param type $aFilters
     * @return type
     */
    protected function _getCount ($aFilters = [])
    {
        t::s('Cache_CmsPages::_getCount');
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        $sBaseSql = $this->_getSqlBaseQuery($aFilters);
        
        $sCountSql = "
            SELECT
                COUNT(*)
            FROM
                ($sBaseSql) AS x
        ";
        
        $iCount = (int)$oxDb->getOne($sCountSql);
        
        t::e('Cache_CmsPages::_getCount');
        
        return $iCount;
    }
    
    /**
     * Internal, return the SQL query base necessary to fetch cache keys for this shop,
     * using the passed filters.
     * 
     * @param type $aFilters
     * @return string
     */
    protected function _getSqlBaseQuery ($aFilters)
    {
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oxConfig = oxRegistry::getConfig();
        
        $sBaseSql = "
            SELECT
                `key`
            FROM
                `" . CMSc_Utils::DB_TABLE_CACHE_HTTPRESULTS . "`
            WHERE 1
                AND oxshopid = " . $oxDb->quote($oxConfig->getShopId()) . "
        ";
        
        if ($aFilters && is_array($aFilters) && count($aFilters)) {
            if ($aFilters['pageid'] || $aFilters['pageid'] === '0' || $aFilters['pageid'] === 0) {
                $sBaseSql .= "
                    AND pageid LIKE " . $oxDb->quote($aFilters['pageid']) . "
                ";
            }
            if ($aFilters['pagepath']) {
                $sBaseSql .= "
                    AND pagepath LIKE " . $oxDb->quote($aFilters['pagepath']) . "
                ";
            }
            if ($aFilters['url']) {
                $sBaseSql .= "
                    AND url LIKE " . $oxDb->quote($aFilters['url']) . "
                ";
            }
            if ($aFilters['http_code'] || $aFilters['http_code'] === '0' || $aFilters['url'] === 0) {
                $sBaseSql .= "
                    AND http_code LIKE " . $oxDb->quote($aFilters['http_code']) . "
                ";
            }
            if ($aFilters['http_method']) {
                $sBaseSql .= "
                    AND http_method LIKE " . $oxDb->quote($aFilters['http_method']) . "
                ";
            }
            if ($aFilters['post_params']) {
                $sBaseSql .= "
                    AND post_params LIKE " . $oxDb->quote($aFilters['post_params']) . "
                ";
            }
        }
        
        return $sBaseSql;
    }
    
    /**
     * Commit the collected index modifications (adds/deletes).
     */
    public function commit ()
    {
        t::s(__METHOD__);
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oxConfig = oxRegistry::getConfig();
        
        if (count($this->_aIndexCommitCache['deleteCacheKeys'])) {
            t::s('delete');
            $oxDb->query("
                DELETE FROM
                    `" . CMSc_Utils::DB_TABLE_CACHE_HTTPRESULTS . "`
                WHERE 1
                    AND `key` IN (" . implode(', ', array_map([$oxDb, 'quote'], $this->_aIndexCommitCache['deleteCacheKeys'])) . ")
                    AND oxshopid = " . $oxDb->quote($oxConfig->getShopId()) . "
            ");
            
            $this->_aIndexCommitCache['deleteCacheKeys'] = [];
            t::e('delete');
        }
        
        if (count($this->_aIndexCommitCache['add'])) {
            t::s('add');
            
            $aKeysToAdd = [];
            foreach ($this->_aIndexCommitCache['add'] as $oCmsPage) {
                $aKeysToAdd[] = $oCmsPage->getCacheKey();
            }
            
            $sSql = "
                SELECT
                    `key`
                FROM
                    `" . CMSc_Utils::DB_TABLE_CACHE_HTTPRESULTS . "`
                WHERE 1
                    AND `key` IN (" . implode(', ', array_map([$oxDb, 'quote'], $aKeysToAdd)) . ")
                    AND oxshopid = " . $oxDb->quote($oxConfig->getShopId()) . "
            ";
            $aExistingCacheKeys = $oxDb->getCol($sSql);
            
//            var_dump(__METHOD__, '$aPresentIds', $aPresentIds);
            
            $sInsertSql = "
                INSERT INTO
                    `" . CMSc_Utils::DB_TABLE_CACHE_HTTPRESULTS . "`

                (
                    `key`,
                    `oxshopid`,
                    `url`,
                    `pageid`,
                    `pagepath`,
                    `http_code`,
                    `http_method`,
                    `post_params`
                )
            ";

            $aInsertPieces = [];
            foreach ($this->_aIndexCommitCache['add'] as $oCmsPage) {
//                echo "<pre>";
//                var_dump(__METHOD__, 'ADDING', $oCmsPage->getCacheKey(), $oCmsPage->getPageId());
//                echo "</pre>";
                
                if (in_array($oCmsPage->getCacheKey(), $aExistingCacheKeys)) {
//                    var_dump(__METHOD__, "Skipping " . $oCmsPage->getCacheKey() . " because it is already in the database");
                    continue;
                }
                
                $oHttpResult = $oCmsPage->getHttpResult();

                $iHttpCode = 0;
                if ($oHttpResult && $oHttpResult->info) {
                    $iHttpCode = $oHttpResult->info['http_code'];
                }

                $aInsertPieces[] = "
                    (
                        " . $oxDb->quote($oCmsPage->getCacheKey()) . ",
                        " . $oxDb->quote($oxConfig->getShopId()) . ",
                        " . $oxDb->quote($oCmsPage->getUrl()) . ",
                        " . ($oCmsPage->getPageId() ? $oxDb->quote($oCmsPage->getPageId()) : 'NULL') . ",
                        " . ($oCmsPage->getPagePath() ? $oxDb->quote($oCmsPage->getPagePath()) : 'NULL') . ",
                        " . $iHttpCode . ",
                        " . $oxDb->quote(($oCmsPage->isPostPage() ? 'POST' : 'GET')) . ",
                        " . $oxDb->quote(json_encode($oCmsPage->getPostParams(), JSON_PRETTY_PRINT)) . "
                    )
                ";
            }

            $sInsertSql .= "
                VALUES

                " . implode(', ', $aInsertPieces) . "
            ";
            
            try {
                $oxDb->query($sInsertSql);
            } catch (Exception $ex) {
                // Catch duplicate key error
            }
            
            $this->_aIndexCommitCache['add'] = [];
            
            t::e('add');
        }
        
        t::e(__METHOD__);
    }
    
    /**
     * Caller for internal function _synchronizeIndex()
     * 
     * @return type
     */
    public function synchronizeIndex()
    {
        return $this->_synchronizeIndex();
    }
    
    /**
     * Runs a synchronization between the DB index and the actual storage
     * by getting both list of keys and deleting where one is not present in
     * the other, on the basis that they can always be reloaded on demand.
     * 
     * Some thought has gone into performance points.
     */
    protected function _synchronizeIndex ()
    {
        t::s('_synchronizeIndex');
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oxConfig = oxRegistry::getConfig();
        
        
        t::s('get storage keys');
        $aStorageKeys = $this->_getStorageKeysList();
        t::e('get storage keys');
        
        t::s('get index keys');
        $aDbKeys = $oxDb->getCol("
            SELECT
                `key`
            FROM
                `" . CMSc_Utils::DB_TABLE_CACHE_HTTPRESULTS . "`
            WHERE 1
                AND oxshopid = " . $oxDb->quote($oxConfig->getShopId()) . "
        ");
        t::e('get index keys');
        
//        var_dump_pre(__METHOD__, '$aStorageKeys', $aStorageKeys, '$aDbKeys', $aDbKeys);
        
        
        t::s('delete obsolete index items');
        t::s('diff cache to db');
        $aDeleteFromIndex = CMSc_Utils::fastArrayDiff($aDbKeys, $aStorageKeys);
        t::e('diff cache to db');
        
        if (count($aDeleteFromIndex)) {
//            var_dump_pre(__METHOD__, "Deleting keys from index: ", $aDeleteFromIndex);
            
            $sSql = "
                DELETE FROM
                    `" . CMSc_Utils::DB_TABLE_CACHE_HTTPRESULTS . "`
                WHERE 1
                    AND oxshopid = " . $oxDb->quote($oxConfig->getShopId()) . "
                    AND `key` IN (" . implode(', ', array_map([$oxDb, 'quote'], $aDeleteFromIndex)) . ")
            ";
            
            t::s('execute');
            $oxDb->query($sSql);
            t::e('execute');
        }
        t::e('delete obsolete index items');
        
        
        t::s('delete obsolete storage items');
        t::s('diff db to cache');
        $aDeleteFromStorage = CMSc_Utils::fastArrayDiff($aStorageKeys, $aDbKeys);
        t::e('diff db to cache');
        t::s('diff db to deleted from index');
        $aDeleteFromStorage = CMSc_Utils::fastArrayDiff($aDeleteFromStorage, $aDeleteFromIndex);
        t::e('diff db to deleted from index');
        
        if (count($aDeleteFromStorage)) {
//            var_dump_pre(__METHOD__, "Deleting keys from cache: ", $aDeleteFromStorage);
            
            t::s('execute');
            foreach ($aDeleteFromStorage as $sCacheKey) {
                $this->deleteHttpResultByCacheKey($sCacheKey);
            }
            t::e('execute');
        }
        t::e('delete obsolete storage items');
        
        t::e('_synchronizeIndex');
    }
}