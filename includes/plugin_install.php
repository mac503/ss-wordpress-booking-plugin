<?php

defined( 'ABSPATH' ) or die( 'Access denied.' );

function plugin_install(){

  //TODO automatically create pages with content
  //TODO automatically create user

  //add role and capability
  global $wp_roles;
	$sub = $wp_roles->get_role('Subscriber');
	$wp_roles->add_role('Booking Admin', 'Booking Admin', $sub->capabilities);
	$bookadmin = $wp_roles->get_role('Booking Admin');
	$bookadmin->add_cap('read_private_pages');

  //delta db
  global $wpdb;

   $table_name = $wpdb->prefix . "ss_booking_bookings";
	 $charset_collate = $wpdb->get_charset_collate();

		$sql1 = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
      datetimeRequested datetime NOT NULL,
		  start datetime NOT NULL,
			length mediumint(5) NOT NULL,
			type tinytext NOT NULL,
		  nombre tinytext NOT NULL,
			apellidos tinytext NOT NULL,
			email tinytext NOT NULL,
			phone tinytext NOT NULL,
			confirmed tinyint(1) NULL DEFAULT 0,
			deleted tinyint(1) NULL DEFAULT 0,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		$table_name = $wpdb->prefix . "ss_booking_windows";

		$sql2 = "CREATE TABLE $table_name (
		  windowsdate date NOT NULL,
      idWithinDay tinyint(1) NOT NULL,
      start smallint(6),
      end smallint(6),
		  PRIMARY KEY  (windowsdate, idWithinDay)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql1 );
		dbDelta( $sql2 );
}

?>
