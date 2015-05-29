<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */

/**
 * CmsxidIdPage
 */
class CmsxidIdPage extends CmsxidPage
{
    /**
     * _sType
     *
     * @var string
     */
    protected $_sType = CmsxidUtils::TYPE_IDENTIFIER_ID;
    
    /**
     * _sPageId
     *
     * @var string
     */
    protected $_sPageId = null;
    
    /**
     * Constructor for the id-based page object
     *
     * @param string        $sPageId        Requested page id
     * @param string        $sLang          Requested language
     */
    function __construct ( $sPageId, $sLang )
    {
        parent::__construct();
        
        $this->_sPageId  = $sPageId;
        $this->_sLang   = $sLang;
    }
    
    /**
     * Getter method for the id of this page
     *
     * @return string
     */
    public function getPageId ()
    {
        return $this->_sPageId;
    }
    
    /**
     * Returns the base URL: only identifies the page, no extra query string or similar
     *
     * @return string
     */
    public function getBaseUrl ()
    {
        return CmsxidUtils::getFullPageUrlById( $this->getPageId(), $this->getLang() );
    }
}