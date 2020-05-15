<?php
namespace wh\CmsConnect\Application;

use wh\CmsConnect\Application\Models\CmsPage;
use wh\CmsConnect\Application\Utils as CMSc_Utils;
use wh\CmsConnect\Application\Models\Cache;
use wh\CmsConnect\Application\Models\SessionCache;

/**
 * cmsconnect
 *
 * Terminology
 * - Local page = local page. An article page is a page. A category list view is a page. An implicit CMS seo page is a page.
 * - CMS page = full remote CMS page obtained when fetching a CMS URL.
 * - Content = part of a CMS page, identified by a node identifier
 *
 * All getter functions do not throw errors when cms pages/contents cannot
 * be found; rather, they return false. This enables smooth handling and prevents
 * hard-to-handle crashes in the templates.
 */
class CmsConnect
{
    function __construct()
    {
        static::init();
    }

    /**
     * Returns the processed text of the requested content on the requested CMS
     * page and the requested OXID language ID
     *
     * @param string        $sContentIdent  Content ident
     * @param string        $sCmsPagePath   CMS page path. When omitted, CMSconnect will attempt to determine from current local SEO page path.
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getContent($sContentIdent, $sCmsPagePath = null, $sLang = null)
    {
        if ($sCmsPagePath === null) {
            $oCmsPage = new CmsPage\Path\Implicit($sLang);
        } else {
            $oCmsPage = new CmsPage\Path($sCmsPagePath, $sLang);
        }

        return $oCmsPage->getContent($sContentIdent);
    }

    /**
     * Returns the processed text of the requested content on the requested CMS
     * page and the requested OXID language ID
     *
     * @param string        $sContentIdent  Content ident
     * @param int           $sCmsPageId     CMS page ID
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getContentById($sContentIdent, $sCmsPageId, $sLang = null)
    {
        $oCmsPage = new CmsPage\Id($sCmsPageId, $sLang);

        return $oCmsPage->getContent($sContentIdent);
    }

    /**
     * Returns an array containing the text content of all content nodes for the requested CMS page and OXID language ID.
     * Specific nodes can be returned by passing the last argument.
     *
     * @param string        $sCmsPagePath       CMS page path. When omitted, CMSconnect will attempt to determine from current local SEO page.
     * @param int|string    $sLang              OXID language ID/abbreviation.
     * @param string[]      $aContentIdents     List of contents to return (empty array for all: default)
     *
     * @return string[]
     */
    public function getContentArray($sCmsPagePath = null, $sLang = null, $aContentIdents = [])
    {
        if ($sCmsPagePath === null) {
            $oCmsPage = new CmsPage\Path\Implicit($sLang);
        } else {
            $oCmsPage = new CmsPage\Path($sCmsPagePath, $sLang);
        }

        return $oCmsPage->getContentArray($aContentIdents);
    }

    /**
     * Returns an array containing the text content of all content nodes for the requested CMS page and OXID language ID.
     * Specific nodes can be returned by passing the last argument.
     *
     * @param int           $sCmsPageId         CMS page ID
     * @param int|string    $sLang              OXID language ID/abbreviation.
     * @param string[]      $aContentIdents     List of contents names to return (empty array for all: default)
     *
     * @return string[]
     */
    public function getContentArrayById($sCmsPageId, $sLang = null, $aContentIdents = [])
    {
        $oCmsPage = new CmsPage\Id($sCmsPageId, $sLang);

        return $oCmsPage->getContentArray($aContentIdents);
    }

    /**
     * Returns the full XML object for the requested CMS page and OXID language ID.
     *
     * @param int           $sCmsPagePath   CMS page path. When omitted, CMSconnect will attempt to determine from current local SEO page.
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return \SimpleXMLElement
     */
    public function getXml($sCmsPagePath = null, $sLang = null)
    {
        if ($sCmsPagePath === null) {
            $oCmsPage = new CmsPage\Path\Implicit($sLang);
        } else {
            $oCmsPage = new CmsPage\Path($sCmsPagePath, $sLang);
        }

        return $oCmsPage->getXml();
    }

