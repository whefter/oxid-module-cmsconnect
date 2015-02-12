<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * cmsxid
 *
 * All content getter functions do not throw errors when pages/content snippets cannot
 * be found; rather, they return false. This enables smooth handling and prevents
 * hard-to-handle crashes in the templates.
 */
class cmsxid
{
    /**
     * Returns the processed text content of the requested snippet on the requested TYPO3
     * page and the requested OXID language ID
     *
     * @param string        $sSnippet       Snippet name
     * @param string        $sPage          TYPO3 page
     * @param int|string    $sLang          OXID language ID/Abbrev.
     *
     * @return SimpleXMLObject
     */
    public function getContent ( $sSnippet, $sPage = null, $sLang = null )
    {
        if ( $sPage === null ) {
            $sPage = $this->_getCurrentSeoPage();
        }
        
        $sUrl = $this->_getFullPageUrl( $sPage, $sLang );
        
        return $this->_getContent( $sUrl, $sSnippet );
    }
    
    /**
     * Returns the processed text content of the requested snippet on the requested TYPO3
     * page ID and the requested OXID language ID
     *
     * @param string        $sSnippet       Snippet name
     * @param int           $sPageId        TYPO3 page ID
     * @param int|string    $sLang          OXID language ID/Abbrev.
     *
     * @return SimpleXMLObject
     */
    public function getContentById ( $sSnippet, $sPageId, $sLang = null )
    {
        $sUrl = $this->_getFullPageUrlById( $sPageId, $sLang );
        
        return $this->_getContent( $sUrl, $sSnippet );
    }
    
    /**
     * Processes the passed content XML and returns a string
     *
     * @param   string      $sUrl       Page URL
     * @param   string      $sSnippet   Content snippet name
     *
     * @return string
     */
    public function _getContent ( $sUrl, $sSnippet )
    {
        $oXml = $this->_getXmlByUrl( $sUrl );
        
        $sReturnSource = false;
        
        if ( is_object($oXml) ) {
            $aSnippets = $oXml->xpath( '/' . $oXml->getName() . '/' . $sSnippet ); 
            if ( count($aSnippets) ) {
                $oSnippetXml = $aSnippets[0];
                
                $sContentSource = $this->_getTextContentFromXmlObject( $oSnippetXml );
                // var_dump($sContentSource);
                $sContentSource = $this->_processContent( $sContentSource );
                
                $sReturnSource = $sContentSource;
            }
        }
        
        return $sReturnSource;
    }
    
    /**
     * Returns an array with the text content of all content columns for the passed TYPO3 page path and OXID language ID.
     * Can be forced to return all columns by setting $blOnlyContentColumns to false
     *
     * @param string        $sPage                  TYPO3 page path
     * @param int|string    $sLang                  OXID language ID/Abbrev.
     * @param bool          $blOnlyContentColumns   Only return actual content columns (true by default)
     *
     * @return string[]
     */
    public function getContentArray ( $sPage = null, $sLang = null, $blOnlyContentColumns = true )
    {
        if ( $sPage === null ) {
            $sPage = $this->_getCurrentSeoPage();
        }
        
        $sUrl = $this->_getFullPageUrl( $sPage, $sLang );
        
        return $this->_getContentArray( $sUrl, $blOnlyContentColumns );
    }
    
    /**
     * Returns an array with the text content of all content columns for the passed TYPO3 page ID and OXID language ID.
     * Can be forced to return all columns by setting $blOnlyContentColumns to false
     *
     * @param int           $sPageId                TYPO3 page ID
     * @param int|string    $sLang                  OXID language ID/Abbrev.
     * @param bool          $blOnlyContentColumns   Only return actual content columns (true by default)
     *
     * @return string[]
     */
    public function getContentArrayById ( $sPageId, $sLang = null, $blOnlyContentColumns = true )
    {
        $sUrl = $this->_getFullPageUrlById( $sPageId, $sLang );
        
        return $this->_getContentArray( $sUrl, $blOnlyContentColumns );
    }
    
