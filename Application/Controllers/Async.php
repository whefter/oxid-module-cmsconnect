<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Controllers;

use \wh\CmsConnect\Application\Models\CmsPage;

/**
 * cmsconnect_async
 */
class Async extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Constructor. Sets the $_sThisTemplate to the current class name with a .tpl suffix.
     
     * @return void
     */   
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = 'modules/wh/cmsconnect/async.tpl';
    }
    
    /**
     * 
     *
     * @return string
     */
    public function render()
    {
        // echo __METHOD__;
        // var_dump(parent::render());
        // die;
        
        return parent::render();
    }
    
    public function getContent ()
    {
        $sContent       = $_POST['content'];
        $sMethod        = $_POST['method'];
        $sIdentifier    = $_POST['identifier'];
        $sLang          = $_POST['lang'];
        
        if ( $sMethod === 'id' ) {
            $oPage = new CmsPage\Id($sIdentifier, $sLang);
        } else {
            $oPage = new CmsPage\Path($sIdentifier, $sLang);
        }

        return $oPage->getContent($sContent);
    }
}
