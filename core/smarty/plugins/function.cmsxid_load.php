<?php
/**
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File: function.cmsxid_load.php
 * Type: string, html
 * Name: cmsxid_load
 * Purpose: Fetch requested content snippet (defaults to 'normal') from requested page (by
 * relative path or by ID) in the requested language (optional); either
 * display (default) or assign (if assign= is set)
 *
 * [{ cmsxid_load page="service/legal_information" content="normal" }]
 * [{ cmsxid_load page="service/legal_information" content="normal" lang=2 }]
 * [{ cmsxid_load page="service/legal_information" content="normal" lang="de" }]
 * [{ cmsxid_load page="service/legal_information" content="normal" lang="de" assign="sLegalInfoContent" }]
 * [{ cmsxid_load page="service/legal_information" nodes="left,right" }]
 *
 * [{ cmsxid_load id=213 content="normal" }]
 * [{ cmsxid_load id=213 content="normal" lang="de" }]
 * ...
 *
 * -------------------------------------------------------------
 *
 * @param array     $aParams    Parameters
 * @param Smarty    &$oSmarty   Clever simulation of a method
 *
 * @return string|null
 */
function smarty_function_cmsxid_load( $aParams, &$oSmarty )
{
    $oCMSxid = oxRegistry::get('oxViewConfig')->getCMSxid();
    
    // Check requested language
    $sLang      =    ( array_key_exists('lang', $aParams) && $aParams['lang'] !== false ) 
                        ? $aParams['lang']
                        : null
                ;
    
    // Check requested content snippet (specific or all)
    $sContent   =     ( array_key_exists('content', $aParams) && $aParams['content'] )
                        ? $aParams['content']
                        : ''
                ;
    
    // Check for specific nodes
    $aNodes     =     ( array_key_exists('nodes', $aParams) && $aParams['nodes'] )
                        ? explode(',', $aParams['nodes'])
                        : array()
                ;
                
    $blXml = ( array_key_exists('type', $aParams) && $aParams['type'] == 'xml' );
    
    $sPageIdentifier    = null;
    $sFunctionSuffix    = '';
    
    $mReturn = false;
    
    // If neither page nor id identifier is supplied, it's still possible the user is on a CMS page,
    // which will be handled by the CMSxid functions
    if ( array_key_exists('page', $aParams) ) {
        $sPageIdentifier = $aParams['page'];
    } elseif ( array_key_exists('id', $aParams) ) {
        $sPageIdentifier = $aParams['id'];
        $sFunctionSuffix = 'ById';
    }
    
    $mReturn =  $blXml
                    ?   (   $sContent
                                ? $oCMSxid->{ 'getContentXml' . $sFunctionSuffix }( $sContent, $sPageIdentifier, $sLang )
                                : $oCMSxid->{ 'getXml' . $sFunctionSuffix }( $sPageIdentifier, $sLang )
                        )
                    :   (   $sContent
                                ? $oCMSxid->{ 'getContent' . $sFunctionSuffix }( $sContent, $sPageIdentifier, $sLang )
                                : $oCMSxid->{ 'getContentArray' . $sFunctionSuffix }( $sPageIdentifier, $sLang, $aNodes )
                        )
                ;
    
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