    /**
     * Internally processes the passed XML to extract the snippet contents
     *
     * @param string        $sUrl                   Page URL
     * @param bool          $blOnlyContentColumns   Only return actual content columns (true by default)
     *
     * @return string[]
     */
    public function _getContentArray ( $sUrl, $blOnlyContentColumns )
    {
        $oXml = $this->_getXmlByUrl( $sUrl );
        
        $aSnippets = array();
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty array.
        if ( is_object($oXml) ) {
            $aXpathSnippets = $oXml->xpath('/' . $oXml->getName()); 
            foreach ( $aXpathSnippets[0] as $sSnippet => $oSnippetXml ) {
                // Let _getContent do the work here
                $sSnippetContent = $this->_getContent( $sUrl, $sSnippet );
            
                switch ( $sSnippet ) {
                    case 'left':
                    case 'normal': case 'content':
                    case 'right':
                    case 'border':
                        if ( $sSnippetContent ) {
                            $aSnippets[$sSnippet] = $sSnippetContent;
                        }
                    break;
                    
                    default:
                        if ( !$blOnlyContentColumns ) {
                            if ( $sSnippetContent ) {
                                $aSnippets[$sSnippet] = $sSnippetContent;
                            }
                        }
                    break;
                }
            }
        }
        
        return $aSnippets;
    }
    
    /**
     * Returns the full XML object for the requested TYPO3 page and OXID language ID.
     *
     * @param int           $sPage      TYPO3 page path
     * @param int|string    $sLang      OXID language ID/Abbrev.
     *
     * @return SimpleXMLObject
     */
    public function getXml ( $sPage = null, $sLang = null )
    {
        if ( $sPage === null ) {
            $sPage = $this->_getCurrentSeoPage();
        }
        
        $sUrl = $this->_getFullPageUrl( $sPage, $sLang );
        
        return $this->_getXml( $sUrl );
    }
    
    /**
     * Returns the full XML object for the requested TYPO3 page ID and OXID language ID.
     *
     * @param int           $sPageId                TYPO3 page ID
     * @param int|string    $sLang                  OXID language ID/Abbrev.
     *
     * @return SimpleXMLObject
     */
    public function getXmlById ( $sPageId, $sLang = null )
    {
        $sUrl = $this->_getFullPageUrlById( $sPageId, $v );
        
        return $this->_getXml( $sUrl );
    }
    
    /**
     * Fetches the XML source associated with the passed URL, removes all CDATA tags 
     * and parses it, returning the resulting object
     *
     * @param string        $sUrl       XML source URL
     *
     * @return SimpleXMLObject
     */
    public function _getXml ( $sUrl )
    {
        $sXml = $this->_getXmlSourceByUrl( $sUrl );
        $sXml = $this->_unwrapCDATA( $sXml );
        $sXml = $this->_fixXmlSourceEntities( $sXml );
        
        $oXml = $this->_getXmlObjectFromSource( $sXml );
        
        return $oXml;
    }
    
    /**
     * Returns an XML object for the requested snippet, TYPO3 page and OXID language ID
     *
     * @param string        $sSnippet   Snippet name
     * @param int           $sPage      TYPO3 page path
     * @param int|string    $sLang      OXID language ID/Abbrev.
     *
     * @return SimpleXMLObject
     */
    public function getContentXml ( $sSnippet, $sPage = null, $sLang = null )
    {
        if ( $sPage === null ) {
            $sPage = $this->_getCurrentSeoPage();
        }
        
        $sUrl = $this->_getFullPageUrl( $sPage, $sLang );
        
        return $this->_getContentXml( $sUrl, $sSnippet );
    }
    
    /**
     * Returns an XML object for the requested snippet, TYPO3 page ID and OXID language ID
     *
     * @param string        $sSnippet               Snippet name
     * @param int           $sPageId                TYPO3 page ID
     * @param int|string    $sLang                  OXID language ID/Abbrev.
     *
     * @return SimpleXMLObject
     */
    public function getContentXmlById ( $sSnippet, $sPageId, $sLang = null )
    {
        $sUrl = $this->_getFullPageUrlById( $sPageId, $sLang );
        
        return $this->_getContentXml( $sUrl, $sSnippet );
    }
    
