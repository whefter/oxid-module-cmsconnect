[{ include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign }]

[{ assign var=oxConfig value=$oViewConf->getConfig() }]

<script type="text/javascript" src="[{ $oxConfig->getResourceUrl('js/libs/jquery.min.js', true) }]"></script>
[{*
<script type="text/javascript" src="[{ $oxConfig->getResourceUrl('js/libs/jquery-ui.min.js', true) }]"></script>
*}]

<script type="text/javascript">
$.noConflict();
(function ($) {
    var displayDemos = function () {
        $('.demo').each( function () {
            var $el     = $(this),
                $parent = $el.closest("fieldset[class!='demo']");
            
            var sBaseUrl    = $("[name*='aCMScBaseUrls']", $parent).val(),
                sPagePath   = $("[name*='aCMScPagePaths']", $parent).val(),
                sIdParam    = $("[name*='aCMScIdParams']", $parent).val(),
                sLangParam  = $("[name*='aCMScLangParams']", $parent).val(),
                sParams     = $("[name*='aCMScParams']", $parent).val();
            
            var sPathUrl    = sBaseUrl + '/' + sPagePath + '/[{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_DEMO_EXAMPLE" }]/?' + sParams,
                sIdUrl      = sBaseUrl + '/?' + sIdParam + '=123&' + sLangParam + '&' + sParams;
            
            sPathUrl    = sPathUrl.replace(/\/+/g, '/').replace(/^([\w-]+:)\/(\w)/, '$1//$2').replace(/[?&]$/, '');
            sIdUrl      = sIdUrl.replace(/\/+/g, '/').replace(/^([\w-]+:)\/(\w)/, '$1//$2').replace(/[?&]$/, '');
            
            $('.path td:last-child', $el).text( sPathUrl );
            $('.id td:last-child',   $el).text( sIdUrl );
        } );
    }
    
    $( displayDemos );
    $( function () {
        $( '[name^="editval"]' ).keyup( displayDemos );
    } );
})(jQuery);
</script>

[{ if $readonly }]
    [{ assign var="readonly" value="readonly disabled" }]
[{ else }]
    [{ assign var="readonly" value="" }]
