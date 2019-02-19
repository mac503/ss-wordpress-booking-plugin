<?php
  function get_script_location(){
    global $wpdb;
    return $wpdb->get_var("select value from {$wpdb->prefix}ss_booking_settings where setting='script_location'");
  }
?>