    /**
     * Fetches the XML source associated with the passed URL, removes all CDATA tags 
     * from the requested content snippet and parses it, returning the resulting object
     *
     * @param string        $sUrl       Page URL
     * @param string        $sSnippet   Snippet to fetch and parse
     *
     * @return SimpleXMLObject
     */
    public function _getContentXml ( $sUrl, $sSnippet )
    {
        $oXml = $this->_getXmlByUrl( $sUrl );
        
        $oReturnXml = false;
        
        if ( is_object($oXml) ) {
            $aSnippets = $oXml->xpath( '/' . $oXml->getName() . '/' . $sSnippet ); 
            if ( count($aSnippets) ) {
                $oSnippetXml = $aSnippets[0];
                
                $sContentSource = $this->_unwrapCDATA( $oSnippetXml->asXml() );
                $sContentSource = $this->_fixXmlSourceEntities( $sContentSource );
                
                $oReturnXml = $this->_getXmlObjectFromSource( $sContentSource );
            }
        }
        
        return $oReturnXml;
    }
    
    /**
     * Fetch the XML object associated with a URL. The children's text content might
     * still be wrapped in CDATA!
     *
     * @param string    $sUrl       Page URL
     * 
     * @return SimpleXMLObject
     */
    protected function _getXmlByUrl ( $sUrl )
    {
        $sXml = $this->_getXmlSourceByUrl( $sUrl );
        
        return $this->_getXmlObjectFromSource( $sXml );
    }
    
    /**
     * Fetch the XML source associated with a URL
     *
     * @param string    $sUrl       Page URL
     * 
     * @return SimpleXMLObject
     */
    protected function _getXmlSourceByUrl ( $sUrl )
    {
        // URL should be sanitized at this point
        
        $oResult = $this->_getXmlSourceFromCache( $sUrl );
        
        if ( !is_object($oResult) ) {
            // If false, we need to fetch from remote
            $oResult = $this->_fetchXmlSourceFromRemote( $sUrl );
            
            $this->_saveXmlSourceToCache( $oResult, $sUrl );
        }
        
        // Return an empty string so as not to break anything upstream
        $sXml = is_object($oResult) ? $oResult->content : '';
        
        return $sXml;
    }
    
    /**
     * Returns the metadata field value for the passed metadata field name of the
     * requested TYPO3 page and the requested OXID language ID
     *
     * @param string        $sMetadata      Metadata field name
     * @param string        $sPage          TYPO3 page
     * @param int|string    $sLang          OXID language ID/Abbrev.
     *
     * @return string
     */
    public function getPageMetadata ( $sMetadata, $sPage = null, $sLang = null )
    {
        if ( $sPage === null ) {
            $sPage = $this->_getCurrentSeoPage();
        }
        
        $sUrl = $this->_getFullPageUrl( $sPage, $sLang );
        
        return $this->_getPageMetadataByUrl( $sUrl, $sMetadata );
    }
    
    /**
     * Returns the metadata field value for the passed metadata field name of the
     * requested TYPO3 page ID and the requested OXID language ID
     *
     * @param string        $sMetadata      Metadata field name
     * @param int           $sPageId        TYPO3 page ID
     * @param int|string    $sLang          OXID language ID/Abbrev.
     *
     * @return string
     */
    public function getPageMetadataById ( $sMetadata, $sPageId, $sLang = null )
    {
        $sUrl = $this->_getFullPageUrlById( $sPageId, $sLang );
        
        return $this->_getPageMetadataByUrl( $sUrl, $sMetadata );
    }
    
