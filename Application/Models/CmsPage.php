<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Models;

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Utils as CMSc_Utils;
use \wh\CmsConnect\Application\Models\Cache;
use \wh\CmsConnect\Application\Models\SessionCache;
use \t as t;

/**
 * CMSc_CmsPage
 */
abstract class CmsPage implements \Serializable
{
    /**
     * @var string
     */
    protected $_sLang = null;
        
    /**
     * @var array
     */
    protected $_aGetParams = [];
        
    /**
     * @var array
     */
    protected $_aPostParams = [];

    /**
     * @var string
     */
    protected $_sType = null;
    
    /**
     * __construct
     */
    function __construct ()
    {
        // throw new Exception('This function must be overwritten.');
    }
    
    /**
     * Setter method for the language of this content
     *
     * @param string        $sLang          Language identifier
     *
     * @return string
     */
    public function setLang ($sLang)
    {
        if ( empty($sLang) ) {
            // $sLang = null;
            
            // Hard-code the language HERE. Otherwise "null" will be committed to cache, meaning
            // that upon unserialization, the language will be whatever is the current language
            // at that time.
            $sLang = Registry::getLang()->getBaseLanguage();
        }
        
        $this->_sLang = $sLang;
    }
    
    /**
     * Getter method for the language of this content
     *
     * @return string
     */
    public function getLang ()
    {
        return $this->_sLang;
    }
    
    /**
     * Returns the type identifier for the current content object. This is essentially a variable
     * set in the child classes
     *
     * @return string
     */
    public function getType ()
    {
        return $this->_sType;
    }
    
    /**
     * Returns the text value of the requested content
     *
     * @param   string      $sContentIdent      Content identifier
     *
     * @return string
     */
    public function getContent ($sContentIdent)
    {
        $oXml = $this->_getXmlObject();
        $sContent = false;

        if ( $oXml instanceof \SimpleXMLElement ) {
            $aNodes = CMSc_Utils::safeExecuteXPath($oXml, '/' . $oXml->getName() . '/content/' . $sContentIdent );

            if ( $aNodes !== false && count($aNodes) ) {
                $oNodeXml = $aNodes[0];

                if ($oNodeXml instanceof \SimpleXMLElement) {
                    $sContent = CMSc_Utils::getTextContentFromXmlObject( $oNodeXml );
                    $sContent = $this->_processTextContent( $sContent );
                }
            }
        }
        
        return $sContent;
    }
    
    /**
     *
     * 
     * @param string[]      $aContentIdents     List of content identifiers
     *
     * @return string[]
     */
    public function getContentArray ($aContentIdents)
    {
        // Invalid parameter, return nothing.
        // TODO: throw exception
        if ( !is_array($aContentIdents) && $aContentIdents !== false && $aContentIdents !== null ) {
            return array();
        }
        
        // Default to return all nodes. Covers false and null as well.
        if ( empty($aContentIdents) ) {
            $aContentIdents = array();
        }
        
        $oXml = $this->_getXmlObject();
        
        $aContents = array();
        
        // Check if returned object is actually valid and has not returned an error,
        // else return empty array.
        if ( is_object($oXml) ) {
            $aXpathContents = CMSc_Utils::safeExecuteXPath($oXml, '/' . $oXml->getName() . '/content');
            
            if ( $aXpathContents !== false && count($aXpathContents) ) {
                foreach ( $aXpathContents[0] as $sContentIdent => $oCmsPageXml ) {
                    if ( !count($aContentIdents) || in_array($sContentIdent, $aContentIdents) ) {
                        $sTextContent = $this->getContent($sContentIdent);
                        
                        if ( $sTextContent ) {
                            $aContents[$sContentIdent] = $sTextContent;
                        }
                    }
                }
            }
        }
        
        return $aContents;
    }
    
    /**
     * Returns the full XML object of this CMS page. CDATA tags are removed, which might cause issues on parsing.
     *
     * @return \SimpleXMLElement
     */
    public function getXml ()
    {
        $sXml = $this->_getXmlSource();
        $sXml = CMSc_Utils::unwrapCDATA( $sXml );
        $sXml = CMSc_Utils::fixXmlSourceEntities( $sXml );
        
        $oXml = CMSc_Utils::createXmlObjectFromSource( $sXml );
        
        return $oXml;
    }
    
    /**
     * Fetches the requested content (unprocessed except for CDATA unwrapping
     * and XML entity fixing) and returns a SimpleXML object created from that content.
     *
     * @param string        $sContentIdent      Content to fetch and return as SimpleXML
     *
     * @return \SimpleXMLElement
     */
    public function getContentXml ($sContentIdent)
    {
        $oXml = $this->_getXmlObject();
        
        $oReturnXml = false;
        
        if ( is_object($oXml) ) {
            $aContents = CMSc_Utils::safeExecuteXPath($oXml, '/' . $oXml->getName() . '/content/' . $sContentIdent);
            
            if ( $aContents !== false && count($aContents) ) {
                $oCmsPageXml = $aContents[0];
                
                $sContentSource = CMSc_Utils::unwrapCDATA( $oCmsPageXml->asXml() );
                $sContentSource = CMSc_Utils::fixXmlSourceEntities( $sContentSource );
                
                $oReturnXml = CMSc_Utils::createXmlObjectFromSource( $sContentSource );
            }
        }
        
        return $oReturnXml;
    }
    
