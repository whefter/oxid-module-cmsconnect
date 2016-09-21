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
        [{ oxmultilang ident="cmsconnect_setup_cache_cmspages" }]
    </h1>
    
    <p>
        Engine: [{ $oCmsPagesCache->getEngineLabel() }]
    </p>
    
    
    <script type="text/javascript">
    $(document).ready( function () {
        var $editForm = $('#myedit');
        
        var $fncInput = $('[name="fnc"]', $editForm);
        var $keyInput = $('[name="key"]', $editForm);
        
        var $deleteAllBtn = $('#deleteAll');
        
        window.deleteCmsPage = function (key)
        {
            $keyInput.val(key);
            $fncInput.val('deleteCmsPage');
            $editForm.submit();
        };
        
        $deleteAllBtn.click(function () {
            $fncInput.val('deleteAllCmsPages');
            $editForm.submit();
        });
    });
    </script>
    
    <button type="button" id="deleteAll">
        Cache leeren
    </button>
    
    <table style="width: 100%;">
        <tr>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                URL
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                HTTP-Methode
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                POST-Parameter
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Cache läuft ab in (hh:mm:ss)
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Aktionen
            </th>
        </tr>
        
        [{ foreach from=$oCmsPagesCache->getList() key=sOxidCacheKey item=oHttpResult }]
            [{ assign var=oCmsPage value=$oHttpResult->oCmsPage }]
            <tr>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ $oCmsPage->getUrl() }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ if $oCmsPage->isPostPage() }]
                        POST
                    [{ else }]
                        GET
                    [{ /if }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ $oCmsPage->getPostParams()|@print_r:1 }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ php }]
                        $now = time();
                        $then = $this->get_template_vars('oHttpResult')->ttl;
                        $diff = max($then - $now, 0);
                        
                        $hours = floor( $diff/3600 );
                        $minutes = floor( ($diff - ($hours * 3600)) / 60 );
                        $seconds = floor( $diff - ($hours * 3600) - ($minutes * 60) );
                        
                        $this->assign('sRemainingTime', sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds));
                    [{ /php }]
                    [{ $sRemainingTime }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    <a href="javascript:deleteCmsPage('[{ $oCmsPage->getIdent() }]');">Löschen</a>
                </td>
            </tr>
        [{ /foreach }]
    </table>
</form>

[{ include file="bottomnaviitem.tpl" }]
[{ include file="bottomitem.tpl" }]