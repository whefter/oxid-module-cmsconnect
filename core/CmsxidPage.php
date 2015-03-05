<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * CmsxidPage
 */
abstract class CmsxidPage
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
     * __construct
     */
    function __construct ()
    {
    }
    
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
        $sBaseUrl = $this->getBaseUrl();
        $sFullUrl = false;
        
        if ( $sBaseUrl ) {
            $sBaseQuery     = parse_url( $sBaseUrl, PHP_URL_QUERY );
            $sAddQuery      = http_build_query( CmsxidUtils::getExplicitQueryParams() );

            $sFullUrl = $sBaseUrl;
            $sFullUrl = preg_replace( '/' . preg_quote($sBaseQuery, '/') . '$/', '', $sFullUrl );
            $sFullUrl = rtrim( $sFullUrl, '?&' );
            $sFullUrl .= '?' . $sBaseQuery . '&' . $sAddQuery;
            $sFullUrl = rtrim( $sFullUrl, '?&' );
        }
        
        return $sFullUrl;
    }
    
    /**
     * Returns the type identifier for the current page object. This is essentially a variable
     * set in the child classes
     *
     * @return string
     */
    public function getType ()
    {
        return $this->_sType;
    }
}