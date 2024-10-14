<?php

define('PROXY_START', microtime(true));

require("vendor/autoload.php");

use Proxy\Config;
use Proxy\Http\Request;
use Proxy\Proxy;

// Ensure cURL is loaded
if (!function_exists('curl_version')) {
    die("cURL extension is not loaded!");
}

// Load the proxy configuration
Config::load('./config.php');
Config::load('./custom_config.php'); // If you have any custom configuration

// Ensure app_key is not empty
if (!Config::get('app_key')) {
    die("app_key inside config.php cannot be empty!");
}

// Remove 'X-Powered-By' header if expose_php is disabled
if (!Config::get('expose_php')) {
    header_remove('X-Powered-By');
}

// Start PHP session if enabled
if (Config::get('session_enable')) {
    session_start();
}

// Set encryption keys for URL encoding based on url_mode
if (Config::get('url_mode') == 2) {
    Config::set('encryption_key', md5(Config::get('app_key') . $_SERVER['REMOTE_ADDR']));
} elseif (Config::get('url_mode') == 3) {
    Config::set('encryption_key', md5(Config::get('app_key') . session_id()));
}

// Close session write to prevent blocking
if (Config::get('session_enable')) {
    session_write_close();
}

// Handle form submission for the URL
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $url = add_http($url);

    header("HTTP/1.1 302 Found");
    header('Location: ' . proxify_url($url));
    exit;

} elseif (!isset($_GET['q'])) {
    // Default redirect to sbobet.com if no URL is provided
    header("HTTP/1.1 302 Found");
    header("Location: https://www.sbobet.com");
    exit;
}

// Decode the 'q' parameter from the URL (used by the proxy)
$url = url_decrypt($_GET['q']);

$proxy = new Proxy();

// Load plugins for proxy functionality
foreach (Config::get('plugins', array()) as $plugin) {
    $plugin_class = $plugin . 'Plugin';

    if (file_exists('./plugins/' . $plugin_class . '.php')) {
        // Load user-defined plugin
        require_once('./plugins/' . $plugin_class . '.php');
    } elseif (class_exists('\\Proxy\\Plugin\\' . $plugin_class)) {
        // Use native plugin from php-proxy package
        $plugin_class = '\\Proxy\\Plugin\\' . $plugin_class;
    }

    // Add the plugin to the proxy
    $proxy->addSubscriber(new $plugin_class());
}

try {
    // Create the request from global variables
    $request = Request::createFromGlobals();

    // Clear GET parameters such as ?q=
    $request->get->clear();

    // Forward the request to the actual URL
    $response = $proxy->forward($request, $url);

    // Send the response back to the client
    $response->send();

} catch (Exception $ex) {
    // Handle errors by rendering the main template with the error message
    echo render_template("./templates/main.php", array(
        'url' => $url,
        'error_msg' => $ex->getMessage(),
        'version' => Proxy::VERSION
    ));
}
