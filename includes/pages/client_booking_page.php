<?php

defined( 'ABSPATH' ) or die( 'Access denied.' );

function client_booking_page(){
  ob_start();
  session_start();
  ?>
  <div id='ss_messages'>
  <?php
    if($_GET['data_id']){
      echo "<span style='font-size: 1.5em'>Hubo un problema con su petición.<br/><b>".$_SESSION['DATA_'.$_GET['data_id']]['message']."</b></span>";
    }
  ?>
  </div>
  <div id='ss_booking_calendar'></div>
  <div id='ss_booking_calendar_form'>
    <form id='form' action='<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>' method='post'>
      <input type="hidden" name="action" value="booking_request">
    <?php
      if($_GET['data_id'] && $_SESSION['DATA_'.$_GET['data_id']]['dataPassed'] == true) echo '<input type="hidden" id="sabai-salud-calendar-input-passed" value="true">';
      else echo '<input type="hidden" id="sabai-salud-calendar-input-passed" value="false">';
    ?>
      <label id='ss_scroll_target' for="sabai-salud-calendar-input-date" style="display:none">Fecha y Hora</label>
      <div id='selected-text'>Por favor sugiera una fecha y hora usando el calendario de arriba. <b>Le contactaremos para confirmar disponibilidad</b></div>
      <div id='dateTimeEntry'>
        <label for="sabai-salud-calendar-input-date">Date</label> <select name='date' id='sabai-salud-calendar-input-date'>
          <?php
          $data = get_time_block();
          //draw date dropdown
          for($i=0; $i<$data["totalDays"]; $i++){
            $val = date('Y-m-d', strtotime('+'.$data["daysFromToday"]+$i.' days', $data["now"]));
            $display = date('D d M', strtotime('+'.$data["daysFromToday"]+$i.' days', $data["now"]));
            $selected = "";
            if($_GET['data_id'] && $_SESSION['DATA_'.$_GET['data_id']]['date'] == $val) $selected = " selected";
            echo "<option value='$val'$selected>$display</option>";
          }
          ?>
        </select>
        <label for="sabai-salud-calendar-input-time">Time</label> <select name='time' id='sabai-salud-calendar-input-time'>
          <?php
          //draw time dropdown
          for($i=$data["dayStart"]; $i<$data["dayEnd"]; $i+=($data["granularity"]/60)){
            $val = str_pad(intval($i),2,"0",STR_PAD_LEFT).":".str_pad(($i-intval($i))*60,2,"0",STR_PAD_LEFT);
            $selected = "";
            if($_GET['data_id'] && $_SESSION['DATA_'.$_GET['data_id']]['time'] == $val) $selected = " selected";
            echo "<option value='$val'$selected>$val</option>";
          }
          ?>
        </select>
      </div>
      <label for="sabai-salud-calendar-input-type">Tipo</label> <select name='type' id='sabai-salud-calendar-input-type' required>
        <?php
          $types = get_massage_types(false, true);
          foreach($types as $type){
            $selected = "";
            if($_GET["masaje"] == $type["name"]) $selected = " selected";
            echo "<option data-massage-length='{$type["length"]}' value='{$type["name"]}'$selected>";
            echo $type["displayname"];
            echo "</option>";
          }
        ?>
      </select>
      <label for="sabai-salud-calendar-input-name">Nombre</label> <input type='text' name='name' id='sabai-salud-calendar-input-name' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['name'];?>' required></input>
      <label for="sabai-salud-calendar-input-surnames">Apellidos</label> <input type='text' name='surnames' id='sabai-salud-calendar-input-surnames' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['surnames'];?>' required></input>
      <label for="sabai-salud-calendar-input-email">Correo electrónico</label> <input type='email' name='email' id='sabai-salud-calendar-input-email' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['email'];?>' required></input>
      <label for="sabai-salud-calendar-input-phone">Teléfono</label> <input type='tel' name='phone' id='sabai-salud-calendar-input-phone' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['phone'];?>' required></input>
      <input type='checkbox' name='permission' id='sabai-salud-calendar-input-permission'></input> <label for="sabai-salud-calendar-input-permission">Estoy de acuerdo en recibir emails de Sabai Salud sobre servicios que podrían interesarme.</label><br><br>
      <input type="submit" id='reserve-button' value="Reservar"></input>
    </form>
  </div>
  <?php
  return ob_get_clean();
}

?>
