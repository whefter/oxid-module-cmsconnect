<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */
 
/**
 * cmsxid_async
 */
class cmsxid_async extends oxUBase
{
    /**
     * Constructor. Sets the $_sThisTemplate to the current class name with a .tpl suffix.
     
     * @return void
     */   
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = 'modules/cmsxid/async.tpl';
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
        
        $oCxid = $this->getViewConfig()->getCmsxid();
        
        $sReturn = '';
        
        if ( $sMethod === 'id' ) {
            $sReturn = $oCxid->getContentById($sContent, $sIdentifier, $sLang);
        } else {
            $sReturn = $oCxid->getContent($sContent, $sIdentifier, $sLang);
        }

        return $sReturn;
    }
}
