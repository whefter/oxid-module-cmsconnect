<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

class cmsconnect_oxviewconfig extends cmsconnect_oxviewconfig_parent
{
    /**
     * Instance getter
     *
     * @return object
     */
    public function getCMSconnect()
    {
        return oxRegistry::get('cmsconnect');
    }
    
    /**
     * Instance getter
     *
     * @return object
     */
    public function getCMSxid()
    {
        return $this->getCMSconnect();
    }

    
    /**
     * Compatibility with toxid_curl
     *
     * @return object
     */
    public function getToxid()
    {
        return $this->getCMSconnect();
    }
}