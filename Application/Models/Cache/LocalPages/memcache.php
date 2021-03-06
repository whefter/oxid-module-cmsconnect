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
 * CMSc_Cache_LocalPages_memcache
 */
class memcache extends Cache\LocalPages
{
    const ENGINE_LABEL = 'memcache';
    
    protected $_oMemcache = null;
    
    protected function _getMemcache ()
    {
        if ( $this->_oMemcache === null ) {
            $this->_oMemcache = new \Memcache();
            $this->_oMemcache->connect('127.0.0.1', 11211);
        }
        
        return $this->_oMemcache;
    }
    
    protected function _getMemcacheKey ($sCacheKey)
    {
        $oxConfig = Registry::getConfig();
        
        return $this->_getCachePrefix() . $sCacheKey;
    }
    
    protected function _addPageToIndex ($sCacheKey)
    {
        $oxConfig = Registry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( !in_array($sCacheKey, $aIndex) ) {
            $aIndex[] = $sCacheKey;
            
            $this->_getMemcache()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _deletePageFromIndex ($sCacheKey)
    {
        $oxConfig = Registry::getConfig();
        
        $aIndex = $this->_getIndex();
        
        if ( in_array($sCacheKey, $aIndex) ) {
            unset( $aIndex[array_search($sCacheKey, $aIndex)] );
            $this->_getMemcache()->set($this->_getCachePrefix(), $aIndex);
        }
    }
    
    protected function _getIndex ()
    {
        $oxConfig = Registry::getConfig();
        
        $aIndex = $this->_getMemcache()->get($this->_getCachePrefix());
        
        if ( !is_array($aIndex) ) {
            $aIndex = [];
        }
        
        return $aIndex;
    }
    
    protected function _getLocalPageCache ($sCacheKey)
    {
        if ( !isset($this->_aPageCache[$sCacheKey]) ) {
            $aCache = $this->_getMemcache()->get( $this->_getMemcacheKey($sCacheKey) );
            
            if ( $aCache ) {
                $aCache['pages'] = array_map(function ($v)
                {
                    return unserialize($v);
                }, $aCache['pages']);
            }
            
            $this->_aPageCache[$sCacheKey] = $aCache ?: false;
        }
        
        return $this->_aPageCache[$sCacheKey];
    }
    
    /**
     * Override
     */
    protected function _deleteLocalPageCache ($sCacheKey)
    {
        class_exists('t') && t::s(__METHOD__);
        
        // Memcache::delete() is broken in several versions of php-memcache
        // $this->_getMemcache()->delete( $this->_getMemcacheKey($sCacheKey) );e
        $this->_getMemcache()->set($this->_getMemcacheKey($sCacheKey), false);
        $this->_deletePageFromIndex($sCacheKey);
        
        class_exists('t') && t::e(__METHOD__);
    }
    
    /**
     * Override
     */
    protected function _getCount ()
    {
        $aIndex = $this->_getIndex();
        
        return count($aIndex);
    }
    
    /**
     * Override
     */
    protected function _getList ($limit = null, $offset = null, $aFilters = [])
    {
        $aIndex = $this->_getIndex();
        
        $iCnt = 0;
        $aList = [];
        foreach ( $aIndex as $sCacheKey ) {
            $iCnt++;
            if ( $offset !== null && $iCnt <= $offset ) {
                continue;
            }
            if ( $limit !== null && $iCnt > ((int)$offset + $limit) ) {
                continue;
            }
            
            $aCache = $this->_getLocalPageCache($sCacheKey);
            
            if ( $aCache ) {
                $aList[$sCacheKey] = $aCache;
            }
        }
        // var_dump(__METHOD__, $aIndex, $aList);
        
        return $aList;
    }
    
    /**
     * Override parent.
     */
    public function commit ()
    {
        // echo "<pre>";
        // var_dump(__METHOD__, $this->_aPageCache);
        // echo "</pre>";
        
        foreach ( $this->_aPageCache as $sCacheKey => $aCache ) {
            if ( $aCache ) {
                $aCache['pages'] = array_map(function ($v)
                {
                    return serialize($v);
                }, $aCache['pages']);
                
                $this->_getMemcache()->set( $this->_getMemcacheKey($sCacheKey), $aCache );
                
                $this->_addPageToIndex($sCacheKey);
            }
        }
    }
}