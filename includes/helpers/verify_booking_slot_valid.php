<?php
  function verify_booking_slot_valid($postData, $admin, $idToIgnore){
    $data = get_masked_availability(true, $admin, $idToIgnore);
    $types = get_massage_types(true);
    $length = array_values(array_filter(
      $types, function($x) use ($postData){
        return $x["name"] == $postData["type"];
      } ))[0]["length"];
    $slotsAfter = $length/$data["granularity"];
    $totalSlots = $slotsAfter + $data["prepSlots"];
    //calculate the starting slot
    $slotVal = $data["granularity"] / 60;
    $startingSlot = (toDecimal($postData["time"]) - $data["dayStart"]) / $slotVal - $data["prepSlots"];
    $startingDayDiff = date_diff(date_create(date("Y-m-d", strtotime("+{$data["daysFromToday"]} days", $data["now"]))), date_create(substr($postData["date"],0,10)));
    $startingDayIndex = intval($startingDayDiff->format("%a"));
    for($i=0; $i<$totalSlots; $i++){
      if($data["days"][$startingDayIndex][$i+$startingSlot] == 0) return false;
    }
    return true;
  }
?>

<?php

function toDecimal($time){
  $split = explode(':', $time);
  return intval($split[0]) + intval($split[1])/60;
}

?>
