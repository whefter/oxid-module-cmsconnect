<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models\Cache\LocalPages;

use \wh\CmsConnect\Application\Models\Cache;
use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \t as t;

/**
 * CMSc_Cache_LocalPages_Disabled
 */
class Disabled extends Cache\LocalPages
{
    const ENGINE_LABEL = 'Disabled';
    
    /**
     * Override
     */
    protected function _deleteLocalPageCache ($sCacheKey)
    {
        class_exists('t') && t::s(__METHOD__);
        
        class_exists('t') && t::e(__METHOD__);
    }

    protected function _getCount ()
    {
        return 0;
    }
    
    /**
     * Override parent.
     */
    protected function _getList ($limit = null, $offset = null, $aFilters = [])
    {
        return [];
    }
    
    /**
     * Override parent.
     */
    protected function _getLocalPageCache ($sCacheKey)
    {
        return [];
    }
    
    /**
     * Override parent.
     */
    public function commit ()
    {
    }
}