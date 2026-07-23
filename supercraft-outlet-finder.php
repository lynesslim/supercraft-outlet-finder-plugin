<?php
/**
 * Plugin Name: Supercraft Outlet Finder
 * Description: Interactive outlet finder with Leaflet map. Manage outlets and styling from wp-admin. Shortcode: <code>[supercraft_outlets]</code>
 * Version:     1.0.11
 * Author:      Your Name
 * Text Domain: supercraft-of
 * Domain Path: /languages
 *
 * @package Supercraft_Outlet_Finder
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SC_OF_VERSION', '1.0.11');
define('SC_OF_FILE', __FILE__);
define('SC_OF_PATH', plugin_dir_path(__FILE__));
define('SC_OF_URL', plugin_dir_url(__FILE__));

require_once SC_OF_PATH . 'includes/class-post-type.php';
require_once SC_OF_PATH . 'includes/class-settings.php';
require_once SC_OF_PATH . 'includes/class-shortcode.php';
require_once SC_OF_PATH . 'lib/plugin-update-checker.php';

new SC_OF_Post_Type();
new SC_OF_Shortcode();

if (is_admin()) {
    new SC_OF_Settings();
}

/**
 * Initialize Plugin Update Checker for automatic updates from GitHub
 */
$updateChecker = \YahnisElsts\PluginUpdateChecker\v5p5\PucFactory::buildUpdateChecker(
    'https://github.com/lynesslim/supercraft-outlet-finder-plugin',
    SC_OF_FILE,
    'supercraft-outlet-finder'
);

if ($updateChecker) {
    $updateChecker->setBranch('main');

    // Enable downloading release assets (.zip) attached to GitHub Releases if available
    $vcsApi = $updateChecker->getVcsApi();
    if ($vcsApi && method_exists($vcsApi, 'enableReleaseAssets')) {
        $vcsApi->enableReleaseAssets();
    }

    // Support optional private repo authentication via wp-config constant SC_OF_GITHUB_TOKEN
    if (defined('SC_OF_GITHUB_TOKEN') && SC_OF_GITHUB_TOKEN) {
        $updateChecker->setAuthentication(SC_OF_GITHUB_TOKEN);
    }
}

