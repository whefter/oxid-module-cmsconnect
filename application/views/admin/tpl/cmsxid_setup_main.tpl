[{ include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign }]

[{ assign var=oxConfig value=$oViewConf->getConfig() }]

<script type="text/javascript" src="[{ $oxConfig->getResourceUrl('js/libs/jquery.min.js', true) }]"></script>
<script type="text/javascript" src="[{ $oxConfig->getResourceUrl('js/libs/jquery-ui.min.js', true) }]"></script>

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
        [{ oxmultilang ident="cmsxid_setup" }]
    </h1>
    
<script type="text/javascript">
(function ($) {
    var displayDemos = function () {
        $('.demo').each( function () {
            $el     = $(this);
            $parent = $el.closest('fieldset');
            
            sBaseUrl    = $("[name*='aCmsxidBaseUrls']", $parent).val();
            sPagePath   = $("[name*='aCmsxidPagePaths']", $parent).val();
            sIdParam    = $("[name*='aCmsxidIdParams']", $parent).val();
            sLangParam  = $("[name*='aCmsxidLangParams']", $parent).val();
            sParams     = $("[name*='aCmsxidParams']", $parent).val();
            
            $('.path span', $el).text( sBaseUrl + '/' + sPagePath + '/[{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_DEMO_EXAMPLE" }]/?' + sParams );
            $('.id span', $el).text( sBaseUrl + '/?' + sIdParam + '=123&' + sLangParam + '&' + sParams );
        } );
    }
    
    $( displayDemos );
    $( function () {
        $( '[name^="editval"]' ).keyup( displayDemos );
    } );
})(jQuery);
</script>
    
    [{ foreach from=$languages key=iLang item=oLang }]
        <fieldset>
            <legend>[{ $oLang->name }]</legend>
            <div class="demo">
                <p class="path">
                    [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_DEMO_PATH" }]:
                    <span></span>
                </p>
                <p class="id">
                    [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_DEMO_ID" }]:
                    <span></span>
                </p>
            </div>
            <table>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidBaseUrls" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidBaseUrls][[{ $iLang }]]" value="[{ $aCmsxidBaseUrls.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidBaseUrls_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidBaseSslUrls" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidBaseSslUrls][[{ $iLang }]]" value="[{ $aCmsxidBaseSslUrls.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidBaseSslUrls_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidPagePaths" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidPagePaths][[{ $iLang }]]" value="[{ $aCmsxidPagePaths.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidPagePaths_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidIdParams" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidIdParams][[{ $iLang }]]" value="[{ $aCmsxidIdParams.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidIdParams_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidLangParams" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidLangParams][[{ $iLang }]]" value="[{ $aCmsxidLangParams.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidLangParams_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidParams" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidParams][[{ $iLang }]]" value="[{ $aCmsxidParams.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidParams_HELP" }]
                    </td>
                </tr>
                <tr>
                    <td valign="top" class="edittext">
                        [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_aCmsxidSeoIdents" }]: 
                    </td>
                    <td valign="top" class="edittext">
                        <input type="text" name="editval[aCmsxidSeoIdents][[{ $iLang }]]" value="[{ $aCmsxidSeoIdents.$iLang }]" />
                        [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_aCmsxidSeoIdents_HELP" }]
                    </td>
                </tr>
            </table>
        </fieldset>
    [{ /foreach }]
    
    <fieldset>
        <legend>[{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_GENERAL" }]</legend>
        
        <p>
            <input type="checkbox" name="editval[blCmsxidLeaveUrls]" value="1" [{ if $blCmsxidLeaveUrls }]checked="checked"[{ /if }] />
            [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_blCmsxidLeaveUrls" }]
            [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_blCmsxidLeaveUrls_HELP" }]
        </p>
        
        <table>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefault" }]:
                </td>
                <td class="edittext">
                    <input type="text" name="editval[iCmsxidTtlDefault]" value="[{ $iCmsxidTtlDefault }]" />
            [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefault_HELP" }]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                    [{ oxmultilang ident="CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefaultRnd" }]:
                </td>
                <td class="edittext">
                    <input type="text" name="editval[iCmsxidTtlDefaultRnd]" value="[{ $iCmsxidTtlDefaultRnd }]" size="2" />%
                    [{ oxinputhelp ident="CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefaultRnd_HELP" }]
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