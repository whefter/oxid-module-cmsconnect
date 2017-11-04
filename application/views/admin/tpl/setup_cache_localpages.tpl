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
    <input type="hidden" name="key" value="">
    <input type="hidden" name="language" value="[{ $actlang }]">
    
    <h1>
        [{ oxmultilang ident="cmsconnect_setup_cache_localpages" }]
    </h1>
    
    <p>
        Engine: [{ $oLocalPagesCache->getEngineLabel() }]
    </p>
    
    <script type="text/javascript">
    $(document).ready( function () {
        var $editForm = $('#myedit');
        
        var $fncInput = $('[name="fnc"]', $editForm);
        var $keyInput = $('[name="key"]', $editForm);
        
        var $deleteAllBtn = $('#deleteAll');
        var $deleteAllGlobalBtn = $('#deleteAllGlobal');
        
        window.deleteLocalPage = function (key)
        {
            $keyInput.val(key);
            $fncInput.val('deleteLocalPage');
            $editForm.submit();
        };
        
        $deleteAllBtn.click(function () {
            $fncInput.val('deleteAllLocalPages');
            $editForm.submit();
        });
        
        $deleteAllGlobalBtn.click(function () {
            $fncInput.val('deleteAllLocalPagesGlobal');
            $editForm.submit();
        });
    });
    </script>
    
    <button type="button" id="deleteAll">
        Cache leeren
    </button>
    
    <button type="button" id="deleteAllGlobal">
        Cache für ALLE SHOPS leeren (!)
    </button>
    
    [{ include  file="modules/wh/cmsconnect/admin/includes/cache_list_pagination.tpl"
                oCache=$oLocalPagesCache
    }]
    
    <table style="width: 100%;">
        <tr>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Lokale URL
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Lokale GET-Parameter
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Lokale POST-Parameter
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Eingebundene CMS-Seiten
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Aktionen
            </th>
        </tr>
        
        [{ foreach from=$oLocalPagesCache->getList($iLimit,$iOffset) key=sLocalPageCacheKey item=aLocalPage }]
            <tr>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ $aLocalPage.data.url }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    <pre>[{ $aLocalPage.data.queryData|@print_r:1}]</pre>
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    <pre>[{ $aLocalPage.data.postData|@print_r:1}]</pre>
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ foreach from=$aLocalPage.pages item=oCmsPage }]
                        [{ $oCmsPage->getUrl() }]<br />
                    [{ /foreach }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    <a href="javascript:deleteLocalPage('[{ $sLocalPageCacheKey }]');">Löschen</a>
                </td>
            </tr>
        [{ /foreach }]
    </table>
    
</form>

[{ php }]
t::dAll();
[{ /php }]

[{ include file="bottomnaviitem.tpl" }]
[{ include file="bottomitem.tpl" }]