<?php
function get_single_booking_data($id){
  global $wpdb;
  return $wpdb->get_row("select * from {$wpdb->prefix}ss_booking_bookings where id={$id}", ARRAY_A);
}
?>
