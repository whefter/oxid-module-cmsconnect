<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2015 William Hefter
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
     * @param string        $sPage          TYPO3 page. When omitted, CMSxid will attempt to determine from current SEO page.
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getContent ( $sSnippet, $sPage = null, $sLang = null )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( $sPage === null ) {
            $sPage = $oUtils->getCurrentSeoPage();
        }
        
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        return $this->_getContent( $oPage, $sSnippet );
    }
    
    /**
     * Returns the processed text content of the requested snippet on the requested TYPO3
     * page ID and the requested OXID language ID
     *
     * @param string        $sSnippet       Snippet name
     * @param int           $sPageId        TYPO3 page ID
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getContentById ( $sSnippet, $sPageId, $sLang = null )
    {
        // $oUtils = CmsxidUtils::getInstance();
        
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        return $this->_getContent( $oPage, $sSnippet );
    }
    
    /**
     * Processes the passed content XML and returns a string
     *
     * @param   CmsxidPage  $oPage      Page object
     * @param   string      $sSnippet   Content snippet name
     *
     * @return string
     */
    protected function _getContent ( $oPage, $sSnippet )
    {
        $oUtils = CmsxidUtils::getInstance();

        if ( false !== ($sDummyContent = $this->_getDummyContent($oPage, $sSnippet)) ) {
            return $sDummyContent;
        }
        
        $oXml = $this->_getXmlByPage( $oPage );
        $sReturnSource = false;

        if ($oXml instanceof SimpleXMLElement) {
            try {
                $aSnippets = $oXml->xpath( '/' . $oXml->getName() . '/' . $sSnippet );
                
                if ( $aSnippets !== false && count($aSnippets) ) {
                    $oSnippetXml = $aSnippets[0];
                    
                    if ($oSnippetXml instanceof SimpleXMLElement) {
                        $sContentSource = $oUtils->getTextContentFromXmlObject( $oSnippetXml );
                        $sContentSource = $this->_processContent( $sContentSource );
                        
                        $sReturnSource = $sContentSource;
                    }
                }
            } catch ( Exception $e) {
				//var_dump($e->getMessage());
            }
        }
        
        return $sReturnSource;
    }
    
    /**
     * Returns an array with the text content of all content nodes for the passed TYPO3 page path and OXID language ID.
     * Specific nodes can be returned by passing the $aNodes argument.
     *
     * @param string        $sPage                  TYPO3 page. When omitted, CMSxid will attempt to determine from current SEO page.
     * @param int|string    $sLang                  OXID language ID/abbreviation.
     * @param string[]      $aNodes                 List of nodes names to return (empty array for all: default)
     *
     * @return string[]
     */
    public function getContentArray ( $sPage = null, $sLang = null, $aNodes = array() )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( $sPage === null ) {
            $sPage = $oUtils->getCurrentSeoPage();
        }
        
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);

        return $this->_getContentArray( $oPage, $aNodes );
    }
    
    /**
     * Returns an array with the text content of all content nodes for the passed TYPO3 page ID and OXID language ID.
     * Specific nodes can be returned by passing the $aNodes argument.
     *
     * @param int           $sPageId                TYPO3 page ID
     * @param int|string    $sLang                  OXID language ID/abbreviation.
     * @param string[]      $aNodes                 List of nodes names to return (empty array for all: default)
     *
     * @return string[]
     */
    public function getContentArrayById ( $sPageId, $sLang = null, $aNodes = array() )
    {
        // $oUtils = CmsxidUtils::getInstance();
        
        $oPage = CmsxidIdPage::getInstance( $sPageId, $sLang );
        
        return $this->_getContentArray( $oPage, $aNodes );
    }
    
    /**
     * Internally processes the passed XML to extract the snippet contents
     *
     * @param CmsxidPage    $oPage                  Page object
     * @param string[]      $aNodes                 List of nodes names to return (empty array for all: default)
     *
     * @return string[]
     */
    protected function _getContentArray ( $oPage, $aNodes )
    {
        // Invalid parameter, return nothing.
        // TODO: throw exception
        if ( !is_array($aNodes) && $aNodes !== false && $aNodes !== null ) {
            return array();
        }
        
        // Default to return all nodes. Covers false and null as well.
        if ( empty($aNodes) ) {
            $aNodes = array();
        }
        
        if ( false !== ($aDummyContentArray = $this->_getDummyContentArray($oPage, $aNodes)) ) {
            return $aDummyContentArray;
        }
        
        $oXml = $this->_getXmlByPage( $oPage );
        
        $aSnippets = array();
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty array.
        if ( is_object($oXml) ) {
            $aXpathSnippets = $oXml->xpath('/' . $oXml->getName()); 
            
            if ( $aXpathSnippets !== false && count($aXpathSnippets) ) {
                foreach ( $aXpathSnippets[0] as $sSnippet => $oSnippetXml ) {
                    // Let _getContent do the work here
                    
                    switch ( $sSnippet ) {
                        case 'metadata';
                        break;
                        
                        default:
                            if ( !count($aNodes) || in_array($sSnippet, $aNodes) ) {
                                $sSnippetContent = $this->_getContent( $oPage, $sSnippet );
                                
                                if ( $sSnippetContent ) {
                                    $aSnippets[$sSnippet] = $sSnippetContent;
                                }
                            }
                        break;
                    }
                }
            }
        }
        
        return $aSnippets;
    }
    
    /**
     * Returns the full XML object for the requested TYPO3 page and OXID language ID.
     *
     * @param int           $sPage      TYPO3 page. When omitted, CMSxid will attempt to determine from current SEO page.
     * @param int|string    $sLang      OXID language ID/abbreviation.
     *
     * @return SimpleXMLObject
     */
    public function getXml ( $sPage = null, $sLang = null )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( $sPage === null ) {
            $sPage = $oUtils->getCurrentSeoPage();
        }
        
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        return $this->_getXml( $oPage );
    }
    
    /**
     * Returns the full XML object for the requested TYPO3 page ID and OXID language ID.
     *
     * @param int           $sPageId                TYPO3 page ID
     * @param int|string    $sLang                  OXID language ID/abbreviation.
     *
     * @return SimpleXMLObject
     */
    public function getXmlById ( $sPageId, $sLang = null )
    {
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        return $this->_getXml( $oPage );
    }
    
    /**
     * Fetches the XML source associated with the passed URL, removes all CDATA tags 
     * and parses it, returning the resulting object
     *
     * @param CmsxidPage    $oPage                  Page object
     *
     * @return SimpleXMLObject
     */
    protected function _getXml ( $oPage )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( false !== ($oDummyXml = $this->_getDummyXml($oPage)) ) {
            return $oDummyXml;
        }

        $sXml = $this->_getXmlSourceByPage( $oPage );
        $sXml = $oUtils->unwrapCDATA( $sXml );
        $sXml = $oUtils->fixXmlSourceEntities( $sXml );
        
        $oXml = $oUtils->getXmlObjectFromSource( $sXml );
        
        return $oXml;
    }
    
    /**
     * Returns an XML object for the requested snippet, TYPO3 page and OXID language ID
     *
     * @param string        $sSnippet   Snippet name
     * @param int           $sPage      TYPO3 page. When omitted, CMSxid will attempt to determine from current SEO page.
     * @param int|string    $sLang      OXID language ID/abbreviation.
     *
     * @return SimpleXMLObject
     */
    public function getContentXml ( $sSnippet, $sPage = null, $sLang = null )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( $sPage === null ) {
            $sPage = $oUtils->getCurrentSeoPage();
        }
        
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        return $this->_getContentXml( $oPage, $sSnippet );
    }
    
    /**
     * Returns an XML object for the requested snippet, TYPO3 page ID and OXID language ID
     *
     * @param string        $sSnippet               Snippet name
     * @param int           $sPageId                TYPO3 page ID
     * @param int|string    $sLang                  OXID language ID/abbreviation.
     *
     * @return SimpleXMLObject
     */
    public function getContentXmlById ( $sSnippet, $sPageId, $sLang = null )
    {
        // $oUtils = CmsxidUtils::getInstance();
        
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        return $this->_getContentXml( $oPage, $sSnippet );
    }
    
    /**
     * Fetches the XML source associated with the passed URL, removes all CDATA tags 
     * from the requested content snippet and parses it, returning the resulting object
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sSnippet   Snippet to fetch and parse
     *
     * @return SimpleXMLObject
     */
    protected function _getContentXml ( $oPage, $sSnippet )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( false !== ($oDummyContentXml = $this->_getDummyContentXml($oPage, $sSnippet)) ) {
            return $oDummyContentXml;
        }
        
        $oXml = $this->_getXmlByPage( $oPage );
        
        $oReturnXml = false;
        
        if ( is_object($oXml) ) {
            $aSnippets = $oXml->xpath( '/' . $oXml->getName() . '/' . $sSnippet );
            
            if ( $aSnippets !== false && count($aSnippets) ) {
                $oSnippetXml = $aSnippets[0];
                
                $sContentSource = $oUtils->unwrapCDATA( $oSnippetXml->asXml() );
                $sContentSource = $oUtils->fixXmlSourceEntities( $sContentSource );
                
                $oReturnXml = $oUtils->getXmlObjectFromSource( $sContentSource );
            }
        }
        
        return $oReturnXml;
    }
    
    /**
     * Fetch the XML object associated with a URL. The children's text content might
     * still be wrapped in CDATA!
     *
     * @param CmsxidPage    $oPage      Page object
     * 
     * @return SimpleXMLObject
     */
    protected function _getXmlByPage ( $oPage )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        $sXml = $this->_getXmlSourceByPage( $oPage );
        
        return $oUtils->getXmlObjectFromSource( $sXml );
    }
    
    /**
     * Fetch the XML source associated with a URL. This should be the only actual source of freshly
     * fetched XML in this class.
     *
     * @param CmsxidPage    $oPage      Page object
     * 
     * @return SimpleXMLObject
     */
    protected function _getXmlSourceByPage ( $oPage )
    {
        startProfile(__METHOD__);
        
        $oUtils = CmsxidUtils::getInstance();
        
        $sBaseUrl   = $oPage->getBaseUrl();
        $sFullUrl   = $oPage->getFullUrl();
        
        $blUseFileCache     = true;
        $sSessionCacheUrl   = $sBaseUrl;
        
        $blIsImplicitSeoPage = $oUtils->checkIsImplicitSeoPage($oPage);
        
        if ( $blIsImplicitSeoPage ) {
            // The implicit SEO page is exempt from caching IF
            // any query parameters at all have been passed along, since
            // we have to assume some plugin or similar on the page needs these.
            // We have no way of identifying which parameters belong to OXID and
            // which don't, so, to prevent cache flooding, cache ONLY if a plain page
            // has been requested.
            //
            // Additionally, we now use the full page URL for session cache
            if ( count($oUtils->getExplicitQueryParams()) )  {
                // @TODO Add some debug output here
                $blUseFileCache     = false;
                $sSessionCacheUrl   = $sFullUrl;
            }
        }
        
        // Determine which URL to use for session cache based on file
        $sUrl       = $oPage->getFullUrl();
        
        // var_dump("Retrieving " . $sSessionCacheUrl . " from session cache");
        $oResult    = $oUtils->getResultFromSessionCache( $sSessionCacheUrl );
        
        if ( $oUtils->getConfigValue(CmsxidUtils::CONFIG_KEY_ENABLE_TEST_CONTENT) ) {
            $oResult = $oUtils->fetchXmlSourceFromTestContent( $oPage );
        }
        
        // No result so far and caching enabled for this page, attempt to read from file cache
        if ( !is_object($oResult) && $blUseFileCache ) {
            // var_dump("Retrieving " . $oPage->getBaseUrl() . " from file cache");
            
            // This is URL-based. We want our cache to be dumb; in turn, we have
            // to be smart about which URL to pass it (see above)
            $oResult = $oUtils->getXmlSourceFromCache( $sBaseUrl );
            // $oResult = CmsxidUtils::getXmlSourceFromCache( $oPage );
        }
        
        // Still no result, fetch from remote
        if ( !is_object($oResult) ) {
            // var_dump("No cache result, fetching " . $oPage->getBaseUrl() . " from remote");
            
            // If false, we need to fetch from remote
            // $oResult = CmsxidUtils::fetchXmlSourceFromRemote( $sUrl );
            $oResult = $oUtils->fetchXmlSourceFromRemote( $oPage );
            
            if ( $blUseFileCache ) {
                // var_dump("Saving " . $oPage->getBaseUrl() . " to file cache");
                $oUtils->saveXmlSourceToCache( $oResult, $sBaseUrl );
            }
        }
        
        // Save to session cache
        // var_dump("Saving " . $sSessionCacheUrl . " to session cache");
        $oUtils->saveResultToSessionCache( $sSessionCacheUrl, $oResult );
        
        // Return an empty string so as not to break anything upstream
        $sXml = is_object($oResult) ? $oResult->content : '';
        
        // echo "</pre>";
        
        stopProfile(__METHOD__);
        
        return $sXml;
    }
    
    /**
     * Returns the metadata field value for the passed metadata field name of the
     * requested TYPO3 page and the requested OXID language ID
     *
     * @param string        $sMetadata      Metadata field name
     * @param string        $sPage          TYPO3 page. When omitted, CMSxid will attempt to determine from current SEO page.
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getPageMetadata ( $sMetadata, $sPage = null, $sLang = null )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( $sPage === null ) {
            $sPage = $oUtils->getCurrentSeoPage();
        }
        
        $oPage = CmsxidPathPage::getInstance($sPage, $sLang);
        
        return $this->_getPageMetadataByPage( $oPage, $sMetadata );
    }
    
    /**
     * Returns the metadata field value for the passed metadata field name of the
     * requested TYPO3 page ID and the requested OXID language ID
     *
     * @param string        $sMetadata      Metadata field name
     * @param int           $sPageId        TYPO3 page ID
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getPageMetadataById ( $sMetadata, $sPageId, $sLang = null )
    {
        // $oUtils = CmsxidUtils::getInstance();
        
        $oPage = CmsxidIdPage::getInstance($sPageId, $sLang);
        
        return $this->_getPageMetadataByPage( $oPage, $sMetadata );
    }
    
    /**
     * Returns the value of the passed metadata field on the passed page URL
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sMetadata  Metadata field name
     * 
     * @return string
     */
    protected function _getPageMetadataByPage ( $oPage, $sMetadata )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        if ( false !== ($sDummyMetadata = $this->_getDummyMetadata($oPage, $sMetadata)) ) {
            return $sDummyMetadata;
        }
        
        $oXml = $this->_getXmlByPage( $oPage );
        
        $sMetadataValue = false;
        
        if ( is_object($oXml) ) {
            $aXpathResults = $oXml->xpath( '/' . $oXml->getName() . '/metadata/' . $sMetadata );
            
            if ( $aXpathResults !== false && count($aXpathResults) == 1 ) {
                $sMetadataValue = $oUtils->getTextContentFromXmlObject( $aXpathResults[0] );
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
        $oUtils = CmsxidUtils::getInstance();
        
        $sContent = $oUtils->rewriteContentUrls( $sContent );
        $sContent = $oUtils->fixContentEncoding( $sContent );
        $sContent = $oUtils->decodeContentEntities( $sContent );
        $sContent = $oUtils->parseContentThroughSmarty( $sContent );
        
        return $sContent;
    }
    
    /**
     * Return dummy content
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sSnippet   Snippet name
     * 
     * @return string
     */
    protected function _getDummyContent ( $oPage, $sSnippet )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        // if ( $oUtils->getConfiguredDummyContentValue() ) {
            // return $oUtils->getDummyString($oPage, $sSnippet);
        // } else {
            return false;
        // }
    }
    
    /**
     * Return dummy content array
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $aNodes     Snippet name
     * 
     * @return string
     */
    protected function _getDummyContentArray ( $oPage, $aNodes = array() )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        // if ( $oUtils->getConfiguredDummyContentValue() ) {
            // if ( empty($aNodes) ) {
                // $aNodes = array(
                    // 'left',
                    // 'normal',
                    // 'right',
                    // 'border',
                // );
            // }
            
            // $aContentArray = array();
            
            // foreach ( $aNodes as $sSnippet ) {
                // $aContentArray[$sSnippet] = $oUtils->getDummyString($oPage, $sSnippet);
            // }
            
            // return $aContentArray;
        // } else {
            return false;
        // }
    }
    
    /**
     * Return dummy XML
     *
     * @param CmsxidPage    $oPage      Page object
     * 
     * @return string
     */
    protected function _getDummyXml ( $oPage )
    {
        return $this->_getDummyContentXml($oPage, 'page');
    }
    
    /**
     * Return dummy content XML
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sSnippet   Snippet name
     * 
     * @return string
     */
    protected function _getDummyContentXml ( $oPage, $sSnippet )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        // if ( $oUtils->getConfiguredDummyContentValue() ) {
            // $sDummyString = $oUtils->getDummyString($oPage, $sSnippet);
            
            // return $oUtils->getXmlObjectFromSource('<xml>' . $sDummyString . '</xml>');
        // } else {
            return false;
        // }
    }
    
    /**
     * Return dummy metadata
     *
     * @param CmsxidPage    $oPage      Page object
     * @param string        $sMetadata  Metadata key
     * 
     * @return string
     */
    protected function _getDummyMetadata ( $oPage, $sMetadata )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        // if ( $oUtils->getConfiguredDummyContentValue() ) {
            // return $oUtils->getDummyString($oPage, $sMetadata);
        // } else {
            return false;
        // }
    }
    
    /**
     * Public access to sanitizePageTitle helper function
     *
     * @param string    $sUrl   Page page to sanitize
     *
     * @return string
     */
    public function sanitizePagePath( $sUrl )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        return $oUtils->sanitizePageTitle( $sUrl );
    }
    
    /**
     * Public access to rewriteContentUrls helper function
     *
     * @param string    $sContent   Content to rewrite URLs in
     *
     * @return string
     */
    public function rewriteContentUrls($sContent)
    {
        $oUtils = CmsxidUtils::getInstance();
        
        return $oUtils->rewriteContentUrls( $sContent );
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
    public function getSnippetList( $sCustomPage = null, $blOnlyContentNodes = true )
    {
        $aNodes = array();
        
        if ( $blOnlyContentNodes ) {
            // Default TYPO3 column names
            $aNodes = array(
                'left',
                'normal', 'content',
                'right',
                'border',
            );
        }
        
        return $this->getContentArray( $sCustomPage, null, $aNodes );
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
        $oUtils = CmsxidUtils::getInstance();
        
        return $oUtils->rewriteContentUrls( $sContent );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidRewriteUrl( $sUrl, $iLangId = null, $blMultiLang = false )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        return $oUtils->rewriteContentUrls( $sContent );
    }
    
    /**
     * TOXID compatibility function
     */
    public function toxidEncodeTitle( $sUrl, $iLang = null )
    {
        $oUtils = CmsxidUtils::getInstance();
        
        return $oUtils->sanitizePageTitle( $sUrl );
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
