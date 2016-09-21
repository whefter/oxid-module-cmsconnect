<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2016 William Hefter
 */

/**
 * CMSc_Utils
 */
class CMSc_Utils
{
    const
        TYPE_IDENTIFIER_PATH            = 1,
        TYPE_IDENTIFIER_ID              = 2,
        
        CONFIG_KEY_SSL_DONT_VERIFY_PEER     = 'blCMScSslDontVerifyPeer',
        CONFIG_KEY_ENABLE_TEST_CONTENT      = 'blCMScEnableTestContent',
        CONFIG_KEY_TEST_CONTENT             = 'sCMScTestContent',
        CONFIG_KEY_CURL_EXECUTE_TIMEOUT     = 'sCMScCurlExecuteTimeout',
        CONFIG_KEY_CURL_CONNECT_TIMEOUT     = 'sCMScCurlConnectTimeout',
        CONFIG_KEY_DONT_REWRITE_URLS        = 'blCMScLeaveUrls',
        CONFIG_KEY_TTL_DEFAULT_RND          = 'sCMScTtlDefaultRnd',
        CONFIG_KEY_TTL_DEFAULT              = 'sCMScTtlDefault',
        
        CONFIG_KEY_LOCAL_PAGES_CACHE_ENGINE          = 'sCMScLocalPageCacheEngine',
        VALUE_LOCAL_PAGES_CACHE_ENGINE_AUTO          = 'LOCAL_PAGE_CACHE_AUTO',
        VALUE_LOCAL_PAGES_CACHE_ENGINE_OXIDFILECACHE = 'LOCAL_PAGE_CACHE_OXIDFILECACHE',
        VALUE_LOCAL_PAGES_CACHE_ENGINE_DB            = 'LOCAL_PAGE_CACHE_DB',
        VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHE      = 'LOCAL_PAGE_CACHE_MEMCACHE',
        VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHED     = 'LOCAL_PAGE_CACHE_MEMCACHED',
        
        CONFIG_KEY_CMS_PAGES_CACHE_ENGINE            = 'sCMScCmsPageCacheEngine',
        VALUE_CMS_PAGES_CACHE_ENGINE_AUTO            = 'CMS_PAGE_CACHE_AUTO',
        VALUE_CMS_PAGES_CACHE_ENGINE_OXIDFILECACHE   = 'CMS_PAGE_CACHE_OXIDFILECACHE',
        VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHED       = 'CMS_PAGE_CACHE_MEMCACHED',
        VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHE        = 'CMS_PAGE_CACHE_MEMCACHE',
        
        CONFIG_KEY_BASE_URLS            = 'aCMScBaseUrls',
        CONFIG_KEY_BASE_SSL_URLS        = 'aCMScBaseSslUrls',
        CONFIG_KEY_PAGE_PATHS           = 'aCMScPagePaths',
        CONFIG_KEY_QUERY_PARAMETERS     = 'aCMScParams',
        CONFIG_KEY_ID_PARAMETERS        = 'aCMScIdParams',
        CONFIG_KEY_LANG_PARAMETERS      = 'aCMScLangParams',
        CONFIG_KEY_SEO_IDENTIFIERS      = 'aCMScSeoIdents',
        
        CONFIG_DEFAULTVALUE_TTL                     = 36000,
        CONFIG_DEFAULTVALUE_TTL_RND                 = 10,
        CONFIG_DEFAULTVALUE_CURL_EXECUTE_TIMEOUT    = 1000,
        CONFIG_DEFAULTVALUE_CURL_CONNECT_TIMEOUT    = 1000
        ;
    
    /**
     * GET/POST parameters that should never get passed along to 
     * the CMS (known to belong to OXID or simply troublesome)
     *
     * @var string[]
     */
    protected static $_aImplicitParamsBlacklist = array(
        'cl',
        'fn',
        'shp',
        'stoken',
        'PHPSESSID',
        'force_sid',
        'force_admin_sid',
        'editlanguage',
    );
    
    /**
     * Cache variable for the sources configuration, prevents the system from having
     * to fetch all configuration all the time
     *
     * @var array[]
     */
    protected static $_aConfiguredSources = null;
    
