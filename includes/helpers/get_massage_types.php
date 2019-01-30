<?php
  function get_massage_types($forUpdate, $hideDeleted){
    if($forUpdate == true) $lockString = " for update";
    if($hideDeleted == true) $deletedString = " where allowbookings = 1";
    global $wpdb;
    return $wpdb->get_results("select * from {$wpdb->prefix}ss_booking_types$deletedString$lockString", ARRAY_A );
    return array(
      array("id"=>1, "name"=>"Massage 1 - 60 min", "length"=>60),
      array("id"=>2, "name"=>"Massage 2 - 60 min", "length"=>60),
      array("id"=>3, "name"=>"Massage 3 - 100 min", "length"=>100)
    );
  }
  function get_massage_type_names($forUpdate, $hideDeleted){
    $types = get_massage_types($forUpdate, $hideDeleted);
    return array_map("extract_name", $types);
  }
  function extract_name($type){
    return $type["name"];
  }
?>
