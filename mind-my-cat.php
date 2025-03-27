<?php

/**
 * Plugin Name: Mindmycat Pet Service
 * Description: The most basic draft of the plugin.
 * Plugin URI: https://atiqsu.com/wp-next-bridge
 * Author: WPmessiah
 * Version: 1.0.0
 * Author URI: https://boomdevs.co
 *
 * Text Domain: mind-my-cat
 * Domain Path: /languages
 *
 */



const MMC_VERSION = '1.0.0';
const MMC_DEV_MODE = true;
const PLUGIN_MODEL_PREFIX = 'mmc_';

define('MMC_PLG_PATH', plugin_dir_path(__FILE__));
define('MMC_PLG_URL', plugin_dir_url(__FILE__));

defined('ABSPATH') || exit;


require __DIR__ . '/Autoloader.php';


register_activation_hook(__FILE__, function () {
    \Mindmycat\Handler\Installer::activate();
});


register_deactivation_hook(__FILE__, function () {
    \Mindmycat\Handler\Installer::deactivate();
});

new \Mindmycat\Boot(__FILE__);



