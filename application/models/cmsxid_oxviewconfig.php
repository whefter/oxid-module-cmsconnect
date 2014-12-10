<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

class cmsxid_oxviewconfig extends cmsxid_oxviewconfig_parent
{
    /**
     * Instance getter
     *
     * @return object
     */
    public function getCMSxid()
    {
        return oxRegistry::get('cmsxid');
    }

    
    /**
     * Compatibility with toxid_curl
     *
     * @return object
     */
    public function getToxid()
    {
        return $this->getCMSxid();
    }
}