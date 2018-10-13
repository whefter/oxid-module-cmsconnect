<?php
$sMetadataVersion = '2.0';

$aModule = array(
    'id'                => 'cmsconnect',
    'title'             => 'CMSConnect',
    'email'             => 'william@whefter.de',
    'url'               => 'http://www.whefter.de',
    'version'           => '3.1.1',
    'author'            => 'William Hefter',
    'description'       => array(
        'de'    => 'Erlaubt das Einbinden von CMS-Inhalten im OXID eShop. <p class="warning">Benötigt das whbase (wh Module Extensions)-Modul.</p>',
        'en'    => 'Enables the fetching and rendering of CMS pages inside OXID eShop. <p class="warning">Requires the whbase (wh Module Extension) module.</p>',
    ),

    'controllers' => array(
        'cmsconnect_frontend' => \wh\CmsConnect\Application\Controllers\Frontend::class,
        'cmsconnect_async' => \wh\CmsConnect\Application\Controllers\Async::class,
        'cmsconnect_cache' => \wh\CmsConnect\Application\Controllers\Cache::class,

        // Admin
        'cmsconnect_setup'                  => \wh\CmsConnect\Application\Controllers\Admin\Setup::class,
        'cmsconnect_setup_list'             => \wh\CmsConnect\Application\Controllers\Admin\Setup\ListController::class,
        'cmsconnect_setup_main'             => \wh\CmsConnect\Application\Controllers\Admin\Setup\Main::class,
        'cmsconnect_setup_testcontent'      => \wh\CmsConnect\Application\Controllers\Admin\Setup\TestContent::class,
        'cmsconnect_setup_cache_localpages' => \wh\CmsConnect\Application\Controllers\Admin\Setup\Cache\LocalPages::class,
        'cmsconnect_setup_cache_cmspages'   => \wh\CmsConnect\Application\Controllers\Admin\Setup\Cache\CmsPages::class,
    ),

    'extend' => array(
        \OxidEsales\Eshop\Core\Config::class => wh\CmsConnect\Modules\Core\Config::class,
        \OxidEsales\Eshop\Application\Model\Content::class => wh\CmsConnect\Modules\Application\Model\Content::class,
        \OxidEsales\Eshop\Core\UtilsView::class => wh\CmsConnect\Modules\Core\UtilsView::class,
        \OxidEsales\Eshop\Core\Utils::class => wh\CmsConnect\Modules\Core\Utils::class,
        \OxidEsales\Eshop\Core\SeoDecoder::class => wh\CmsConnect\Modules\Core\SeoDecoder::class,
        \OxidEsales\Eshop\Core\SeoEncoder::class => wh\CmsConnect\Modules\Core\SeoEncoder::class,
        \OxidEsales\Eshop\Core\ViewConfig::class => wh\CmsConnect\Modules\Core\ViewConfig::class,
    ),

    'templates' => array(
        'modules/wh/cmsconnect/frontend.tpl'        => 'wh/cmsconnect/Application/views/tpl/frontend.tpl',
        'modules/wh/cmsconnect/async.tpl'           => 'wh/cmsconnect/Application/views/tpl/async.tpl',
        'modules/wh/cmsconnect/async_snippet.tpl'   => 'wh/cmsconnect/Application/views/tpl/async_snippet.tpl',
        'modules/wh/cmsconnect/cache.tpl'           => 'wh/cmsconnect/Application/views/tpl/cache.tpl',
        
        // Admin
        'modules/wh/cmsconnect/admin/setup_list.tpl'                        => 'wh/cmsconnect/Application/views/admin/tpl/setup_list.tpl',
        'modules/wh/cmsconnect/admin/setup_main.tpl'                        => 'wh/cmsconnect/Application/views/admin/tpl/setup_main.tpl',
        'modules/wh/cmsconnect/admin/setup_testcontent.tpl'                 => 'wh/cmsconnect/Application/views/admin/tpl/setup_testcontent.tpl',
        'modules/wh/cmsconnect/admin/setup_cache_localpages.tpl'            => 'wh/cmsconnect/Application/views/admin/tpl/setup_cache_localpages.tpl',
        'modules/wh/cmsconnect/admin/setup_cache_cmspages.tpl'              => 'wh/cmsconnect/Application/views/admin/tpl/setup_cache_cmspages.tpl',
        'modules/wh/cmsconnect/admin/includes/cache_list_pagination.tpl'    => 'wh/cmsconnect/Application/views/admin/tpl/includes/cache_list_pagination.tpl',
    ),
    
    'blocks'    => array(
        array(
            'template'  => 'content_main.tpl',
            'block'     => 'admin_content_main_form',
            'file'      => '/Application/views/admin/blocks/content_main.tpl/admin_content_main_form.tpl'
        ),
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
    
//    'events'    => array(
//        'onActivate'    => 'cmsconnect_events::onActivate',
//        'onDeactivate'  => 'cmsconnect_events::onDeactivate',
//    ),
);