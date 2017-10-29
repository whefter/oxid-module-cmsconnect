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
    
    <input type="submit" value="submit" style="display: none;" />
    
    <h1>
        [{ oxmultilang ident="cmsconnect_setup_cache_cmspages" }]
    </h1>
    
    <p>
        Engine: [{ $oCmsPagesCache->getEngineLabel() }]
    </p>
    
    
    <script type="text/javascript">
    $(document).ready( function () {
        var $editForm = $('#myedit');
        
        /*
        $editForm.submit(function (e) {
            console.warn('submit', e);
        });
        */
        
        var $fncInput = $('[name="fnc"]', $editForm);
        var $keyInput = $('[name="key"]', $editForm);
        
        var $deleteSelectedBtn = $('#deleteSelected');
        var $deleteAllBtn = $('#deleteAll');
        var $deleteAllGlobalBtn = $('#deleteAllGlobal');
        var $toggleAllCheckbox = $('#toggleAllCheckbox');
        
        window.deleteCmsPage = function (key)
        {
            $keyInput.val(key);
            $fncInput.val('deleteCmsPage');
            $editForm.submit();
        };
        
        $deleteSelectedBtn.click(function () {
            var cacheKeys = [];
            
            $('[id^="cacheEntrySelect_"]').each( function (index, checkbox) {
                var $checkbox = $(checkbox);
                
                if ($checkbox.attr('checked')) {
                    cacheKeys.push($checkbox.attr('id').substr('cacheEntrySelect_'.length));
                }
            });
            $('[name="selectedCacheKeysList"]').val(JSON.stringify(cacheKeys));
            
            $fncInput.val('deleteCmsPages');
            $editForm.submit();
        });
        
        $deleteAllBtn.click(function () {
            $fncInput.val('deleteAllCmsPages');
            $editForm.submit();
        });
        
        $deleteAllGlobalBtn.click(function () {
            $fncInput.val('deleteAllCmsPagesGlobal');
            $editForm.submit();
        });
        
        $toggleAllCheckbox.click(function () {
            $('[id^="cacheEntrySelect_"]').each( function (index, checkbox) {
                if ($toggleAllCheckbox.attr('checked')) {
                    $(checkbox).attr('checked', 'checked');
                } else {
                    $(checkbox).attr('checked', false);
                }
            });
        });
    });
    </script>
    
    <input type="hidden" name="selectedCacheKeysList" value="" />
    <button type="button" id="deleteSelected">
        Ausgewählte Löschen
    </button>
    
    <button type="button" id="deleteAll">
        Cache leeren
    </button>
    
    <button type="button" id="deleteAllGlobal">
        Cache für ALLE SHOPS leeren (!)
    </button>
    
    [{ include  file="modules/wh/cmsconnect/admin/includes/cache_list_pagination.tpl"
                oCache=$oCmsPagesCache
    }]
    
    <table style="width: 100%;">
        <tr>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                <input type="checkbox" id="toggleAllCheckbox" />
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                URL
                <div>
                    <input type="text" name="filters[url]" value="[{ $aFilters.url }]" />
                </div>
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                HTTP-Methode
                <div>
                    <input type="text" name="filters[http_method]" value="[{ $aFilters.http_method }]" />
                </div>
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                POST-Parameter
                <div>
                    <input type="text" name="filters[post_params]" value="[{ $aFilters.post_params }]" />
                </div>
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                ID
                <div>
                    <input type="text" name="filters[pageid]" value="[{ $aFilters.pageid }]" />
                </div>
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                HTTP-Code
                <div>
                    <input type="text" name="filters[http_code]" value="[{ $aFilters.http_code }]" />
                </div>
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Cache läuft ab in (hh:mm:ss)
            </th>
            <th style="border: 1px solid grey; border-top: none; border-left: none;">
                Aktionen
            </th>
        </tr>
        
        [{ foreach from=$aList key=sCacheKey item=oHttpResult }]
            [{ assign var=oCmsPage value=$oHttpResult->oCmsPage }]
            <tr>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    <input type="checkbox" id="cacheEntrySelect_[{ $sCacheKey }]" value="1" />
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    <a href="javascript:showDialog('&cl=cmsconnect_setup_cache_cmspages&fnc=showCacheEntryContent&cacheKey=[{ $sCacheKey }]')">
                        [{ $oCmsPage->getUrl() }]
                    </a>
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
                    [{ $oCmsPage->getPageId() }]
                </td>
                <td style="border: 1px solid grey; border-top: none; border-left: none;">
                    [{ $oHttpResult->info.http_code }]
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
                    <a href="javascript:deleteCmsPage('[{ $oCmsPage->getCacheKey() }]');">Löschen</a>
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