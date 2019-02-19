<?php
  function send_email($email_type, $id, $data){

    foreach(get_massage_types() as $type){
      if($data['type'] == $type['name']){
        $data['type'] = $type['displayname'];
        break;
      }
    }

    $GASUrl = get_script_location();
    $GASUrl .= "?id=" . urlencode($id);
    $GASUrl .= "&email=" . urlencode($data['email']);
    $GASUrl .= "&nombre=" . urlencode($data['name']);
    $GASUrl .= "&date=" . urlencode($data['date']);
    $GASUrl .= "&time=" . urlencode($data['time']);
    $GASUrl .= "&type=" . urlencode($data['type']);

    $GASUrl .= "&action=" . urlencode($email_type);

    if($email_type == 'new'){
      $GASUrl .= "&phone=" . urlencode($data['phone']);
      $GASUrl .= "&apellidos=" . urlencode($data['surnames']);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $GASUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($curl);
    curl_close($curl);
  }
?>
