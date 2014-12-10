[{ cmsxid_load content="navigation" assign="sNavigation" }]
[{capture append="oxidBlock_sidebar"}]
    [{ if $sNavigation }]
        <div class="cms-navigation tree">
            [{ $sNavigation }]
        </div>
    [{ /if }]
[{/capture}]

[{capture append="oxidBlock_content"}]
    [{ cmsxid_load content="breadcrumb" assign="sBreadcrumb" }]
    [{ if $sBreadcrumb }]
        <div id="cms-breadcrumb-wrap">
            <div id="cms-breadcrumb">
                [{ $sBreadcrumb }]
            </div>
        </div>
    [{ /if }]
    
    <div class="cms-content">
        [{ cmsxid_load assign="aCmsContent" }]
        [{ assign var="iSnippetCount" value=$aCmsContent|@count }]
        
        [{ foreach from=$aCmsContent item=sSnippet name=fCmsSnippets }]
            <div class="cms-cols-[{ $iSnippetCount }] cms-col-[{ $smarty.foreach.fCmsSnippets.iteration }]">
                [{ $sSnippet }]
            </div>
        [{ /foreach }]
    </div>
    
    [{insert name="oxid_tracker" title=$template_title }]
[{/capture}]

[{include file="layout/page.tpl" sidebar=$sNavigation}]  