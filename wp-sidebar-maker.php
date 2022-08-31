<?php

/*
 
Plugin Name: Wp sidebar maker
 
Plugin URI: https://www.techyazh.in/
 
Description: This plugin for create the dynamic sidebar
 
Version: 1.0
 
Author: Mohammed Shameer
 
Author URI: https://www.techyazh.in/
 
License: GPLv2 or later
 
Text Domain: wp-sidebar-maker
 
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


// Load all files
require_once plugin_dir_path(__FILE__).'/load.php';

//Wp_Sidebar_Maker class instance
$wp_sidebar_maker = new Wp_Sidebar_Maker();

register_activation_hook(__FILE__,array($wp_sidebar_maker,'activate_plugin'));
register_deactivation_hook(__FILE__,array($wp_sidebar_maker,'deactivate_plugin'));

