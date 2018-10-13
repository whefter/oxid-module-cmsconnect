<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

namespace wh\CmsConnect\Application\Controllers;

use \wh\CmsConnect;
use \wh\CmsConnect\Core;
use \wh\CmsConnect\Models;
use \wh\CmsConnect\Application\Models\CmsPage;
use \wh\CmsConnect\Application\Utils as CMSc_Utils;

use \OxidEsales\Eshop\Core\Registry as Registry;
use \OxidEsales\Eshop\Core\Request as Request;

/**
 * cmsconnect_frontend
 */
class frontend extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Constructor. Sets the $_sThisTemplate to the current class name with a .tpl suffix.
     
     * @return void
     */   
    function __construct()
    {
        parent::__construct();
        
        $this->_sThisTemplate = 'modules/wh/cmsconnect/frontend.tpl';
    }
    
    /**
     * Template variable getter. Overwrites the parent function.
     *
     * Returns the title of the current CMS page from its metadata.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_getCmsPage()->getMetadata('title');
    }
    
    /**
     * Template variable getter. Overwrites the parent function.
     *
     * Returns the description of the current CMS page from its metadata.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $sDescription = $this->_getCmsPage()->getMetadata('description');
        $sDescription = strip_tags( $sDescription );
        
        return $this->_prepareMetaDescription( $sDescription );
    }

    public function getCmsPage ()
    {
        return $this->_getCmsPage();
    }
    
    protected function _getCmsPage ()
    {
        if (CMSc_Utils::getCurrentLocalPageSeoPath()) {
            return new CmsPage\Path\Implicit();
        } else {
            $oxRequest = Registry::get(Request::class);
            $sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();

            if ($id = $oxRequest->getRequestParameter('pageId')) {
                return new CmsPage\Id($id, $sLang);
            } elseif ($id = $oxRequest->getRequestParameter('id')) {
                // Deprecated (make sure we don't interfere with OXID variables)
                return new CmsPage\Id($id, $sLang);
            } else if ($page = $oxRequest->getRequestParameter('pagePath')) {
                return new CmsPage\Path($page, $sLang);
            } else if ($page = $oxRequest->getRequestParameter('page')) {
                // Deprecated (make sure we don't interfere with OXID variables)
                return new CmsPage\Path($page, $sLang);
            }
        }

        return new CmsPage\Path('/');
    }
    
    /**
     * Template variable getter. Overwrites the parent function.
     *
     * Returns the keywords of the current CMS page from its metadata.
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        $sKeywords = $this->_getCmsPage()->getMetadata('keywords');
        
        return $this->_prepareMetaKeyword( $sKeywords );
    }
    
    /**
     * Override parent function.
     */
    public function getBreadCrumb ()
    {
        $oXml = $this->_getCmsPage()->getBreadcrumb();

        $aCrumbs = [];
        
        if ( is_object($oXml) ) {
            foreach ( $oXml->crumb as $crumb ) {
                $aCrumbs[] = [
                    'link' => CMSc_Utils::rewriteUrl($crumb->url),
                    'title' => $crumb->title,
                ];
            }
        }
        
        return $aCrumbs;
    }
    
    /**
     * Returns the current page's navigation
     *
     * @return string
     */
    public function getNavigation ()
    {
        return $this->_getCmsPage()->getNavigation();
    }

    /**
     * collects _GET parameters used by eShop SEO and returns uri
     *
     * @return string
     */
    protected function _getSeoRequestParams()
    {
        $oxRequest = Registry::get(Request::class);

        $url = call_user_func_array('parent::_getSeoRequestParams', func_get_args());

        if ($id = $oxRequest->getRequestEscapedParameter('pageId')) {
            $url .= '&amp;pageId=' . $id;
        } elseif ($id = $oxRequest->getRequestEscapedParameter('id')) {
            // Legacy compatibility
            $url .= '&amp;pageId=' . $id;
        } elseif ($path = $oxRequest->getRequestEscapedParameter('pagePath')) {
            $url .= '&amp;pagePath=' . $path;
        } elseif ($path = $oxRequest->getRequestEscapedParameter('page')) {
            // Legacy compatibility
            $url .= '&amp;pagePath=' . $path;
        }

        return $url;
    }
}
