<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * cmsxid_dbhandler
 */
class cmsxid_dbhandler
{
    /**
     * Multilang tables
     *
     * @var string[]
     */
    protected static $_aMultiLangTables = array();
    
    /**
     * Multishop tables
     *
     * @var string[]
     */
    protected static $_aMultiShopTables = array();
    
    /**
     * Custom columns to create in existing tables. Structure:
     *
     * array(
     *      'oxarticles' => array(
     *          'field_name' => array(
     *              'multilang'     => true/false,
     *              'schema'        => 'varchar(32) NULL',
     *          ),
     *          ...
     *      ),
     *      ...
     * )
     *
     * @var array
     */
    protected static $_aCustomColumns = array(
        'oxcontents' => array(
            'CMSXIDPAGE' => array(
                'multilang' => true,
                'schema'    => 'varchar(1024) NULL',
            ),
            'CMSXIDPAGEID' => array(
                'multilang' => false,
                'schema'    => 'varchar(32) NULL',
            ),
        ),
    );
    
    /**
     * Creates custom columns
     *
     * @return bool
     */
    public static function addCustomColumns()
    {
        $oxDb = oxDb::getDb();
        $oxDbMetaDataHandler = oxRegistry::get('oxDbMetaDataHandler');
        
        foreach ( static::$_aCustomColumns as $sTable => $aFields ) {
            foreach ( $aFields as $sField => $aMetadata ) {
                $blMultilang    = $aMetadata['multilang'];
                $sSchema        = $aMetadata['schema'];
                
                if ( !$oxDbMetaDataHandler->fieldExists($sField, $sTable) ) {
                     $sSql = "
                        ALTER TABLE 
                            `$sTable` 
                        ADD COLUMN 
                            `${sField}` $sSchema 
                    ";
                    $oxDb->query($sSql);
                    
                    if ( $blMultilang ) {
                        $sSql1 = "
                            ALTER TABLE 
                                `$sTable` 
                            ADD COLUMN 
                                `${sField}_1` $sSchema 
                        ";
                        $oxDb->query($sSql1);
                    }
                }
            }
                
            array_push( static::$_aMultiLangTables, $sTable );
        }
        
        static::updateTables();
        
        return true;
    }
    
    /**
     * Adds required multilang fields to tables
     *
     * @return void
     */
    public static function updateTables()
    {
        $oxDbMetaDataHandler = oxNew( 'oxdbmetadatahandler' );
        $oxLang = oxRegistry::getLang();
        
        $iMaxLangId = max( array_keys( $oxLang->getLanguageIds() ) );
        
        foreach ( static::$_aMultiLangTables as $sTable ) {
            $aFields            = $oxDbMetaDataHandler->getFields($sTable);
            $aMultiLangFields   = $oxDbMetaDataHandler->getMultilangFields($sTable);
            
            foreach ( $aMultiLangFields as $sMultiLangField ) {
                for ( $iLang = 2; $iLang <= $iMaxLangId; $iLang++ ) {
                    $sLangTableName = getLangTableName( $sTable, $iLang );
                    $sPrevField = $sMultiLangField . '_' . ($iLang - 1);
                    $sFullField = $sMultiLangField . '_' . $iLang;
                    
                    // No such field, yet
                    if ( !in_array($sFullField, $aFields) ) {
                        $aSql = array_merge(
                            array(
                                //getting add field sql
                                $oxDbMetaDataHandler->getAddFieldSql( $sTable, $sMultiLangField, $sFullField, $sPrevField, $sLangTableName ),
                            ),

                            //getting add index sql on added field
                            $oxDbMetaDataHandler->getAddFieldIndexSql( $sTable, $sMultiLangField, $sFullField, $sLangTableName)
                        );
                        
                        $oxDbMetaDataHandler->executeSql($aSql);
                    }
                }
            }
        }
        
        $oxDbMetaDataHandler->updateViews(static::$_aMultiLangTables);
    }
}
