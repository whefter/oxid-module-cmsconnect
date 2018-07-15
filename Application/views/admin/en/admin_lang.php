<?php
$sLangName  = "English";

$aLang = array(
    'charset'                                               => 'UTF-8',

    'cmsconnect_setup'                                          => 'CMSconnect',
    'cmsconnect_setup_main'                                     => 'Settings',
    'cmsconnect_setup_testcontent'                              => 'Test content',
    'cmsconnect_setup_cache_localpages'                         => 'Cache: Local pages <=> CMS pages',
    'cmsconnect_setup_cache_cmspages'                           => 'Cache: CMS pages',


    'CMSCONNECT_ADMIN_SETTINGS_SOURCE'                          => 'Source',
    'CMSCONNECT_ADMIN_SETTINGS_DEMO'                            => 'Preview',
    'CMSCONNECT_ADMIN_SETTINGS_DEMO_PATH'                       => 'Path-based fetching',
    'CMSCONNECT_ADMIN_SETTINGS_DEMO_ID'                         => 'ID-based fetching',
    'CMSCONNECT_ADMIN_SETTINGS_DEMO_EXAMPLE'                    => 'example',

    'CMSCONNECT_ADMIN_SETTINGS_aCMScBaseUrls'             => 'Base URL to the CMS',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScBaseSslUrls'          => 'Base SSL URL to the CMS',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScPagePaths'            => 'Path-based: relative path',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScIdParams'             => 'ID-based: query parameter for page ID',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScIdParams_HELP'        => 'Example value for CMS: \'id\'',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScLangParams'           => 'Query parameter string for language ID',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScLangParams_HELP'      => 'Example value for CMS: \'L=0\', if the CMS language ID for the current OXID language is 0.',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScParams'               => 'Additional query parameters',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScSearchUrls'           => 'Search URL',
    'CMSCONNECT_ADMIN_SETTINGS_aCMScSeoIdents'            => 'SEO-Snippet for OXID URLs',

    'CMSCONNECT_ADMIN_SETTINGS_GENERAL'                         => 'General Settings',

    'CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting'               => 'Rewrite URLs',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_HELP'          => 'Sets whether URLs in CMS texts should be rewritten to point back to the OXID shop. This allows navigation of the CMS content inside the OXID shop. Setting this to "none" will lead to users being redirected away from the shop to the CMS when they click a link.
                                                            <ul>
                                                                <li><b>Paths only:</b> rewrite URLs matching the configured relative paths only
                                                                <!--
                                                                <li><b>All URLs:</b> es werden alle URLs umgeschrieben, auch solche, die nur mit den Basis-URLs beginnen und nicht den relativen Pfad enthalten (kann erforderlich sein, um auf SSL-Seiten auch Bilder und sonstige Resourcen über SSL laden zu können, falls diese nicht unterhalb der relativen Pfades zu erreichen sind)
                                                                -->
                                                                <li><b>None:</b> rewrite no URLs at all
                                                            </ul>',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_PathOnly'      => 'Paths only',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_AllUrls'       => 'All URLs',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScUrlRewriting_None'          => 'None',

    'CMSCONNECT_ADMIN_SETTINGS_blCMScEnableTestContent'         => 'Serve test content',
    'CMSCONNECT_ADMIN_SETTINGS_blCMScEnableTestContent_HELP'    => 'Nothing is fetched from remote servers, the test content is used. For debug purposes.',
    'CMSCONNECT_ADMIN_SETTINGS_blCMScSslDontVerifyPeer'         => 'Don\'t verify SSL peers',
    'CMSCONNECT_ADMIN_SETTINGS_blCMScSslDontVerifyPeer_HELP'    => 'Don\'t verify peer certificate when fetching SSL pages. <p class="warning">WARNING! COMPROMISES SAFETY: this option should only ever be enabled for debug purposes.</p>',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCurlConnectTimeout'         => 'Connect-Timeout for cURL calls in seconds (default: %s ms)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCurlExecuteTimeout'         => 'Execute-Timeout for cURL calls in seconds (default: %s ms)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefault'                 => 'Cache lifetime (default: %s s)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefaultRnd'              => 'Cache lifetime randomization (%%) (default: %s %%)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefaultRnd_HELP'         => 'To prevent large amounts of request being sent to the CMS when the cache expires in shops with numerous CMSconnect calls, the cache lifetime should be randomized for each content object. A randomization of 10% means a cache lifetime of 600 seconds would result in effective lifetimes between 540 and 660 seconds.',

    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine'               => 'Cache engine for local pages (global)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_HELP'          => 'Caches which local page includes which CMS page. <div class="error">Global setting for all shops</div> <div class="error">Can lead to errors if required modules are not loaded. Be careful with values other than "auto".</div>',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_auto'          => 'Auto (recommended)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_Disabled'      => 'Disabled',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_OxidFileCache' => 'OXID file cache',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_DB'            => 'Database',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_memcache'      => 'memcache',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_memcached'     => 'memcached',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine'                 => 'Cache engine for CMS pages (global)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_HELP'            => 'Intelligent cache for fetched CMS pages to prevent repeated calls to the CMS every time a local page is loaded. <div class="error">Global setting for all shops</div> <div class="error">Can lead to errors if required modules are not loaded. Be careful with values other than "auto".</div>',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_auto'            => 'Auto (recommended)',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_OxidFileCache'   => 'OXID file cache',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_memcache'        => 'memcache',
    'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_memcached'       => 'memcached',


    'CMSCONNECT_ADMIN_TESTCONTENT'                              => 'Test-Inhalt',
    'CMSCONNECT_ADMIN_TESTCONTENT_INFO'                         => 'Test-Inhalt, der auf alle Anfragen ausgeliefert wird.',


    'CMSCONNECT_ADMIN_CONTENT_PAGE_ID'                          => '(CMSconnect) Page ID',
    'CMSCONNECT_ADMIN_CONTENT_PAGE_ID_HELP'                     => 'ID of the page to load via CMSconnect. <p class="warning">Content configured here will be OVERWRITTEN!</p>',
    'CMSCONNECT_ADMIN_CONTENT_PAGE'                             => '(CMSconnect) Page-URL',
    'CMSCONNECT_ADMIN_CONTENT_PAGE_HELP'                        => 'URL, relative to the configured base URL for the current shop and language. <p class="warning">Loading via page ID takes precedence if both fields are specified.</p> <p class="warning">Content configured here will be OVERWRITTEN!</p>',
);
