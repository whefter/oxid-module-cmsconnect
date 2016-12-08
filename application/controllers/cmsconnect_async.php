<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */
 
/**
 * cmsconnect_async
 */
class cmsconnect_async extends oxUBase
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
            $oPage = new CMSc_CmsPage_Id($sIdentifier, $sLang);
        } else {
            $oPage = new CMSc_CmsPage_Path($sIdentifier, $sLang);
        }

        return $oPage->getContent($sContent);
    }
}
