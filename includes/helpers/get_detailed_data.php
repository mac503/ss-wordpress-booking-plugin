<?php
  function get_detailed_data($forUpdate){
    $data = get_basic_availability($forUpdate, true);

    global $wpdb;
    if($forUpdate == true) $lockString = " for update";
    $data["bookings"] = $wpdb->get_results("select * from {$wpdb->prefix}ss_booking_bookings$lockString", ARRAY_A );
    $data["types"] = get_massage_types();
    $bookingWindows = $wpdb->get_results("select * from {$wpdb->prefix}ss_booking_windows where windowsdate between '{$data["startDate"]}' and '{$data["endDate"]}'$lockString", OBJECT );
    $data["availability"] = array();
    foreach($bookingWindows as $window){
      if(array_key_exists($window->windowsdate, $data["availability"]) == false) $data["availability"][$window->windowsdate] = array();
      $data["availability"][$window->windowsdate][$window->idWithinDay] = array(
        "start"=>str_pad(intval($window->start/60), 2, "0", STR_PAD_LEFT).":".str_pad(intval($window->start%60), 2, "0", STR_PAD_LEFT),
        "end"=>str_pad(intval($window->end/60), 2, "0", STR_PAD_LEFT).":".str_pad(intval($window->end%60), 2, "0", STR_PAD_LEFT)
      );
    }
    return $data;
  }
?>
