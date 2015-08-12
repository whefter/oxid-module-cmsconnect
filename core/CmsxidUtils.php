<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
 */

/**
 * CmsxidUtils
 */
class CmsxidUtils
{
    const TYPE_IDENTIFIER_PATH  = 1;
    const TYPE_IDENTIFIER_ID    = 2;
    
    const
        CONFIG_KEY_SSL_DONT_VERIFY_PEER = 'blCmsxidSslDontVerifyPeer',
        CONFIG_KEY_ENABLE_DUMMY_CONTENT = 'blCmsxidEnableDummyContent',
        CONFIG_KEY_CURL_EXECUTE_TIMEOUT = 'iCmsxidCurlExecuteTimeout',
        CONFIG_KEY_CURL_CONNECT_TIMEOUT = 'iCmsxidCurlConnectTimeout',
        CONFIG_KEY_DONT_REWRITE_URLS    = 'blCmsxidLeaveUrls',
        CONFIG_KEY_TTL_DEFAULT_RND      = 'iCmsxidTtlDefaultRnd',
        CONFIG_KEY_TTL_DEFAULT          = 'iCmsxidTtlDefault',
        
        CONFIG_KEY_BASE_URLS            = 'aCmsxidBaseUrls',
        CONFIG_KEY_BASE_SSL_URLS        = 'aCmsxidBaseSslUrls',
        CONFIG_KEY_PAGE_PATHS           = 'aCmsxidPagePaths',
        CONFIG_KEY_QUERY_PARAMETERS     = 'aCmsxidParams',
        CONFIG_KEY_ID_PARAMETERS        = 'aCmsxidIdParams',
        CONFIG_KEY_LANG_PARAMETERS      = 'aCmsxidLangParams',
        CONFIG_KEY_SEO_IDENTIFIERS      = 'aCmsxidSeoIdents'
        
