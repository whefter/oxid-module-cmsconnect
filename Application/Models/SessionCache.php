<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models;

/**
 * This is a "level one" cache, just an associative array with convenient
 * interface which internally stores values identified by a key which is
 * really just a hash of the key that was passed.
 */
class SessionCache
{
    /**
     * Internal cache array.
     *
     * @var mixed[]
     */
    protected static $_aCache = array();

    /**
     * @param string $sGroup
     * @param string $sKey
     * @param mixed $sValue
     */
    public static function set ($sGroup, $sKey, $sValue)
    {
        $sCacheKey = static::createCacheKey($sGroup, $sKey);

        static::$_aCache[$sCacheKey] = $sValue;
    }

    /**
     * @param string $sGroup
     * @param string $sKey
     * @return mixed|null
     */
    public static function get ($sGroup, $sKey)
    {
        $sCacheKey = static::createCacheKey($sGroup, $sKey);

        if ( isset(static::$_aCache[$sCacheKey]) ) {
            return static::$_aCache[$sCacheKey];
        }

        return null;
    }

    /**
     * @param string $sGroup
     * @param string $sKey
     * @return bool
     */
    public static function has ($sGroup, $sKey)
    {
        $sCacheKey = static::createCacheKey($sGroup, $sKey);

        if ( isset(static::$_aCache[$sCacheKey]) ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    protected static function createCacheKey ($a, $b)
    {
        return md5($a . $b);
    }
}
