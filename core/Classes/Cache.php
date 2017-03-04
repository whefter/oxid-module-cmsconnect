<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * CMSc_Cache
 */
abstract class CMSc_Cache
{
    abstract protected function _getList ($limit = null, $offset = null);
    abstract protected function _getCount ();
    
    /**
     * @var
     */
    protected $blInitialized = false;
    
    /**
     * @var
     */
    protected $_sShopId = null;
    
    /**
     *
     */
    public function init ()
    {
        if ( $this->blInitialized ) {
            return;
        }
        
        $this->blInitialized = true;
    }
    
    /**
     *
     */
    public function getShopId ()
    {
        if ( $this->_sShopId === null ) {
            return oxRegistry::getConfig()->getShopId();
        } else {
            return $this->_sShopId;
        }
    }
    
    /**
     *
     */
    public function setShopId ($sShopId)
    {
        $this->_sShopId = $sShopId;
    }
    
    /**
     */
    public function getCount ()
    {
        return $this->_getCount();
    }
    
    /**
     */
    public function getList ($limit = null, $offset = null)
    {
        return $this->_getList($limit, $offset);
    }
    
    /**
     * Returns a text label identifying the cache engine currently in use
     */
    public function getEngineLabel ()
    {
        return static::ENGINE_LABEL;
    }
}