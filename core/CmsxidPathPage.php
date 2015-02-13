<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * CmsxidPathPage
 */
class CmsxidPathPage
{
    /**
     * _sPagePath
     *
     * @var string
     */
    protected $_sPagePath = null;
    
    /**
     * Creator for the page path-based page object
     *
     * @param string        $sPagePath      Requested page path
     * @param string        $sLang          Requested language
     */
    function __construct ( $sPagePath, $sLang )
    {
        $this->_sPagePath   = $sPage;
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
        return CmsxidUtils::getFullPageUrl( $this->_sPage, $this->_sLang );
    }
}