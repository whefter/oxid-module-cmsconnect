[{*
@param  string      sUid            Placeholder UID
@param  string      sMethod         Method (page/id)
@param  string      sLang           Language to fetch the element in
@param  string      sContent        Snippet identifier
@param  string      sIdentifier     Page identifier (an ID in ID mode, page path in path mode)
@param  string      sBaseUrl        Shop base URL (for POST request)
*}]

<span id="[{ $sUid }]" class="cmsconnect-async-placeholder">
    <span class="loading-indicator"></span>
</span>

[{ capture assign="js_cmsconnect_async_snippet" }]
$( function () {
    var $el = $('#[{ $sUid }]');
    
    if ( window.wh && window.wh.cms && window.wh.cms.onInView ) {
        window.wh.cms.onInView($el, fetch);
    } else if ( window.jQuery && window.$ && $.event.special.inview ) {
        $el.on('inview', function (e, isInView) {
            if ( isInView ) {
                fetch();
                $el.off('inview');
            }
        });
        // console.warn("binding to inview");
    } else {
        fetch();
    }
    
    function fetch () {
        $.post('[{ $sBaseUrl }]?cl=cmsconnect_async', {
            method:     '[{ $sMethod }]',
            lang:       '[{ $sLang }]',
            content:    '[{ $sContent }]',
            identifier: '[{ $sIdentifier }]',
        })
        .done(function (data) {
            $el.after(data);
            $el.remove();
        });
    }
});
[{ /capture }]
[{ oxscript add=$js_cmsconnect_async_snippet }]