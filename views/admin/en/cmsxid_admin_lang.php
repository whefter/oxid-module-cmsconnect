<?php
$sLangName  = "English";

$aLang = array(
'charset'                                               => 'UTF-8',

'cmsxid_setup'                                          => 'CMSxid',
'cmsxid_setup_main'                                     => 'CMSxid settings',

'CMSXID_ADMIN_SETTINGS_SOURCE'                          => 'Source',
'CMSXID_ADMIN_SETTINGS_DEMO'                            => 'Preview',
'CMSXID_ADMIN_SETTINGS_DEMO_PATH'                       => 'Path-based fetching',
'CMSXID_ADMIN_SETTINGS_DEMO_ID'                         => 'ID-based fetching',
'CMSXID_ADMIN_SETTINGS_DEMO_EXAMPLE'                    => 'example',

'CMSXID_ADMIN_SETTINGS_aCmsxidBaseUrls'                 => 'Base URL to the CMS',
'CMSXID_ADMIN_SETTINGS_aCmsxidBaseSslUrls'              => 'Base SSL URL to the CMS',
'CMSXID_ADMIN_SETTINGS_aCmsxidPagePaths'                => 'Path-based: relative path',
'CMSXID_ADMIN_SETTINGS_aCmsxidIdParams'                 => 'ID-based: query parameter for page ID',
'CMSXID_ADMIN_SETTINGS_aCmsxidIdParams_HELP'            => 'Example value for TYPO3: \'id\'',
'CMSXID_ADMIN_SETTINGS_aCmsxidLangParams'               => 'Query parameter string for language ID',
'CMSXID_ADMIN_SETTINGS_aCmsxidLangParams_HELP'          => 'Example value for TYPO3: \'L=0\', if the TYPO3 language ID for the current OXID language is 0.',
'CMSXID_ADMIN_SETTINGS_aCmsxidParams'                   => 'Additional query parameters',
'CMSXID_ADMIN_SETTINGS_aCmsxidSearchUrls'               => 'Search URL',
'CMSXID_ADMIN_SETTINGS_aCmsxidSeoIdents'                => 'SEO-Snippet for OXID URLs',
'CMSXID_ADMIN_SETTINGS_iCmsxidCurlConnectTimeout'       => 'Connect-Timeout for cURL calls in seconds (default: 1 s)',
'CMSXID_ADMIN_SETTINGS_iCmsxidCurlExecuteTimeout'       => 'Execute-Timeout for cURL calls in seconds (default: 3 s)',
'CMSXID_ADMIN_SETTINGS_blCmsxidEnableDummyContent'      => 'Server dummy content',
'CMSXID_ADMIN_SETTINGS_blCmsxidEnableDummyContent_HELP' => 'Nothing is fetched from remote servers, and dummy content is returned by all functions. For debug purposes.',

'CMSXID_ADMIN_SETTINGS_blCmsxidLeaveUrls'               => 'Do not rewrite URLs',

'CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefault'               => 'Cache lifetime (default: 3600 s)',
'CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefaultRnd'            => 'Randomize cache lifetime by',
'CMSXID_ADMIN_SETTINGS_iCmsxidTtlDefaultRnd_HELP'       => 'To prevent large amounts of request being sent to the CMS when the cache expires in shops with numerous CMSxid calls, the cache lifetime should be randomized for each content object. A randomization of 10% means a cache lifetime of 600 seconds would result in effective lifetimes between 540 and 660 seconds.',

'CMSXID_ADMIN_SETTINGS_GENERAL'                         => 'General Settings',

'CMSXID_ADMIN_CONTENT_PAGE_ID'                          => '(CMSxid) Page ID',
'CMSXID_ADMIN_CONTENT_PAGE_ID_HELP'                     => 'ID of the page to load via CMSxid. <p class="warning">Content configured here will be OVERWRITTEN!</p>',
'CMSXID_ADMIN_CONTENT_PAGE'                             => '(CMSxid) Page-URL',
'CMSXID_ADMIN_CONTENT_PAGE_HELP'                        => 'URL, relative to the configured base URL for the current shop and language. <p class="warning">Loading via page ID takes precedence if both fields are specified.</p> <p class="warning">Content configured here will be OVERWRITTEN!</p>',
);
