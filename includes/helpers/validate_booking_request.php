<?php
  function validate_booking_request($postData){
    $types = get_massage_type_names(true);
    if(array_key_exists('date', $postData) == false || $postData['date'] == '') return array("success"=>false, "message"=>"Please select a date.");
    if(array_key_exists('time', $postData) == false || $postData['time'] == '') return array("success"=>false, "message"=>"Please select a time.");
    if(array_key_exists('type', $postData) == false || $postData['type'] == '') return array("success"=>false, "message"=>"Please select a type.");
    if(in_array($postData['type'], $types) == false) return array("success"=>false, "message"=>"Please select a valid type.");
    if(array_key_exists('name', $postData) == false || $postData['name'] == '') return array("success"=>false, "message"=>"Please enter your name.");
    if(array_key_exists('surnames', $postData) == false || $postData['surnames'] == '') return array("success"=>false, "message"=>"Please enter your surnames.");
    if(array_key_exists('phone', $postData) == false || $postData['phone'] == '') return array("success"=>false, "message"=>"Please enter your phone number.");
    if(array_key_exists('email', $postData) == false || filter_var($postData['email'], FILTER_VALIDATE_EMAIL) == false) return array("success"=>false, "message"=>"Please enter a valid email.");
    if(verify_booking_slot_valid($postData)) return array("success"=>true);
    else return array("success"=>false, "message"=>"That booking slot is no longer available.");
  }
?>
