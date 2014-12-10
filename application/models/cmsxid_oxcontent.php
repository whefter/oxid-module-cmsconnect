<?php
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 * @copyright   2014 William Hefter
 */

/**
 * cmsxid_oxcontent
 */
class cmsxid_oxcontent extends cmsxid_oxcontent_parent
{
    /**
     * The content of this oxContent object as defined in the admin backend.
     * Saved here to allow it to be written back to database instead of the CMSxid fetched content.
     *
     * @var string
     */
    protected $_sOriginalContent = null;
    
    /**
     * Loads Content by using field oxloadid instead of oxid. We also use this function to ignore
     * the cache backend used by the EE version, since CMSxid handles caching.
     *
     * @param   string      $sLoadId    Content load ID
     *
     * @return bool
     */
    public function loadByIdent( $sLoadId )
    {
        $blRes = parent::loadByIdent($sLoadId);
        
        if ( $this->_hasCmsxidContent() ) {
            $this->_assignCmsxidContent( $this->_getCmsxidContent() );
        }
        
        return $blRes;
    }

    /**
     * Overrides the base load() function. Checks if the oxContent object has an assigned CMS page ID
     * if not, calls the parent class. If so, handles loading from CMSxid
     *
     * @param   string      $sOxid      OXID
     *
     * @return bool
     */
    public function load( $sOxid )
    {
        $blRes = parent::load($sOxid);
        
        if ( $this->_hasCmsxidContent() ) {
            $this->_assignCmsxidContent( $this->_getCmsxidContent() );
        }
        
        return $blRes;
    }

    /**
     * Overrides parent function; makes sure the CMSxid content is not saved to database.
     *
     * @return bool
     */
    public function save()
    {
        // if ( $this->_hasCmsxidContent() ) {
            // $sCurrentContent = $this->oxcontents__oxcontent->getRawValue();
            // $this->oxcontents__oxcontent->setValue( $this->_sOriginalContent, oxField::T_RAW );
        // }
        
        $blRes = parent::save();
        
        // if ( $this->_hasCmsxidContent() ) {
            // $this->_sOriginalContent = $this->oxcontents__oxcontent->getRawValue();
            // $this->oxcontents__oxcontent->setValue( $sCurrentContent, oxField::T_RAW );
        
            // unset($sCurrentContent);
        // }
        
        return $blRes;
    }

    /**
     * Fetches the page content from CMSxid and returns it. Page ID takes precedence over page name.
     *
     * @return string
     */
    protected function _getCmsxidContent()
    {
        $oCmsxid = $this->_getCmsxid();
        
        // First, try the page ID
        if ( ($sPageId = $this->_getCmsxidPageId()) !== false ) {
            if ( $sContent = $oCmsxid->getContentById('content', $sPageId) ) {
                return $sContent;
            }
        }
        
        if ( ($sPage = $this->_getCmsxidPage()) !== false ) {
            if ( $sContent = $oCmsxid->getContent('content', $sPage) ) {
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
    protected function _assignCmsxidContent( $sContent )
    {
        // $this->_sOriginalContent = $this->oxcontents__oxcontent->getRawValue();
        
        $this->oxcontents__oxcontent->setValue( $sContent, oxField::T_RAW );
    }

    /**
     * Checks if the current object has a CMSxid page ID assigned
     *
     * @return bool
     */
    protected function _hasCmsxidContent()
    {
        return ( $this->_getCmsxidPageId() !== false || $this->_getCmsxidPage() !== false );
    }

    /**
     * Returns the assigned CMS page, if there is one
     *
     * @return string|bool
     */
    protected function _getCmsxidPage()
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
    protected function _getCmsxidPageId()
    {
        $sPageId = $this->oxcontents__cmsxidpageid->rawValue;
        
        if ( $sPageId !== null && $sPageId !== false && $sPageId !== '' ) {
            return $sPageId;
        }
        
        return false;
    }

    /**
     * Returns an instance of Cmsxid
     *
     * @return object
     */
    protected function _getCmsxid()
    {
        return oxRegistry::get('oxViewConfig')->getCMSxid();
    }
}