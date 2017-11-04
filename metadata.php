<?php
$sMetadataVersion = '1.1';

$aModule = array(
    'id'                => 'cmsconnect',
    'title'             => 'CMSconnect',
    'email'             => 'william@whefter.de',
    'url'               => 'http://www.whefter.de',
    'version'           => '1.3.1',
    'author'            => 'William Hefter',
    'description'       => array(
        'de'    => 'Erlaubt das Einbinden von CMS-Inhalten im OXID eShop. <p class="warning">Benötigt das whbase (wh Module Extensions)-Modul.</p>',
        'en'    => 'Enables the fetching and rendering of CMS pages inside OXID eShop. <p class="warning">Requires the whbase (wh Module Extension) module.</p>',
    ),
    
    'templates' => array(
        'modules/wh/cmsconnect/frontend.tpl'        => 'wh/cmsconnect/application/views/tpl/frontend.tpl',
        'modules/wh/cmsconnect/async.tpl'           => 'wh/cmsconnect/application/views/tpl/async.tpl',
        'modules/wh/cmsconnect/async_snippet.tpl'   => 'wh/cmsconnect/application/views/tpl/async_snippet.tpl',
        'modules/wh/cmsconnect/cache.tpl'           => 'wh/cmsconnect/application/views/tpl/cache.tpl',
        
        // Admin
        'modules/wh/cmsconnect/admin/setup_list.tpl'                        => 'wh/cmsconnect/application/views/admin/tpl/setup_list.tpl',
        'modules/wh/cmsconnect/admin/setup_main.tpl'                        => 'wh/cmsconnect/application/views/admin/tpl/setup_main.tpl',
        'modules/wh/cmsconnect/admin/setup_testcontent.tpl'                 => 'wh/cmsconnect/application/views/admin/tpl/setup_testcontent.tpl',
        'modules/wh/cmsconnect/admin/setup_cache_localpages.tpl'            => 'wh/cmsconnect/application/views/admin/tpl/setup_cache_localpages.tpl',
        'modules/wh/cmsconnect/admin/setup_cache_cmspages.tpl'              => 'wh/cmsconnect/application/views/admin/tpl/setup_cache_cmspages.tpl',
        'modules/wh/cmsconnect/admin/includes/cache_list_pagination.tpl'    => 'wh/cmsconnect/application/views/admin/tpl/includes/cache_list_pagination.tpl',
    ),
    
    'blocks'    => array(
        array(
            'template'  => 'content_main.tpl',
            'block'     => 'admin_content_main_form',
            'file'      => '/application/views/admin/blocks/content_main.tpl/admin_content_main_form.tpl'
        ),
    ),
    
    'extend' => array(
        'oxconfig'      => 'wh/cmsconnect/application/models/cmsconnect_oxconfig',
        'oxcontent'     => 'wh/cmsconnect/application/models/cmsconnect_oxcontent',
        'oxutilsview'   => 'wh/cmsconnect/application/models/cmsconnect_oxutilsview',
        'oxutils'       => 'wh/cmsconnect/application/models/cmsconnect_oxutils',
        'oxseodecoder'  => 'wh/cmsconnect/application/models/cmsconnect_oxseodecoder',
        'oxseoencoder'  => 'wh/cmsconnect/application/models/cmsconnect_oxseoencoder',
        'oxviewconfig'  => 'wh/cmsconnect/application/models/cmsconnect_oxviewconfig',
    ),
    
    'files' => array(
        'cmsconnect'                    => 'wh/cmsconnect/core/cmsconnect.php',
        'cmsconnect_frontend'           => 'wh/cmsconnect/application/controllers/cmsconnect_frontend.php',
        'cmsconnect_async'              => 'wh/cmsconnect/application/controllers/cmsconnect_async.php',
        'cmsconnect_cache'              => 'wh/cmsconnect/application/controllers/cmsconnect_cache.php',
        
        'cmsconnect_events'             => 'wh/cmsconnect/core/cmsconnect_events.php',
        
        // Admin
        'cmsconnect_setup'                  => 'wh/cmsconnect/application/controllers/admin/cmsconnect_setup.php',
        'cmsconnect_setup_list'             => 'wh/cmsconnect/application/controllers/admin/cmsconnect_setup_list.php',
        'cmsconnect_setup_main'             => 'wh/cmsconnect/application/controllers/admin/cmsconnect_setup_main.php',
        'cmsconnect_setup_testcontent'      => 'wh/cmsconnect/application/controllers/admin/cmsconnect_setup_testcontent.php',
        'cmsconnect_setup_cache_localpages' => 'wh/cmsconnect/application/controllers/admin/cmsconnect_setup_cache_localpages.php',
        'cmsconnect_setup_cache_cmspages'   => 'wh/cmsconnect/application/controllers/admin/cmsconnect_setup_cache_cmspages.php',
    ),
    
    'settings' => array(
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'blCMScSslDontVerifyPeer',
            'type'      => 'bool',
            'value'     => false,
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'blCMScEnableTestContent',
            'type'      => 'bool',
            'value'     => false,
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScTestContent',
            'type'      => 'str',
            'value'     => 'str',
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScCurlExecuteTimeout',
            'type'      => 'str',
            'value'     => '1000',
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScCurlConnectTimeout',
            'type'      => 'str',
            'value'     => '1000',
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScTtlDefaultRnd',
            'type'      => 'str',
            'value'     => '10',
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScTtlDefault',
            'type'      => 'str',
            'value'     => '36000',
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScUrlRewriting',
            'type'      => 'str',
            'value'     => 'URL_REWRITING_PATH_ONLY',
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScLocalPageCacheEngine',
            'type'      => 'str',
            'value'     => 'LOCAL_PAGE_CACHE_AUTO',
            'global'    => true,
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'sCMScCmsPageCacheEngine',
            'type'      => 'str',
            'value'     => 'CMS_PAGE_CACHE_AUTO',
            'global'    => true,
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScBaseUrls',
            'type'      => 'aarr',
            'value'     => [],
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScBaseSslUrls',
            'type'      => 'aarr',
            'value'     => [],
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScPagePaths',
            'type'      => 'aarr',
            'value'     => [],
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScParams',
            'type'      => 'aarr',
            'value'     => [],
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScIdParams',
            'type'      => 'aarr',
            'value'     => [],
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScLangParams',
            'type'      => 'aarr',
            'value'     => [],
        ),
        array(
            'group'     => 'DONOTMODIFY',
            'name'      => 'aCMScSeoIdents',
            'type'      => 'aarr',
            'value'     => [],
        ),
    ),
    
    'events'    => array(
        'onActivate'    => 'cmsconnect_events::onActivate',
        'onDeactivate'  => 'cmsconnect_events::onDeactivate',
    ),
);