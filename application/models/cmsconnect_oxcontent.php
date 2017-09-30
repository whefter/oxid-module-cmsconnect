<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014-2017 William Hefter
 */

/**
 * cmsconnect_oxcontent
 */
class cmsconnect_oxcontent extends cmsconnect_oxcontent_parent
{
    /**
     * The content of this oxContent object as defined in the admin backend.
     * Saved here to allow it to be written back to database instead of the CMSconnect fetched content.
     *
     * @var string
     */
    protected $_sOriginalContent = null;
    
    /**
     * Loads Content by using field oxloadid instead of oxid. We also use this function to ignore
     * the cache backend used by the EE version, since CMSconnect handles caching.
     *
     * @param   string      $sLoadId    Content load ID
     *
     * @return bool
     */
    public function loadByIdent( $sLoadId )
    {
        $blRes = parent::loadByIdent($sLoadId);
        
        if ( $this->_hasCMScContent() ) {
            $this->_assignCMSconnectContent( $this->_getCMSconnectContent() );
        }
        
        return $blRes;
    }

    /**
     * Overrides the base load() function. Checks if the oxContent object has an assigned CMS page ID
     * if not, calls the parent class. If so, handles loading from CMSconnect
     *
     * @param   string      $sOxid      OXID
     *
     * @return bool
     */
    public function load( $sOxid )
    {
        $blRes = parent::load($sOxid);
        
        if ( $this->_hasCMScContent() ) {
            $this->_assignCMSconnectContent( $this->_getCMSconnectContent() );
        }
        
        return $blRes;
    }

    /**
     * Overrides parent function; makes sure the CMSconnect content is not saved to database.
     *
     * @return bool
     */
    public function save()
    {
        // if ( $this->_hasCMScContent() ) {
            // $sCurrentContent = $this->oxcontents__oxcontent->getRawValue();
            // $this->oxcontents__oxcontent->setValue( $this->_sOriginalContent, oxField::T_RAW );
        // }
        
        $blRes = parent::save();
        
        // if ( $this->_hasCMScContent() ) {
            // $this->_sOriginalContent = $this->oxcontents__oxcontent->getRawValue();
            // $this->oxcontents__oxcontent->setValue( $sCurrentContent, oxField::T_RAW );
        
            // unset($sCurrentContent);
        // }
        
        return $blRes;
    }

    /**
     * Fetches the page content from CMSconnect and returns it. Page ID takes precedence over page name.
     *
     * @return string
     */
    protected function _getCMSconnectContent()
    {
        $oCMSconnect = $this->_getCMSconnect();
        
        // First, try the page ID
        if ( ($sPageId = $this->_getCMSc_CmsPageId()) !== false ) {
            if ( $sContent = $oCMSconnect->getContentById('normal', $sPageId) ) {
                return $sContent;
            }
        }
        
        if ( ($sPage = $this->_getCMSc_CmsPage()) !== false ) {
            if ( $sContent = $oCMSconnect->getContent('normal', $sPage) ) {
                return $sContent;
            }
        }
        
        return false;
    }

    /**
     * Assigns the passed content to the content field.
     *
     * @param   string      $sContent   Content
     *
     * @return void
     */
    protected function _assignCMSconnectContent( $sContent )
    {
        // $this->_sOriginalContent = $this->oxcontents__oxcontent->getRawValue();
        
        $this->oxcontents__oxcontent->setValue( $sContent, oxField::T_RAW );
    }

    /**
     * Checks if the current object has a CMSconnect page ID assigned
     *
     * @return bool
     */
    protected function _hasCMScContent()
    {
        return ( $this->_getCMSc_CmsPageId() !== false || $this->_getCMSc_CmsPage() !== false );
    }

    /**
     * Returns the assigned CMS page, if there is one
     *
     * @return string|bool
     */
    protected function _getCMSc_CmsPage()
    {
        $sPage = $this->oxcontents__cmsxidpage->rawValue;
        
        if ( $sPage !== null && $sPage !== false && (string)$sPage !== '' ) {
            return $sPage;
        }
        
        return false;
    }

    /**
     * Returns the assigned CMS page ID, if there is one
     *
     * @return string|bool
     */
    protected function _getCMSc_CmsPageId()
    {
        $sPageId = $this->oxcontents__cmsxidpageid->rawValue;
        
        if ( $sPageId !== null && $sPageId !== false && $sPageId !== '' ) {
            return $sPageId;
        }
        
        return false;
    }

    /**
     * Returns an instance of CMSconnect
     *
     * @return object
     */
    protected function _getCMSconnect()
    {
        return oxRegistry::get('oxViewConfig')->getCMSconnect();
    }
}