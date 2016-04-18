<?php
/**
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File: function.cmsxid_load_async.php
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
function smarty_function_cmsxid_load_async( $aParams, &$oSmarty )
{
    $oCMSxid = oxRegistry::get('oxViewConfig')->getCMSxid();
    
    // Check requested language
    $sLang  =   ( array_key_exists('lang', $aParams) && $aParams['lang'] !== false ) 
                ? $aParams['lang']
                : null
                ;
    
    // Check requested content snippet (specific or all)
    $sContent = $aParams['content'];
    
    if ( !$sContent ) {
        return '';
    }
    
    $sIdentifier    = null;
    $sMethod        = '';
    
    if ( array_key_exists('page', $aParams) ) {
        $sIdentifier    = $aParams['page'];
        $sMethod        = 'page';
    } elseif ( array_key_exists('id', $aParams) ) {
        $sIdentifier    = $aParams['id'];
        $sMethod        = 'id';
    }
    
    $oxConfig   = oxRegistry::getConfig();
    $oxLang     = oxRegistry::getLang();
    
    // $sCacheId = sha1($oxConfig->getShopId() . $oxLang->getBaseLanguage() . $sIdentifier . $sContent . $sMethod);
    $sCacheId = 'cmsxid_async_snippet';
    
    $oSmarty->assign('sUid',        uniqid());
    $oSmarty->assign('sContent',    $sContent);
    $oSmarty->assign('sMethod',     $sMethod);
    $oSmarty->assign('sIdentifier', $sIdentifier);
    $oSmarty->assign('sLang',       $sLang);
    $oSmarty->assign('sBaseUrl',    $oxConfig->getCurrentShopUrl());
            
    $mReturn = oxRegistry::get("oxUtilsView")->parseThroughSmarty('[{ include file="modules/cmsxid/async_snippet.tpl" }]', $sCacheId);
    
    return $mReturn;
}