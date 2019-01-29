<?php
/*
Plugin Name:  Sabai Salud Massage Booking
Version:      1
Author:       Mike Carter
*/
defined( 'ABSPATH' ) or die( 'Access denied.' );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

register_activation_hook( __FILE__, 'plugin_install' );

define( 'PUBLIC_DIR', plugin_dir_url( __FILE__ ) . 'public/' );
foreach ( glob( plugin_dir_path( __FILE__ ) . "includes/admin_menu/*.php" ) as $file ) {
  include_once $file;
}
foreach ( glob( plugin_dir_path( __FILE__ ) . "includes/ajax/*.php" ) as $file ) {
  include_once $file;
}
foreach ( glob( plugin_dir_path( __FILE__ ) . "includes/helpers/*.php" ) as $file ) {
  include_once $file;
}
foreach ( glob( plugin_dir_path( __FILE__ ) . "includes/pages/*.php" ) as $file ) {
  include_once $file;
}
foreach ( glob( plugin_dir_path( __FILE__ ) . "includes/*.php" ) as $file ) {
    include_once $file;
}

?>
