<?php

$sLangName  = "Deutsch";

$aLang = array(
'charset'                                               => 'UTF-8',

'cmsxid_setup'                                          => 'CMSxid',
'cmsxid_setup_main'                                     => 'CMSxid-Einstellungen',

'CMSXID_ADMIN_SETTINGS_DEMO_PATH'                       => 'Vorschau Pfad-basierter Abruf',
'CMSXID_ADMIN_SETTINGS_DEMO_ID'                         => 'Vorschau ID-basierter Abruf',
'CMSXID_ADMIN_SETTINGS_DEMO_EXAMPLE'                    => 'beispiel',

'CMSXID_ADMIN_SETTINGS_aCmsxidBaseUrls'                 => 'Basis-URL zur CMS-Installation',
'CMSXID_ADMIN_SETTINGS_aCmsxidBaseSslUrls'              => 'Basis-SSL-URL zur CMS-Installation',
'CMSXID_ADMIN_SETTINGS_aCmsxidPagePaths'                => 'Pfad-basiert: relativer Pfad',
'CMSXID_ADMIN_SETTINGS_aCmsxidIdParams'                 => 'ID-basiert: Query-Parameter für Page-ID',
'CMSXID_ADMIN_SETTINGS_aCmsxidIdParams_HELP'            => 'Für TYPO3 z.B.: Wert \'id\'',
'CMSXID_ADMIN_SETTINGS_aCmsxidLangParams'               => 'Query-Parameter für Sprache',
'CMSXID_ADMIN_SETTINGS_aCmsxidLangParams_HELP'          => 'Für TYPO3 z.B.: Wert \'L=0\', falls die TYPO3-seitige ID für die aktuelle Sprache 0 ist.',
'CMSXID_ADMIN_SETTINGS_aCmsxidParams'                   => 'Zusätzliche Query-Parameter',
'CMSXID_ADMIN_SETTINGS_aCmsxidSearchUrls'               => 'Such-URL',
'CMSXID_ADMIN_SETTINGS_aCmsxidSeoIdents'                => 'SEO-Schnipsel für OXID-URLs',

'CMSXID_ADMIN_SETTINGS_blCmsxidLeaveUrls'               => 'URLs nicht umschreiben',

'CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefault'               => 'Cache-Lebensdauer (Standard: 3600 s)',
'CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefaultRnd'            => 'Cache-Lebensdauer zufällig streuen um ',
'CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefaultRnd_HELP'       => 'Um bei vielen mit CMSxid zu ladenden Elementen pro Seite zu verhindern, dass nach Ablauf des Caches ein Seitenaufruf sehr viele Aufrufe zum CMS produziert, sollte die Cache-Lebensdauer pro Element leicht von der Cache-Lebensdauer-Einstellung abweichen. Dazu sollte sie eine zufällige Komponente enhalten, die hier eingestellt werden kann. Bei einer Cache-Lebensdauer von 600 Sekunden bedeuten 10% Streuung, dass die tatsächliche Cache-Lebensdauer für jedes Element zwischen 540 und 660 Sekunden liegen kann.',

'CMSXID_ADMIN_SETTINGS_GENERAL'                         => 'Allgemeine Einstellungen',

'CMSXID_ADMIN_CONTENT_PAGE_ID'                          => '(CMSxid) Page-ID',
'CMSXID_ADMIN_CONTENT_PAGE_ID_HELP'                     => 'CMS-Page-ID; die Seite mit der zugehörigen ID wird in der eingestellten Sprache für den aktuellen Shop geladen. <p class="warning">Der hier konfigurierte Inhalt der CMS-Seite wird ÜBERSCHRIEBEN!</p>',
'CMSXID_ADMIN_CONTENT_PAGE'                             => '(CMSxid) Page-URL',
'CMSXID_ADMIN_CONTENT_PAGE_HELP'                        => 'URL relativ zur konfigurierten Basis-URL für den aktuellen Shop und die aktuelle Sprache. <p class="warning">Das Laden über die Page-ID geht vor, falls sowohl ID als auch URL angegeben sind!</p> <p class="warning">Der hier konfigurierte Inhalt der CMS-Seite wird ÜBERSCHRIEBEN!</p>',
);