        ;
    
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
    public function getConfigValue ($sKey)
    {
        startProfile(__METHOD__);
        
        $mVal = $this->getFromSessionCache('config', $sKey);
        
        if ( $mVal === null ) {
            $oxConfig = oxRegistry::getConfig();
            
            $mVal = $oxConfig->getShopConfVar($sKey, $oxConfig->getShopId(), 'module:cmsxid');
            
            $this->saveToSessionCache('config', $sKey, $mVal);
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
    public function getLangConfigValue ($sKey, $sLang = null)
    {
        startProfile(__METHOD__);
        
        $aVal = $this->getConfigValue($sKey);
        
        if ( $sLang === null ) {
            $sLang = oxRegistry::getLang()->getBaseLanguage();
        }
        
        $sLang = $this->_getStandardLanguageIdentifier($sLang);
        
        if ( !array_key_exists($sLang, $aVal) ) {
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
    protected function _getStandardLanguageIdentifier ($sLang)
    {
        startProfile(__METHOD__);
        
        $sLangMapped = $this->getFromSessionCache('langIdentMap', $sLang);

        if ( $sLangMapped === null ) {
            $oxLang     = oxRegistry::getLang();
            $aLanguages = $oxLang->getLanguageArray();
            
            foreach ( $aLanguages as $oLang ) {
                $this->saveToSessionCache('langIdentMap', $oLang->abbr, (int)$iLang);
                $this->saveToSessionCache('langIdentMap', (int)$iLang,  (int)$iLang);
                $this->saveToSessionCache('langIdentMap', $iLang,       (int)$iLang);
            }
            
            $sLangMapped = $this->getFromSessionCache('langIdentMap', $sLang);
        }
        
        stopProfile(__METHOD__);
        
        return $sLangMapped;
    }
    
    /**
     * The CmsxidUtils singleton instance.
     *
     * @var object
     */
    protected static $_oInstance = null;
    
    /**
     * "Level one" cache. Pages retrieved from remote or from cache are
     * cached here to reduce the hits to file cache or to prevent multiple
     * fetches of a remote page if the cache isn't used.
     *
     * @var CmsxidResult[]
     */
    protected static $_aSessionCache = array();
    
    /**
     * GET/POST parameters that should never get passed along to 
     * the CMS (known to belong to OXID or simply troublesome)
     *
     * @var string[]
     */
    protected static $_aRequestParamBlacklist = array(
        'cl',
        'fn',
        'shp',
    );
    
    /**
     * Cache variable for the sources configuration, prevents the system from having
     * to fetch all configuration all the time
     *
     * @var array[]
     */
    protected static $_aConfiguredSources = null;
    
    /**
     * Return the Cmsxid singleton instance. Construct if not present.
     *
     * @return object
     */
    public static function getInstance ()
    {
        if ( null === self::$_oInstance ) {
            self::$_oInstance = new CmsxidUtils();
        }
        
        return self::$_oInstance;
    }
    
    /**
     * Build full TYPO3 URL for the passed page and OXID lang ID. The lang ID is mapped to the
     * corresponding TYPO3 language ID.
     *
     * @param string        $sPage      TYPO3 page
     * @param int|string    $sLang      OXID language ID/Abbrev.
     *
     * @return string
     */
    public function getFullPageUrl ( $sPage = null, $sLang = null )
    {
        // Requesting the root page must be done by passing '/'
        if ( !$sPage ) {
            return false;
        }
        
        startProfile(__METHOD__);
        
        $oxConfig       = oxRegistry::getConfig();
        $blSsl          = $oxConfig->isSsl();
        
        $sBaseUrl       = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_BASE_URLS,          $sLang);
        $sBaseUrlSsl    = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_BASE_SSL_URLS,      $sLang);
        $sPagePath      = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_PAGE_PATHS,         $sLang);
        $sParams        = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_QUERY_PARAMETERS,   $sLang);
        
        // We don't know how the user input his parameters, so parse them to be sure
        $aParams = array();
        parse_str( $sParams, $aParams );
        $sParams = http_build_query( $aParams );
        
        $sFullPageUrl   =     ($sBaseUrlSsl && $blSsl ? $sBaseUrlSsl : $sBaseUrl)
                            . '/' . $sPagePath
                            . '/' . $this->sanitizePageTitle($sPage)
                            . '/?'
                            . $sParams
                        ;
        
        stopProfile(__METHOD__);
        
        return $this->sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Build full CMS URL for the passed page ID and OXID lang ID. The lang ID is mapped to the
     * corresponding CMS language ID.
     *
     * @param int           $sPageId    Page ID
     * @param int|string    $sLang      OXID language ID/Abbrev.
     *
     * @return string
     */
    public function getFullPageUrlById ( $sPageId, $sLang = null )
    {
        // This checks for empty values but also makes sure the passed page ID isn't just 0; in other words,
        // this checks for empty strings, null, false, etc.
        if ( empty($sPageId) && !is_numeric($sPageId) ) {
            return false;
        }
        
        startProfile(__METHOD__);
        
        $oxConfig       = oxRegistry::getConfig();
        $blSsl          = $oxConfig->isSsl();
        
        $sBaseUrl       = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_BASE_URLS,          $sLang);
        $sBaseUrlSsl    = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_BASE_SSL_URLS,      $sLang);
        $sIdParam       = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_ID_PARAMETERS,      $sLang);
        $sLangParam     = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_LANG_PARAMETERS,    $sLang);
        $sParams        = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_QUERY_PARAMETERS,   $sLang);
        
        // Parse the user-specified params and add the id and L parameters
        $aParams = array();
        parse_str( $sParams, $aParams );
        parse_str( $sLangParam, $aParams );
        $aParams = array_merge(
            array(
                $sIdParam   => (int)$sPageId,
            ),
            $aParams
        );
        $sParams = http_build_query( $aParams );
        
        $sFullPageUrl =  ($sBaseUrlSsl && $blSsl ? $sBaseUrlSsl : $sBaseUrl)
                            . '/?'
                            . $sParams
                        ;
        
        stopProfile(__METHOD__);
        
        return $this->sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Sanitize URLs prior to caching to prevent double caching of a page under different URLs.
     * This method gets called so often it has its own caching mechanism.
     *
     * @param string    $sUnsanitizedUrl    Page URL
     * 
     * @return string
     */
    public function sanitizeUrl ( $sUnsanitizedUrl)
    {
        startProfile(__METHOD__);
        
        $sSanitizedUrl = $this->getFromSessionCache('urls', $sUnsanitizedUrl);
        
        if ( null === $sSanitizedUrl ) {
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
            
            $this->saveToSessionCache('urls', $sUnsanitizedUrl, $sSanitizedUrl);
        }
        
        stopProfile(__METHOD__);
        
        return $sSanitizedUrl;
    }
    
    /**
     * Ensure that a page title can be used in a URL without causing problems
     *
     * @param string    $sUnsanitizedTitle      Title
     * 
     * @return string
     */
    public function sanitizePageTitle ( $sUnsanitizedTitle )
    {
        startProfile(__METHOD__);
        
        $sSanitizedTitle = $this->getFromSessionCache('title', $sUnsanitizedTitle);
        
        if ( null === $sSanitizedTitle ) {
            
            // Automatic encoding handling
            $oStr = getStr();
            
            $sTitle = $sUnsanitizedTitle;
            
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
                $sTitlePart = $oxSeoEncoder->encodeString($sTitlePart, true, $iLang);
                
                // Convert to lowercase
                $sTitlePart = $oStr->strtolower($sTitlePart);
                
                // Remove Tags
                $sTitlePart = strip_tags($sTitlePart);
                
                // Replace special characters with hyphen
                $sTitlePart = preg_replace('/[ \-+_]+/', '-', $sTitlePart);
                
                $aTitleParts[$i] = rawurlencode($sTitlePart);
            }
            
            $sTitle = implode('/', $aTitleParts);
            
            $sSanitizedTitle = $sTitle;
                
            $this->saveToSessionCache('title', $sUnsanitizedTitle, $sSanitizedTitle);
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
    public function getTextContentFromXmlObject ( $oXml )
    {
        startProfile(__METHOD__);
        
        $sText = '';
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty string.
        if ( $oXml !== false ) {
            $sText = trim( $oXml->asXML() );
            
            // var_dump(__METHOD__.':'.htmlentities($sText));
            
            // Remove CDATA tag
            $sText = $this->unwrapCDATA( $sText );
            
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
     * Returns an object containing the curl information array (->info) and, on success,
     * the page content (->content) for the requested URL.
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $blPost     Use POST instead of GET
     * 
     * @return object
     */
    public function fetchXmlSourceFromRemote ( $oPage, $blPost = false )
    {
        startProfile(__METHOD__);
        
        // We know nothing about caching here, so we always use the full URL.
        $sUrl = $oPage->getFullUrl();
        
        $oResult = new stdClass();
        $oResult->content   = '';
        $oResult->info      = array();
        
        if ( $sUrl ) {
            // var_dump('Connect: ', $this->getConfigValue(CmsxidUtils::CONFIG_KEY_CURL_CONNECT_TIMEOUT));
            // var_dump('Execute: ', $this->getConfigValue(CmsxidUtils::CONFIG_KEY_CURL_EXECUTE_TIMEOUT));
            
            $curl_handle = curl_init();
            curl_setopt( $curl_handle, CURLOPT_URL,             $sUrl );
            curl_setopt( $curl_handle, CURLOPT_FOLLOWLOCATION,  1 );
            curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER,  1 );
            
            curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER,  !$this->getConfigValue(CmsxidUtils::CONFIG_KEY_SSL_DONT_VERIFY_PEER) );
            
            // curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT,  2 );
            // curl_setopt( $curl_handle, CURLOPT_TIMEOUT,         1 );
            // curl_setopt( $curl_handle, CURLOPT_HEADER, 0 );
            
            // For POST
            if ( $blPost ) {
                curl_setopt( $curl_handle, CURLOPT_POST, $blPost );
                
                curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/x-www-form-urlencoded'
                ) );
                // curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, 
                    // 'code_url=' . urlencode($sTmpFileUrl)
                    // . '&output_info=compiled_code'
                    // . '&compilation_level=SIMPLE_OPTIMIZATIONS'
                    // . '&output_format=text'
                // );
            }
            
            $oResult->content   = curl_exec( $curl_handle );
            $oResult->info      = curl_getinfo( $curl_handle );

            // var_dump("<pre>errno: ", curl_error($curl_handle), "</pre>");
            
            curl_close( $curl_handle );
            
            // var_dump("<pre>info: ", $oResult->info, "</pre>");
            
            if ( $oResult->info['http_code'] != 200 ) {
                $oResult->content = '';
            }
        }
        
        stopProfile(__METHOD__);
        
        return $oResult;
    }
    
    /**
     * Returns the cached result object for the page URL $sUrl, or false
     *
     * @param string    $sUrl       URL
     * 
     * @return object
     */
    public function getXmlSourceFromCache ( $sUrl )
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->getCacheFilenameFromUrl($sUrl);
        
        $oResult = oxRegistry::get('oxUtils')->fromFileCache( $sCacheName );
        
        if ( $oResult  ) {
            return $oResult;
        }
        
        stopProfile(__METHOD__);
        
        return false;
    }
    
    /**
     * Saves the passed XML source for the passed URL to cache
     *
     * @param object        $oResult    Result object
     * @param string        $sUrl       URL
     * @param int           $iTtl       Cache TTL
     * 
     * @return bool
     */
    public function saveXmlSourceToCache ( $oResult, $sUrl, $iTtl = null )
    {
        startProfile(__METHOD__);
        
        $sCacheName = $this->getCacheFilenameFromUrl($sUrl);
        
        // Figure out cache TTL
        if ( null === $iTtl ) {
            if ( !($iTtl = $this->getConfigValue(CmsxidUtils::CONFIG_KEY_TTL_DEFAULT)) ) {
                $iTtl = 600;
            }
            if ( !($iTtlRnd = $this->getConfigValue(CmsxidUtils::CONFIG_KEY_TTL_DEFAULT_RND)) ) {
                $iTtlRnd = 10;
            }
        }
        
        // Randomize by $iCacheRandomize percentage
        $iTtl = mt_rand( floor( $iTtl * (1 - $iTtlRnd/100) ), ceil( $iTtl * (1 + $iTtlRnd/100) ) );
        
        $blSuccess = oxRegistry::get('oxUtils')->toFileCache( $sCacheName, $oResult, (int)$iTtl );
        
        stopProfile(__METHOD__);
        
        return $blSuccess;
    }
    
    /**
     * Removes named HTML entities from the passed XML string
     *
     * @param string    $sUrl       Page URL
     * 
     * @return string
     */
    public function getCacheFilenameFromUrl ( $sUrl )
    {
        $oStr = getStr();
        
        // Strip extra slashes etc.
        $sUrl = $this->sanitizeUrl( $sUrl );
        
        // Only keep word characters
        $sUrl = $oStr->preg_replace( '/[^a-zA-Z0-9-]/', '_', $sUrl );
        
        // Cut off at 200 chars to prevent problems with filename length
        // Append checksum of _FULL_ URL for safety
        return 'cmsxid_' . substr( $sUrl, 0, 200 ) . '_' . substr( md5($sUrl), 0, 12 );
    }
    
    /**
     * Checks if the passed page is the page called implicitly on the CMSxid SEO page
     *
     * @param CmsxidPage    $oPage      Page object
     * 
     * @return bool
     */
    public function checkIsImplicitSeoPage ( $oPage )
    {
        // Only path-based pages can be SEO pages at all
        // Mainly this check ensures the call to getPagePath() below does not cause a fatal error
        if ( $oPage->getType() != $this->TYPE_IDENTIFIER_PATH ) {
            return false;
        }
        
        // Since oPage's page path is set by a front-end function which, in turn,
        // calls CmsxidUtils::getCurrentSeoPage() when the requested page is null
        // this will return true when we are on an implicit seo page
        if ( $oPage->getPagePath() != $this->getCurrentSeoPage() ) {
            return false;
        }
        
        
        return true;
    }
    
    /**
     * Returns an array of explicit query parameters, i.e. parameters that could have been
     * passed by CMS plugins. Excludes all implicit parameters set by OXID when it resolves
     * SEO URLs and excludes well-known OXID parameters (see top of file)
     *
     * @return array()
     */
    public function getExplicitQueryParams ()
    {
        // These are the explicit query params, not the one OXID set after looking up the SEO
        // URL
        $aExplicitQueryParams = array();
        parse_str( $_SERVER['QUERY_STRING'], $aExplicitQueryParams );
        
        // Remove parameters specified in the static blacklist
        foreach ( self::$_aRequestParamBlacklist as $sBlacklistedParam ) {
            unset( $aExplicitQueryParams[$sBlacklistedParam] );
        }
        
        return $aExplicitQueryParams;
    }
    
    /**
     * Removes named HTML entities from the passed XML string
     *
     * @param string    $sXml       XML source string
     * 
     * @return string
     */
    public function fixXmlSourceEntities ( $sXml )
    {
        startProfile(__METHOD__);
        
        $sXml = str_replace('&nbsp;', '&#160;', $sXml);
        
        // Fix stray ampersands, lit. '& not followed by word characters and a semicolon will be replaced with &amp;'
        $sXml = preg_replace( '/&(?![\w#]+;)/', '&amp;', $sXml );

        stopProfile(__METHOD__);
        
        return $sXml;
    }
    
    /**
     * Loads the passed XML source with the SimpleXML loader and returns the generated object
     *
     * @param string    $sXml       XML source string
     * 
     * @return string
     */
    public function getXmlObjectFromSource ( $sXml )
    {
        startProfile(__METHOD__);
        
        $oXml = false;
        
        try {
            // $oXml = simplexml_load_string($sXml, null, LIBXML_NOCDATA);
            $oXml = simplexml_load_string($sXml);
        } catch ( Exception $e ) {
            $oXml = false;
            // return false;
        }
        
        if ( $oXml !== false ) {
            //return $oReturnXml->children();
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
    public function unwrapCDATA( $sXml )
    {
        startProfile(__METHOD__);
        
        $oStr = getStr();
        
        // Remove CDATA tag
        $sXml = $oStr->preg_replace( '/<!\[CDATA\[(.*?)\]\]>/ms', '\\1', $sXml );
        
        stopProfile(__METHOD__);
        
        return $sXml;
    }
    
    /**
     * Returns the contents of the sCmsxidPage config param which, if we are on an SEO-loaded page, should
     * contain the page to load through CMSxid
     *
     * @return string
     */
    public function getCurrentSeoPage ()
    {
        return oxRegistry::getConfig()->getConfigParam( 'sCmsxidPage' );
    }
    
    /**
     * Checks if the passed URL refers to any known (configured) CMS source. If so, return an
     * array with that information
     * 
     * @param string        $sSeoUrl        SEO URL to check. Per OXID convention this does not include a domain name
     * 
     * @return array
     */
    public function getPageSeoInfoByUrl ( $sSeoUrl )
    {
        startProfile(__METHOD__);
        
        $oxConfig   = oxRegistry::getConfig();
        $oxLang     = oxRegistry::getLang();
        $oUtils     = CmsxidUtils::getInstance();
        
        $aSeoInfo = false;
        
        foreach ( $oxLang->getLanguageArray() as $oLang ) {
            $sSeoIdent = $oUtils->getLangConfigValue(CmsxidUtils::CONFIG_KEY_SEO_IDENTIFIERS, $oLang->id);
            
            // Either starts with SEO identifier and a slash (subpage of CMS is called)
            // or is just the plain SEO identifier
            if ( strpos($sSeoUrl, $sSeoIdent . '/') === 0 || $sSeoUrl == $sSeoIdent ) {
                $sPage =    ($sSeoUrl == $sSeoIdent)
                                ? ''
                                : str_replace( $sSeoIdent . '/', '', $sSeoUrl )
                            ;
                
                $aSeoInfo = array(
                    'lang'          => $oLang->abbr,
                    'cl'            => 'cmsxid_fe',
                    'page'          => $sPage,
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
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public function rewriteContentUrls ( $sContent )
    {
        startProfile(__METHOD__);
        
        $oxConfig   = oxRegistry::getConfig();
        $oxLang     = oxRegistry::getLang();
        
        if ( $this->getConfigValue(CmsxidUtils::CONFIG_KEY_DONT_REWRITE_URLS) == true ) {
            stopProfile(__METHOD__);
            
            return $sContent;
        }
        
        foreach ( $oxLang->getLanguageArray() as $oLang ) {
            $sSourceUrl     = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_BASE_URLS);
            $sSourceSslUrl  = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_BASE_SSL_URLS);
            
            // No configured URLs - skip this language
            if ( !$sSourceUrl && !$sSourceSslUrl ) {
                continue;
            }
            
            // No matter what URLs the CMS returns, the URLs schema needs to be rewritten to the current shop's schema
            foreach ( array($sSourceUrl, $sSourceSslUrl) as $sSourceBaseUrl ) {
                $sSourcePagePath    = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_PAGE_PATHS, $oLang->id);
                $sFullBaseUrl       = $this->sanitizeUrl( $sSourceBaseUrl . '/' . $sSourcePagePath . '/' );
                
                // The target is defined by the current shop's SSL setting
                $sTargetBaseUrl     = $oxConfig->isSsl() ? $oxConfig->getSslShopUrl($sLang) : $oxConfig->getShopUrl($sLang);
                $sTargetSeoIdent    = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_SEO_IDENTIFIERS, $oLang->id);
                $sFullTargetUrl     = $this->sanitizeUrl( $sTargetBaseUrl . '/' . $sTargetSeoIdent . '/' );
                
                // Replace all links
                unset($aMatches);
                $sLinkPattern = '/href=(\'|")' . preg_quote($sFullBaseUrl, '/') . '[^"\']*(.|\/|\.html|\.php|\.asp)(\?[^"\']*)?(\'|")/';
                preg_match_all( $sLinkPattern, $sContent, $aMatches, PREG_SET_ORDER );
                
                foreach ( $aMatches as $aMatch ) {
                    $sContent = str_replace( $aMatch[0], str_replace($sFullBaseUrl, $sFullTargetUrl, $aMatch[0]), $sContent );
                }
            }
                
            // If the shop is in SSL mode, replace all links and sources to the non-SSL CMS with references to the SSL source,
            // if configured, to prevent browser complaints about mixed modes
            if ( $oxConfig->isSsl() ) {
                // Do this ONLY if an SSL source URL has actually been configured
                if ( $sSourceSslUrl ) {
                    // We can safely do this crude replace since, in theory, all URLs left on the page should be to
                    // non-page CMS content
                    $sSourceBaseUrl     = $this->sanitizeUrl( $sSourceUrl . '/' );
                    $sSourceSslBaseUrl  = $this->sanitizeUrl( $sSourceSslUrl . '/' );
                
                    $sContent = str_replace( $sSourceBaseUrl, $sSourceSslBaseUrl, $sContent );
                }
            }
        }
        
        stopProfile(__METHOD__);

        return $sContent;
    }
    
    /**
     * Fixes the passed content's encoding to match that of the shop
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public function fixContentEncoding ( $sContent )
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
    public function decodeContentEntities ( $sContent )
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
    public function parseContentThroughSmarty ( $sContent )
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
     * Configuration: fetch default cache TTL value
     * 
     * @return int
     */
    public function getConfiguredDefaultCacheTTL ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $oxConfig->getShopConfVar('iCmsxidTtlDefault', $oxConfig->getShopId(), 'module:cmsxid');
    }
    
    /**
     * Configuration: fetch default cache TTL randomization value
     * 
     * @return int
     */
    public function getConfiguredDefaultCacheTTLRandomization ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $oxConfig->getShopConfVar('iCmsxidTtlDefaultRnd', $oxConfig->getShopId(), 'module:cmsxid');
    }
    
    /**
     * Configuration: fetch "no url rewriting" value
     * 
     * @return bool
     */
    public function getConfiguredNoUrlRewriteSetting ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $oxConfig->getShopConfVar('blCmsxidLeaveUrls', $oxConfig->getShopId(), 'module:cmsxid');
    }
    
    /**
     * Configuration: return configured cURL connect timeout and default to 1
     * 
     * @return int
     */
    public function getConfiguredCurlConnectTimeout ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $mVal = $oxConfig->getShopConfVar('iCmsxidCurlConnectTimeout', $oxConfig->getShopId(), 'module:cmsxid');
        
        return $mVal ?: 1;
    }
    
    /**
     * Configuration: return configured cURL fetch timeout and default to 3
     * 
     * @return int
     */
    public function getConfiguredCurlExecuteTimeout ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $mVal = $oxConfig->getShopConfVar('iCmsxidCurlExecuteTimeout', $oxConfig->getShopId(), 'module:cmsxid');
        
        return $mVal ?: 3;
    }
    
    /**
     * Configuration: return the configuration value for blCmsxidEnableDummyContent
     * 
     * @return bool
     */
    public function getConfiguredDummyContentValue ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $mVal = $oxConfig->getShopConfVar('blCmsxidEnableDummyContent', $oxConfig->getShopId(), 'module:cmsxid');
        
        return $mVal ?: false;
    }
    
    /**
     * Configuration: return the configuration value for blCmsxidEnableDummyContent
     * 
     * @return bool
     */
    public function getConfiguredSslVerifyPeerValue ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        $mVal = $oxConfig->getShopConfVar('blCmsxidSslDontVerifyPeer', $oxConfig->getShopId(), 'module:cmsxid');
        
        return !(bool)$mVal;
    }
    
    /**
     * Configuration: returns an array of all configured sources by language ID and language abbreviation as object
     * 
     * @return object[]
     */
    public function getConfiguredSources ()
    {
        startProfile(__METHOD__);
        
        if ( null === self::$_aConfiguredSources ) {
            $oxLang     = oxRegistry::getLang();
            $oxConfig   = oxRegistry::getConfig();
            $iShop      = $oxConfig->getShopId();
            
            $aCmsxidBaseUrls    = $oxConfig->getShopConfVar('aCmsxidBaseUrls',      $iShop, 'module:cmsxid');
            $aCmsxidBaseSslUrls = $oxConfig->getShopConfVar('aCmsxidBaseSslUrls',   $iShop, 'module:cmsxid');
            $aCmsxidPagePaths   = $oxConfig->getShopConfVar('aCmsxidPagePaths',     $iShop, 'module:cmsxid');
            $aCmsxidParams      = $oxConfig->getShopConfVar('aCmsxidParams',        $iShop, 'module:cmsxid');
            $aCmsxidIdParams    = $oxConfig->getShopConfVar('aCmsxidIdParams',      $iShop, 'module:cmsxid');
            $aCmsxidLangParams  = $oxConfig->getShopConfVar('aCmsxidLangParams',    $iShop, 'module:cmsxid');
            $aCmsxidSeoIdents   = $oxConfig->getShopConfVar('aCmsxidSeoIdents',     $iShop, 'module:cmsxid');
            
            $aLanguages = $oxLang->getLanguageArray();
            
            $aSources   = array();
            
            foreach ( $aLanguages as $oLang ) {
                $iLang = $oLang->id;
                
                // No URLs configured; invalid source, ignore
                if ( !$aCmsxidBaseUrls[$iLang] && !$aCmsxidBaseSslUrls[$iLang] ) {
                    continue;
                }
                
                $o = new stdClass();
                
                $o->sBaseUrl    = $aCmsxidBaseUrls[$iLang];
                $o->sBaseUrlSsl = $aCmsxidBaseSslUrls[$iLang];
                $o->sPagePath   = $aCmsxidPagePaths[$iLang];
                $o->sParams     = $aCmsxidParams[$iLang];
                $o->sIdParam    = $aCmsxidIdParams[$iLang];
                $o->sLangParam  = $aCmsxidLangParams[$iLang];
                $o->sSeoIdent   = $aCmsxidSeoIdents[$iLang];
                
                $aSources[$iLang]       = clone $o;
                $aSources[$oLang->abbr] = clone $o;
            }
            
            self::$_aConfiguredSources = $aSources;
        
            // echo "<pre>";var_dump(self::$_aConfiguredSources);echo "</pre>";
        }
        
        stopProfile(__METHOD__);
        
        return self::$_aConfiguredSources;
    }
    
    /**
     * Configuration: helper function
     * 
     * @param string        $sLang          Language to return the source property for
     * @param string        $sProperty      Property to return for the source
     *
     * @return mixed
     */
    public function getConfiguredSourceProperty ( $sLang, $sProperty )
    {
        if ( $sLang === null ) {
            $sLang = oxRegistry::getLang()->getBaseLanguage();
        }
        
        $aSources = $this->getConfiguredSources();
        
        if ( !array_key_exists($sLang, $aSources) ) {
            return false;
        } else {
            return $aSources[$sLang]->{$sProperty};
        }
    }
    
    /**
     * Configuration: return source base url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the property for
     * 
     * @return string
     */
    public function getConfiguredSourceBaseUrl ( $sLang )
    {
        $sBaseUrl = $this->getConfiguredSourceProperty( $sLang, 'sBaseUrl' );
        $sBaseUrl = $this->sanitizeUrl( $sBaseUrl );
        
        return $sBaseUrl;
    }
    
    /**
     * Configuration: return source ssl base url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the property for
     * 
     * @return string
     */
    public function getConfiguredSourceSslBaseUrl ( $sLang )
    {
        $sBaseUrlSsl = $this->getConfiguredSourceProperty( $sLang, 'sBaseUrlSsl' );
        $sBaseUrlSsl = $this->sanitizeUrl( $sBaseUrlSsl );
        
        return $sBaseUrlSsl;
    }
    
    /**
     * Configuration: return page path for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public function getConfiguredSourcePagePath ( $sLang )
    {
        return $this->getConfiguredSourceProperty( $sLang, 'sPagePath' );
    }
    
    /**
     * Configuration: return source parameter string for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public function getConfiguredSourceParams ( $sLang )
    {
        return $this->getConfiguredSourceProperty( $sLang, 'sParams' );
    }
    
    /**
     * Configuration: return source CMS ID query parameter name for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public function getConfiguredSourceIdParam ( $sLang )
    {
        return $this->getConfiguredSourceProperty( $sLang, 'sIdParam' );
    }
    
    /**
     * Configuration: return source CMS language ID for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public function getConfiguredSourceLangParam ( $sLang )
    {
        return $this->getConfiguredSourceProperty( $sLang, 'sLangParam' );
    }
    
    /**
     * Configuration: return source url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public function getConfiguredSourceSeoIdentifier ( $sLang )
    {
        return $this->getConfiguredSourceProperty( $sLang, 'sSeoIdent' );
    }
    
    /**
     * Returns extra GET parameters that have been passed to the page call. The idea being
     * that (e.g.) TYPO3 plugins that rely on GET parameters being passed along the request
     * will still work when being surfed via OXID
     * 
     * @return array
     */
    public function getPassedHttpGetParameters ()
    {
        // $_GET is extended by OXID with the parameters that "should" be in it
        // when parsing the SEO url; it's thus unreliable.
        
        // The QUERY_STRING server variable contains the _original_ query string
        $aGet = array();
        parse_str( $_SERVER['QUERY_STRING'], $aGet );
        
        return $aGet;
    }
    
    /**
     * Returns extra POST parameters that have been passed to the page call. The idea being
     * that (e.g.) TYPO3 plugins that rely on POST parameters being passed along the request
     * will still work when being surfed via OXID
     * 
     * @return array
     */
    public function getPassedHttpPostParameters ()
    {
        return $_POST;
    }
    
    /**
     * Returns extra GET parameters after having unset the configured id / language parameters
     * for the passed language
     * 
     * @param string        $sLang          Language to sanitize the parameter array for
     * 
     * @return array
     */
    public function getSanitizedPassedHttpGetParameters ( $sLang )
    {
        $aParams = $this->getPassedHttpGetParameters();
        $aParams = $this->sanitizeHttpParameterArray( $aParams, $sLang );
        
        return $aParams;
    }
    
    /**
     * Returns extra POST parameters after having unset the configured id / language parameters
     * for the passed language
     * 
     * @return array
     */
    // public function getSanitizedPassedHttpPostParameters ( $sLang )
    // {
        // $aParams = $this->getPassedHttpPostParameters();
        // $aParams = $this->sanitizeHttpParameterArray( $aParams, $sLang );
        
        // return $aParams;
    // }
    
    /**
     * Unsets the configured id / language parameters for the passed language in the passed parameter array
     * 
     * @param array         $aParams        Associative HTTP parameter array
     * @param string        $sLang          Language to sanitize the parameter array for
     * 
     * @return array
     */
    public function sanitizeHttpParameterArray ( $aParams, $sLang )
    {
        // Remove parameters specified in the static blacklist
        foreach ( self::$_aRequestParamBlacklist as $sBlacklistedParam ) {
            unset( $aParams[$sBlacklistedParam] );
        }      
        
        $sIdParam = $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_ID_PARAMETERS, $sLang);
        unset( $aParams[$sIdParam] );
        
        // Obtain the name of the language parameter so we can unset it
        $aLangParam = array();
        parse_str( $this->getLangConfigValue(CmsxidUtils::CONFIG_KEY_LANG_PARAMETERS, $sLang), $aLangParam );
        $sLangParam = implode( '', array_keys($aLangParam) );
        unset( $aParams[$sLangParam] );
        
        return $aParams;
    }
    
    /**
     * Returns the result object associated with a URL
     *
     * @param string    $sUrl       Full URL of the page
     * 
     * @return CmsxidResult
     */
    public function getResultFromSessionCache ( $sUrl )
    {
        return $this->getFromSessionCache('results', $sUrl);
    }
    
    /**
     * Saves a result object to session cache using its (hashed) URL as key
     *
     * @param string        $sUrl           Full URL of the page
     * @param CmsxidResult  $oResult        Result object
     * 
     * @return void
     */
    public function saveResultToSessionCache ( $sUrl, $oResult )
    {
        $this->saveToSessionCache('results', $sUrl, $oResult);
    }
    
    /**
     * Generic session cache read method.
     *
     * @param string        $sGroup         Group identifier
     * @param string        $sUnhashedKey   Unhashed key, will be hashed by MD5
     * 
     * @return CmsxidResult
     */
    public function getFromSessionCache ( $sGroup, $sUnhashedKey )
    {
        $this->_initializeSessionCache($sGroup);
        
        $sKey = md5($sUnhashedKey);
        
        if ( array_key_exists($sKey, self::$_aSessionCache[$sGroup]) ) {
            return self::$_aSessionCache[$sGroup][$sKey];
        }
        
        return null;
    }
    
    /**
     * Generic session cache write method.
     *
     * @param string        $sGroup         Group identifier
     * @param string        $sUnhashedKey   Unhashed key, will be hashed by MD5
     * @param string        $sValue         Value to save
     * 
     * @return void
     */
    public function saveToSessionCache ( $sGroup, $sUnhashedKey, $sValue )
    {
        $this->_initializeSessionCache($sGroup);
        
        $sKey = md5($sUnhashedKey);
        
        self::$_aSessionCache[$sGroup][$sKey] = $sValue;
    }
    
    /**
     * Initialize the session cache
     * 
     * @param string        $sGroup         Group identifier
     *
     * @return void
     */
    protected function _initializeSessionCache ($sGroup)
    {
        if ( !is_array(self::$_aSessionCache) ) {
            self::$_aSessionCache = array();
        }
        
        if ( !array_key_exists($sGroup, self::$_aSessionCache) ) {
            self::$_aSessionCache[$sGroup] = array();
        }
    }
    
    /**
     * Return a dummy string with a few page infos for debug purposes
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sSnippet   Snippet name
     * 
     * @return string
     */
    public function getDummyString ($oPage, $sSnippet)
    {
        return '<span class="cmsxid-dummy">Cmsxid dummy content for URL: ' . $oPage->getFullUrl() . ', argument: ' . $sSnippet . '</span>';
    }
}