    /**
     * Returns this CMS page's SimpleXML object obtained directly from the unprocessed XML
     * source.
     * 
     * @return \SimpleXMLElement
     */
    protected function _getXmlObject ()
    {
        $sXml = $this->_getXmlSource();

        $oXml = CMSc_Utils::createXmlObjectFromSource($sXml);
        
        return $oXml;
    }
    
    /**
     * Returns this CMS page's unprocessed XML source, as returned by the CMS.
     * 
     * @return string
     */
    protected function _getXmlSource ()
    {
        class_exists('t') && t::s(__METHOD__);
        
        $oHttpResult = $this->getHttpResult();
        
        // Return an empty string so as not to break anything upstream
        $sXml = is_object($oHttpResult) ? $oHttpResult->content : '';
        
        class_exists('t') && t::e(__METHOD__);
        
        return $sXml;
    }
    
    public function getHttpResult ()
    {
        if ( !$this->getUrl() ) {
            $oHttpResult = false;
        } elseif ( CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_ENABLE_TEST_CONTENT) ) {
            $oHttpResult = $this->getTestContentXmlSource();
        } else {
            $blIsCacheable = $this->isCacheable();
            $sSessionCacheKey = $this->getSessionCacheKey();
            
            if ( $blIsCacheable ) {
                Cache\LocalPages::get()->registerCmsPage($this);
            }
            
//             var_dump("Retrieving " . $sSessionCacheKey . " from session cache");
            $oHttpResult = SessionCache::get('results', $sSessionCacheKey);
            
            // No result so far and caching enabled for this content, attempt to read from file cache
            if ( !is_object($oHttpResult) && $blIsCacheable ) {
//                 var_dump("Retrieving " . $this->getUrl() . " from file cache");
                
                // This is URL-based. We want our cache to be dumb; in turn, we have
                // to be smart about which URL to pass it (see above)
                $oHttpResult = Cache\CmsPages::get()->fetchHttpResult($this);
            }
            
            // Still no result, fetch from remote
            if ( !is_object($oHttpResult) ) {
//                 var_dump("No cache result, fetching " . $this->getUrl() . " from remote");
                
                // If false, we need to fetch from remote
                $oHttpResult = $this->fetchHttpResultFromRemote();
                
                if ( $blIsCacheable ) {
//                     var_dump("Saving " . $this->getUrl() . " to file cache");
                    Cache\CmsPages::get()->saveHttpResult($this, $oHttpResult);
                }
            }
            
            // Save to session cache
//             var_dump("Saving " . $sSessionCacheKey . " to session cache");
            SessionCache::set('results', $sSessionCacheKey, $oHttpResult);
        }
        
