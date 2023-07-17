<?php 
/**
 * Plugin Name: Webp easy
 * Plugin URI: https://github.com/ProductionPanic/WpWebpEasy.git
 * Description: This wordpress plugin will convert all your images to webp format and serve them to supported browsers.
 * Version: 1.0.0
 * Author: ProductionPanic
 * Author URI: https://github.com/PanicProduction
 * Requires at least: 5.2
 * Requires PHP: 8.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants.
require_once plugin_dir_path( __FILE__ ) . 'webp-easy-config.php';

// include plugin files
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';