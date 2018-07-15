<?php

use \OxidEsales\Eshop\Core\Registry as Registry;

use \wh\CmsConnect\Application\Models\CmsPage;

/**
 * Smarty plugin
 *
 * -------------------------------------------------------------
 * File: function.cmsc_load_async.php
 * Type: string, html
 * Name: cmsc_load_async
 * Purpose: Display the requested content snippet (defaults to 'normal') from the specified page asynchronously (via AJAX)
 *
 * [{ cmsc_load_async page="service/legal_information" content="normal" }]
 * [{ cmsc_load_async page="service/legal_information" content="normal" lang=2 }]
 * [{ cmsc_load_async page="service/legal_information" content="normal" lang="de" }]
 *
 * [{ cmsc_load_async id=213 content="normal" }]
 * [{ cmsc_load_async id=213 content="normal" lang="de" }]
 * ...
 *
 * -------------------------------------------------------------
 *
 * @param array     $aParams    Parameters
 * @param Smarty    &$oSmarty   Clever simulation of a method
 *
 * @return string|null
 */
function smarty_function_cmsc_load_async ( $aParams, &$oSmarty )
{
    // Check requested language
    $sLang  =   ( array_key_exists('lang', $aParams) && $aParams['lang'] !== false ) 
                ? $aParams['lang']
                : null
                ;
    
    // Check requested content snippet (specific or all)
    $sContent = $aParams['content'];
    
    if ( !$sContent ) {
        $sContent = 'normal';
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
    
    $oxConfig   = Registry::getConfig();
    $oxLang     = Registry::getLang();
    
    $oSmarty->assign('sUid',        uniqid());
    $oSmarty->assign('sContent',    $sContent);
    $oSmarty->assign('sMethod',     $sMethod);
    $oSmarty->assign('sIdentifier', $sIdentifier);
    $oSmarty->assign('sLang',       $oxLang->getBaseLanguage());
    $oSmarty->assign('sBaseUrl',    $oxConfig->getCurrentShopUrl());
            
    $mReturn = Registry::get("oxUtilsView")->parseThroughSmarty('[{ include file="modules/wh/cmsconnect/async_snippet.tpl" }]', 'cmsconnect_async_snippet');
    
    return $mReturn;
}