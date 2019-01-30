<?php

  function get_basic_availability($forUpdate, $admin){

    $data = get_time_block($admin);

    /*
    $defaultOpenings = array(
      array(
        array("start" => 14.5*60, "end" => 16*60)
      ),
      array(
        array("start" => 9*60, "end" => 13*60),
        array("start" => 17*60, "end" => 21*60)
      ),
      array(
        array("start" => 9*60, "end" => 13*60),
        array("start" => 17*60, "end" => 21*60)
      ),
      array(
        array("start" => 9*60, "end" => 13*60),
        array("start" => 17*60, "end" => 21*60)
      ),
      array(
        array("start" => 9*60, "end" => 13*60),
        array("start" => 17*60, "end" => 21*60)
      ),
      array(
        array("start" => 9*60, "end" => 13*60),
        array("start" => 17*60, "end" => 21*60)
      ),
      array(
        array("start" => 9*60, "end" => 13*60)
      )
    );
    */
    global $wpdb;
    if($forUpdate == true) $lockString = " for update";

    $defaultOpenings = get_default_availability($forUpdate);

    $overrideOpenings = $wpdb->get_results("select * from {$wpdb->prefix}ss_booking_windows where (windowsdate between '{$data["startDate"]}' and '{$data["endDate"]}') and IF(start <> 0 and end <> 0, 1, 0)$lockString", ARRAY_A );
    $overrideOpeningsTemp = array();

    foreach($overrideOpenings as $opening){
      if(array_key_exists($opening["windowsdate"], $overrideOpeningsTemp) == false) $overrideOpeningsTemp[$opening["windowsdate"]] = array();
      $overrideOpeningsTemp[$opening["windowsdate"]][intval($opening["idWithinDay"])] = array("start"=>$opening["start"], "end"=>$opening["end"]);
    }
    $overrideOpenings = $overrideOpeningsTemp;

    //iterate through days
    for($i=0; $i<$data["totalDays"]; $i++){
      $n = $i + $data["daysFromToday"];
      $date = date('Y-m-d', strtotime("+$n days", $data["now"]));
      //if there is specific opening info for that day, use that info to open timeslots in the array
      //otherwise, use the default for that weekday
      if(isset($overrideOpenings[$date])){
        $dayOpenings = $overrideOpenings[$date];
      }
      else{
        $dayOpenings = $defaultOpenings[date('w', strtotime("+$n days", $data["now"]))];
      }

      foreach($dayOpenings as $opening){
        $startOffset = ceil(($opening["start"]/60 - $data["dayStart"]) / ($data["granularity"] / 60));
        $endOffset = ceil(($opening["end"]/60 - $data["dayStart"]) / ($data["granularity"] / 60));
        foreach($data["days"][$i] as $j=>$slot){
          if($startOffset <= $j && $j < $endOffset) $data["days"][$i][$j] = 1;
        }
      }
    }

    return $data;

  }

?>
