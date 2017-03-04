<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * CMSc_CmsPage_Path
 */
class CMSc_CmsPage_Path extends CMSc_CmsPage
{
    /**
     * _sType
     *
     * @var string
     */
    protected $_sType = CMSc_Utils::TYPE_IDENTIFIER_PATH;
    
    /**
     * _sPath
     *
     * @var string
     */
    protected $_sPath = null;
    
    /**
     * Constructor for the path-based CMS page object
     *
     * @param string        $sCmsPagePath   Requested CMS page path
     * @param string        $sLang          Requested language
     */
    function __construct ( $sCmsPagePath, $sLang )
    {
        parent::__construct();
        
        $this->setPagePath($sCmsPagePath);
        $this->setLang($sLang);
    }
    
    /**
     * Setter method for the path of this page
     *
     * @param string        $sCmsPagePath     CMS page path
     *
     * @return string
     */
    private function setPagePath ($sCmsPagePath)
    {
        $this->_sPath = $sCmsPagePath;
    }
    
    /**
     * Getter method for the path of this CMS page
     *
     * @return string
     */
    public function getPagePath ()
    {
        return $this->_sPath;
    }
    
    /**
     * Returns the base URL: only identifies the page, no extra query string or similar
     *
     * @return string
     */
    public function getUrl ()
    {
        $sUrl = CMSc_Utils::buildCmsPathPageFullUrl( $this->getPagePath(), $this->getLang() );
        
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
                'sPath' => $this->getPagePath(),
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
        
        $this->setPagePath($aData['sPath']);
    }
}