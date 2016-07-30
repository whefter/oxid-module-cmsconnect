<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_CmsPage_Id
 */
class CMSc_CmsPage_Id extends CMSc_CmsPage
{
    /**
     * _sType
     *
     * @var string
     */
    protected $_sType = CMSc_Utils::TYPE_IDENTIFIER_ID;
    
    /**
     * _sId
     *
     * @var string
     */
    protected $_sId = null;
    
    /**
     * Constructor for the id-based CMS page object
     *
     * @param string        $sCmsPageId     Requested CMS page id
     * @param string        $sLang          Requested language
     */
    function __construct ( $sCmsPageId, $sLang )
    {
        parent::__construct();
        
        $this->_sId = $sCmsPageId;
        $this->setLang($sLang);
    }
    
    /**
     * Getter method for the id of this page
     *
     * @return string
     */
    public function getPageId ()
    {
        return $this->_sId;
    }
    
    /**
     * Returns the base URL: only identifies the page, no extra query string or similar
     *
     * @return string
     */
    public function getUrl ()
    {
        $sUrl = CMSc_Utils::buildCmsIdPageFullUrl( $this->getPageId(), $this->getLang() );
        
        return $sUrl;
    }
}