<?php
  function get_masked_availability_with_prep_time($forUpdate){

    $data = get_masked_availability($forUpdate);

    foreach($data["days"] as $i=>$day){
      $dayCopy = $day;
      foreach($day as $j=>$slot){
        if($j>0 && $slot==1 && $day[$j-1]==0) $dayCopy[$j] = 0;
      }
      $data["days"][$i] = $dayCopy;
    }

    return $data;

  }
?>
