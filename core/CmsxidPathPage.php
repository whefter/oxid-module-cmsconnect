<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * CmsxidPathPage
 */
class CmsxidPathPage extends CmsxidPage
{
    /**
     * _sType
     *
     * @var string
     */
    protected $_sType = CmsxidUtils::TYPE_IDENTIFIER_PATH;
    
    /**
     * _sPagePath
     *
     * @var string
     */
    protected $_sPagePath = null;
    
    /**
     * Constructor for the page path-based page object
     *
     * @param string        $sPagePath      Requested page path
     * @param string        $sLang          Requested language
     */
    function __construct ( $sPagePath, $sLang )
    {
        parent::__construct();
        
        $this->_sPagePath   = $sPagePath;
        $this->_sLang       = $sLang;
    }
    
    /**
     * Getter method for the page path of this page
     *
     * @return string
     */
    public function getPagePath ()
    {
        return $this->_sPagePath;
    }
    
    /**
     * Returns the base URL: only identifies the page, no extra query string or similar
     *
     * @return string
     */
    public function getBaseUrl ()
    {
        return CmsxidUtils::getFullPageUrl( $this->_sPagePath, $this->_sLang );
    }
}