        return $oHttpResult;
    }
    
    public function fetchHttpResultFromRemote()
    {
        return CMSc_Utils::httpMultiRequest( [$this->getHttpRequest()] )[0];
    }
    
    /**
     * Return a request array for this cms page as might be
     * passed to CMSc_Utils::httpMultiRequest()
     */
    public function getHttpRequest ()
    {
        $aRequest = [
            'url' => $this->getUrl(),
        ];
        
        if ( $this->isPostPage() ) {
            $aRequest['method'] = 'post';
            $aRequest['params'] = $this->getPostParams();
        }
        
        return $aRequest;
    }
    
    /**
     * Returns the value of the passed metadata field
     *
     * @param string    $sField      Metadata field name
     * 
     * @return string
     */
    public function getMetadata ($sField)
    {
        $oXml = $this->_getXmlObject();
        
        $sValue = false;
        
        if ( is_object($oXml) ) {
            $aXpathResults = CMSc_Utils::safeExecuteXPath($oXml, '/' . $oXml->getName() . '/metadata/' . $sField);
            
            if ( $aXpathResults !== false && count($aXpathResults) == 1 ) {
                $sValue = CMSc_Utils::getTextContentFromXmlObject( $aXpathResults[0] );
            }
        }
        
        return $sValue;
    }
    
    /**
     * Returns the breadcrumbs XML object
     *
     * @return string
     */
    public function getBreadcrumb ()
    {
        $oXml = $this->_getXmlObject();
        
        $mValue = false;
        
        if ( is_object($oXml) && $oXml->breadcrumbs ) {
            $mValue = $oXml->breadcrumbs;
        }
        
        return $mValue;
    }
    
    /**
     * Returns the navigation HTML
     *
     * @return string
     */
    public function getNavigation ()
    {
        $mValue = false;
        
        $oXml = $this->_getXmlObject();
        if ( is_object($oXml) ) {
            $aNode = CMSc_Utils::safeExecuteXPath($oXml, '/' . $oXml->getName() . '/navigation');
            
            if ( $aNode !== false && count($aNode) ) {
                $mValue = CMSc_Utils::getTextContentFromXmlObject( $aNode[0] );
                $mValue = $this->_processTextContent( $mValue );
            }
        }
        
        return $mValue;
    }
    
    /**
     * Processes text content for user display
     *
     * @param string    $sContent       Content to process
     * 
     * @return string
     */
    protected function _processTextContent ( $sContent )
    {
        $sContent = CMSc_Utils::rewriteTextContentLinks( $sContent );
        $sContent = CMSc_Utils::fixTextContentEncoding( $sContent );
//        $sContent = CMSc_Utils::decodeTextContentEntities( $sContent );
        $sContent = CMSc_Utils::parseTextContentThroughSmarty( $sContent );
        
        return $sContent;
    }
    
    /**
     * Returns an the test content XML source defined in the module options.
     * 
     * @return object
     */
    protected function getTestContentXmlSource ()
    {
        class_exists('t') && t::s(__METHOD__);
        
        $sTestContent = CMSc_Utils::getConfigValue(CMSc_Utils::CONFIG_KEY_TEST_CONTENT);
        
        if ( !$sTestContent ) {
            $sTestContent = CMSc_Utils::getDefaultTestContent();
        }
        
        class_exists('t') && t::e(__METHOD__);
        
        return $sTestContent;
    }
    
    /**
     * Checks if this CMS page is the page called implicitly on the SEO page
     *
     * @return bool
     */
    public function isImplicit ()
    {
        return false;
    }
    
    public function isPostPage ()
    {
        return (bool) count($this->getPostParams());
    }
    
    /**
     */
    public function setGetParam ($sKey, $mVal)
    {
        $this->_aGetParams[$sKey] = $mVal;
    }
    
    /**
     */
    public function setGetParams ($aParams)
    {
        foreach ( $aParams as $sKey => $mVal ) {
            $this->setGetParam($sKey, $mVal);
        }
    }
    
    /**
     */
    public function unsetGetParam ($sKey)
    {
        unset($this->_aGetParams[$sKey]);
    }
    
    /**
     */
    public function unsetGetParams ($aParams)
    {
        foreach ( $aParams as $sKey ) {
            $this->unsetGetParam($sKey);
        }
    }
    
    /**
     */
    public function getGetParam ($sKey)
    {
        return $this->_aGetParams[$sKey];
    }
    
    /**
     */
    public function getGetParams ()
    {
        return $this->_aGetParams;
    }
    
    /**
     */
    public function setPostParam ($sKey, $mVal)
    {
        $this->_aPostParams[$sKey] = $mVal;
    }
    
    /**
     */
    public function setPostParams ($aParams = [])
    {
        if (!$aParams || !is_array($aParams)) {
            return;
        }
        
        foreach ( $aParams as $sKey => $mVal ) {
            $this->setPostParam($sKey, $mVal);
        }
    }
    
    /**
     */
    public function unsetPostParam ($sKey)
    {
        unset($this->_aPostParams[$sKey]);
    }
    
    /**
     */
    public function unsetPostParams ($aParams)
    {
        foreach ( $aParams as $sKey ) {
            $this->unsetPostParam($sKey);
        }
    }
    
    /**
     */
    public function getPostParam ($sKey)
    {
        return $this->_aPostParams[$sKey];
    }
    
    /**
     */
    public function getPostParams ()
    {
        return $this->_aPostParams;
    }
    
    /**
     * Returns whether or not this page can be cached in the CmsPageCache
     */
    public function isCacheable ()
    {
        return true;
    }
    
    /**
     * Returns the cache key for the SessionCache
     */
    public function getSessionCacheKey ()
    {
        return $this->getIdent();
    }
    
    /**
     * Returns the cache key for the CmsPages cache
     * 
     * @return string
     */
    public function getCacheKey ()
    {
        return $this->getIdent();
    }
    
    /**
     *
     */
    public function getIdent ()
    {
        return md5($this->getUrl() . $this->isPostPage() . serialize($this->getGetParams()) . serialize($this->getPostParams()));
    }
    
    public function serialize ()
    {
        // return serialize($this);
        $a = serialize([
            'sLang' => $this->getLang(),
            'aGetParams' => $this->getGetParams(),
            'aPostParams' => $this->getPostParams(),
        ]);
        
        // echo "<pre>";
        // var_dump(__METHOD__, $a);
        // echo (new Exception)->getTraceAsString();
        // echo "</pre>";
        
        return $a;
    }
    
    public function unserialize ($data)
    {
        $aData = unserialize($data);
        
        $this->setLang($aData['sLang']);
        $this->setGetParams($aData['aGetParams']);
        $this->setPostParams($aData['aPostParams']);
    }
    
    /**
     * @overridable
     */
    public function getPagePath ()
    {
        return null;
    }
    
    /**
     * @overridable
     */
    public function getPageId ()
    {
        return null;
    }

    abstract public function getUrl();
}