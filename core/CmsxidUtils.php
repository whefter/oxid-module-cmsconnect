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
     * Build full TYPO3 URL for the passed page and OXID lang ID. The lang ID is mapped to the
     * corresponding TYPO3 language ID.
     *
     * @param string        $sPage      TYPO3 page
     * @param int|string    $sLang      OXID language ID/Abbrev.
     *
     * @return string
     */
    public static function getFullPageUrl ( $sPage = null, $sLang = null )
    {
        // Requesting the root page must be done by passing '/'
        if ( !$sPage ) {
            return false;
        }
        
        $oxConfig       = oxRegistry::getConfig();
        $blSsl          = $oxConfig->isSsl();
        
        $sBaseUrl       = self::getConfiguredSourceBaseUrl($sLang);
        $sBaseUrlSsl    = self::getConfiguredSourceSslBaseUrl($sLang);
        $sPagePath      = self::getConfiguredSourcePagePath($sLang);
        $sParams        = self::getConfiguredSourceParams($sLang);
        
        // We don't know how the user input his parameters, so parse them to be sure
        $aParams = array();
        parse_str( $sParams, $aParams );
        $sParams = http_build_query( $aParams );
        
        $sFullPageUrl   =     ($sBaseUrlSsl && $blSsl ? $sBaseUrlSsl : $sBaseUrl)
                            . '/' . $sPagePath
                            . '/' . self::sanitizePageTitle($sPage)
                            . '/?'
                            . $sParams
                        ;
        
        return self::sanitizeUrl($sFullPageUrl);
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
    public static function getFullPageUrlById ( $sPageId, $sLang = null )
    {
        // This checks for empty values but also makes sure the passed page ID isn't just 0; in other words,
        // this checks for empty strings, null, false, etc.
        if ( empty($sPageId) && !is_numeric($sPageId) ) {
            return false;
        }
        
        $oxConfig       = oxRegistry::getConfig();
        $blSsl          = $oxConfig->isSsl();
        
        $sBaseUrl       = self::getConfiguredSourceBaseUrl($sLang);
        $sBaseUrlSsl    = self::getConfiguredSourceSslBaseUrl($sLang);
        $sIdParam       = self::getConfiguredSourceIdParam($sLang);
        $sLangParam     = self::getConfiguredSourceLangParam($sLang);
        $sParams        = self::getConfiguredSourceParams($sLang);
        
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
        
        return self::sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Sanitize URLs prior to caching to prevent double caching of a page under different URLs
     *
     * @param string    $sUrl       Page URL
     * 
     * @return string
     */
    public static function sanitizeUrl ( $sUrl )
    {
        // Automatic encoding handling
        $oStr = getStr();
        
        // Replace multiple slashes, except for the protocol part
        $sUrl = $oStr->preg_replace( '/(?<!:)\/+/', '/', $sUrl);
        
        // Put through PHP's functions to ensure standardized URL
        // $sUrl = http_build_url( parse_url( $sUrl ) );
        
        // Remove ending '&' or '?'
        // Do this without regular expressions (which are expensive)
        $sUrl = rtrim( $sUrl, '&?' );
        
        return $sUrl;
    }
    
    /**
     * Ensure that a page title can be used in a URL without causing problems
     *
     * @param string    $sTitle     Title
     * 
     * @return string
     */
    public static function sanitizePageTitle ( $sTitle )
    {
        // Automatic encoding handling
        $oStr = getStr();
        
        // Strip leading slashes
        $sTitle = $oStr->preg_replace( '/^\/+/', '', $sTitle);
        
        // Replace multiple instances of slashes with a single one, but make sure there is an ending slash
        $sTitle = $oStr->preg_replace( '/\/+/', '/', $sTitle . '/');
        
        // Slashes should be left intact
        $sTitle = $oStr->preg_replace('/\/+/', '/', $sTitle);
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

        
        return $sTitle;
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
        $sText = '';
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty string.
        if ( $oXml !== false ) {
            $sText = trim( $oXml->asXML() );
            
            // var_dump(__METHOD__.':'.htmlentities($sText));
            
            // Remove CDATA tag
            $sText = self::unwrapCDATA( $sText );
            
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
    public static function fetchXmlSourceFromRemote ( $oPage, $blPost = false )
    {
        // We know nothing about caching here, so we always use the full URL.
        $sUrl = $oPage->getFullUrl();
        
        $oResult = new stdClass();
        $oResult->content   = '';
        $oResult->info      = array();
        
        if ( $sUrl ) {
            // var_dump('Connect: ', self::getConfiguredCurlConnectTimeout());
            // var_dump('Execute: ', self::getConfiguredCurlExecuteTimeout());
            
            $curl_handle = curl_init();
            curl_setopt( $curl_handle, CURLOPT_URL,             $sUrl );
            curl_setopt( $curl_handle, CURLOPT_FOLLOWLOCATION,  1 );
            curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER,  1 );
            
            curl_setopt( $curl_handle, CURLOPT_SSL_VERIFYPEER,  self::getConfiguredSslVerifyPeerValue() );
            
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
        
        return $oResult;
    }
    
    /**
     * Returns the cached result object for the page URL $sUrl, or false
     *
     * @param string    $sUrl       URL
     * 
     * @return object
     */
    public static function getXmlSourceFromCache ( $sUrl )
    {
        $sCacheName = self::getCacheFilenameFromUrl($sUrl);
        
        $oResult = oxRegistry::get('oxUtils')->fromFileCache( $sCacheName );
        
        if ( $oResult  ) {
            return $oResult;
        }
        
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
    public static function saveXmlSourceToCache ( $oResult, $sUrl, $iTtl = null )
    {
        $sCacheName = self::getCacheFilenameFromUrl($sUrl);
        
        // Figure out cache TTL
        if ( null === $iTtl ) {
            if ( !($iTtl = self::getConfiguredDefaultCacheTTL()) ) {
                $iTtl = 600;
            }
            if ( !($iTtlRnd = self::getConfiguredDefaultCacheTTLRandomization()) ) {
                $iTtlRnd = 10;
            }
        }
        
        // Randomize by $iCacheRandomize percentage
        $iTtl = mt_rand( floor( $iTtl * (1 - $iTtlRnd/100) ), ceil( $iTtl * (1 + $iTtlRnd/100) ) );
        
        return oxRegistry::get('oxUtils')->toFileCache( $sCacheName, $oResult, (int)$iTtl );
    }
    
    /**
     * Removes named HTML entities from the passed XML string
     *
     * @param string    $sUrl       Page URL
     * 
     * @return string
     */
    public static function getCacheFilenameFromUrl ( $sUrl )
    {
        $oStr = getStr();
        
        // Strip extra slashes etc.
        $sUrl = self::sanitizeUrl( $sUrl );
        
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
    public static function checkIsImplicitSeoPage ( $oPage )
    {
        // Only path-based pages can be SEO pages at all
        // Mainly this check ensures the call to getPagePath() below does not cause a fatal error
        if ( $oPage->getType() != self::TYPE_IDENTIFIER_PATH ) {
            return false;
        }
        
        // Since oPage's page path is set by a front-end function which, in turn,
        // calls CmsxidUtils::getCurrentSeoPage() when the requested page is null
        // this will return true when we are on an implicit seo page
        if ( $oPage->getPagePath() != self::getCurrentSeoPage() ) {
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
    public static function getExplicitQueryParams ()
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
    public static function fixXmlSourceEntities ( $sXml )
    {
        $sXml = str_replace('&nbsp;', '&#160;', $sXml);
        
        // Fix stray ampersands, lit. '& not followed by word characters and a semicolon will be replaced with &amp;'
        $sXml = preg_replace( '/&(?![\w#]+;)/', '&amp;', $sXml );
        
        return $sXml;
    }
    
    /**
     * Loads the passed XML source with the SimpleXML loader and returns the generated object
     *
     * @param string    $sXml       XML source string
     * 
     * @return string
     */
    public static function getXmlObjectFromSource ( $sXml )
    {
        $oXml = false;
        
        try {
            // $oXml = simplexml_load_string($sXml, null, LIBXML_NOCDATA);
            $oXml = simplexml_load_string($sXml);
        } catch ( Exception $e ) {
            return false;
        }
        
        if ( $oXml !== false ) {
            //return $oReturnXml->children();
        }
        
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
        $oStr = getStr();
        
        // Remove CDATA tag
        $sXml = $oStr->preg_replace( '/<!\[CDATA\[(.*?)\]\]>/ms', '\\1', $sXml );
        
        return $sXml;
    }
    
    /**
     * Returns the contents of the sCmsxidPage config param which, if we are on an SEO-loaded page, should
     * contain the page to load through CMSxid
     *
     * @return string
     */
    public static function getCurrentSeoPage ()
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
    public static function getPageSeoInfoByUrl ( $sSeoUrl )
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aSources = CmsxidUtils::getConfiguredSources();
        
        $aSeoInfo = false;
        
        foreach ( $aSources as $sLang => $oSource ) {
            // We only want actual numeric language IDs at this point
            if ( !is_numeric($sLang) ) {
                continue;
            }
            
            $sSeoIdent  = CmsxidUtils::getConfiguredSourceSeoIdentifier($sLang);
            
            // Either starts with SEO identifier and a slash (subpage of CMS is called)
            // or is just the plain SEO identifier
            if ( strpos($sSeoUrl, $sSeoIdent . '/') === 0 || $sSeoUrl == $sSeoIdent ) {
                $sPage =    ($sSeoUrl == $sSeoIdent)
                                ? ''
                                : str_replace( $sSeoIdent . '/', '', $sSeoUrl )
                            ;
                
                $aSeoInfo = array(
                    'lang'          => $sLang,
                    'cl'            => 'cmsxid_fe',
                    'page'          => $sPage,
                );
                
                break;
            }
        }
        
        return $aSeoInfo;
    }
    
    /**
     * Rewrites all CMS-related URLs in the passed content to point to the shop
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public static function rewriteContentUrls ( $sContent )
    {
        $oxConfig = oxRegistry::getConfig();
        
        if ( self::getConfiguredNoUrlRewriteSetting() == true ) {
            return $sContent;
        }
        
        // We want to replace URLs for all configured sources
        $aLanguages = array_keys( self::getConfiguredSources() );

        foreach ( $aLanguages as $sLang ) {
            // Deal only with numeric language IDs, since all OXID functions only take those
            if ( !is_numeric($sLang) ) {
                continue;
            }
            
            // No matter what URLs the CMS returns, the URLs schema needs to be rewritten to the current shop's schema
            foreach ( array(self::getConfiguredSourceBaseUrl($sLang), self::getConfiguredSourceSslBaseUrl($sLang)) as $sSourceBaseUrl ) {
                $sSourcePagePath    = self::getConfiguredSourcePagePath($sLang);
                $sFullBaseUrl       = self::sanitizeUrl( $sSourceBaseUrl . '/' . $sSourcePagePath . '/' );
                
                // The target is defined by the current shop's SSL setting
                $sTargetBaseUrl     = $oxConfig->isSsl() ? $oxConfig->getSslShopUrl($sLang) : $oxConfig->getShopUrl($sLang);
                $sTargetSeoIdent    = self::getConfiguredSourceSeoIdentifier( $sLang );
                $sFullTargetUrl     = self::sanitizeUrl( $sTargetBaseUrl . '/' . $sTargetSeoIdent . '/' );
                
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
                if ( self::getConfiguredSourceSslBaseUrl($sLang) ) {
                    // We can safely do this crude replace since, in theory, all URLs left on the page should be to
                    // non-page CMS content
                    $sSourceBaseUrl     = self::sanitizeUrl( self::getConfiguredSourceBaseUrl($sLang) . '/' );
                    $sSourceSslBaseUrl  = self::sanitizeUrl( self::getConfiguredSourceSslBaseUrl($sLang) . '/' );
                
                    $sContent = str_replace( $sSourceBaseUrl, $sSourceSslBaseUrl, $sContent );
                }
            }
        }

        return $sContent;
    }
    
    /**
     * Fixes the passed content's encoding to match that of the shop
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    public static function fixContentEncoding ( $sContent )
    {
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
    public static function decodeContentEntities ( $sContent )
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
    public static function parseContentThroughSmarty ( $sContent )
    {
        $oxUtilsView = oxRegistry::get('oxUtilsView');
        
        $sContent = $oxUtilsView->parseThroughSmarty(
            $sContent,
            // Identifier
            md5($sContent),
            
            null,
            true
        );
        
        return $sContent;
    }
    
    /**
     * Configuration: fetch default cache TTL value
     * 
     * @return int
     */
    public static function getConfiguredDefaultCacheTTL ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $oxConfig->getShopConfVar('iCmsxidTtlDefault', $oxConfig->getShopId(), 'module:cmsxid');
    }
    
    /**
     * Configuration: fetch default cache TTL randomization value
     * 
     * @return int
     */
    public static function getConfiguredDefaultCacheTTLRandomization ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $oxConfig->getShopConfVar('iCmsxidTtlDefaultRnd', $oxConfig->getShopId(), 'module:cmsxid');
    }
    
    /**
     * Configuration: fetch "no url rewriting" value
     * 
     * @return bool
     */
    public static function getConfiguredNoUrlRewriteSetting ()
    {
        $oxConfig = oxRegistry::getConfig();
        
        return $oxConfig->getShopConfVar('blCmsxidLeaveUrls', $oxConfig->getShopId(), 'module:cmsxid');
    }
    
    /**
     * Configuration: return configured cURL connect timeout and default to 1
     * 
     * @return int
     */
    public static function getConfiguredCurlConnectTimeout ()
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
    public static function getConfiguredCurlExecuteTimeout ()
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
    public static function getConfiguredDummyContentValue ()
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
    public static function getConfiguredSslVerifyPeerValue ()
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
    public static function getConfiguredSources ()
    {
        $oxLang     = oxRegistry::getLang();
        $oxConfig   = oxRegistry::getConfig();
        
        $aCmsxidBaseUrls    = $oxConfig->getShopConfVar('aCmsxidBaseUrls',      $oxConfig->getShopId(), 'module:cmsxid');
        $aCmsxidBaseSslUrls = $oxConfig->getShopConfVar('aCmsxidBaseSslUrls',   $oxConfig->getShopId(), 'module:cmsxid');
        $aCmsxidPagePaths   = $oxConfig->getShopConfVar('aCmsxidPagePaths',     $oxConfig->getShopId(), 'module:cmsxid');
        $aCmsxidParams      = $oxConfig->getShopConfVar('aCmsxidParams',        $oxConfig->getShopId(), 'module:cmsxid');
        $aCmsxidIdParams    = $oxConfig->getShopConfVar('aCmsxidIdParams',      $oxConfig->getShopId(), 'module:cmsxid');
        $aCmsxidLangParams  = $oxConfig->getShopConfVar('aCmsxidLangParams',    $oxConfig->getShopId(), 'module:cmsxid');
        $aCmsxidSeoIdents   = $oxConfig->getShopConfVar('aCmsxidSeoIdents',     $oxConfig->getShopId(), 'module:cmsxid');
        
        $aLanguages = $oxLang->getLanguageArray();
        $aSources = array();
        
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
        
        return $aSources;
    }
    
    /**
     * Configuration: helper function
     * 
     * @param string        $sLang          Language to return the source property for
     * @param string        $sProperty      Property to return for the source
     *
     * @return mixed
     */
    public static function getConfiguredSourceProperty ( $sLang, $sProperty )
    {
        if ( $sLang === null ) {
            $sLang = oxRegistry::getLang()->getBaseLanguage();
        }
        
        $aSources = self::getConfiguredSources();
        
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
    public static function getConfiguredSourceBaseUrl ( $sLang )
    {
        $sBaseUrl = self::getConfiguredSourceProperty( $sLang, 'sBaseUrl' );
        $sBaseUrl = self::sanitizeUrl( $sBaseUrl );
        
        return $sBaseUrl;
    }
    
    /**
     * Configuration: return source ssl base url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the property for
     * 
     * @return string
     */
    public static function getConfiguredSourceSslBaseUrl ( $sLang )
    {
        $sBaseUrlSsl = self::getConfiguredSourceProperty( $sLang, 'sBaseUrlSsl' );
        $sBaseUrlSsl = self::sanitizeUrl( $sBaseUrlSsl );
        
        return $sBaseUrlSsl;
    }
    
    /**
     * Configuration: return page path for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public static function getConfiguredSourcePagePath ( $sLang )
    {
        return self::getConfiguredSourceProperty( $sLang, 'sPagePath' );
    }
    
    /**
     * Configuration: return source parameter string for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public static function getConfiguredSourceParams ( $sLang )
    {
        return self::getConfiguredSourceProperty( $sLang, 'sParams' );
    }
    
    /**
     * Configuration: return source CMS ID query parameter name for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public static function getConfiguredSourceIdParam ( $sLang )
    {
        return self::getConfiguredSourceProperty( $sLang, 'sIdParam' );
    }
    
    /**
     * Configuration: return source CMS language ID for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public static function getConfiguredSourceLangParam ( $sLang )
    {
        return self::getConfiguredSourceProperty( $sLang, 'sLangParam' );
    }
    
    /**
     * Configuration: return source url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    public static function getConfiguredSourceSeoIdentifier ( $sLang )
    {
        return self::getConfiguredSourceProperty( $sLang, 'sSeoIdent' );
    }
    
    /**
     * Returns extra GET parameters that have been passed to the page call. The idea being
     * that (e.g.) TYPO3 plugins that rely on GET parameters being passed along the request
     * will still work when being surfed via OXID
     * 
     * @return array
     */
    public static function getPassedHttpGetParameters ()
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
    public static function getPassedHttpPostParameters ()
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
    public static function getSanitizedPassedHttpGetParameters ( $sLang )
    {
        $aParams = self::getPassedHttpGetParameters();
        $aParams = self::sanitizeHttpParameterArray( $aParams, $sLang );
        
        return $aParams;
    }
    
    /**
     * Returns extra POST parameters after having unset the configured id / language parameters
     * for the passed language
     * 
     * @return array
     */
    // public static function getSanitizedPassedHttpPostParameters ( $sLang )
    // {
        // $aParams = self::getPassedHttpPostParameters();
        // $aParams = self::sanitizeHttpParameterArray( $aParams, $sLang );
        
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
    public static function sanitizeHttpParameterArray ( $aParams, $sLang )
    {
        // Remove parameters specified in the static blacklist
        foreach ( self::$_aRequestParamBlacklist as $sBlacklistedParam ) {
            unset( $aParams[$sBlacklistedParam] );
        }      
        
        $sIdParam = self::getConfiguredSourceIdParam($sLang);
        unset( $aParams[$sIdParam] );
        
        // Obtain the name of the language parameter so we can unset it
        $aLangParam = array();
        parse_str( self::getConfiguredSourceLangParam($sLang), $aLangParam );
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
    public static function getResultFromSessionCache ( $sUrl )
    {
        self::_initializeSessionCache();
        
        $sKey = md5($sUrl);
        
        if ( array_key_exists($sKey, self::$_aSessionCache['results']) ) {
            return self::$_aSessionCache['results'][$sKey];
        }
        
        return false;
    }
    
    /**
     * Returns the result object associated with a URL
     *
     * @param string        $sUrl           Full URL of the page
     * @param CmsxidResult  $oResult        Result object
     * 
     * @return void
     */
    public static function saveResultToSessionCache ( $sUrl, $oResult )
    {
        self::_initializeSessionCache();
        
        $sKey = md5($sUrl);
        
        self::$_aSessionCache['results'][$sKey] = $oResult;
    }
    
    /**
     * Initialize the session cache
     * 
     * @return bool
     */
    protected static function _initializeSessionCache ()
    {
        if ( empty(self::$_aSessionCache) ) {
            self::$_aSessionCache = array(
                'results' => array(),
            );
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
    public static function getDummyString ($oPage, $sSnippet)
    {
        return '<span class="cmsxid-dummy">Cmsxid dummy content for URL: ' . $oPage->getFullUrl() . ', argument: ' . $sSnippet . '</span>';
    }
}