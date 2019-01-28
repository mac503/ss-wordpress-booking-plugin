<?php

function get_masked_availability($forUpdate, $admin, $idToIgnore){

  if(isset($idToIgnore)) $excludeId = " id <> $idToIgnore";

  $data = get_basic_availability($forUpdate, $admin);

  global $wpdb;

  if($forUpdate == true) $lockString = " for update";
  $bookings = $wpdb->get_results("select start, length from {$wpdb->prefix}ss_booking_bookings where (start between '{$data["startDate"]}' and '{$data["endDate"]}') and deleted <> 1 $excludeId$lockString", ARRAY_A );

  foreach($bookings as $booking){
    //figure out the day on which it occurs
    $diff = date_diff(date_create(date("Y-m-d", strtotime("+{$data["daysFromToday"]} days", $data["now"]))), date_create(substr($booking["start"],0,10)));
    $index = intval($diff->format("%a"));
    //figure out the timeslot it starts in + how many it occupies
    $startTime = intval(substr($booking["start"],11,2)) + (intval(substr($booking["start"],14,2))/60);
    $startSlot = floor(($startTime - $data["dayStart"]) / ($data["granularity"] / 60)) - $data["prepSlots"];
    $occupiesSlots = ceil($booking["length"] / $data["granularity"]) + $data["prepSlots"];
    //cycle through the affected timeslots on that day and blank them out
    for($i = $startSlot; $i<($startSlot+$occupiesSlots); $i++){
      if($i < count($data["days"][$index])){
        $data["days"][$index][$i] = 0;
      }
    }
  }

  return $data;

}

?>