    /**
     * Returns the value of the passed metadata field on the passed page URL
     *
     * @param string    $sUrl       Page URL
     * @param string    $sMetadata  Metadata field name
     * 
     * @return string
     */
    protected function _getPageMetadataByUrl ( $sUrl, $sMetadata )
    {
        $oXml = $this->_getXmlByUrl( $sUrl );
        
        $sMetadataValue = false;
        
        if ( is_object($oXml) ) {
            $aXpathResults = $oXml->xpath( '/' . $oXml->getName() . '/metadata/' . $sMetadata );
            if ( count($aXpathResults) == 1 ) {
                $sMetadataValue = $this->_getTextContentFromXmlObject( $aXpathResults[0] );
            }
        }
        
        return $sMetadataValue;
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
    protected function _getFullPageUrl ( $sPage = null, $sLang = null )
    {
        $sBaseUrl       = $this->_getConfiguredSourceBaseUrl($sLang);
        $sBaseUrlSsl    = $this->_getConfiguredSourceSslBaseUrl($sLang);
        $sPagePath      = $this->_getConfiguredSourcePagePath($sLang);
        $sParams        = $this->_getConfiguredSourceParams($sLang);
        
        // We don't know how the user input his parameters, so parse them to be sure
        $aParams = array();
        parse_str( $sParams, $aParams );
        $sParams = http_build_query( $aParams );
        
        $sFullPageUrl   =     $sBaseUrlSsl ? $sBaseUrlSsl : $sBaseUrl
                            . '/' . $sPagePath
                            . '/' . $this->_sanitizePageTitle($sPage)
                            . '/' . '?' . $sParams
                        ;
        
        return $this->_sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Build full TYPO3 URL for the passed page ID and OXID lang ID. The lang ID is mapped to the
     * corresponding TYPO3 language ID.
     *
     * @param int           $sPageId    TYPO3 page ID
     * @param int|string    $sLang      OXID language ID/Abbrev.
     *
     * @return string
     */
    protected function _getFullPageUrlById ( $sPageId, $sLang = null )
    {
        $sBaseUrl       = $this->_getConfiguredSourceBaseUrl($sLang);
        $sBaseUrlSsl    = $this->_getConfiguredSourceSslBaseUrl($sLang);
        $sIdParam       = $this->_getConfiguredSourceIdParam($sLang);
        $sLangParam     = $this->_getConfiguredSourceLangParam($sLang);
        $sParams        = $this->_getConfiguredSourceParams($sLang);
        
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
        
        
        $sFullPageUrl =     $sBaseUrlSsl ? $sBaseUrlSsl : $sBaseUrl
                            . '/?' . $sParams
                        ;
        
        return $this->_sanitizeUrl($sFullPageUrl);
    }
    
    /**
     * Processes text content for user display
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    protected function _processContent ( $sContent )
    {
        $sContent = $this->_rewriteContentUrls ( $sContent );
        $sContent = $this->_fixContentEncoding ( $sContent );
        // var_dump(htmlentities($sContent));
        $sContent = $this->_decodeContentEntities ( $sContent );
        // var_dump(htmlentities($sContent));
        $sContent = $this->_parseContentThroughSmarty ( $sContent );
        
        return $sContent;
    }
    
    /**
     * Fetches the actual text content of a SimpleXML node; removes the root tag and CDATA tags around the text node
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    protected function _getTextContentFromXmlObject ( $oXml )
    {
        $sText = '';
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty string.
        if ( $oXml !== false ) {
            $sText = trim( $oXml->asXML() );
            
            // var_dump(__METHOD__.':'.htmlentities($sText));
            
            // Remove CDATA tag
            $sText = $this->_unwrapCDATA( $sText );
            
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
     * Sanitize URLs prior to caching to prevent double caching of a page under different URLs
     *
     * @param string    $sUrl       Page URL
     * 
     * @return string
     */
    protected function _sanitizeUrl ( $sUrl )
    {
        // Automatic encoding handling
        $oStr = getStr();
        
        // Replace multiple slashes, except for the protocol part
        $sUrl = $oStr->preg_replace( '/(?<!:)\/+/', '/', $sUrl);
        
        // Put through PHP's functions to ensure standardized URL
        // $sUrl = http_build_url( parse_url( $sUrl ) );
        
        return $sUrl;
    }
    
    /**
     * Ensure that a page title can be used in a URL without causing problems
     *
     * @param string    $sTitle     Title
     * 
     * @return string
     */
    protected function _sanitizePageTitle ( $sTitle )
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
     * Returns an object containing the curl information array (->info) and, on success,
     * the page content (->content) for the requested URL.
     *
     * @param string    $sUrl       URL
     * 
     * @return object
     */
    protected function _fetchXmlSourceFromRemote ( $sUrl )
    {
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $sUrl );
        curl_setopt( $curl_handle, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
        
        $oResult = new stdClass();
        
        $oResult->content   = curl_exec( $curl_handle );
        $oResult->info      = curl_getinfo( $curl_handle );
        
        // $sContent = curl_exec( $curl_handle );
        // $aInfo    = curl_getinfo( $curl_handle );
        
        curl_close( $curl_handle );
        
        // var_dump("info: ", $aResult['info']);
        
        if ( $oResult->info['http_code'] != 200 ) {
            $oResult->content = '';
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
    protected function _getXmlSourceFromCache ( $sUrl )
    {
        $sCacheName = $this->_getCacheFilenameFromUrl($sUrl);
        
        $oResult = oxRegistry::get('oxUtils')->fromFileCache( $sCacheName );
        
        if ( $oResult  ) {
            return $oResult;
        }
        
        return false;
    }
    
    /**
     * Saves the passed XML source for the passed URL to cache
     *
     * @param object    $oResult    Result object
     * @param string    $sUrl       URL
     * @param int       $iTtl       Cache TTL
     * 
     * @return bool
     */
    protected function _saveXmlSourceToCache ( $oResult, $sUrl, $iTtl = null )
    {
        $sCacheName = $this->_getCacheFilenameFromUrl($sUrl);
        
        // Figure out cache TTL
        if ( null === $iTtl ) {
            if ( !($iTtl = $this->_getConfiguredDefaultCacheTTL()) ) {
                $iTtl = 600;
            }
            if ( !($iTtlRnd = $this->_getConfiguredDefaultCacheTTLRandomization) ) {
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
    protected function _getCacheFilenameFromUrl ( $sUrl )
    {
        $oStr = getStr();
        
        // Strip extra slashes etc.
        $sUrl = $this->_sanitizeUrl( $sUrl );
        
        // Only keep word characters
        $sUrl = $oStr->preg_replace( '/[^a-zA-Z0-9-]/', '_', $sUrl );
        
        // Cut off at 200 chars to prevent problems with filename length
        $sUrl = substr( $sUrl, 0, 200 );
        
        // Apend checksum for safety
        return 'cmsxid_' . $sUrl . '_' . substr( md5($sUrl), 0, 12 );
    }
    
    /**
     * Removes named HTML entities from the passed XML string
     *
     * @param string    $sXml       XML source string
     * 
     * @return string
     */
    protected function _fixXmlSourceEntities ( $sXml )
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
    protected function _getXmlObjectFromSource ( $sXml )
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
    protected function _unwrapCDATA( $sXml )
    {
        $oStr = getStr();
        
        // Remove CDATA tag
        $sXml = $oStr->preg_replace( '/<!\[CDATA\[(.*?)\]\]>/ms', '\\1', $sXml );
        
        return $sXml;
    }
    
    /**
     * Rewrites all CMS-related URLs in the passed content to point to the shop
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    protected function _rewriteContentUrls ( $sContent )
    {
        $oxConfig = oxRegistry::getConfig();
        
        if ( $this->_getConfiguredNoUrlRewriteSetting() == true ) {
            return $sContent;
        }
        
        // We want to replace URLs for all configured sources
        $aLanguages = array_keys( $this->_getConfiguredSources() );

        foreach ( $aLanguages as $sLang ) {
            // Deal only with numeric language IDs, since all OXID functions only take those
            if ( !is_numeric($sLang) ) {
                continue;
            }
            
            // No matter what URLs the CMS returns, the URLs schema needs to be rewritten to the current shop's schema
            foreach ( array($this->_getConfiguredSourceBaseUrl($sLang), $this->_getConfiguredSourceSslBaseUrl($sLang)) as $sSourceBaseUrl ) {
                $sSourcePagePath    = $this->_getConfiguredSourcePagePath($sLang);
                $sFullBaseUrl       = $this->_sanitizeUrl( $sSourceBaseUrl . '/' . $sSourcePagePath . '/' );
                
                // The target is defined by the current shop's SSL setting
                $sTargetBaseUrl     = $oxConfig->isSsl() ? $oxConfig->getSslShopUrl($sLang) : $oxConfig->getShopUrl($sLang);
                $sTargetSeoIdent    = $this->_getConfiguredSourceSeoIdentifier( $sLang );
                $sFullTargetUrl     = $this->_sanitizeUrl( $sTargetBaseUrl . '/' . $sTargetSeoIdent . '/' );
                
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
                if ( $this->_getConfiguredSourceSslBaseUrl($sLang) ) {
                    // We can safely do this crude replace since, in theory, all URLs left on the page should be to
                    // non-page CMS content
                    $sSourceBaseUrl     = $this->_sanitizeUrl( $this->_getConfiguredSourceBaseUrl($sLang) . '/' );
                    $sSourceSslBaseUrl  = $this->_sanitizeUrl( $this->_getConfiguredSourceSslBaseUrl($sLang) . '/' );
                
                    $sContent = str_replace( $sSourceBaseUrl, $sSourceSslBaseUrl );
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
    protected function _fixContentEncoding ( $sContent )
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
    protected function _decodeContentEntities ( $sContent )
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
    protected function _parseContentThroughSmarty ( $sContent )
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
     * Returns the contents of the sCmsxidPage config param which, if we are on an SEO-loaded page, should
     * contain the page to load through CMSxid
     *
     * @return string
     */
    protected function _getCurrentSeoPage ()
    {
        return oxRegistry::getConfig()->getConfigParam( 'sCmsxidPage' );
    }
    
    /**
     * Configuration: fetch default cache TTL value
     * 
     * @return int
     */
    protected function _getConfiguredDefaultCacheTTL ()
    {
        return oxRegistry::getConfig()->getShopConfVar('iCmsxidTtlDefault');
    }
    
    /**
     * Configuration: fetch default cache TTL randomization value
     * 
     * @return int
     */
    protected function _getConfiguredDefaultCacheTTLRandomization ()
    {
        return oxRegistry::getConfig()->getShopConfVar('iCmsxidTtlDefaultRnd');
    }
    
    /**
     * Configuration: fetch "no url rewriting" value
     * 
     * @return bool
     */
    protected function _getConfiguredNoUrlRewriteSetting ()
    {
        return oxRegistry::getConfig()->getShopConfVar('blCmsxidLeaveUrls');
    }
    
    /**
     * Configuration: returns an array of all configured sources by language ID and language abbreviation as object
     * 
     * @return object[]
     */
    protected function _getConfiguredSources ()
    {
        $oxLang     = oxRegistry::getLang();
        $oxConfig   = oxRegistry::getConfig();
        
        $aCmsxidBaseUrls    = $oxConfig->getShopConfVar('aCmsxidBaseUrls');
        $aCmsxidBaseSslUrls = $oxConfig->getShopConfVar('aCmsxidBaseSslUrls');
        $aCmsxidPagePaths   = $oxConfig->getShopConfVar('aCmsxidPagePaths');
        $aCmsxidParams      = $oxConfig->getShopConfVar('aCmsxidParams');
        $aCmsxidIdParams    = $oxConfig->getShopConfVar('aCmsxidIdParams');
        $aCmsxidLangParams  = $oxConfig->getShopConfVar('aCmsxidLangParams');
        $aCmsxidSeoIdents   = $oxConfig->getShopConfVar('aCmsxidSeoIdents');
        
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
    protected function _getConfiguredSourceProperty ( $sLang, $sProperty )
    {
        if ( $sLang === null ) {
            $sLang = oxRegistry::getLang()->getBaseLanguage();
        }
        
        $aSources = $this->_getConfiguredSources();
        
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
    protected function _getConfiguredSourceBaseUrl ( $sLang )
    {
        $sBaseUrl = $this->_getConfiguredSourceProperty( $sLang, 'sBaseUrl' );
        $sBaseUrl = $this->_sanitizeUrl( $sBaseUrl );
        
        return $sBaseUrl;
    }
    
    /**
     * Configuration: return source ssl base url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the property for
     * 
     * @return string
     */
    protected function _getConfiguredSourceSslBaseUrl ( $sLang )
    {
        $sBaseUrlSsl = $this->_getConfiguredSourceProperty( $sLang, 'sBaseUrlSsl' );
        $sBaseUrlSsl = $this->_sanitizeUrl( $sBaseUrlSsl );
        
        return $sBaseUrlSsl;
    }
    
    /**
     * Configuration: return page path for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    protected function _getConfiguredSourcePagePath ( $sLang )
    {
        return $this->_getConfiguredSourceProperty( $sLang, 'sPagePath' );
    }
    
    /**
     * Configuration: return source parameter string for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    protected function _getConfiguredSourceParams ( $sLang )
    {
        return $this->_getConfiguredSourceProperty( $sLang, 'sParams' );
    }
    
    /**
     * Configuration: return source CMS ID query parameter name for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    protected function _getConfiguredSourceIdParam ( $sLang )
    {
        return $this->_getConfiguredSourceProperty( $sLang, 'sIdParam' );
    }
    
    /**
     * Configuration: return source CMS language ID for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    protected function _getConfiguredSourceLangParam ( $sLang )
    {
        return $this->_getConfiguredSourceProperty( $sLang, 'sLangParam' );
    }
    
    /**
     * Configuration: return source url for the passed OXID language
     * 
     * @param string        $sLang          Language to return the source property for
     * 
     * @return string
     */
    protected function _getConfiguredSourceSeoIdentifier ( $sLang )
    {
        return $this->_getConfiguredSourceProperty( $sLang, 'sSeoIdent' );
    }
    
    /**
     * Checks if the passed URL refers to any known (configured) CMS source. If so, return an
     * array with that information
     * 
     * @param string        $sSeoUrl        SEO URL to check
     * 
     * @return array
     */
    public function getPageSeoInfoByUrl ( $sSeoUrl )
    {
        $oxConfig = oxRegistry::getConfig();
        
        $aSources = $this->_getConfiguredSources();
        
        $aSeoInfo = false;
        
        foreach ( $aSources as $sLang => $oSource ) {
            // We only want actual numeric language IDs at this point
            if ( !is_numeric($sLang) ) {
                continue;
            }
            
            $sSeoIdent  = $this->_getConfiguredSourceSeoIdentifier($sLang);
            
            // Either starts with SEO identifier and a slash (longer SEO URL) or is just the plain SEO identifier
            if ( strpos($sSeoUrl, $sSeoIdent . '/') === 0 || $sSeoUrl == $sSeoIdent ) {
                $sPage =    ($sSeoUrl == $sSeoIdent)
                                ? ''
                                : str_replace( $sSeoIdent . '/', '', $sSeoUrl )
                            ;
                
                $aSeoInfo = array(
                    'lang'  => $sLang,
                    'cl'    => 'cmsxid_fe',
                    'page'  => $sPage,
                );
                
                break;
            }
        }
        
        return $aSeoInfo;
    }
    
    /**
     * TOXID compatibility function
     */
    public function getXmlObject( $sPage = null, $sSnippet = null )
    {
        if ( $sSnippet !== null ) {
            return $this->getContentXml( $sSnippet, $sPage );
        } else {
            return $this->getXml( $sPage );
        }
    }
    
    /**
     * TOXID compatibility function
     */
    public function getSnippetList( $sCustomPage = null, $blOnlyContentColumns = true )
    {
        return $this->getContentArray( $sCustomPage, null, $blOnlyContentColumns );
    }
    
    /**
     * TOXID compatibility function
     */
    public function getCmsSnippet( $sSnippet = null, $blMultiLang = false, $sCustomPage = null )
    {
        return $this->getContent( $sSnippet, $sCustomPage );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidRewriteUrls($sContent, $iLangId = null, $blMultiLang = false)
    {
        return $this->_rewriteContentUrls( $sContent );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidRewriteUrl( $sUrl, $iLangId = null, $blMultiLang = false )
    {
        return $this->_rewriteContentUrls( $sContent );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidEncodeTitle( $sUrl, $iLang = null )
    {
        return $this->_sanitizePageTitle( $sUrl );
    }
    
    /**
     * TOXID compatibility function
     */
    public function getSearchResult($sKeywords)
    {
    
    }
    
    /**
     * TOXID compatibility function
     */
    public function getHttpCode( $sUrl = null )
    {
        return 200;
    }
}