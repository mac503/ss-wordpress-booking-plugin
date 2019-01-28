<?php
  function update_booking($id, $fields){

    global $wpdb;
    if($wpdb->update("{$wpdb->prefix}ss_booking_bookings",$fields,array("id"=>$id))){
      return array("success"=>true, "data"=>get_detailed_data());
    }
    else return array("success"=>false, "error"=>"Error updating database.");

  }
?>
