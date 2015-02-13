<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * CmsxidPage
 */
class CmsxidPage
{
    /**
     * _sLang
     *
     * @var string
     */
    protected $_sLang = null;
        
    /**
     * aGetParams
     *
     * @var array
     */
    protected $_aGetParams;
        
    /**
     * aPostParams
     *
     * @var array
     */
    protected $_aPostParams;
    
    /**
     * General function, inherited in child classes. Creates and returns an instance of a Cmsxid Page object
     *
     * @param string        $sIdentifier    Identifier, currently either an ID or a page path.
     * @param string        $sLang          Requested language
     *
     *
     * @return CmsxidPage
     */
    public static function getInstance ( $sIdentifier, $sLang )
    {
        $sClassName = get_called_class();
        
        return new $sClassName( $sIdentifier, $sLang );
    }
    
    /**
     * Getter method for the language of this page
     *
     * @return string
     */
    public function getLang ()
    {
        return $this->_sLang;
    }
    
    /**
     * Returns the full URL with all GET params
     *
     * @return string
     */
    public function getFullUrl ()
    {
        $sBaseQuery     = parse_url( $this->getBaseUrl(), PHP_URL_QUERY );
        $sAddQuery      = http_build_query( $this->getGetParams() );
        
        $sFullUrl = str_replace( $sBaseQuery, )
    }
}