[{ /if }]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]    
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{ $oViewConf->getActiveShopId() }]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="[{ $oViewConf->getActiveClassName() }]">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="language" value="[{ $actlang }]">
    
    <h1>
        [{ oxmultilang ident="cmsconnect_setup_main" }]
    </h1>
    
    [{ foreach from=$languages key=iLang item=oLang }]
        <fieldset>
            <legend>
                [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_SOURCE" }]:
                <b>
                    [{ $oLang->name }]
                </b>
            </legend>
            
            <br />
            
            <fieldset class="demo">
                <legend>
                    <b>
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_DEMO" }]
                    </b>
                </legend>
                
                <table>
                    <tr class="path">
                        <td>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_DEMO_PATH" }]:
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr class="id">
                        <td>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_DEMO_ID" }]:
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </fieldset>
            
            <br />
            
            <table>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScBaseUrls" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScBaseUrls][[{ $iLang }]]" value="[{ $aCMScBaseUrls.$iLang }]" size="75" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScBaseUrls_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScBaseSslUrls" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScBaseSslUrls][[{ $iLang }]]" value="[{ $aCMScBaseSslUrls.$iLang }]" size="75" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScBaseSslUrls_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScPagePaths" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScPagePaths][[{ $iLang }]]" value="[{ $aCMScPagePaths.$iLang }]" size="50" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScPagePaths_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScIdParams" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScIdParams][[{ $iLang }]]" value="[{ $aCMScIdParams.$iLang }]" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScIdParams_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScLangParams" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScLangParams][[{ $iLang }]]" value="[{ $aCMScLangParams.$iLang }]" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScLangParams_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScParams" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScParams][[{ $iLang }]]" value="[{ $aCMScParams.$iLang }]" size="50" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScParams_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_aCMScSeoIdents" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCMScSeoIdents][[{ $iLang }]]" value="[{ $aCMScSeoIdents.$iLang }]" />
                        [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_aCMScSeoIdents_HELP" }]
                    </td>
                </tr>
            </table>
        </fieldset>
        <br />
    [{ /foreach }]
    
    <fieldset>
        <legend>[{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_GENERAL" }]</legend>
        
        <table>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting" }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_HELP" }]
                </td>
                <td class="edittext">
                    <select name="editval[sCMScUrlRewriting]">
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_URL_REWRITING_PATH_ONLY"|constant }]" [{ if !$sCMScUrlRewriting || $sCMScUrlRewriting === "\wh\CmsConnect\Application\Utils::VALUE_URL_REWRITING_PATH_ONLY"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_PathOnly" }]
                        </option>
                        <!--
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_URL_REWRITING_ALL_CMS_URLS"|constant }]" [{ if $sCMScUrlRewriting === "\wh\CmsConnect\Application\Utils::VALUE_URL_REWRITING_ALL_CMS_URLS"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_AllUrls" }]
                        </option>
                        -->
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_URL_REWRITING_NONE"|constant }]" [{ if $sCMScUrlRewriting === "\wh\CmsConnect\Application\Utils::VALUE_URL_REWRITING_NONE"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_None" }]
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                </td>
                <td class="edittext">
                    <input type="hidden" name="editval[blCMScEnableTestContent]" value="0" />
                    <input type="checkbox" name="editval[blCMScEnableTestContent]" value="1" [{ if $blCMScEnableTestContent }]checked="checked"[{ /if }] />
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_blCMScEnableTestContent" }]
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_blCMScEnableTestContent_HELP" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                </td>
                <td class="edittext">
                    <input type="hidden" name="editval[blCMScSslDontVerifyPeer]" value="0" />
                    <input type="checkbox" name="editval[blCMScSslDontVerifyPeer]" value="1" [{ if $blCMScSslDontVerifyPeer }]checked="checked"[{ /if }] />
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_blCMScSslDontVerifyPeer" }]
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_blCMScSslDontVerifyPeer_HELP" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefault" args="\wh\CmsConnect\Application\Utils::CONFIG_DEFAULTVALUE_TTL"|constant }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefault_HELP" }]
                </td>
                <td class="edittext">
                    <input type="text" name="editval[sCMScTtlDefault]" value="[{ $sCMScTtlDefault }]" />
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefaultRnd" args="\wh\CmsConnect\Application\Utils::CONFIG_DEFAULTVALUE_TTL_RND"|constant }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefaultRnd_HELP" }]
                </td>
                <td class="edittext">
                    <input type="text" name="editval[sCMScTtlDefaultRnd]" value="[{ $sCMScTtlDefaultRnd }]" size="2" />%
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCurlConnectTimeout" args="\wh\CmsConnect\Application\Utils::CONFIG_DEFAULTVALUE_CURL_CONNECT_TIMEOUT"|constant }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCurlConnectTimeout_HELP" }]
                </td>
                <td class="edittext">
                    <input type="text" name="editval[sCMScCurlConnectTimeout]" value="[{ $sCMScCurlConnectTimeout }]" size="5" /> ms
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCurlExecuteTimeout" args="\wh\CmsConnect\Application\Utils::CONFIG_DEFAULTVALUE_CURL_EXECUTE_TIMEOUT"|constant }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCurlExecuteTimeout_HELP" }]
                </td>
                <td class="edittext">
                    <input type="text" name="editval[sCMScCurlExecuteTimeout]" value="[{ $sCMScCurlExecuteTimeout }]" size="5" /> ms
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine" }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_HELP" }]
                </td>
                <td class="edittext">
                    <select name="editval[sCMScLocalPageCacheEngine]">
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_AUTO"|constant }]" [{ if $sCMScLocalPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_AUTO"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_auto" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DISABLED"|constant }]" [{ if $sCMScLocalPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DISABLED"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_Disabled" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_OXIDFILECACHE"|constant }]" [{ if $sCMScLocalPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_OXIDFILECACHE"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_OxidFileCache" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DB"|constant }]" [{ if $sCMScLocalPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_DB"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_DB" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHED"|constant }]" [{ if $sCMScLocalPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHED"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_memcached" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHE"|constant }]" [{ if $sCMScLocalPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_LOCAL_PAGES_CACHE_ENGINE_MEMCACHE"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_memcache" }]
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine" }]:
                    [{ oxinputhelp ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_HELP" }]
                </td>
                <td class="edittext">
                    <select name="editval[sCMScCmsPageCacheEngine]">
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_AUTO"|constant }]" [{ if $sCMScCmsPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_AUTO"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_auto" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_OXIDFILECACHE"|constant }]" [{ if $sCMScCmsPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_OXIDFILECACHE"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_OxidFileCache" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHED"|constant }]" [{ if $sCMScCmsPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHED"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_memcached" }]
                        </option>
                        <option value="[{ "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHE"|constant }]" [{ if $sCMScCmsPageCacheEngine === "\wh\CmsConnect\Application\Utils::VALUE_CMS_PAGES_CACHE_ENGINE_MEMCACHE"|constant }]selected="selected"[{ /if }]>
                            [{ oxmultilang ident="CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_memcache" }]
                        </option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    
    <p>
        <input type="submit" class="edittext" id="oLockButton" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onclick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]>
    </p>

</form>

[{ include file="bottomnaviitem.tpl" }]
[{ include file="bottomitem.tpl" }]