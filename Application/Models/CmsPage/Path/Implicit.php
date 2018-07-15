<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models\CmsPage\Path;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Models\CmsPage;
use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \t as t;

/**
 * CMSc_CmsPage_Path_Implicit
 */
class Implicit extends CmsPage\Path
{
    /**
     * _aExtraBlacklistedQueryParams
     *
     * @var array
     */
    protected static $_aExtraBlacklistedQueryParams = array('id');
    
    /**
     * Constructor for the path-based CMS page object
     *
     * @param string        $sLang          Requested language
     */
    function __construct ($sLang = null)
    {
        parent::__construct(CMSc_Utils::getCurrentLocalPageSeoPath(), $sLang);
    }
    
    /**
     * Override parent.
     */
    public function isImplicit ()
    {
        return true;
    }
    
    /**
     * Override parent function, add implicit query params (supplied by the current local page, not
     * set programmatically)
     *
     * @return string
     */
    public function getUrl ()
    {
        class_exists('t') && t::s(__METHOD__);
        
        $sBaseUrl = parent::getUrl();
        $sFullUrl = false;
        
        if ( $sBaseUrl ) {
            // $sBaseQuery     = parse_url( $sBaseUrl, PHP_URL_QUERY );
            
            $aMatches = array();
            preg_match('/(\?(?:.*?))(#.*)?$/', $sBaseUrl, $aMatches);
            
            $sBaseQuery = $aMatches[1];
            $sAddQuery  = http_build_query( $this->getImplicitQueryParams() );

            $sFullUrl = $sBaseUrl;
            
            // Strip away the query string
            // $sFullUrl = preg_replace( '/' . preg_quote($sBaseQuery, '/') . '$/', '', $sFullUrl );
            $sFullUrl = substr( $sFullUrl, 0, strlen($sFullUrl) - strlen($sBaseQuery) );
            
            $sFullUrl = rtrim( $sFullUrl, '?&' );
            $sFullUrl .= '?' . CMSc_Utils::trimQuery(CMSc_Utils::trimQuery($sAddQuery) . '&') . '&' . CMSc_Utils::trimQuery($sBaseQuery);
            $sFullUrl = rtrim( $sFullUrl, '?&' );
        }
        
        class_exists('t') && t::e(__METHOD__);
        
        // var_dump(__METHOD__);
        // var_dump($sFullUrl);
        
        return $sFullUrl;
    }
    
    /**
     * Returns the implicit query params; that is, the ones specified in the URL, minus the
     * ones blacklisted in the current page class.
     *
     * @return string[]
     */
    protected function getImplicitQueryParams ()
    {
        $aParams = CMSc_Utils::getImplicitQueryParams();
        
        // Remove parameters specified in the static blacklist
        foreach ( $this->getExtraBlacklistedQueryParams() as $sBlacklistedParam ) {
            unset( $aParams[$sBlacklistedParam] );
        }
        
        return $aParams;
    }
    
    /**
     * Returns the implicit post params; that is, the ones specified in the URL, minus the
     * ones blacklisted in the current page class.
     *
     * @return string[]
     */
    protected function getImplicitPostParams ()
    {
        $aParams = CMSc_Utils::getImplicitPostParams();
        
        // Remove parameters specified in the static blacklist
        foreach ( $this->getExtraBlacklistedQueryParams() as $sBlacklistedParam ) {
            unset( $aParams[$sBlacklistedParam] );
        }
        
        return $aParams;
    }
    
    
    /**
     * @return string[]
     */
    protected function getExtraBlacklistedQueryParams ()
    {
        return static::$_aExtraBlacklistedQueryParams;
    }
    
    /**
     * Override parent.
     */
    public function isCacheable ()
    {
        // The implicit SEO page is exempt from caching IF
        // any query parameters at all have been passed along, since
        // we have to assume some plugin or similar on the page needs these.
        // We have no way of identifying which parameters belong to OXID and
        // which don't, so, to prevent cache flooding, cache ONLY if a plain page
        // has been requested.
        //
        // Additionally, we now use the full page URL for session cache
        if ( count(CMSc_Utils::getImplicitQueryParams()) || $this->isPostPage() ) {
            return false;
        } else {
            return true;
        }
    }
    
    public function getPostParams ()
    {
        return array_merge($this->getImplicitPostParams(), parent::getPostParams());
    }
}