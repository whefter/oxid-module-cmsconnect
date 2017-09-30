[{ $smarty.block.parent }]

<tr>
    <td class="edittext" width="70">
        [{ oxmultilang ident="CMSCONNECT_ADMIN_CONTENT_PAGE_ID" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__cmsxidpageid->fldmax_length}]" name="editval[oxcontents__cmsxidpageid]" value="[{$edit->oxcontents__cmsxidpageid->value}]" [{ $readonly }]>
        [{ oxinputhelp ident="CMSCONNECT_ADMIN_CONTENT_PAGE_ID_HELP" }]
    </td>
</tr>
<tr>
    <td class="edittext" width="70">
        [{ oxmultilang ident="CMSCONNECT_ADMIN_CONTENT_PAGE" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__cmsxidpage->fldmax_length}]" name="editval[oxcontents__cmsxidpage]" value="[{$edit->oxcontents__cmsxidpage->value}]" [{ $readonly }]>
        [{ oxinputhelp ident="CMSCONNECT_ADMIN_CONTENT_PAGE_HELP" }]
    </td>
</tr>

<tr>
    <td colspan="2">
        &nbsp;
    </td>
</tr>