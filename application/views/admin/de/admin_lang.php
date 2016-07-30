<?php
$sLangName  = "Deutsch";

$aLang = array(
'charset'                                               => 'UTF-8',

'cmsconnect_setup'                                          => 'CMSconnect',
'cmsconnect_setup_main'                                     => 'Einstellungen',
'cmsconnect_setup_testcontent'                              => 'Test-Inhalt',
'cmsconnect_setup_cache_localpages'                         => 'Cache: Lokale Seiten <=> CMS-Seiten',
'cmsconnect_setup_cache_cmspages'                           => 'Cache: CMS-Seiten',


'CMSCONNECT_ADMIN_SETTINGS_SOURCE'                          => 'Quelle',
'CMSCONNECT_ADMIN_SETTINGS_DEMO'                            => 'Vorschau',
'CMSCONNECT_ADMIN_SETTINGS_DEMO_PATH'                       => 'Pfad-basierter Abruf',
'CMSCONNECT_ADMIN_SETTINGS_DEMO_ID'                         => 'ID-basierter Abruf',
'CMSCONNECT_ADMIN_SETTINGS_DEMO_EXAMPLE'                    => 'beispiel',

'CMSCONNECT_ADMIN_SETTINGS_aCMScBaseUrls'             => 'Basis-URL zur CMS-Installation',
'CMSCONNECT_ADMIN_SETTINGS_aCMScBaseSslUrls'          => 'Basis-SSL-URL zur CMS-Installation',
'CMSCONNECT_ADMIN_SETTINGS_aCMScPagePaths'            => 'Pfad-basiert: relativer Pfad',
'CMSCONNECT_ADMIN_SETTINGS_aCMScIdParams'             => 'ID-basiert: Query-Parameter für Page-ID',
'CMSCONNECT_ADMIN_SETTINGS_aCMScIdParams_HELP'        => 'Für CMS z.B.: Wert \'id\'',
'CMSCONNECT_ADMIN_SETTINGS_aCMScLangParams'           => 'Query-Parameter für Sprache',
'CMSCONNECT_ADMIN_SETTINGS_aCMScLangParams_HELP'      => 'Für CMS z.B.: Wert \'L=0\', falls die CMS-seitige ID für die aktuelle Sprache 0 ist.',
'CMSCONNECT_ADMIN_SETTINGS_aCMScParams'               => 'Zusätzliche Query-Parameter',
'CMSCONNECT_ADMIN_SETTINGS_aCMScSearchUrls'           => 'Such-URL',
'CMSCONNECT_ADMIN_SETTINGS_aCMScSeoIdents'            => 'SEO-Schnipsel für OXID-URLs',

'CMSCONNECT_ADMIN_SETTINGS_GENERAL'                         => 'Allgemeine Einstellungen',

'CMSCONNECT_ADMIN_SETTINGS_blCMScLeaveUrls'                 => 'URLs nicht umschreiben',
'CMSCONNECT_ADMIN_SETTINGS_blCMScEnableTestContent'         => 'Test-Content ausliefern',
'CMSCONNECT_ADMIN_SETTINGS_blCMScEnableTestContent_HELP'    => 'Es werden keine Inhalte mittels cURL abgerufen, sondern der Test-Content zurückgegeben. Für Debug-Zwecke gedacht.',
'CMSCONNECT_ADMIN_SETTINGS_blCMScSslDontVerifyPeer'         => 'SSL-Peer nicht überprüfen',
'CMSCONNECT_ADMIN_SETTINGS_blCMScSslDontVerifyPeer_HELP'    => 'Beim abrufen von SSL-Seiten das Zertifikat der Gegenstelle nicht überprüfen. <p class="warning">ACHTUNG SICHERHEITSRISIKO: Diese Option sollte nur zu Debug-Zwecken eingeschaltet werden.</p>',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCurlConnectTimeout'         => 'Connect-Timeout für cURL-Aufrufe in Sekunden (Standard: %s ms)',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCurlExecuteTimeout'         => 'Execute-Timeout für cURL-Aufrufe in Sekunden (Standard: %s ms)',
'CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefault'                 => 'Cache-Lebensdauer (Standard: %s s)',
'CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefaultRnd'              => 'Streuung Cache-Lebensdauer (%%) (Standard: %s %%)',
'CMSCONNECT_ADMIN_SETTINGS_sCMScTtlDefaultRnd_HELP'         => 'Um bei vielen mit CMSconnect zu ladenden Elementen pro Seite zu verhindern, dass nach Ablauf des Caches ein Seitenaufruf sehr viele Aufrufe zum CMS produziert, sollte die Cache-Lebensdauer pro Element leicht von der Cache-Lebensdauer-Einstellung abweichen. Dazu sollte sie eine zufällige Komponente enhalten, die hier eingestellt werden kann. Bei einer Cache-Lebensdauer von 600 Sekunden bedeuten 10% Streuung, dass die tatsächliche Cache-Lebensdauer für jedes Element zwischen 540 und 660 Sekunden liegen kann.',

'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine'               => 'Cache-Engine für lokale Seiten',
'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_HELP'          => 'Speichert, auf welcher lokalen Seite welche CMS-Seiten eingebunden sind. <div class="error">Kann zu Fehlern führen, wenn passende Module nicht geladen sind! Vorsicht bei Auswahl von Werten, die nicht "automatisch" sind!</div>',
'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_auto'          => 'Automatisch (empfohlen)',
'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_OxidFileCache' => 'OXID File-Cache',
'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_DB'            => 'Datenbank',
'CMSCONNECT_ADMIN_SETTINGS_sCMScLocalPageCacheEngine_memcache'      => 'memcache',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine'                 => 'Cache-Engine für CMS-Seiten',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_HELP'            => 'Intelligenter Cache für abgerufene CMS-Seiten, damit diese nicht bei jedem lokalen Seitenaufruf neu geladen werden müssen.  <div class="error">Kann zu Fehlern führen, wenn passende Module nicht geladen sind! Vorsicht bei Auswahl von Werten, die nicht "automatisch" sind!</div>',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_auto'            => 'Automatisch (empfohlen)',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_OxidFileCache'   => 'OXID File-Cache',
'CMSCONNECT_ADMIN_SETTINGS_sCMScCmsPageCacheEngine_memcache'        => 'memcache',


'CMSCONNECT_ADMIN_TESTCONTENT'                              => 'Test-Inhalt',
'CMSCONNECT_ADMIN_TESTCONTENT_INFO'                         => 'Test-Inhalt, der auf alle Anfragen ausgeliefert wird.',


'CMSCONNECT_ADMIN_CONTENT_PAGE_ID'                          => '(CMSconnect) Page-ID',
'CMSCONNECT_ADMIN_CONTENT_PAGE_ID_HELP'                     => 'CMS-Page-ID; die Seite mit der zugehörigen ID wird in der eingestellten Sprache für den aktuellen Shop geladen. <p class="warning">Der hier konfigurierte Inhalt der CMS-Seite wird ÜBERSCHRIEBEN!</p>',
'CMSCONNECT_ADMIN_CONTENT_PAGE'                             => '(CMSconnect) Page-URL',
'CMSCONNECT_ADMIN_CONTENT_PAGE_HELP'                        => 'URL relativ zur konfigurierten Basis-URL für den aktuellen Shop und die aktuelle Sprache. <p class="warning">Das Laden über die Page-ID geht vor, falls sowohl ID als auch URL angegeben sind!</p> <p class="warning">Der hier konfigurierte Inhalt der CMS-Seite wird ÜBERSCHRIEBEN!</p>',
);
