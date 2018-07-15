<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Utils as CMSc_Utils;

/**
 */
abstract class Cache
{
    abstract protected function _getList ($limit = null, $offset = null, $aFilters = []);
    abstract protected function _getCount ();

    /**
     * Override in child classes;
     */
    const ENGINE_LABEL = null;

    /**
     * @var boolean
     */
    protected $blInitialized = false;
    
    /**
     * @var int|string
     */
    protected $_sShopId = null;
    
    public function init ()
    {
        if ( $this->blInitialized ) {
            return;
        }
        
        $this->blInitialized = true;
    }

    /**
     * @return int|string
     */
    public function getShopId ()
    {
        if ( $this->_sShopId === null ) {
            return Registry::getConfig()->getShopId();
        } else {
            return $this->_sShopId;
        }
    }

    /**
     * @param int|string $sShopId
     */
    public function setShopId ($sShopId)
    {
        $this->_sShopId = $sShopId;
    }

    /**
     * @return int
     */
    public function getCount ()
    {
        return call_user_func_array([$this, '_getCount'], func_get_args());
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array $aFilters
     * @return mixed
     */
    public function getList ($limit = null, $offset = null, $aFilters = [])
    {
        return $this->_getList($limit, $offset, $aFilters);
    }
    
    /**
     * Returns a text label identifying the cache engine currently in use
     *
     * @return string
     */
    public function getEngineLabel ()
    {
        return static::ENGINE_LABEL;
    }
}