    /**
     * Configuration: return a configuration value. Caches values to circumvent any
     * problems of getShopConfVar(); getShopConfVar() is the only way to ensure
     * the configuration value is for the module, but it requests the value from
     * the database, so cache it.
     *
     * @param   string      $sKey       Configuration key
     * 
     * @return mixed
     */
    public static function getConfigValue ($sKey)
    {
        startProfile(__METHOD__);
        
        $mVal = CMSc_SessionCache::get('config', $sKey);
        
        if ( $mVal === null ) {
            $oxConfig = oxRegistry::getConfig();
            
            $mVal = $oxConfig->getShopConfVar($sKey, $oxConfig->getShopId(), 'module:cmsconnect');
            
            CMSc_SessionCache::set('config', $sKey, $mVal);
        }
        
        stopProfile(__METHOD__);
        
        return $mVal;
    }
    
    /**
     * Same as getConfigValue(), but for configuration values that are language specific,
     * i.e. present once for each language.
     * 
     * @param   string      $sKey       Configuration key
     * @param   string      $sLang      Language to return the configuration value for
     *
     * @return mixed
     */
    public static function getLangConfigValue ($sKey, $sLang = null)
    {
        startProfile(__METHOD__);
        
        $aVal = CMSc_Utils::getConfigValue($sKey);
        
        if ( $sLang === null ) {
            $sLang = oxRegistry::getLang()->getBaseLanguage();
        }
        
        $sLang = CMSc_Utils::_getStandardLanguageIdentifier($sLang);
        
        if ( !is_array($aVal) || !array_key_exists($sLang, $aVal) ) {
            $mVal = false;
        } else {
            $mVal = $aVal[$sLang];
        }
        
        stopProfile(__METHOD__);
        
        return $mVal;
    }
    
    /**
     * Returns an integer language identifier no matter what. Used for accessing configuration variables.
     * 
     * @param   string      $sLang      Language identifier in arbitrary form (abbreviation/integer)
     *
     * @return int
     */
    protected static function _getStandardLanguageIdentifier ($sLang)
    {
        startProfile(__METHOD__);
        
        $sLangMapped = CMSc_SessionCache::get('langIdentMap', $sLang);

        if ( $sLangMapped === null ) {
            $oxLang     = oxRegistry::getLang();
            $aLanguages = $oxLang->getLanguageArray();
            
            foreach ( $aLanguages as $iLang => $oLang ) {
                CMSc_SessionCache::set('langIdentMap', $oLang->abbr,     (int)$iLang);
                CMSc_SessionCache::set('langIdentMap', (int)$iLang,      (int)$iLang);
                CMSc_SessionCache::set('langIdentMap', (string)$iLang,   (int)$iLang);
            }
            
            $sLangMapped = CMSc_SessionCache::get('langIdentMap', $sLang);
        }
        
        stopProfile(__METHOD__);
        
        return $sLangMapped;
    }
    
