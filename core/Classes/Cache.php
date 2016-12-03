<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Cache
 */
abstract class CMSc_Cache
{
    abstract protected function _getList ();
    
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
     * Returns the list of cached CmsPages
     *
     * @return CMSc_CmsPage[]
     */
    public function getList ()
    {
        return $this->_getList();
    }
    
    /**
     * Returns a text label identifying the cache engine currently in use
     */
    public function getEngineLabel ()
    {
        return static::ENGINE_LABEL;
    }
}