<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * cmsconnect_dbhandler
 */
class cmsconnect_dbhandler
{
    /**
     * createModuleTables
     *
     * @param array     $aModuleTables      Module table creation info as defined in its metadata file
     *
     * @return void
     */
    public static function addModuleTables( $aModuleTables )
    {
        if ( !is_array($aModuleTables) || !count($aModuleTables) ) {
            return;
        }
        
        $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        
        foreach ( $aModuleTables as $sTableName => $aTableInfo ) {
            $sSql           = $aTableInfo['sql'];
            $blMultiShop    = $aTableInfo['multishop'];
            $blMultiLang    = $aTableInfo['multilang'];
            
            if ( !static::tableExists($sTableName) ) {
                $aSql = is_array($sSql) ? $sSql : [$sSql];
                
                foreach ( $aSql as $sQuery ) {
                    $oxDb->query($sQuery);
                }
            }
            
            if ( $blMultiShop ) {
                self::ensureMultiShopAssignmentTable($sTableName);
                self::addMultiShopTableToConfig($sTableName);
            }
            
            if ( $blMultiLang ) {
                self::addMultiLangTableToConfig($sTableName);
            }
        }
        
        $aTablesToUpdate = array_keys($aModuleTables);
        self::updateTables($aTablesToUpdate);
    }
    
    /**
     * Doesn't actually delete tables, merely removes them from the multilang/multishop configuration variable so they
     * can be safely deleted manually
     *
     * @param array     $aModuleTables      Module table creation info as defined in its metadata file
     *
     * @return void
     */
    public static function removeModuleTables( $aModuleTables )
    {
        if ( !is_array($aModuleTables) || !count($aModuleTables) ) {
            return;
        }
        
        foreach ( $aModuleTables as $sTableName => $aTableInfo ) {
            $blMultiShop    = $aTableInfo['multishop'];
            $blMultiLang    = $aTableInfo['multilang'];
            
            if ( $blMultiShop ) {
                self::removeMultiShopTableFromConfig($sTableName);
            }
            
            if ( $blMultiLang ) {
                self::removeMultiLangTableFromConfig($sTableName);
            }
        }
    }
    
    /**
     * Make shop aware of our the passed multishop table
     *
     * @param string        $sTable     Table name
     *
     * @return void
     */
    public static function addMultiShopTableToConfig( $sTable )
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aMultiShopTables = is_array($oxConfig->getShopConfVar('aMultiShopTables')) ? $oxConfig->getShopConfVar('aMultiShopTables') : array();
        
        if ( !in_array($sTable, $aMultiShopTables) ) {
            $aMultiShopTables[] = $sTable;
        }
        
