<?php

defined( 'ABSPATH' ) or die( 'Access denied.' );

add_action( 'init', 'register_shortcodes');

function register_shortcodes(){
	add_shortcode('client_booking_page', 'client_booking_page');
	add_shortcode('booking_request_confirmation', 'booking_request_confirmation');
	add_shortcode('manage_bookings_page', 'manage_bookings_page');
	add_shortcode('test', 'z_test');
}

?>
