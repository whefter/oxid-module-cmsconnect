<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache_LocalPages_memcached
 */
class CMSc_Cache_LocalPages_memcached extends CMSc_Cache_LocalPages_memcache
{
    const ENGINE_LABEL = 'memcached';
    
    protected function _getMemcache ()
    {
        if ( $this->_oMemcache === null ) {
            $this->_oMemcache = new Memcache();
            $this->_oMemcache->addServer('127.0.0.1', 11211);
        }
        
        return $this->_oMemcache;
    }
}