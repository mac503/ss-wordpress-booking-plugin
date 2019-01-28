<?php

function get_time_block($admin){

  $data = get_basic_data($admin);

  $slots = ($data["dayEnd"] - $data["dayStart"]) * (60 / $data["granularity"]);
  $data["days"] = array_fill(0, $data["totalDays"], array_fill(0, $slots, 0));

  return $data;

}

?>
