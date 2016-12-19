<?php
$sMetadataVersion = '1.1';

$aModule = array(
    'id'                => 'cmsconnect',
    'title'             => 'CMSconnect',
    'email'             => 'william@whefter.de',
    'url'               => 'http://www.whefter.de',
    'version'           => '1.0',
    'author'            => 'William Hefter',
    'description'       => array(
        'de'    => 'Erlaubt das Einbinden von CMS-Inhalten im OXID eShop.',
        'en'    => 'Enables the fetching and rendering of CMS pages inside OXID eShop.',
    ),
    
    'templates' => array(
        'modules/wh/cmsconnect/frontend.tpl'        => 'wh/cmsconnect/application/views/tpl/frontend.tpl',
        'modules/wh/cmsconnect/async.tpl'           => 'wh/cmsconnect/application/views/tpl/async.tpl',
        'modules/wh/cmsconnect/async_snippet.tpl'   => 'wh/cmsconnect/application/views/tpl/async_snippet.tpl',
        
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
        
        'cmsconnect_dbhandler'          => 'wh/cmsconnect/core/cmsconnect_dbhandler.php',
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
    ),
    
    'events'    => array(
        'onActivate'    => 'cmsconnect_events::onActivate',
        'onDeactivate'  => 'cmsconnect_events::onDeactivate',
    ),
);