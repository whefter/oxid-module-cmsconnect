[{ $smarty.block.parent }]

<tr>
    <td class="edittext" width="70">
        [{ oxmultilang ident="CMSCONNECT_ADMIN_CONTENT_PAGE_ID" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__CMSc_CmsPageid->fldmax_length}]" name="editval[oxcontents__CMSc_CmsPageid]" value="[{$edit->oxcontents__CMSc_CmsPageid->value}]" [{ $readonly }]>
        [{ oxinputhelp ident="CMSCONNECT_ADMIN_CONTENT_PAGE_ID_HELP" }]
    </td>
</tr>
<tr>
    <td class="edittext" width="70">
        [{ oxmultilang ident="CMSCONNECT_ADMIN_CONTENT_PAGE" }]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__CMSc_CmsPage->fldmax_length}]" name="editval[oxcontents__CMSc_CmsPage]" value="[{$edit->oxcontents__CMSc_CmsPage->value}]" [{ $readonly }]>
        [{ oxinputhelp ident="CMSCONNECT_ADMIN_CONTENT_PAGE_HELP" }]
    </td>
</tr>

<tr>
    <td colspan="2">
        &nbsp;
    </td>
</tr>