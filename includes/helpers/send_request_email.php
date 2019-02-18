<?php
  function send_request_email($id, $data){
    $GASUrl = "https://script.google.com/macros/s/AKfycbyyUq9o9pdHNw0U-UcjYqPKe1oJwwIU-xTAaG0-Z9bPxIgSLYQ/exec";
    $GASUrl .= "?date=" . urlencode($data['date']);
    $GASUrl .= "&time=" . urlencode($data['time']);
    $GASUrl .= "&nombre=" . urlencode($data['name']);
    $GASUrl .= "&apellidos=" . urlencode($data['surnames']);
    $GASUrl .= "&type=" . urlencode($data['type']);
    $GASUrl .= "&email=" . urlencode($data['email']);
    $GASUrl .= "&phone=" . urlencode($data['phone']);
    $GASUrl .= "&id=" . urlencode($id);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $GASUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($curl);
    curl_close($curl);
  }
?>
