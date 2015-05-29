<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */
 
/**
 * cmsxid_fe
 */
class cmsxid_fe extends oxUBase
{
    /**
     * Constructor. Sets the $_sThisTemplate to the current class name with a .tpl suffix.
     
     * @return void
     */   
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = get_class() . '.tpl';
    }
    
    /**
     * Template variable getter. Overwrites the parent function.
     *
     * Returns the title of the current CMS page from its metadata.
     *
     * @return string
     */
    public function getTitle()
    {
        return oxRegistry::get('oxViewConfig')->getCMSxid()->getPageMetadata('title');
    }
    
    /**
     * Template variable getter. Overwrites the parent function.
     *
     * Returns the description of the current CMS page from its metadata.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $sDescription = oxRegistry::get('oxViewConfig')->getCMSxid()->getPageMetadata('description');
        $sDescription = strip_tags( $sDescription );
        
        return $this->_prepareMetaDescription( $sDescription );
    }
    
    /**
     * Template variable getter. Overwrites the parent function.
     *
     * Returns the keywords of the current CMS page from its metadata.
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        $sKeywords = oxRegistry::get('oxViewConfig')->getCMSxid()->getPageMetadata('keywords');
        
        return $this->_prepareMetaKeyword( $sKeywords );
    }
}
