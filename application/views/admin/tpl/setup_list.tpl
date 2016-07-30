[{*
    Very slightly adapted copy of shop_list.tpl
/**
 * @author      William Hefter <william@whefter.de>
 * @link        http://www.whefter.de
 */
*}]

[{ include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list" }]

[{ assign var="where" value=$oView->getListFilter() }]

[{ if $readonly}]
    [{ assign var="readonly" value="readonly disabled" }]
[{ else }]
    [{ assign var="readonly" value="" }]
[{ /if }]

<script type="text/javascript">
<!--
function editThis( sID )
{
    var oForm = top.navigation.adminnav.document.getElementById( "search" );
    if ( oForm ) {
        // passing this info about active view and tab to nav frame
        var oInputElement = document.createElement( 'input' );
        oInputElement.setAttribute( 'name', 'listview');
        oInputElement.setAttribute( 'type', 'hidden' );
        oInputElement.value = "[{ $oViewConf->getActiveClassName() }]";
        oForm.appendChild( oInputElement );

        var oInputElement = document.createElement( 'input' );
        oInputElement.setAttribute( 'name', 'actedit');
        oInputElement.setAttribute( 'type', 'hidden' );
        oInputElement.value = "[{ $actedit }]";
        oForm.appendChild( oInputElement );

        var oInputElement = document.createElement( 'input' );
        oInputElement.setAttribute( 'name', 'editview');
        oInputElement.setAttribute( 'type', 'hidden' );
        oInputElement.value = top.oxid.admin.getClass( sID );
        oForm.appendChild( oInputElement );

        // selecting shop
        top.navigation.adminnav.selectShop( sID );
    }
}

function deleteThis( sID)
{
    var currentshop = [{ $oxid }];
    var newshop = (sID == currentshop)?1:currentshop;

    blCheck = confirm("[{ oxmultilang ident="SHOP_LIST_YOUWANTTODELETE" }]");
    if( blCheck == true)
    {   var oSearch = top.basefrm.list.document.getElementById( "search" );
        oSearch.delshopid.value = sID;
        oSearch.fnc.value = 'deleteentry';
        oSearch.actedit.value = 0;
        oSearch.submit();

        var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
        oTransfer.oxid.value = newshop;
        oTransfer.actshop.value = newshop;
        oTransfer.cl.value='[{ $default_edit }]';
        oTransfer.updatenav.value = 1;

        //forcing edit frame to reload after submit
        top.forceReloadingEditFrame();
    }
}

window.onload = function ()
{
    [{ if $updatenav }]
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.updatenav.value = 1;
    oTransfer.cl.value = '[{ $default_edit }]';
    [{ /if}]
    top.reloadEditFrame();
}
//-->
</script>

<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ include file="_formparams.tpl" cl=$oViewConf->getActiveClassName() lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang delshopid="" updatenav="" }]
  <div id="liste">


    <!--//MALL OFF-->

    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <colgroup>
        [{ block name="admin_shop_list_colgroup" }]
            <col width="98%"><col width="2%">
        [{ /block }]
    </colgroup>
    <tr class="listitem">
        [{ block name="admin_shop_list_filter" }]
            <td valign="top" class="listfilter first" colspan="2">
                <div class="r1"><div class="b1">
                <div class="find">
                    <input class="listedit" type="submit" name="submitit" value="[{ oxmultilang ident="GENERAL_SEARCH" }]">
                </div>
                <input class="listedit" type="text" size="60" maxlength="128" name="where[oxshops][oxname]" value="[{ $where.oxshops.oxname }]">
                </div></div>
            </td>
        [{ /block }]
    </tr>
    <tr>
        [{ block name="admin_shop_list_sorting" }]
            <td class="listheader first" height="15" colspan="2">&nbsp;<a href="Javascript:top.oxid.admin.setSorting( document.search, 'oxshops', 'oxname', 'asc');document.search.submit();" class="listheader">[{ oxmultilang ident="GENERAL_DESCRIPTION" }]</a></td>
        [{ /block }]
    </tr>

    [{ assign var="blWhite" value="" }]
    [{ assign var="_cnt" value=0 }]
    [{ foreach from=$mylist item=listitem}]
    [{ assign var="_cnt" value=$_cnt+1 }]
    <tr id="row.[{ $_cnt }]">
        [{ block name="admin_shop_list_item" }]
            [{ if $listitem->blacklist == 1}]
                [{ assign var="listclass" value=listitem3 }]
            [{ else}]
                [{ assign var="listclass" value=listitem$blWhite }]
            [{ /if}]
            [{ if $listitem->getId() == $oxid }]
                [{ assign var="listclass" value=listitem4 }]
            [{ /if}]
            <td valign="top" class="[{ $listclass}]" height="15"><div class="listitemfloating">&nbsp;<a href="Javascript:editThis('[{ $listitem->oxshops__oxid->value}]');" class="[{ $listclass}]">[{ if $listitem->oxshops__oxid->value == '1'}]<b>[{ /if }][{ $listitem->oxshops__oxname->value }][{ if $listitem->oxshops__oxid->value == '1'}]</b>[{ /if }]&nbsp;([{ $listitem->oxshops__oxid->value }])</a></div></td>
            <td class="[{ $listclass}]">
            [{ if !$listitem->isOx() && !$listitem->isparent && !$readonly && $listitem->oxshops__oxid->value != 1}]
            <a href="Javascript:deleteThis('[{ $listitem->oxshops__oxid->value }]');" class="delete" id="del.[{ $_cnt }]" [{ include file="help.tpl" helpid=item_delete }]></a>
            [{ /if }]
            </td>
        [{ /block }]
    </tr>
    [{ if $blWhite == "2"}]
        [{ assign var="blWhite" value="" }]
    [{ else }]
        [{ assign var="blWhite" value="2" }]
    [{ /if }]
    [{ /foreach }]
    [{ include file="pagenavisnippet.tpl" colspan="2" }]
    </table>
    </form>
  </div>




[{ include file="pagetabsnippet.tpl" }]

<script type="text/javascript">
if (parent.parent != null && parent.parent.setTitle )
{   parent.parent.sShopTitle   = "[{ $actshopobj->oxshops__oxname->getRawValue()|oxaddslashes }]";
    parent.parent.sMenuItem    = "[{ oxmultilang ident="SHOP_LIST_MENUITEM" }]";
    parent.parent.sMenuSubItem = "[{ oxmultilang ident="SHOP_LIST_MENUSUBITEM" }]";
    parent.parent.sWorkArea    = "[{ $_act }]";
    parent.parent.setTitle();
}
window.onload = function ()
{
    [{ if $updatenav }]
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.updatenav.value = 1;
    oTransfer.cl.value = '[{ $default_edit }]';
    [{ /if}]
    top.reloadEditFrame();
} 
</script>
</body>
</html>
