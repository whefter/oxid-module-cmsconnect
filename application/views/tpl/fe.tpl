[{ cmsxid_load content="navigation" assign="sNavigation" }]
[{ capture append="oxidBlock_sidebar" }]
    [{ if $sNavigation }]
        <div id="cms-navigation">
            [{ $sNavigation }]
        </div>
    [{ /if }]
[{ /capture }]

[{ capture append="oxidBlock_content" }]
    [{*
        Replaced by XML-based breadcrumbs
    *}]
    [{*
    [{ cmsxid_load content="breadcrumb" assign="sBreadcrumb" }]
    [{ if $sBreadcrumb }]
        <div id="cms-breadcrumb-wrap">
            <div id="cms-breadcrumb">
                [{ $sBreadcrumb }]
            </div>
        </div>
    [{ /if }]
    *}]
    
    <div id="cms-content">
        [{ cmsxid_load nodes="left,content,right,border" assign="aCmsContent" }]
        [{ assign var="iSnippetCount" value=$aCmsContent|@count }]
        
        [{ foreach from=$aCmsContent item=sSnippet name=fCmsSnippets }]
            <div class="cms-cols-[{ $iSnippetCount }] cms-col-[{ $smarty.foreach.fCmsSnippets.iteration }]">
                [{ $sSnippet }]
            </div>
        [{ /foreach }]
    </div>
    
    [{ insert name="oxid_tracker" title=$template_title }]
[{ /capture }]

[{ include file="layout/page.tpl" sidebar=$sNavigation }]  