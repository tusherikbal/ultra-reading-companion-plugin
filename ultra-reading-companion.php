<?php 
/**
 * Plugin Name: Ultra Reading Companion
 * Description: Adds a simple "Estimated Reading Time" to the top of posts.
 * Version: 1.1
 * Author: Tusher Ikbal
 * Text Domain: ultra-reading-companion
 * License: GPL2+
 */

// 1. Correct security check (Fixes the immediate issue)
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


//constant define for path plugin dir and url

define('URC_PATH', plugin_dir_path(__FILE__));
define ('urc_url', plugin_dir_url(__FILE__));


//includes core class file
require_once URC_PATH . 'inc/urc-init.php';

//initialize the plugin
if(class_exists('urc_init')){
    new urc_init();
}

