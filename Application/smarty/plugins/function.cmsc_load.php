<?php

use \wh\CmsConnect\Application\Models\CmsPage;

/**
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File: function.cmsc_load.php
 * Type: string, html
 * Name: cmsc_load
 * Purpose: Fetch requested content snippet (defaults to 'normal') from requested page (by
 * relative path or by ID) in the requested language (optional); either
 * display (default) or assign (if assign= is set)
 *
 * Returns a string:
 * [{ cmsc_load page="service/legal_information" content="normal" }]
 * [{ cmsc_load page="service/legal_information" content="normal" lang=2 }]
 * [{ cmsc_load page="service/legal_information" content="normal" lang="de" }]
 * [{ cmsc_load page="service/legal_information" content="normal" lang="de" assign="sLegalInfoContent" }]
 *
 * Returns an array:
 * [{ cmsc_load page="service/legal_information" content="left,right" }]
 *
 * Returns a string:
 * [{ cmsc_load id=213 content="normal" }]
 * [{ cmsc_load id=213 content="normal" lang="de" }]
 * ...
 *
 * -------------------------------------------------------------
 *
 * @param array     $aParams    Parameters
 * @param Smarty    &$oSmarty   Clever simulation of a method
 *
 * @return string|null
 */
function smarty_function_cmsc_load ( $aParams, &$oSmarty )
{
    // Check requested language
    $sLang      =    ( array_key_exists('lang', $aParams) && $aParams['lang'] !== false ) 
                        ? $aParams['lang']
                        : null
                ;
    
    // Check requested content snippet (specific or all)
    if ( array_key_exists('content', $aParams) ) {
        if ( strpos($aParams['content'], ',') ) {
            $mContent = explode(',', $aParams['content']);
        } else {
            $mContent = $aParams['content'];
        }
    }
                
    $blXml = ( array_key_exists('type', $aParams) && $aParams['type'] == 'xml' );
    
    $mReturn = false;
    
    // If neither page nor id identifier is supplied, it's still possible the user is on a CMS page,
    // which will be handled by the CMSconnect functions
    if ( array_key_exists('page', $aParams) ) {
        $oPage = new CmsPage\Path($aParams['page'], $sLang);
    } elseif ( array_key_exists('id', $aParams) ) {
        $oPage = new CmsPage\Id($aParams['id'], $sLang);
    } else {
        $oPage = new CmsPage\Path\Implicit($sLang);
    }
    
    if ( $blXml ) {
        if ( $mContent && is_string($mContent) ) {
            $mReturn = $oPage->getContentXml($mContent);
        } else {
            $mReturn = $oPage->getXml();
        }
    } else {
        if ( is_string($mContent) ) {
            $mReturn = $oPage->getContent($mContent);
        } else {
            $mReturn = $oPage->getContentArray($mContent);
        }
    }
    
    if ( $aParams['wrap'] ) {
        $deArray = false;
        if ( !is_array($mReturn) ) {
            $mReturn = array($mReturn);
            $deArray = true;
        }
        
        $mReturn = array_map( function ($el) {
            return '<div class="cms-content">' . $el . '</div>';
        }, $mReturn);
        
        if ( $deArray ) {
            $mReturn = $mReturn[0];
        }
    }
    
    if ( array_key_exists('assign', $aParams) ) {
        $oSmarty->assign( $aParams['assign'], $mReturn );
    } else {
        if ( $mReturn ) {
            return $mReturn;
        }
    }
}