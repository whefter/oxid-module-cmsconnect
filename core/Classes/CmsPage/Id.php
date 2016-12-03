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
        
        $this->setPageId($sCmsPageId);
        $this->setLang($sLang);
    }
    
    /**
     * Setter method for the id of this page
     *
     * @param string        $sCmsPageId     CMS page id
     *
     * @return string
     */
    private function setPageId ($sCmsPageId)
    {
        $this->_sId = $sCmsPageId;
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
    
    /**
     * Override parent.
     */
    public function serialize ()
    {
        return serialize(array_merge(
            unserialize(parent::serialize()),
            [
                'sId' => $this->getPageId(),
            ]
        ));
    }
    
    /**
     * Override parent.
     */
    public function unserialize ($data)
    {
        parent::unserialize($data);
        
        $aData = unserialize($data);
        
        $this->setPageId($aData['sId']);
    }
}