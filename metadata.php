<?php
$sMetadataVersion = '1.1';

$aModule = array(
    'id'                => 'cmsxid',
    'title'             => 'CMSxid',
    'email'             => 'william@whefter.de',
    'url'               => 'http://www.whefter.de',
    'version'           => '1.0',
    'author'            => 'William Hefter',
    'description'       => array(
        'de'    => 'Erlaubt das Einbinden von CMS-Inhalten im OXID eShop.',
        'en'    => 'Enables the fetching and rendering of CMS pages inside OXID eShop.',
    ),
    
    'templates' => array(
        'cmsxid_fe.tpl'             => 'wh/cmsxid/application/views/tpl/cmsxid_fe.tpl',
        
        // Admin
        'cmsxid_setup_main.tpl'     => 'wh/cmsxid/application/views/admin/tpl/cmsxid_setup_main.tpl',
    ),
    
    'blocks'    => array(
        array('template' => 'content_main.tpl',                 'block' => 'admin_content_main_form',                   'file' => '/application/views/admin/blocks/content_main.tpl/admin_content_main_form.tpl'),
    ),
    
    'extend' => array(
        'oxcontent'                 => 'wh/cmsxid/application/models/cmsxid_oxcontent',
        'oxutilsview'               => 'wh/cmsxid/application/models/cmsxid_oxutilsview',
        'oxseodecoder'              => 'wh/cmsxid/application/models/cmsxid_oxseodecoder',
        'oxviewconfig'              => 'wh/cmsxid/application/models/cmsxid_oxviewconfig',
    ),
    
    'files' => array(
        'cmsxid'                    => 'wh/cmsxid/core/cmsxid.php',
        'cmsxid_fe'                 => 'wh/cmsxid/application/controllers/cmsxid_fe.php',
        
        'CmsxidPage'                => 'wh/cmsxid/core/CmsxidPage.php',
        'CmsxidUtils'               => 'wh/cmsxid/core/CmsxidUtils.php',
        
        'cmsxid_dbhandler'          => 'wh/cmsxid/core/cmsxid_dbhandler.php',
        'cmsxid_events'             => 'wh/cmsxid/core/cmsxid_events.php',
        
        // Admin
        'cmsxid_setup'              => 'wh/cmsxid/application/controllers/admin/cmsxid_setup.php',
        'cmsxid_setup_main'         => 'wh/cmsxid/application/controllers/admin/cmsxid_setup_main.php',
        'cmsxid_setup_list'         => 'wh/cmsxid/application/controllers/admin/cmsxid_setup_list.php',
    ),
    
    'settings' => array(
    ),
    
    'events'    => array(
        'onActivate'                => 'cmsxid_events::onActivate',
        'onDeactivate'              => 'cmsxid_events::onDeactivate',
    ),
);