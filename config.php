<?php

$config = array();

// a unique key that identifies this application - DO NOT LEAVE THIS EMPTY!
$config['app_key'] = 'your_unique_app_key_here';

// a secret key to be used during encryption
$config['encryption_key'] = 'your_encryption_key_here';

// URL running the proxy app
$config['app_url'] = 'http://localhost/proxy/'; // or your live proxy URL

// URL mode (you already set this to IP unique)
$config['url_mode'] = 2;

// Enable sessions only if url_mode is set to 3 (not needed here)
$config['session_enable'] = false;

// Expose PHP in headers
$config['expose_php'] = true;

// Enable necessary plugins (including cookies)
$config['plugins'] = array(
    'HeaderRewrite',
    'Stream',
    'Cookie',       // Cookie management enabled
    'Proxify',
    'UrlForm',
    'Youtube',
    'DailyMotion',
    'RedTube',
    'XHamster',
    'XVideos',
    'Xnxx',
    'Pornhub',
    'Twitter'
);

// Additional cURL options (you can configure proxy settings if necessary)
$config['curl'] = array(
    // CURLOPT_PROXY => '',
    // CURLOPT_CONNECTTIMEOUT => 5
);

// Redirect to sbobet.com when accessing the proxy
$config['index_redirect'] = 'https://www.sbobet.com';

// Return the configuration array
return $config;
