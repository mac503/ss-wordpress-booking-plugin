<?php

  function get_basic_availability($forUpdate, $admin){

    $data = get_time_block($admin);

    //TODO replace this with a lookup to database, once figured out where it will be stored
    $defaultOpenings = array(
      array(
        array("start" => 14, "end" => 16)
      ),
      array(
        array("start" => 9, "end" => 13),
        array("start" => 17, "end" => 21)
      ),
      array(
        array("start" => 9, "end" => 13),
        array("start" => 17, "end" => 21)
      ),
      array(
        array("start" => 9, "end" => 13),
        array("start" => 17, "end" => 21)
      ),
      array(
        array("start" => 9, "end" => 13),
        array("start" => 17, "end" => 21)
      ),
      array(
        array("start" => 9, "end" => 13),
        array("start" => 17, "end" => 21)
      ),
      array(
        array("start" => 9, "end" => 13)
      )
    );

    global $wpdb;

    if($forUpdate == true) $lockString = " for update";
    $overrideOpenings = $wpdb->get_results("select * from {$wpdb->prefix}ss_booking_windows where (windowsdate between '{$data["startDate"]}' and '{$data["endDate"]}') and IF(start <> 0 and end <> 0, 0, 1)$lockString", OBJECT_K );
    $data["query"] = "select * from {$wpdb->prefix}ss_booking_windows where (windowsdate between '{$data["startDate"]}' and '{$data["endDate"]}') and IF(start <> 0 and end <> 0, 0, 1)$lockString";

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
        $startOffset = ceil($opening["start"]/60 - $data["dayStart"]) / ($data["granularity"] / 60);
        $endOffset = ceil($opening["end"]/60 - $data["dayStart"]) / ($data["granularity"] / 60);
        foreach($data["days"][$i] as $j=>$slot){
          if($startOffset <= $j && $j < $endOffset) $data["days"][$i][$j] = 1;
        }
      }
    }

    return $data;

  }

?>
