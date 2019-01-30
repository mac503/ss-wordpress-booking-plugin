<?php

defined( 'ABSPATH' ) or die( 'Access denied.' );

add_action('wp_enqueue_scripts', 'enqueue');

function enqueue(){
	//always load this stuff
	$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'css/sabai-salud-booking-global.css' ));
	wp_enqueue_style('global', PUBLIC_DIR.'css/sabai-salud-booking-global.css', false, $ver);
	//client only
	if(is_page('reservar-masaje')){
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'css/sabai-salud-booking-calendar.css' ));
		wp_enqueue_style('ss-booking-calendar', PUBLIC_DIR.'css/sabai-salud-booking-calendar.css', false, $ver);
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'js/calendar.js' ));
		wp_enqueue_script('calendar', PUBLIC_DIR.'js/calendar.js', array('jquery'), $ver);
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'js/customer.js' ));
		wp_enqueue_script('customer', PUBLIC_DIR.'js/customer.js', array('jquery', 'calendar'), $ver);
		wp_localize_script( 'customer', 'ajax', array(
	    'url' => admin_url( 'admin-ajax.php' )
		) );
	}
	//manage only
	if(is_page('manage-bookings')){
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'css/sabai-salud-booking-calendar.css' ));
		wp_enqueue_style('ss-booking-calendar', PUBLIC_DIR.'css/sabai-salud-booking-calendar.css', false, $ver);
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'js/calendar.js' ));
		wp_enqueue_script('calendar', PUBLIC_DIR.'js/calendar.js', array('jquery'), $ver);
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'js/admin.js' ));
		wp_enqueue_script('admin', PUBLIC_DIR.'js/admin.js', array('jquery', 'calendar'), $ver);
		wp_localize_script( 'admin', 'ajax', array(
	    'url' => admin_url( 'admin-ajax.php' )
		) );
		$ver = date("ymd-Gis", filemtime( PUBLIC_DIR . 'css/sabai-salud-booking-admin.css' ));
		wp_enqueue_style('admin', PUBLIC_DIR.'css/sabai-salud-booking-admin.css', array('ss-booking-calendar'), $ver);
	}
}

?>
