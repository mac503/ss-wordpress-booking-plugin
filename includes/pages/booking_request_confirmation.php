<?php
function booking_request_confirmation(){
  session_start();
  ob_start();
  $name = $_SESSION['DATA_'.$_GET['data_id']]['name'];
  $type = $_SESSION['DATA_'.$_GET['data_id']]['type'];
  $time = $_SESSION['DATA_'.$_GET['data_id']]['time'];
  $date = date("D d M", strtotime($_SESSION['DATA_'.$_GET['data_id']]['date']));
  $daysEn = array(
    "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"
  );
  $daysEs = array(
    "lunes", "martes", "miércoles", "jueves", "viernes", "sábado", "domingo"
  );
  $monthsEn = array(
    "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
  );
  $monthsEs = array(
    "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
  );

  $date = str_replace($daysEn, $daysEs, $date);
  $date = str_replace($monthsEn, $monthsEs, $date);

?>
  <div id='ss_booking_confirmation_text'>
<?php
  echo "$name, <b>te contactaremos para confirmar su petición</b>:<br/><br/>";
  echo "$time, $date<br/>";
  echo "$type<br/><br/>";
  echo "Muchas gracias,<br/>Sabai Salud";
?>
  </div>
<?php
  return ob_get_clean();
}
?>
