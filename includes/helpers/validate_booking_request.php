<?php
  function validate_booking_request($postData){
    $types = get_massage_type_names(true);
    if(array_key_exists('date', $postData) == false || $postData['date'] == '') return array("success"=>false, "message"=>"Por favor, elija una fecha.");
    if(array_key_exists('time', $postData) == false || $postData['time'] == '') return array("success"=>false, "message"=>"Por favor, seleccione una hora.");
    if(array_key_exists('type', $postData) == false || $postData['type'] == '') return array("success"=>false, "message"=>"Por favor, seleccione un tipo.");
    if(in_array($postData['type'], $types) == false) return array("success"=>false, "message"=>"Por favor, seleccione un tipo válido.");
    if(array_key_exists('name', $postData) == false || $postData['name'] == '') return array("success"=>false, "message"=>"Por favor, introduzca su nombre.");
    if(array_key_exists('surnames', $postData) == false || $postData['surnames'] == '') return array("success"=>false, "message"=>"Por favor, introduzca su apellido.");
    if(array_key_exists('phone', $postData) == false || $postData['phone'] == '') return array("success"=>false, "message"=>"Por favor, introduzca su número de teléfono.");
    if(array_key_exists('email', $postData) == false || filter_var($postData['email'], FILTER_VALIDATE_EMAIL) == false) return array("success"=>false, "message"=>"Por favor, introduzca un correo valido.");
    if(verify_booking_slot_valid($postData)) return array("success"=>true);
    else return array("success"=>false, "message"=>"Ese espacio de reserva ya no está disponible.");
  }
?>