    /**
     * Build full CMS URL from the passed page path and OXID lang ID. The lang ID is mapped to the
     * corresponding CMS language ID.
     *
     * @param string        $sCmsPagePath   CMS page path
     * @param int|string    $sLang          OXID language ID/Abbrev.
     *
     * @return string
     */
    public static function buildCmsPathPageFullUrl ( $sCmsPagePath = null, $sLang = null )
    {
        // Requesting the root page must be done by passing '/'
        if ( !$sCmsPagePath ) {
            return false;
        }
        
        startProfile(__METHOD__);
        
        $oxConfig       = oxRegistry::getConfig();
        $blSsl          = $oxConfig->isSsl();
        
        $sBaseUrl       = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_URLS,          $sLang);
        $sBaseUrlSsl    = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_SSL_URLS,      $sLang);
        $sBasePagePath  = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_PAGE_PATHS,         $sLang);
        $sParams        = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_QUERY_PARAMETERS,   $sLang);
        
        // We don't know how the user input his parameters, so parse them to be sure
        $aParams = array();
        parse_str( $sParams, $aParams );
        $sParams = http_build_query( $aParams );
        
        $sFullPageUrl   =     ($sBaseUrlSsl && $blSsl ? $sBaseUrlSsl : $sBaseUrl)
                            . '/' . $sBasePagePath
                            . '/' . static::sanitizePageTitle($sCmsPagePath)
                            . '/?'
                            . $sParams
                        ;
        
        stopProfile(__METHOD__);
        
        return static::sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Build full CMS URL from the passed page ID and OXID lang ID. The lang ID is mapped to the
     * corresponding CMS language ID.
     *
     * @param int           $sCmsPageId     Page ID
     * @param int|string    $sLang          OXID language ID/Abbrev.
     *
     * @return string
     */
    public static function buildCmsIdPageFullUrl ( $sCmsPageId, $sLang = null )
    {
        // This checks for empty values but also makes sure the passed content ID isn't just 0; in other words,
        // this checks for empty strings, null, false, etc.
        if ( empty($sCmsPageId) && !is_numeric($sCmsPageId) ) {
            return false;
        }
        
        startProfile(__METHOD__);
        
        $oxConfig       = oxRegistry::getConfig();
        $blSsl          = $oxConfig->isSsl();
        
        $sBaseUrl       = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_URLS,          $sLang);
        $sBaseUrlSsl    = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_SSL_URLS,      $sLang);
        $sIdParam       = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_ID_PARAMETERS,      $sLang);
        $sLangParam     = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_LANG_PARAMETERS,    $sLang);
        $sParams        = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_QUERY_PARAMETERS,   $sLang);
        
        // Parse the user-specified params and add the id and L parameters
        $aParams = array();
        parse_str( $sParams, $aParams );
        parse_str( $sLangParam, $aParams );
        $aParams = array_merge(
            $aParams,
            array(
                $sIdParam   => (int)$sCmsPageId,
            )
        );
        $sParams = http_build_query( $aParams );
        
        $sFullPageUrl =  ($sBaseUrlSsl && $blSsl ? $sBaseUrlSsl : $sBaseUrl)
                            . '/?'
                            . $sParams
                        ;
        
        stopProfile(__METHOD__);
        
        return static::sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Sanitize URL, useful to ensure HTTP results get cached only once, even if they have been
     * requested mutliple times under different URLs (multiple /, for example).
     *
     * This method gets called so often it has its own caching mechanism.
     *
     * @param string    $sUnsanitizedUrl    Page URL
     * 
     * @return string
     */
    public static function sanitizeUrl ($sUnsanitizedUrl)
    {
        startProfile(__METHOD__);
        
        $sSanitizedUrl = CMSc_SessionCache::get('urls', $sUnsanitizedUrl);
        
        if ( !$sSanitizedUrl ) {
            // Automatic encoding handling
            $oStr = getStr();
            
            // Replace multiple slashes, except for the protocol part
            $sUrl = $oStr->preg_replace( '/(?<!:)\/+/', '/', $sUnsanitizedUrl);
            
            // Put through PHP's functions to ensure standardized URL
            // $sUrl = http_build_url( parse_url( $sUrl ) );
            
            // Remove ending '&' or '?'
            // Do this without regular expressions (which are expensive)
            $sUrl = rtrim( $sUrl, '&?' );
            
            $sSanitizedUrl = $sUrl;
            
            CMSc_SessionCache::set('urls', $sUnsanitizedUrl, $sSanitizedUrl);
        }
        
        stopProfile(__METHOD__);
        
        return $sSanitizedUrl;
    }
    
    /**
     * Ensure that a page title can be used in a URL without causing problems.
     * 
     * The point of this helper method is to SEO-encode a page title such as it
     * might look in the CMS
     *
     * @param string    $sUnsanitizedTitle      Title
     * 
     * @return string
     */
    public static function sanitizePageTitle ( $sUnsanitizedTitle )
    {
        startProfile(__METHOD__);
        
        $sSanitizedTitle = CMSc_SessionCache::get('title', $sUnsanitizedTitle);
        
        if ( !$sSanitizedTitle ) {
            // Automatic encoding handling
            $oStr = getStr();
            
            $sTitle = $sUnsanitizedTitle;
            // var_dump("<br />unsanitized: $sUnsanitizedTitle");
            
            // Strip leading slashes
            // $sTitle = $oStr->preg_replace( '/^\/+/', '', $sTitle);
            $sTitle = ltrim($sTitle, '/');
            
            // Replace multiple instances of slashes with a single one, but make sure there is an ending slash
            $sTitle = $oStr->preg_replace( '/\/+/', '/', $sTitle . '/');
            
            // Slashes should be left intact
            // $sTitle = $oStr->preg_replace('/\/+/', '/', $sTitle);
            $aTitleParts = explode('/', $sTitle);
            
            // Use OXID SEO encoder native functions
            $oxSeoEncoder = oxRegistry::get('oxSeoEncoder');
            
            foreach ( $aTitleParts as $i => $sTitlePart ) {
                // Try to match T3's SEO sanitizing
                // $sTitlePart = "test 12!!(&gt;&amp;/=?=()`?--%&-";
                
                // Decode entities _before_ running them through oxSeoEncoder::encodeString(), as the later
                // would turn (e.g.) "&amp;" into "amp"
                $sTitlePart = html_entity_decode($sTitlePart);
                $sTitlePart = $oxSeoEncoder->encodeString($sTitlePart, true, $iLang);
                $sTitlePart = $oStr->preg_replace('/[^\w\s]/', '-', $sTitlePart);
                
                $sTitlePart = trim($sTitlePart);
                
                // var_dump("after seoencoder: $sTitlePart");
                
                // Convert to lowercase
                $sTitlePart = $oStr->strtolower($sTitlePart);
                
                // Remove Tags
                $sTitlePart = strip_tags($sTitlePart);
                
                // Replace special characters with hyphen
                $sTitlePart = $oStr->preg_replace('/[ \-+_]+/', '-', $sTitlePart);
                
                $sTitlePart = trim($sTitlePart, '-');
                
                $aTitleParts[$i] = rawurlencode($sTitlePart);
            }
            
            $sTitle = implode('/', $aTitleParts);
            
            $sSanitizedTitle = $sTitle;
                
            CMSc_SessionCache::set('title', $sUnsanitizedTitle, $sSanitizedTitle);
        }
        
        stopProfile(__METHOD__);
        
        return $sSanitizedTitle;
    }
    
    /**
     * Fetches the actual text content of a SimpleXML node; removes the root tag and CDATA tags around the text node
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public static function getTextContentFromXmlObject ( $oXml )
    {
        startProfile(__METHOD__);
        
        $sText = '';
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty string.
        if ( $oXml !== false ) {
            $sText = trim( $oXml->asXML() );
            
            // var_dump(__METHOD__.':'.htmlentities($sText));
            
            // Remove CDATA tag
            $sText = static::unwrapCDATA( $sText );
            
            // Detect empty tag
            if ( $sText == '<' . $oXml->getName() . '/>' ) {
                $sText = false;
            } else {
                // Remove enclosing tag
                $iStart = strpos( $sText, '>' ) + 1;
                $iLen   = strrpos( $sText, '<' ) - $iStart;
                
                $sText = substr( $sText, $iStart, $iLen );
                // $sText = preg_replace( '/^<' . $oSnippetXml->getName() . '>(.*)<\/' . $oSnippetXml->getName() . '>$/s', '\\1', $sText );
            }
        }
        
        stopProfile(__METHOD__);
        
        return $sText;
    }
    
    /**
     * Helper function to execute a single GET request
     *
     * @return HttpResult
     */
    public static function httpGet ($sUrl)
    {
        return static::httpMultiRequest([
            'url' => $sUrl,
        ])[0];
    }
    
    /**
     * Helper function to execute a single POST request
     *
     * @return HttpResult
     */
    public static function httpPost ($sUrl, $aParams)
    {
        return static::httpMultiRequest([
            'url' => $sUrl,
            'method' => 'post',
            'params' => $aParams,
        ])[0];
    }
    
    /**
     * Performs multiple cURL requests simultaneously and returns when all have finished.
     *
     * @return HttpResult[]
     */
    public static function httpMultiRequest ($aRequests)
    {
        // http://php.net/manual/en/function.curl-multi-init.php
        
        $chs = [];
        $mh = curl_multi_init();
        
        foreach ( $aRequests as $i => $aRequest ) {
            $sUrl = $aRequest['url'];
            
            $chs[$i] = curl_init();
            curl_setopt( $chs[$i], CURLOPT_URL,             $sUrl );
            curl_setopt( $chs[$i], CURLOPT_FOLLOWLOCATION,  1 );
            curl_setopt( $chs[$i], CURLOPT_RETURNTRANSFER,  1 );
            
            curl_setopt( $chs[$i], CURLOPT_SSL_VERIFYPEER,  !static::getConfigValue(CMSc_Utils::CONFIG_KEY_SSL_DONT_VERIFY_PEER) );
            curl_setopt( $chs[$i], CURLOPT_SSL_VERIFYHOST,  !static::getConfigValue(CMSc_Utils::CONFIG_KEY_SSL_DONT_VERIFY_PEER) );
            
            $iConnectTimeout = static::getConfigValue(CMSc_Utils::CONFIG_KEY_CURL_CONNECT_TIMEOUT);
            $iConnectTimeout = $iConnectTimeout ?: static::CONFIG_DEFAULTVALUE_CURL_CONNECT_TIMEOUT;
            curl_setopt( $chs[$i], CURLOPT_CONNECTTIMEOUT_MS,  $iConnectTimeout );
            
            $iExecuteTimeout = static::getConfigValue(CMSc_Utils::CONFIG_KEY_CURL_EXECUTE_TIMEOUT);
            $iExecuteTimeout = $iConnectTimeout ?: static::CONFIG_DEFAULTVALUE_CURL_EXECUTE_TIMEOUT;
            curl_setopt( $chs[$i], CURLOPT_CONNECTTIMEOUT, $iExecuteTimeout );
            
            // curl_setopt( $chs[$i], CURLOPT_SSLVERSION,      1 );
            
            // For POST
            if ( strtoupper($aRequest['method']) === 'POST' ) {
                curl_setopt( $chs[$i], CURLOPT_POST, $blPost );
                curl_setopt( $chs[$i], CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/x-www-form-urlencoded'
                ) );
                
                curl_setopt( $chs[$i], CURLOPT_POSTFIELDS, http_build_query($aRequest['params']) );
            }
            
            curl_multi_add_handle($mh, $chs[$i]);
        }
        
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            // 1 ms seems to be a good compromise; it is highly unlikely that any CMS will be able to deliver
            // more than one page in 1 ms. If it does, the user isn't likely to notice.
            usleep(1000);
        } while ($running);

        $aResults = [];
        foreach ( $aRequests as $i => $aRequest ) {
            $oHttpResult = new stdClass();
            $oHttpResult->content = curl_multi_getcontent($chs[$i]);
            $oHttpResult->info = curl_getinfo($chs[$i]);
            
            if ( $oHttpResult->info['http_code'] != 200 ) {
                $oHttpResult->content = '';
            }
            
            $aResults[] = $oHttpResult;
        }
        
        foreach ( $aRequests as $i => $aRequest ) {
            curl_multi_remove_handle($mh, $chs[$i]);
        }
        curl_multi_close($mh);
        
        return $aResults;
    }
    
    /**
     * Returns an array of explicit query parameters, i.e. parameters that could have been
     * passed by CMS plugins. Excludes all implicit parameters set by OXID when it resolves
     * SEO URLs and excludes well-known OXID parameters (see top of file)
     *
     * @return array()
     */
    public static function getImplicitQueryParams ()
    {
        // These are the explicit query params, not the one OXID set after
        // looking up the SEO URL
        $aImplicitQueryParams = array();
        parse_str( $_SERVER['QUERY_STRING'], $aImplicitQueryParams );
        
        // var_dump(__METHOD__);
        // var_dump($aImplicitQueryParams);
        
        // Remove parameters specified in the static blacklist
        foreach ( self::$_aImplicitParamsBlacklist as $sBlacklistedParam ) {
            unset( $aImplicitQueryParams[$sBlacklistedParam] );
        }
        // var_dump(__METHOD__, $aImplicitQueryParams);
        return $aImplicitQueryParams;
    }
    
    /**
     * Removes named HTML entities from the passed XML string
     *
     * @param string    $sXml       XML source string
     * 
     * @return string
     */
    public static function fixXmlSourceEntities ( $sXml )
    {
        startProfile(__METHOD__);
        
        $sXml = str_replace('&nbsp;', '&#160;', $sXml);
        
        // Fix stray ampersands, lit. '& not followed by word characters and a semicolon will be replaced with &amp;'
        $sXml = preg_replace( '/&(?![\w#]+;)/', '&amp;', $sXml );
        
        // var_dump(__METHOD__, $sXml);
        // die;

        stopProfile(__METHOD__);
        
        return $sXml;
    }
    
    /**
     * Loads the passed XML source with the SimpleXML loader and returns the generated object
     *
     * @param string    $sXmlSource       XML source string
     * 
     * @return string
     */
    public static function createXmlObjectFromSource ($sXmlSource)
    {
        startProfile(__METHOD__);
        
        try {
            libxml_use_internal_errors(true);
            
            $oXml = simplexml_load_string($sXmlSource);
            
            $aErrors = libxml_get_errors();
            if ( count($aErrors) ) {
                throw new Exception('XML parsing error');
            }
        } catch ( Exception $ex ) {
            $oXml = false;
        }
        
        stopProfile(__METHOD__);
        
        return $oXml;
    }
    
    /**
     * Removes CDATA tags from the source but leaves their content; i.e. their content would get
     * parsed by an XML loader.
     *
     * @param string    $sXml       XML source string
     * 
     * @return string
     */
    public static function unwrapCDATA( $sXml )
    {
        startProfile(__METHOD__);
        
        $oStr = getStr();
        
        // Remove CDATA tag
        $sXml = $oStr->preg_replace( '/<!\[CDATA\[(.*?)\]\]>/ms', '\\1', $sXml );
        
        stopProfile(__METHOD__);
        
        return $sXml;
    }
    
    /**
     * Returns the contents of the sCMScCurSeoPage config param which, if we are on an SEO-loaded page, should
     * contain the page to load through CMSconnect
     *
     * @return string
     */
    public static function getCurrentLocalPageSeoPath ()
    {
        return oxRegistry::getConfig()->getConfigParam( 'sCMScCurSeoPage' );
    }
    
    /**
     * Checks if the passed URL refers to any known (configured) CMS source. If so, return an
     * array with that information
     * 
     * @param string        $sSeoUrl        SEO URL to check. Per OXID convention this does not include a domain name
     * 
     * @return array
     */
    public static function getPageSeoInfoByUrl ( $sSeoUrl )
    {
        startProfile(__METHOD__);
        
        $oxConfig   = oxRegistry::getConfig();
        $oxLang     = oxRegistry::getLang();
        
        $aSeoInfo = false;
        
        foreach ( $oxLang->getLanguageArray() as $oLang ) {
            $sSeoIdent = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_SEO_IDENTIFIERS, $oLang->id);
            
            // Either starts with SEO identifier and a slash (subpage of CMS is called)
            // or is just the plain SEO identifier
            if ( strpos($sSeoUrl, $sSeoIdent . '/') === 0 || $sSeoUrl == $sSeoIdent ) {
                $sPage =    ($sSeoUrl == $sSeoIdent)
                                ? ''
                                : str_replace( $sSeoIdent . '/', '', $sSeoUrl )
                            ;
                
                $aSeoInfo = array(
                    'lang'  => $oLang->id,
                    'cl'    => 'cmsconnect_frontend',
                    'page'  => $sPage,
                );

                break;
            }
        }
        
        stopProfile(__METHOD__);
        
        return $aSeoInfo;
    }
    
    /**
     * Rewrites all CMS-related URLs in the passed content to point to the shop
     *
     * @param string    $sTextContent       Content to process
     * 
     * @return string
     */
    public static function rewriteTextContentLinks ( $sTextContent )
    {
        startProfile(__METHOD__);
        
        $oxConfig = oxRegistry::getConfig();
        $oxLang   = oxRegistry::getLang();
        
        if ( static::getConfigValue(CMSc_Utils::CONFIG_KEY_DONT_REWRITE_URLS) == true ) {
            stopProfile(__METHOD__);
            
            return $sTextContent;
        }
        
        foreach ( $oxLang->getLanguageArray() as $oLang ) {
            $sSourceUrl     = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_URLS);
            $sSourceSslUrl  = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_SSL_URLS);
            
            // No configured URLs - skip this language
            if ( !$sSourceUrl && !$sSourceSslUrl ) {
                continue;
            }
            
            // No matter what URLs the CMS returns, the URLs schema needs to be rewritten to the current shop's schema
            foreach ( array($sSourceUrl, $sSourceSslUrl) as $sSourceBaseUrl ) {
                $sSourcePagePath    = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_PAGE_PATHS, $oLang->id);
                $sFullBaseUrl       = static::sanitizeUrl( $sSourceBaseUrl . '/' . $sSourcePagePath . '/' );
                
                // Replace all links
                unset($aMatches);
                $sLinkPattern = '/href=(\'|")' . preg_quote($sFullBaseUrl, '/') . '[^"\']*(.|\/|\.html|\.php|\.asp)(\?[^"\']*)?(\'|")/';
                preg_match_all( $sLinkPattern, $sTextContent, $aMatches, PREG_SET_ORDER );
                
                foreach ( $aMatches as $aMatch ) {
                    $sTextContent = str_replace( $aMatch[0], static::rewriteUrl($aMatch[0]), $sTextContent );
                }
            }
        }
        
        stopProfile(__METHOD__);

        return $sTextContent;
    }
    
    /**
     * Rewrites a single CMS URL
     *
     * @param string    $sUrl       The URL to process
     * 
     * @return string
     */
    public static function rewriteUrl ( $sUrl )
    {
        startProfile(__METHOD__);
        
        $oxConfig = oxRegistry::getConfig();
        $oxLang   = oxRegistry::getLang();
        
        foreach ( $oxLang->getLanguageArray() as $oLang ) {
            $sSourceUrl     = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_URLS);
            $sSourceSslUrl  = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_BASE_SSL_URLS);
            
            // No configured URLs - skip this language
            if ( !$sSourceUrl && !$sSourceSslUrl ) {
                continue;
            }
            
            // No matter what URLs the CMS returns, the URLs schema needs to be rewritten to the current shop's schema
            foreach ( array($sSourceUrl, $sSourceSslUrl) as $sSourceBaseUrl ) {
                $sSourcePagePath    = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_PAGE_PATHS, $oLang->id);
                $sFullBaseUrl       = static::sanitizeUrl( $sSourceBaseUrl . '/' . $sSourcePagePath . '/' );
                
                // The target is defined by the current shop's SSL setting
                $sTargetBaseUrl     = $oxConfig->isSsl() ? $oxConfig->getSslShopUrl($sLang) : $oxConfig->getShopUrl($sLang);
                $sTargetSeoIdent    = CMSc_Utils::getLangConfigValue(CMSc_Utils::CONFIG_KEY_SEO_IDENTIFIERS, $oLang->id);
                $sFullTargetUrl     = static::sanitizeUrl( $sTargetBaseUrl . '/' . $sTargetSeoIdent . '/' );

                if ( strpos($sUrl, $sFullBaseUrl) !== false ) {
                    $sUrl = str_replace($sFullBaseUrl, $sFullTargetUrl, $sUrl);
                    
                    // If the shop is in SSL mode, replace all links and sources to the non-SSL CMS with references to the SSL source,
                    // if configured, to prevent browser complaints about mixed modes
                    if ( $oxConfig->isSsl() ) {
                        // Do this ONLY if an SSL source URL has actually been configured
                        if ( $sSourceSslUrl ) {
                            // We can safely do this crude replace since, in theory, all URLs left on the page should be to
                            // non-page CMS content
                            $sSourceBaseUrl     = static::sanitizeUrl( $sSourceUrl . '/' );
                            $sSourceSslBaseUrl  = static::sanitizeUrl( $sSourceSslUrl . '/' );
                        
                            $sUrl = str_replace( $sSourceBaseUrl, $sSourceSslBaseUrl, $sUrl );
                        }
                    }
                    
                    break 2;
                }
            }
        }
        
        stopProfile(__METHOD__);

        return $sUrl;
    }
    
    /**
     * Fixes the passed content's encoding to match that of the shop
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public static function fixTextContentEncoding ( $sContent )
    {
        startProfile(__METHOD__);
        
        $oxConfig = oxRegistry::getConfig();
        
        $sShopCharset = oxRegistry::getLang()->translateString( 'charset' );
    
        $sContentEncoding = mb_detect_encoding( $sText, "UTF-8,ISO-8859-1,$sShopCharset" );
        
        if ( $oxConfig->getConfigParam('iUtfMode') != 1 ) {
            $sContent = str_replace("�", "\"", $sText);
            $sContent = str_replace("�", "\"", $sText);
            $sContent = str_replace("�", "'", $sText);
            $sContent = str_replace("�", "'", $sText);
            $sContent = str_replace("�", "-", $sText);
            
            $sContent = mb_convert_encoding( $sContent, $sContentEncoding, 'UTF-8' );
        }
        
        stopProfile(__METHOD__);
        
        return $sContent;
    }
    
    /**
     * Decode entities in the passed content; this is meant to allow inclusion of Smarty
     * tags in CMS content
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public static function decodeTextContentEntities ( $sContent )
    {
        // Decode entities to allow inclusion of Smarty tags in fetched content
        return html_entity_decode( $sContent );
    }
    
    /**
     * Parse the passed content through Smarty and return the resulting string
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public static function parseTextContentThroughSmarty ( $sContent )
    {
        startProfile(__METHOD__);
        
        $oxUtilsView = oxRegistry::get('oxUtilsView');
        
        $sContent = $oxUtilsView->parseThroughSmarty(
            $sContent,
            // Identifier
            md5($sContent),
            
            null,
            true
        );
        
        stopProfile(__METHOD__);
        
        return $sContent;
    }
    
    /**
     * Return a dummy string with a few page infos for debug purposes
     *
     * @param CMSc_CmsPage    $oPage      content object
     * @param string                $sSnippet   Snippet name
     * 
     * @return string
     */
    public static function getDummyString ($oPage, $sSnippet)
    {
        return '<span class="cmsconnect-dummy">CMSconnect dummy content for URL: ' . $oPage->getFullUrl() . ', argument: ' . $sSnippet . '</span>';
    }
    
    /**
     * Trims '?' and '&' from the left/right sides of the passed query string.
     *
     * @param string        $sQuery     Query string
     * 
     * @return string
     */
    public static function trimQuery($sQuery)
    {
        return rtrim(ltrim($sQuery, '?&'), '?&');
    }

    /**
     * Returns the default test content
     *
     * @return string
     */
    public static function getDefaultTestContent ()
    {
        return <<<EOF
<cmsconnect>
    <navigation>
        <![CDATA[ CMSconnect test content navigation ]]>
    </navigation>
    <content>
        <left>
            <![CDATA[ CMSconnect test content left ]]>
        </left>
        <normal>
            <![CDATA[ CMSconnect test content normal ]]>
        </normal>
        <right>
            <![CDATA[ CMSconnect test content right ]]>
        </right>
        <border>
            <![CDATA[ CMSconnect test content border ]]>
        </border>
    </content>
    <metadata>
        <keywords>
            <![CDATA[ CMSconnect test content metadata keywords ]]>
        </keywords>
        <title>
            <![CDATA[ CMSconnect test content metadata title ]]>
        </title>
        <description>
            <![CDATA[ CMSconnect test content metadata description ]]>
        </description>
    </metadata>
    <breadcrumb>
        <![CDATA[ CMSconnect test content breadcrumbs  ]]>
    </breadcrumb>
    <breadcrumb_xml>
        <crumb>
            <url>http://cms.domain.com/service</url>
            <title>
                <![CDATA[ Service ]]>
            </title>
        </crumb>
        <crumb current="1">
            <url>http://cms.domain.com/service/contact-us/</url>
            <title>
                <![CDATA[ Contact us ]]>
            </title>
        </crumb>
    </breadcrumb>
</cmsconnect>
EOF;
    }
}
