<?php

  function get_basic_data($admin){

    $prepTime = 30;

    $data = array(
      "now" => time(),
      "dayStart" => 8,
      "dayEnd" => 22,
      "granularity" => 30,
      "totalDays" => 45,
      "daysFromToday" => 1,
    );

    if($admin){
      $data["daysFromToday"]-=1;
      $data["totalDays"]+=1;
    }

    $endDaysFromToday = $data["daysFromToday"] + $data["totalDays"] + 1; //+1 cause 00:00 on next day  will be used as cutoff in mysql queries
    $data["startDate"] = date("Y-m-d", strtotime("+{$data["daysFromToday"]} days", $data["now"]));
    $data["endDate"] = date("Y-m-d", strtotime("+$endDaysFromToday days", $data["now"]));
    $data["prepSlots"] = $prepTime / $data["granularity"];

    return $data;
  }

?>
