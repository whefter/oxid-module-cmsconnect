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
    public static function set($sGroup, $sKey, $sValue)
    {
        if (!array_key_exists($sGroup, static::$_aCache)) {
            static::$_aCache[$sGroup] = [];
        }

        static::$_aCache[$sGroup][$sKey] = $sValue;
    }

    /**
     * @param string $sGroup
     * @param string $sKey
     * @return mixed|null
     */
    public static function get($sGroup, $sKey)
    {
        if (!array_key_exists($sGroup, static::$_aCache)) {
            return null;
        }

        if (!array_key_exists($sKey, static::$_aCache[$sGroup])) {
            return null;
        }

        return static::$_aCache[$sGroup][$sKey];
    }

    /**
     * @param string $sGroup
     * @param string $sKey
     * @return bool
     */
    public static function has($sGroup, $sKey)
    {
        if (!array_key_exists($sGroup, static::$_aCache)) {
            return false;
        }

        if (!array_key_exists($sKey, static::$_aCache[$sGroup])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $sGroup
     * @param string $sKey
     * @return void
     */
    public static function delete($sGroup, $sKey = null)
    {
        if (!array_key_exists($sGroup, static::$_aCache)) {
            return;
        }

        if ($sKey === null) {
            unset(static::$_aCache[$sGroup]);
        } else {
            if (!array_key_exists($sKey, static::$_aCache[$sGroup])) {
                return;
            }

            unset(static::$_aCache[$sGroup][$sKey]);
        }
    }
}
