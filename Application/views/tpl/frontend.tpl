[{ assign var=sNavigation value=$oView->getNavigation() }]

[{ if $sNavigation }]
    [{ capture append="oxidBlock_sidebar" }]
        <div id="cms-navigation">
            [{ $sNavigation }]
        </div>
    [{ /capture }]
[{ /if }]

[{ capture append="oxidBlock_content" }]
    <div id="cms-content">
        [{ assign var=oCmsPage value=$oView->getCmsPage() }]
        [{ assign_adv var=aContentList value="array('left', 'normal', 'right', 'border')"}]
        [{ assign var=aCmsContent value=$oCmsPage->getContentArray($aContentList) }]
        [{ assign var=iSnippetCount value=$aCmsContent|@count }]
        
        [{ foreach from=$aCmsContent item=sSnippet name=fCmsSnippets }]
            <div class="cms-cols-[{ $iSnippetCount }] cms-col-[{ $smarty.foreach.fCmsSnippets.iteration }]">
                [{ $sSnippet }]
            </div>
        [{ /foreach }]
    </div>
    
    [{ insert name="oxid_tracker" title=$template_title }]
[{ /capture }]

[{ include file="layout/page.tpl" sidebar=$sNavigation }]  