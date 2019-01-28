<?php
function booking_request_confirmation(){
  session_start();
  $name = $_SESSION['DATA_'.$_GET['data_id']]['name'];
  $type = $_SESSION['DATA_'.$_GET['data_id']]['type'];
  $time = $_SESSION['DATA_'.$_GET['data_id']]['time'];
  $date = date("D d M", strtotime($_SESSION['DATA_'.$_GET['data_id']]['date']));

  echo "$name, we will contact you to confirm your request for $type at $time on $date.<p><p>";
  echo "Thank you.";
}
?>
