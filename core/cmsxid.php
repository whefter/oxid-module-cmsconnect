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
     * "Level one" cache. Pages retrieved from remote or from cache are
     * cached here to reduce the hits to file cache or to prevent multiple
     * fetches of a remote page if the cache isn't used.
     *
     * @var CmsxidResult[]
     */
    protected static $_aSessionCache = array();
    
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
            $sPage = CmsxidUtils::getCurrentSeoPage();
        }
        
        // $sUrl = CmsxidUtils::getFullPageUrl( $sPage, $sLang );
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        // return $this->_getContent( $sUrl, $sSnippet );
        return $this->_getContent( $oPage, $sSnippet );
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
        // $sUrl = CmsxidUtils::getFullPageUrlById( $sPageId, $sLang );
        // $oxConfig = oxRegistry::getConfig();
        // echo "<pre>";
        // var_dump(__METHOD__,
        // $oxConfig->getConfigParam('aModuleFiles'),
        // "separator",
        // $oxConfig->getShopConfVar('aModuleFiles')
        // );
        
        // echo "</pre>";
        
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        return $this->_getContent( $oPage, $sSnippet );
    }
    
    /**
     * Processes the passed content XML and returns a string
     *
     // * @param   string      $sUrl       Page URL
     * @param   CmsxidPage  $oPage      Page object
     * @param   string      $sSnippet   Content snippet name
     *
     * @return string
     */
    // public function _getContent ( $sUrl, $sSnippet )
    public function _getContent ( $oPage, $sSnippet )
    {
        // $sUrl = $oPage->getBaseUrl();
        
        // $oXml = $this->_getXmlByUrl( $sUrl );
        $oXml = $this->_getXmlByPage( $oPage );
        
        $sReturnSource = false;
        
        if ( is_object($oXml) ) {
            $aSnippets = $oXml->xpath( '/' . $oXml->getName() . '/' . $sSnippet ); 
            if ( count($aSnippets) ) {
                $oSnippetXml = $aSnippets[0];
                
                $sContentSource = CmsxidUtils::getTextContentFromXmlObject( $oSnippetXml );
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
            $sPage = CmsxidUtils::getCurrentSeoPage();
        }
        
        // $sUrl = CmsxidUtils::getFullPageUrl( $sPage, $sLang );
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        // return $this->_getContentArray( $sUrl, $blOnlyContentColumns );
        return $this->_getContentArray( $oPage, $blOnlyContentColumns );
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
        // $sUrl = CmsxidUtils::getFullPageUrlById( $sPageId, $sLang );
        $oPage = CmsxidIdPage::getInstance( $sPageId, $sLang );
        
        // return $this->_getContentArray( $sUrl, $blOnlyContentColumns );
        return $this->_getContentArray( $oPage, $blOnlyContentColumns );
    }
    
    /**
     * Internally processes the passed XML to extract the snippet contents
     *
     // * @param string        $sUrl                   Page URL
     * @param CmsxidPage    $oPage                  Page object
     * @param bool          $blOnlyContentColumns   Only return actual content columns (true by default)
     *
     * @return string[]
     */
    // public function _getContentArray ( $sUrl, $blOnlyContentColumns )
    public function _getContentArray ( $oPage, $blOnlyContentColumns )
    {
        // $sUrl = $oPage->getBaseUrl();
        
        // $oXml = $this->_getXmlByUrl( $sUrl );
        $oXml = $this->_getXmlByPage( $oPage );
        
        $aSnippets = array();
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty array.
        if ( is_object($oXml) ) {
            $aXpathSnippets = $oXml->xpath('/' . $oXml->getName()); 
            foreach ( $aXpathSnippets[0] as $sSnippet => $oSnippetXml ) {
                // Let _getContent do the work here
                // $sSnippetContent = $this->_getContent( $sUrl, $sSnippet );
                $sSnippetContent = $this->_getContent( $oPage, $sSnippet );
            
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
            $sPage = CmsxidUtils::getCurrentSeoPage();
        }
        
        // $sUrl = CmsxidUtils::getFullPageUrl( $sPage, $sLang );
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        // return $this->_getXml( $sUrl );
        return $this->_getXml( $oPage );
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
        // $sUrl = CmsxidUtils::getFullPageUrlById( $sPageId, $v );
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        // return $this->_getXml( $sUrl );
        return $this->_getXml( $oPage );
    }
    
    /**
     * Fetches the XML source associated with the passed URL, removes all CDATA tags 
     * and parses it, returning the resulting object
     *
     // * @param string        $sUrl       XML source URL
     * @param CmsxidPage    $oPage                  Page object
     *
     * @return SimpleXMLObject
     */
    // public function _getXml ( $sUrl )
    public function _getXml ( $oPage )
    {
        // $sXml = $this->_getXmlSourceByUrl( $sUrl );
        $sXml = $this->_getXmlSourceByPage( $oPage );
        $sXml = CmsxidUtils::unwrapCDATA( $sXml );
        $sXml = CmsxidUtils::fixXmlSourceEntities( $sXml );
        
        $oXml = CmsxidUtils::getXmlObjectFromSource( $sXml );
        
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
            $sPage = CmsxidUtils::getCurrentSeoPage();
        }
        
        // $sUrl = CmsxidUtils::getFullPageUrl( $sPage, $sLang );
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        // return $this->_getContentXml( $sUrl, $sSnippet );
        return $this->_getContentXml( $oPage, $sSnippet );
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
        // $sUrl = CmsxidUtils::getFullPageUrlById( $sPageId, $sLang );
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        // return $this->_getContentXml( $sUrl, $sSnippet );
        return $this->_getContentXml( $oPage, $sSnippet );
    }
    
    /**
     * Fetches the XML source associated with the passed URL, removes all CDATA tags 
     * from the requested content snippet and parses it, returning the resulting object
     *
     // * @param string        $sUrl       Page URL
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sSnippet   Snippet to fetch and parse
     *
     * @return SimpleXMLObject
     */
    // public function _getContentXml ( $sUrl, $sSnippet )
    public function _getContentXml ( $oPage, $sSnippet )
    {
        // $oXml = $this->_getXmlByUrl( $sUrl );
        $oXml = $this->_getXmlByPage( $oPage );
        
        $oReturnXml = false;
        
        if ( is_object($oXml) ) {
            $aSnippets = $oXml->xpath( '/' . $oXml->getName() . '/' . $sSnippet ); 
            if ( count($aSnippets) ) {
                $oSnippetXml = $aSnippets[0];
                
                $sContentSource = CmsxidUtils::unwrapCDATA( $oSnippetXml->asXml() );
                $sContentSource = CmsxidUtils::fixXmlSourceEntities( $sContentSource );
                
                $oReturnXml = CmsxidUtils::getXmlObjectFromSource( $sContentSource );
            }
        }
        
        return $oReturnXml;
    }
    
    /**
     * Fetch the XML object associated with a URL. The children's text content might
     * still be wrapped in CDATA!
     *
     // * @param string    $sUrl       Page URL
     * @param CmsxidPage    $oPage      Page object
     * 
     * @return SimpleXMLObject
     */
    // protected function _getXmlByUrl ( $sUrl )
    protected function _getXmlByPage ( $oPage )
    {
        // $sXml = $this->_getXmlSourceByUrl( $sUrl );
        $sXml = $this->_getXmlSourceByPage( $oPage );
        
        return CmsxidUtils::getXmlObjectFromSource( $sXml );
    }
    
    /**
     * Fetch the XML source associated with a URL. This should be the only actual source of freshly
     * fetched XML in this class.
     *
     // * @param string    $sUrl       Page URL
     * @param CmsxidPage    $oPage      Page object
     * 
     * @return SimpleXMLObject
     */
    // protected function _getXmlSourceByUrl ( $sUrl )
    protected function _getXmlSourceByPage ( $oPage )
    {
        // URL should be sanitized at this point
        
        // echo "<pre>";
        // var_dump(__METHOD__);
        // var_dump("Base URL: " . $oPage->getBaseUrl());
        // var_dump("Full URL: " . $oPage->getFullUrl());
        
        $sBaseUrl       = $oPage->getBaseUrl();
        $sFullUrl       = $oPage->getFullUrl();
        
        $blUseFileCache     = true;
        $sSessionCacheUrl   = $sBaseUrl;
        
        if ( CmsxidUtils::checkIsImplicitSeoPage($oPage) ) {
            // var_dump("Detected implicit page");
            
            // The implicit SEO page is exempt from caching IF
            // any query parameters at all have been passed along, since
            // we have to assume some plugin or similar on the page needs these.
            // We have no way of identifying which parameters belong to OXID and
            // which don't, so, to prevent cache flooding, cache ONLY if a plain page
            // has been requested.
            //
            // Additionally, we now use the full page URL for session cache
            if ( count(CmsxidUtils::getExplicitQueryParams()) )  {
                // var_dump("Detected custom query params");
                // var_dump("Disabling file cache and setting session cache identifier to full URL");
                $blUseFileCache     = false;
                $sSessionCacheUrl   = $sFullUrl;
            }
        }
        
        // Determine which URL to use for session cache based on file
        $sUrl       = $oPage->getFullUrl();
        
        // var_dump("Retrieving " . $sSessionCacheUrl . " from session cache");
        $oResult    = $this->_getResultFromSessionCache( $sSessionCacheUrl );
        
        // No result so far and caching enabled for this page, attempt to read from file cache
        if ( !is_object($oResult) && $blUseFileCache ) {
            // var_dump("Retrieving " . $oPage->getBaseUrl() . " from file cache");
            
            // This is URL-based. We want our cache to be dumb; in turn, we have
            // to be smart about which URL to pass it (see above)
            $oResult = CmsxidUtils::getXmlSourceFromCache( $sBaseUrl );
            // $oResult = CmsxidUtils::getXmlSourceFromCache( $oPage );
        }
        
        // Still no result, fetch from remote
        if ( !is_object($oResult) ) {
            // var_dump("No cache result, fetching " . $oPage->getBaseUrl() . " from remote");
            
            // If false, we need to fetch from remote
            // $oResult = CmsxidUtils::fetchXmlSourceFromRemote( $sUrl );
            $oResult = CmsxidUtils::fetchXmlSourceFromRemote( $oPage );
            
            if ( $blUseFileCache ) {
                CmsxidUtils::saveXmlSourceToCache( $oResult, $sBaseUrl );
                
                // var_dump("Saving " . $oPage->getBaseUrl() . " to file cache");
            }
        }
        
        // Save to session cache
        // var_dump("Saving " . $sSessionCacheUrl . " to session cache");
        $this->_saveResultToSessionCache( $sSessionCacheUrl, $oResult );
        
        // Return an empty string so as not to break anything upstream
        $sXml = is_object($oResult) ? $oResult->content : '';
        
        // echo "</pre>";
        
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
            $sPage = CmsxidUtils::getCurrentSeoPage();
        }
        
        // $sUrl = CmsxidUtils::getFullPageUrl( $sPage, $sLang );
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        // return $this->_getPageMetadataByUrl( $sUrl, $sMetadata );
        return $this->_getPageMetadataByPage( $oPage, $sMetadata );
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
        // $sUrl = CmsxidUtils::getFullPageUrlById( $sPageId, $sLang );
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        // return $this->_getPageMetadataByUrl( $sUrl, $sMetadata );
        return $this->_getPageMetadataByPage( $oPage, $sMetadata );
    }
    
    /**
     * Returns the value of the passed metadata field on the passed page URL
     *
     // * @param string    $sUrl       Page URL
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sMetadata  Metadata field name
     * 
     * @return string
     */
    // protected function _getPageMetadataByUrl ( $sUrl, $sMetadata )
    protected function _getPageMetadataByPage ( $oPage, $sMetadata )
    {
        // $oXml = $this->_getXmlByUrl( $sUrl );
        $oXml = $this->_getXmlByPage( $oPage );
        
        $sMetadataValue = false;
        
        if ( is_object($oXml) ) {
            $aXpathResults = $oXml->xpath( '/' . $oXml->getName() . '/metadata/' . $sMetadata );
            if ( count($aXpathResults) == 1 ) {
                $sMetadataValue = CmsxidUtils::getTextContentFromXmlObject( $aXpathResults[0] );
            }
        }
        
        return $sMetadataValue;
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
        $sContent = CmsxidUtils::rewriteContentUrls( $sContent );
        $sContent = CmsxidUtils::fixContentEncoding( $sContent );
        // var_dump(htmlentities($sContent));
        $sContent = CmsxidUtils::decodeContentEntities( $sContent );
        // var_dump(htmlentities($sContent));
        $sContent = CmsxidUtils::parseContentThroughSmarty( $sContent );
        
        return $sContent;
    }
    
    /**
     * Returns the result object associated with a URL
     *
     * @param string    $sUrl       Full URL of the page
     * 
     * @return CmsxidResult
     */
    protected function _getResultFromSessionCache ( $sUrl )
    {
        $sKey = md5($sUrl);
        
        if ( array_key_exists($sKey, self::$_aSessionCache) ) {
            return self::$_aSessionCache[$sKey];
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
    protected function _saveResultToSessionCache ( $sUrl, $oResult )
    {
        $sKey = md5($sUrl);
        
        self::$_aSessionCache[$sKey] = $oResult;
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
        return CmsxidUtils::rewriteContentUrls( $sContent );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidRewriteUrl( $sUrl, $iLangId = null, $blMultiLang = false )
    {
        return CmsxidUtils::rewriteContentUrls( $sContent );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidEncodeTitle( $sUrl, $iLang = null )
    {
        return CmsxidUtils::sanitizePageTitle( $sUrl );
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