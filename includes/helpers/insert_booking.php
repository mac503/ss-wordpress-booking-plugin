<?php
  function insert_booking($postData){

    $start = $postData["date"]." ".$postData["time"].":00";

    $types = get_massage_types(true);
    $length = array_values(array_filter(
      $types, function($x) use ($postData){
        return $x["name"] == $postData["type"];
      } ))[0]["length"];

    $confirmed = 0;
    if($postData["confirmed"] == true) $confirmed = 1;

    global $wpdb;
    return $wpdb->insert("{$wpdb->prefix}ss_booking_bookings",
      array(
        "datetimeRequested"=>date("Y-m-d H:i:s"),
        "start"=>$start,
        "length"=>$length,
        "type"=>$postData["type"],
        "nombre"=>$postData["name"],
        "apellidos"=>$postData["surnames"],
        "email"=>$postData["email"],
        "phone"=>$postData["phone"],
        "confirmed"=>$confirmed
      )
    );
  }
?>
