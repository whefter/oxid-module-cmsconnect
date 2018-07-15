[{*
Parameters:

@param CMSc_Cache $oCache
*}]

<input type="hidden" name="pgNr" value="">

<script type="text/javascript">
$(document).ready( function () {
    var $editForm = $('#myedit');
    
    var $pgNrInput = $('[name="pgNr"]', $editForm);
    
    window.paginationNavigateToPage = function (pgNr)
    {
        $pgNrInput.val(pgNr);
        $editForm.submit();
    };
});
</script>

<span id="pagination">
    [{ assign var=iTotalPages value=$iCount/$iLimit }]
    [{ assign var=iTotalPages value=$iTotalPages|ceil }]

    [{ if $iCount }]
        [{ if $iCount < ($iOffset + $iLimit) }]
            [{ assign var=iCurMax value=$iCount }]
        [{ else }]
            [{ assign var=iCurMax value=$iOffset+$iLimit }]
        [{ /if }]
        ([{ $iOffset+1 }] - [{ $iCurMax }] / [{ $iCount }])
    [{ /if }]
    
    [{ assign var=blDotsPrinted value=false }]
    [{ section name=pagination start=1 loop=$iTotalPages+1 step=1 }]
        [{ assign var=iIdx value=$smarty.section.pagination.index }]
        
        [{ if $iIdx <= 2 || ($iIdx >= $iPage-2 && $iIdx <= $iPage+2) || $iIdx >= $iTotalPages-1 }]
            <a href="javascript:paginationNavigateToPage([{ $iIdx }])">
                [{ if $iIdx === $iPage }]
                    <b style="font-size: 1.2em;">
                [{ /if }]
                
                [{ $iIdx }]
                
                [{ if $iIdx === $iPage }]
                    </b>
                [{ /if }]
            </a>
            
            [{ assign var=blDotsPrinted value=false }]
        [{ else }]
            [{ if !$blDotsPrinted }]
                [{ assign var=blDotsPrinted value=true }]
                ..
            [{ /if }]
        [{ /if }]
    [{ /section }]
</span>