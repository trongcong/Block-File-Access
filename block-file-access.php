<?php

/*
Plugin Name: Block File Access
Plugin URI: https://wordpress.org/plugins/block-file-access
Description: Prevent users from accessing and downloading files in the wp-contents, ex: .txt, .doc, .pdf, .zip, ... except images.
Version: 1.0
Author: NTC
Author URI: http://2dev4u.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  bfa
Domain Path:  /languages
*/

defined( 'ABSPATH' ) OR exit;

require_once( "includes/init.php" );
require_once( "includes/setting.php" );
register_activation_hook( __FILE__, 'bfa_activate' );
register_deactivation_hook( __FILE__, 'bfa_deactivation' );