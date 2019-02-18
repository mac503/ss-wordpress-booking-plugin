<?php

add_action( 'admin_post_nopriv_booking_request', 'handle_booking_request');
add_action( 'admin_post_booking_request', 'handle_booking_request');

function handle_booking_request(){

  //start transaction
  global $wpdb;
  $wpdb->query('START TRANSACTION');
  $validation = validate_booking_request($_POST);
  if($validation["success"] == true && insert_booking($_POST)){
    $wpdb->query('COMMIT');
    $toPass = array(
      'name' => $_POST['name'],
      'type' => $_POST['type'],
      'date' => $_POST['date'],
      'time' => $_POST['time']
    );

    wp_safe_redirect( get_permalink(get_page_by_title( 'solicitud de reserva recibida' ))."?data_id=".pass_session_data($toPass) );

    send_request_email($wpdb->insert_id, $_POST);
  }
  else{
    //commit the nothingness
    $wpdb->query('ROLLBACK');

    if($validation["success"] == false) $message = $validation["message"];
    else $message = "There was an error requesting your booking, please try again.";

    $toPass = array(
      'dataPassed' => true,
      'message' => $message,
      'date' => $_POST['date'],
      'time' => $_POST['time'],
      'name' => $_POST['name'],
      'surnames' => $_POST['surnames'],
      'phone' => $_POST['phone'],
      'email' => $_POST['email'],
      'type' => $_POST['type']
    );
    wp_safe_redirect( get_permalink(get_page_by_title( 'Reservar Masaje' ))."?data_id=".pass_session_data($toPass) );
  }
}

?>
