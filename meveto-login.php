<?php

/**
 * Plugin Name: Meveto
 * Plugin URI: https://meveto.com
 * Description: This plugin will help you integrate Meveto in your WordPress site to use Meveto's password-less authentication both for you and your users.
 * Version: 1.0.3
 * Author: Meveto Inc
 * Author URI: https://meveto.com
 * License: GPL2
 */

ob_start();

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('MEVETO_OAUTH_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_meveto_oauth()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-meveto-oauth-activator.php';
    Meveto_OAuth_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_meveto_oauth()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-meveto-oauth-deactivator.php';
    Meveto_OAuth_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_meveto_oauth');
register_deactivation_hook(__FILE__, 'deactivate_meveto_oauth');

require_once plugin_dir_path(__FILE__) . 'shortcodes/class-meveto-oauth-public-button.php';
$mopb = new Meveto_OAuth_Public_Button;
$mopb->AddShortCode();

require_once plugin_dir_path(__FILE__) . 'shortcodes/class-meveto-oauth-connect-button.php';
$mocb = new Meveto_OAuth_Connect_Button;
$mocb->AddShortCode();

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-meveto-oauth.php';

/**
 * Load vendor libs
 */
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_meveto_oauth()
{
    $plugin = new Meveto_OAuth();
    $plugin->run();
}

run_meveto_oauth();
