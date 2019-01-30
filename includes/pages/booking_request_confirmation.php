<?php
function booking_request_confirmation(){
  session_start();
  ob_start();
  $name = $_SESSION['DATA_'.$_GET['data_id']]['name'];
  $type = $_SESSION['DATA_'.$_GET['data_id']]['type'];
  $time = $_SESSION['DATA_'.$_GET['data_id']]['time'];
  $date = date("D d M", strtotime($_SESSION['DATA_'.$_GET['data_id']]['date']));
?>
  <div id='ss_booking_confirmation_text'>
<?php
  echo "$name, <b>we will contact you to confirm your request</b>:<br/><br/>";
  echo "$type<br/>";
  echo "$time on $date<br/><br/>";
  echo "Thank you.";
?>
  <br/><br/>Perhaps a picture here.<br/>
  </div>
<?php
  return ob_get_clean();
}
?>
