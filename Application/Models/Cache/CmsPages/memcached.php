<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models\Cache\CmsPages;

use \wh\CmsConnect\Application\Models\Cache;
use \t as t;

/**
 * CMSc_Cache_CmsPages_memcached
 */
class memcached extends Cache\CmsPages\memcache
{
    const ENGINE_LABEL = 'memcached';
    
    protected function _getMemcache ()
    {
        if ( $this->_oMemcache === null ) {
            $this->_oMemcache = new \Memcached();
            $this->_oMemcache->addServer('127.0.0.1', 11211);
        }
        
        return $this->_oMemcache;
    }
}