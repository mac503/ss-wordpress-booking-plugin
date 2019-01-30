<?php
  function get_default_availability($forUpdate){

    global $wpdb;
    if($forUpdate == true) $lockString = " for update";

    $defaultOpeningsRaw = $wpdb->get_results("select * from {$wpdb->prefix}ss_booking_default_windows$lockString", ARRAY_A );
    $defaultOpenings = array(
      array(),
      array(),
      array(),
      array(),
      array(),
      array(),
      array()
    );
    foreach($defaultOpeningsRaw as $opening){
      array_push($defaultOpenings[intval($opening["weekday"])], $opening);
    }

    return $defaultOpenings;

  }
?>
