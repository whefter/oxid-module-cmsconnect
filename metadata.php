<?php
$sMetadataVersion = '1.1';

$aModule = array(
    'id'                => 'cmsxid',
    'title'             => 'CMSxid',
    'email'             => 'william@whefter.de',
    'url'               => 'http://www.whefter.de',
    'version'           => '1.2',
    'author'            => 'William Hefter',
    'description'       => array(
        'de'    => 'Erlaubt das Einbinden von CMS-Inhalten im OXID eShop.',
        'en'    => 'Enables the fetching and rendering of CMS pages inside OXID eShop.',
    ),
    
    'templates' => array(
        'modules/cmsxid/fe.tpl'             => 'wh/cmsxid/application/views/tpl/fe.tpl',
        'modules/cmsxid/async.tpl'          => 'wh/cmsxid/application/views/tpl/async.tpl',
        'modules/cmsxid/async_snippet.tpl'  => 'wh/cmsxid/application/views/tpl/async_snippet.tpl',
        
        // Admin
        'cmsxid_setup_list.tpl'         => 'wh/cmsxid/application/views/admin/tpl/cmsxid_setup_list.tpl',
        'cmsxid_setup_main.tpl'         => 'wh/cmsxid/application/views/admin/tpl/cmsxid_setup_main.tpl',
        'cmsxid_setup_testcontent.tpl'  => 'wh/cmsxid/application/views/admin/tpl/cmsxid_setup_testcontent.tpl',
    ),
    
    'blocks'    => array(
        array('template' => 'content_main.tpl',                 'block' => 'admin_content_main_form',                   'file' => '/application/views/admin/blocks/content_main.tpl/admin_content_main_form.tpl'),
    ),
    
    'extend' => array(
        'oxconfig'      => 'wh/cmsxid/application/models/cmsxid_oxconfig',
        'oxcontent'     => 'wh/cmsxid/application/models/cmsxid_oxcontent',
        'oxutilsview'   => 'wh/cmsxid/application/models/cmsxid_oxutilsview',
        'oxseodecoder'  => 'wh/cmsxid/application/models/cmsxid_oxseodecoder',
        'oxseoencoder'  => 'wh/cmsxid/application/models/cmsxid_oxseoencoder',
        'oxviewconfig'  => 'wh/cmsxid/application/models/cmsxid_oxviewconfig',
    ),
    
    'files' => array(
        'cmsxid'                    => 'wh/cmsxid/core/cmsxid.php',
        'cmsxid_fe'                 => 'wh/cmsxid/application/controllers/cmsxid_fe.php',
        'cmsxid_async'              => 'wh/cmsxid/application/controllers/cmsxid_async.php',
        
        'CmsxidPage'                => 'wh/cmsxid/core/CmsxidPage.php',
        'CmsxidPathPage'            => 'wh/cmsxid/core/CmsxidPathPage.php',
        'CmsxidIdPage'              => 'wh/cmsxid/core/CmsxidIdPage.php',
        'CmsxidUtils'               => 'wh/cmsxid/core/CmsxidUtils.php',
        
        'cmsxid_dbhandler'          => 'wh/cmsxid/core/cmsxid_dbhandler.php',
        'cmsxid_events'             => 'wh/cmsxid/core/cmsxid_events.php',
        
        // Admin
        'cmsxid_setup'              => 'wh/cmsxid/application/controllers/admin/cmsxid_setup.php',
        'cmsxid_setup_list'         => 'wh/cmsxid/application/controllers/admin/cmsxid_setup_list.php',
        'cmsxid_setup_main'         => 'wh/cmsxid/application/controllers/admin/cmsxid_setup_main.php',
        'cmsxid_setup_testcontent'  => 'wh/cmsxid/application/controllers/admin/cmsxid_setup_testcontent.php',
    ),
    
    'settings' => array(
    ),
    
    'events'    => array(
        'onActivate'    => 'cmsxid_events::onActivate',
        'onDeactivate'  => 'cmsxid_events::onDeactivate',
    ),
);