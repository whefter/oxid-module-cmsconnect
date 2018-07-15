<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Modules\Core;

use \OxidEsales\Eshop\Core\Registry as Registry;

class ViewConfig extends ViewConfig_parent
{
    /**
     * Instance getter
     *
     * @return object
     */
    public function getCMSconnect()
    {
        return Registry::get(\wh\CmsConnect\Application\CmsConnect::class);
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