        $oxConfig->saveShopConfVar( 'arr', 'aMultiShopTables', $aMultiShopTables );
    }
    
    /**
     * Make shop aware of our the passed multilang table
     *
     * @param string        $sTable     Table name
     *
     * @return void
     */
    public static function addMultiLangTableToConfig( $sTable )
    {
        $oxConfig           = oxRegistry::getConfig();
        $aMultiLangTables   = is_array($oxConfig->getShopConfVar('aMultiLangTables')) ? $oxConfig->getShopConfVar('aMultiLangTables') : array();
        
        if ( !in_array($sTable, $aMultiLangTables) ) {
            $aMultiLangTables[] = $sTable;
        }
        
        $oxConfig->saveShopConfVar( 'arr', 'aMultiLangTables', $aMultiLangTables );
    }
    
    /**
     * removeMultiShopTableFromConfig
     *
     * @param string        $sTable     Table name
     *
     * @return void
     */
    public static function removeMultiShopTableFromConfig( $sTable )
    {
        $oxConfig           = oxRegistry::getConfig();
        $aMultiShopTables   = is_array($oxConfig->getShopConfVar('aMultiShopTables')) ? $oxConfig->getShopConfVar('aMultiShopTables') : array();
        
        $aTables = array();
        
        foreach ( $aMultiShopTables as $sExistingTable ) {
            if ( $sTable != $sExistingTable ) {
                $aTables[] = $sExistingTable;
            }
        }
        
        $oxConfig->saveShopConfVar( 'arr', 'aMultiShopTables', $aTables );
    }
    
    /**
     * removeMultiLangTableFromConfig
     *
     * @param string        $sTable     Table name
     *
     * @return void
     */
    public static function removeMultiLangTableFromConfig( $sTable )
    {
        $oxConfig           = oxRegistry::getConfig();
        $aMultiLangTables   = is_array($oxConfig->getShopConfVar('aMultiLangTables')) ? $oxConfig->getShopConfVar('aMultiLangTables') : array();

        $aTables = array();
        
        foreach ( $aMultiLangTables as $sExistingTable ) {
            if ( $sTable != $sExistingTable ) {
                $aTables[] = $sExistingTable;
            }
        }
        
        $oxConfig->saveShopConfVar( 'arr', 'aMultiLangTables', $aTables );
    }
    
    public static function ensureMultiShopAssignmentTable ( $sTable )
    {
        $oxConfig = oxRegistry::getConfig();
        
        $sToShopTable = $sTable . "2shop";
        
        if ( !static::tableExists($sToShopTable) ) {
            $oxDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
            
            $oxDb->query("
                CREATE TABLE IF NOT EXISTS `$sToShopTable` (
                  `OXSHOPID` int(11) NOT NULL COMMENT 'Mapped shop id',
                  `OXMAPOBJECTID` bigint(20) NOT NULL COMMENT 'Mapped object id',
                  `OXTIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Mapping table for element subshop assignments';
            ");
            $oxDb->query("
                ALTER TABLE `$sToShopTable`
                  ADD UNIQUE KEY `OXMAPIDX` (`OXSHOPID`,`OXMAPOBJECTID`),
                  ADD KEY `OXMAPOBJECTID` (`OXMAPOBJECTID`),
                  ADD KEY `OXSHOPID` (`OXSHOPID`);
            ");
        }
    }
    
    public static function tableExists ( $sTable )
    {
        $oxDbMetaDataHandler = oxRegistry::get('oxDbMetaDataHandler');
        
        return $oxDbMetaDataHandler->tableExists($sTable);
    }
    
    /**
     * Creates custom columns
     *
     * @param   array[]     $aColumns   Array containing the information for the new columns, structure:
     *      array(
     *          'oxarticles' => array(
     *              'field_name' => array(
     *                  'multilang'     => true/false,
     *                  'schema'        => 'varchar(32) NULL',
     *              ),
     *              ...
     *          ),
     *          ...
     *      )
     *
     * @return bool
     */
    public static function addColumns( $aColumns )
    {
        if ( !is_array($aColumns) || !count($aColumns) ) {
            return;
        }
        
        $oxDb                   = oxDb::getDb();
        $oxDbMetaDataHandler    = oxRegistry::get('oxDbMetaDataHandler');
        
        foreach ( $aColumns as $sTable => $aFields ) {
            foreach ( $aFields as $sField => $aMetadata ) {
                $blMultilang    = $aMetadata['multilang'];
                $sSchema        = $aMetadata['schema'];
                $sComment       = $aMetadata['comment'];
                
                if ( !$oxDbMetaDataHandler->fieldExists($sField, $sTable) ) {
                     $sSql = "
                        ALTER TABLE 
                            `$sTable` 
                        ADD COLUMN 
                            `${sField}` $sSchema COMMENT " . $oxDb->quote($sComment) . " 
                    ";
                    $oxDb->query($sSql);
                    
                    if ( $blMultilang ) {
                        $sSql1 = "
                            ALTER TABLE 
                                `$sTable` 
                            ADD COLUMN 
                                `${sField}_1` $sSchema  COMMENT " . $oxDb->quote($sComment) . "  
                        ";
                        $oxDb->query($sSql1);
                    }
                }
            }
        }
        
        $aTablesToUpdate = array_keys($aColumns);
        self::updateTables($aTablesToUpdate);
        
        return true;
    }
    
    /**
     * Check all passed tables for fields that require their sibling "_1", "_2", etc. fields added, and add them, then updates the views
     *
     * @param   string[]        $aTables        Array of table names
     *
     * @return void
     */
    public static function updateTables( $aTables )
    {
        if ( !is_array($aTables) || !count($aTables) ) {
            return;
        }
        
        $oxDbMetaDataHandler    = oxNew( 'oxdbmetadatahandler' );
        $oxLang                 = oxRegistry::getLang();
        
        $iMaxLangId = max( array_keys( $oxLang->getLanguageIds() ) );
        
        foreach ( $aTables as $sTable ) {
            $aFields            = $oxDbMetaDataHandler->getFields($sTable);
            $aFieldsShortNames  = array_keys($aFields);
            $aMultiLangFields   = $oxDbMetaDataHandler->getMultilangFields($sTable);
            
            foreach ( $aMultiLangFields as $sMultiLangField ) {
                for ( $iLang = 2; $iLang <= $iMaxLangId; $iLang++ ) {
                    $sLangTableName = getLangTableName( $sTable, $iLang );
                    $sPrevField     = $sMultiLangField . '_' . ($iLang - 1);
                    $sFullField     = $sMultiLangField . '_' . $iLang;
                    
                    // No such field, yet
                    if ( !in_array($sFullField, $aFieldsShortNames) ) {
                        $aSql = array_merge(
                            array(
                                //getting add field sql
                                $oxDbMetaDataHandler->getAddFieldSql( $sTable, $sMultiLangField, $sFullField, $sPrevField, $sLangTableName ),
                            ),

                            //getting add index sql on added field
                            $oxDbMetaDataHandler->getAddFieldIndexSql( $sTable, $sMultiLangField, $sFullField, $sLangTableName )
                        );
                        
                        $oxDbMetaDataHandler->executeSql($aSql);
                    }
                }
            }
        }
        
        self::updateViews();
    }
    
    /**
     * Updates database views for the passed tables
     *
     * @return void
     */
    public static function updateViews()
    {
        $oxDbMetaDataHandler = oxNew( 'oxdbmetadatahandler' );
        $oxDbMetaDataHandler->updateViews();
    }
}
