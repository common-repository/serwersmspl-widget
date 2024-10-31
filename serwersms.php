<?php

/*
 * Plugin Name: SerwerSMS.pl
 * Plugin URI: https://serwersms.pl/integracje/moduly-i-wtyczki-sms/81-wordpress
 * Description: Integracja z platformą SerwerSMS.pl
 * Version: 1.3
 * Author: SerwerSMS.pl
 * Author URI: https://serwersms.pl
 * Text Domain: serwersms
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'SERWERSMS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SERWERSMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once(SERWERSMS_PLUGIN_DIR.'api/vendor/autoload.php');
require_once(SERWERSMS_PLUGIN_DIR.'class.serwersms.widget.php');
require_once(SERWERSMS_PLUGIN_DIR.'serwersms.ajax.php');
require_once(SERWERSMS_PLUGIN_DIR.'class.serwersms.php');
add_action('init',array('SerwerSms','ssms_init'));
