<?php
  function get_massage_types($forUpdate){
    //TODO look up from database
    return array(
      array("name"=>"Massage 1 - 60 min", "length"=>60),
      array("name"=>"Massage 2 - 60 min", "length"=>60),
      array("name"=>"Massage 3 - 100 min", "length"=>100)
    );
  }
  function get_massage_type_names($forUpdate){
    $types = get_massage_types($forUpdate);
    return array_map("extract_name", $types);
  }
  function extract_name($type){
    return $type["name"];
  }
?>
