<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
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
     * _aGetParams
     *
     * @var array
     */
    protected $_aGetParams;
        
    /**
     * _aPostParams
     *
     * @var array
     */
    protected $_aPostParams;
    
    /**
     * _aExtraBlacklistedQueryParams
     *
     * @var array
     */
    protected static $_aExtraBlacklistedQueryParams = array();
    
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
     * Setter method for the language of this page
     *
     * @param string        $sLang          Language identifier
     *
     * @return string
     */
    public function setLang ($sLang)
    {
        if ( empty($sLang) ) {
            $sLang = null;
        }
        
        $this->_sLang = $sLang;
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
        startProfile(__METHOD__);
        
        
        $oUtils = CmsxidUtils::getInstance();
        
        $sBaseUrl = $this->getBaseUrl();
        $sFullUrl = false;
        
        if ( $sBaseUrl ) {
            // $sBaseQuery     = parse_url( $sBaseUrl, PHP_URL_QUERY );
            
            $aMatches = array();
            preg_match('/(\?(?:.*?))(#.*)?$/', $sBaseUrl, $aMatches);
            
            $sBaseQuery = $aMatches[1];
            $sAddQuery  = http_build_query( $this->getExplicitQueryParams() );

            $sFullUrl = $sBaseUrl;
            
            // Strip away the query string
            // $sFullUrl = preg_replace( '/' . preg_quote($sBaseQuery, '/') . '$/', '', $sFullUrl );
            $sFullUrl = substr( $sFullUrl, 0, strlen($sFullUrl) - strlen($sBaseQuery) );
            
            $sFullUrl = rtrim( $sFullUrl, '?&' );
            $sFullUrl .= '?' . $oUtils->trimQuery($oUtils->trimQuery($sAddQuery) . '&') . $oUtils->trimQuery($sBaseQuery);
            $sFullUrl = rtrim( $sFullUrl, '?&' );
        }
        
        stopProfile(__METHOD__);
        
        // var_dump(__METHOD__);
        // var_dump($sFullUrl);
        
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
    
    /**
     * Returns the explicit query params; that is, the ones specified in the URL, minus the
     * ones blacklisted in the current Page Class.
     *
     * @return string[]
     */
    protected function getExplicitQueryParams ()
    {
        $oUtils  = CmsxidUtils::getInstance();
        $aParams = $oUtils->getExplicitQueryParams();
        
        // Remove parameters specified in the static blacklist
        foreach ( $this->getExtraBlacklistedQueryParams() as $sBlacklistedParam ) {
            unset( $aParams[$sBlacklistedParam] );
        }
        
        return $aParams;
    }
    
    /**
     * Returns the explicit query params; that is, the ones specified in the URL.
     *
     * Meant to be overridden.
     *
     * @return string[]
     */
    protected function getExtraBlacklistedQueryParams ()
    {
        return static::$_aExtraBlacklistedQueryParams;
    }
}