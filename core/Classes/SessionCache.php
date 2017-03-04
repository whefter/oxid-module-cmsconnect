<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * This is a "level one" cache, just an associative array with convenient
 * interface which internally stores values identified by a key which is
 * really just a hash of the key that was passed.
 */
class CMSc_SessionCache
{
    /**
     * Internal cache array.
     *
     * @var mixed[]
     */
    protected static $_aCache = array();
    
    public static function set ($sGroup, $sKey, $sValue)
    {
        $sCacheKey = static::createCacheKey($sGroup, $sKey);
        
        static::$_aCache[$sCacheKey] = $sValue;
    }
    
    public static function get ($sGroup, $sKey)
    {
        $sCacheKey = static::createCacheKey($sGroup, $sKey);
        
        if ( isset(static::$_aCache[$sCacheKey]) ) {
            return static::$_aCache[$sCacheKey];
        }
        
        return null;
    }
    
    protected static function createCacheKey ($a, $b)
    {
        return md5($a . $b);
    }
}