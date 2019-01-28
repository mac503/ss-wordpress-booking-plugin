<?php
  function pass_session_data($data){
    session_start();
    $data_id = md5( time().microtime().rand(0,100) );
    $_SESSION["DATA_$data_id"] = $data;
    return $data_id;
  }
?>