    /**
     * Returns the full XML object for the requested CMS page and OXID language ID.
     *
     * @param int           $sCmsPageId     CMS page ID
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return \SimpleXMLElement
     */
    public function getXmlById($sCmsPageId, $sLang = null)
    {
        $oCmsPage = new CmsPage\Id($sCmsPageId, $sLang);

        return $oCmsPage->getXml();
    }

    /**
     * Returns an XML object for the requested content, CMS page and OXID language ID
     *
     * @param string        $sContentIdent      Content ident
     * @param int           $sCmsPagePath       CMS page path. When omitted, CMSconnect will attempt to determine from current local SEO page.
     * @param int|string    $sLang              OXID language ID/abbreviation.
     *
     * @return \SimpleXMLElement
     */
    public function getContentXml($sContentIdent = null, $sCmsPagePath = null, $sLang = null)
    {
        if ($sCmsPagePath === null) {
            $oCmsPage = new CmsPage\Path\Implicit($sLang);
        } else {
            $oCmsPage = new CmsPage\Path($sCmsPagePath, $sLang);
        }

        return $oCmsPage->getContentXml($sContentIdent);
    }

    /**
     * Returns an XML object for the requested content, CMS page and OXID language ID
     *
     * @param string        $sContentIdent      Content ident
     * @param int           $sCmsPageId         CMS page ID
     * @param int|string    $sLang              OXID language ID/abbreviation.
     *
     * @return \SimpleXMLElement
     */
    public function getContentXmlById($sContentIdent = null, $sCmsPageId = null, $sLang = null)
    {
        $oCmsPage = new CmsPage\Id($sCmsPageId, $sLang);

        return $oCmsPage->getContentXml($sContentIdent);
    }

    /**
     * @deprecated
     */
    public function getPageMetadata($sField, $sCmsPagePath = null, $sLang = null)
    {
        return $this->getMetadata($sField, $sCmsPagePath, $sLang);
    }
    /**
     * Returns the metadata field value for the passed metadata field name of the
     * requested CMS page and the requested OXID language ID
     *
     * @param string        $sField         Metadata field name
     * @param string        $sCmsPagePath   CMS page path. When omitted, CMSconnect will attempt to determine from current local SEO page.
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getMetadata($sField, $sCmsPagePath = null, $sLang = null)
    {
        if ($sCmsPagePath === null) {
            $oCmsPage = new CmsPage\Path\Implicit($sLang);
        } else {
            $oCmsPage = new CmsPage\Path($sCmsPagePath, $sLang);
        }

        return $oCmsPage->getMetadata($sField);
    }

    /**
     * @deprecated
     */
    public function getPageMetadataById($sField, $sCmsPageId, $sLang = null)
    {
        return $this->getMetadataById($sField, $sCmsPageId, $sLang);
    }

    /**
     * Returns the metadata field value for the passed metadata field name of the
     * requested CMS page and the requested OXID language ID
     *
     * @param string        $sField         Metadata field name
     * @param int           $sCmsPageId     CMS page ID
     * @param int|string    $sLang          OXID language ID/abbreviation.
     *
     * @return string
     */
    public function getMetadataById($sField, $sCmsPageId, $sLang = null)
    {
        $oCmsPage = new CmsPage\Id($sCmsPageId, $sLang);

        return $oCmsPage->getMetadata($sField);
    }

    /**
     * Alias of sanitizePageTitle()
     *
     * @see CmsConnect::preparePagePath()
     *
     * @param string        $sUrl       URL to sanitize
     * @param int|string    $sLang      OXID language ID/abbreviation. Defaults to current language.
     *
     * @return string
     */
    public function preparePagePath($sUrl, $sLang = null)
    {
        return $this->sanitizePageTitle($sUrl, $sLang);
    }

    /**
     * @param string        $sUrl       URL to sanitize
     * @param int|string    $sLang      OXID language ID/abbreviation. Defaults to current language.
     *
     * @return string
     */
    public function sanitizePageTitle($sUrl, $sLang = null)
    {
        return CMSc_Utils::sanitizePageTitle($sUrl, $sLang);
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
        return $this->rewriteTextContentLinks($sContent);
    }
    public function rewriteTextContentLinks($sContent)
    {
        return CMSc_Utils::rewriteTextContentLinks($sContent);
    }

