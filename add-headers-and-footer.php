<?php

/*
Plugin Name: Add Headers and Footers Script
Plugin URI: https://add_hf.com/
Description: Plugin to add headers and footers in your page.
Version: 1.0
Author: wpzita
Author URI: https://wpzita.com/
Text Domain: ahafs
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('AHAFS_URL', plugin_dir_url(__FILE__));
define('AHAFS_PATH', plugin_dir_path(__FILE__));

// including function.php file where all hooks and functions exist
include_once( AHAFS_PATH . 'inc/fnc.php' );

add_action('plugins_loaded','ahafs_include_all');

function ahafs_include_all(){
	$hf_obj = new Ahafs_Script();
}