    /**
     * Public access to rewriteUrl helper function
     *
     * @param string    $sUrl   URL to rewrite
     *
     * @return string
     */
    public function rewriteUrl($sUrl)
    {
        return CMSc_Utils::rewriteUrl($sUrl);
    }

    /**
     * TOXID compatibility function
     */
    public function getXmlObject($sPage = null, $sContentIdent = null)
    {
        if ($sContentIdent !== null) {
            return $this->getContentXml($sContentIdent, $sPage);
        } else {
            return $this->getXml($sPage);
        }
    }

    /**
     * TOXID compatibility function
     */
    public function getCmsSnippet($sContentIdent, $sLang = null, $sPage = null)
    {
        return $this->getCmsContentIdent($sContentIdent, $sLang, $sPage);
    }

    /**
     * TOXID compatibility function
     */
    public function getContentList($sCustomPage = null, $blOnlyContentNodes = true)
    {
        $aNodes = array();

        if ($blOnlyContentNodes) {
            // Default CMS column names
            $aNodes = array('left', 'normal', 'content', 'right', 'border');
        }

        return $this->getContentArray($sCustomPage, null, $aNodes);
    }

    /**
     * TOXID compatibility function
     */
    public function getCmsContentIdent(
        $sContentIdent = null,
        $blMultiLang = false,
        $sCustomPage = null
    ) {
        return $this->getContent($sContentIdent, $sCustomPage);
    }

    /**
     * TOXID compatibility function
     */
    public function toxidRewriteUrls($sContent, $iLangId = null, $blMultiLang = false)
    {
        return CMSc_Utils::rewriteTextContentLinks($sContent);
    }

    /**
     * TOXID compatibility function
     */
    public function toxidRewriteUrl($sUrl, $iLangId = null, $blMultiLang = false)
    {
        return CMSc_Utils::rewriteUrl($sUrl);
    }

    /**
     * TOXID compatibility function
     */
    public function toxidEncodeTitle($sUrl, $iLang = null)
    {
        return $this->sanitizePageTitle($sUrl, $iLang);
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
    public function getHttpCode($sUrl = null)
    {
        return 200;
    }

    protected static $_blCMScInitialized = false;

    /**
     * This method runs the cache initialization. It should be called from every user-facing API
     * function to make sure the cache has been loaded before any requests are made.
     * It is idempotent.
     */
    protected static function init()
    {
        if (!static::$_blCMScInitialized) {
            static::$_blCMScInitialized = true;

            Cache\LocalPages::get()->init();
            Cache\CmsPages::get()->init();

            $start = microtime(true);

            $aCmsPages = Cache\LocalPages::get()->getCurrentLocalPageCmsPages();

            $aPagesToFetch = [];

            if (count($aCmsPages)) {
                foreach ($aCmsPages as $oCmsPage) {
                    $oHttpResult = Cache\CmsPages::get()->fetchHttpResult($oCmsPage);

                    if ($oHttpResult) {
                        SessionCache::set('results', $oCmsPage->getSessionCacheKey(), $oHttpResult);
                    } else {
                        $aPagesToFetch[] = $oCmsPage;
                    }
                }
            }

            // echo "<pre>Pages to fetch";
            // var_dump($aPagesToFetch);
            // echo "</pre>";

            if (count($aPagesToFetch)) {
                $aRequests = [];
                foreach ($aPagesToFetch as $i => $oCmsPage) {
                    $aRequests[] = $oCmsPage->getHttpRequest();
                }

                $aResults = CMSc_Utils::httpMultiRequest($aRequests);

                // $aResults = [];
                // foreach ( $aRequests as $i => $aRequest ) {
                // list($aResult) = CMSc_Utils::httpMultiRequest([$aRequest]);
                // $aResults[] = $aResult;
                // }

                $time = microtime(true) - $start;

                // echo "<br>Time: " . $time . " s<br>";

                foreach ($aPagesToFetch as $i => $oCmsPage) {
                    SessionCache::set('results', $oCmsPage->getSessionCacheKey(), $aResults[$i]);
                    Cache\CmsPages::get()->saveHttpResult($oCmsPage, $aResults[$i]);
                }
            }
        }
    }
}
