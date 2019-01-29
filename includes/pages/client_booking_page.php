<?php

defined( 'ABSPATH' ) or die( 'Access denied.' );

function client_booking_page(){
  ob_start();
  session_start();
  ?>
  Please choose a date and time from the calendar below.<p>
  <div id='ss_booking_calendar'></div><br/>
  <div id='ss_booking_calendar_form'>
    Explanation that this is only a requested time, will need to call client to confirm.<br/>
    <form id='form' action='<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>' method='post'>
      <input type="hidden" name="action" value="booking_request">
    <?php
      if($_GET['data_id'] && $_SESSION['DATA_'.$_GET['data_id']]['dataPassed'] == true) echo '<input type="hidden" id="sabai-salud-calendar-input-passed" value="true">';
      else echo '<input type="hidden" id="sabai-salud-calendar-input-passed" value="false">';
    ?>
      <b><?php
      if($_GET['data_id']){
        echo $_SESSION['DATA_'.$_GET['data_id']]['message'];
      }
      ?></b>
      <div id='dateTimeEntry'>
        <label for="sabai-salud-calendar-input-date">Date</label> <select name='date' id='sabai-salud-calendar-input-date'>
          <?php
            $data = get_time_block();
            echo json_encode($data);
            //draw date dropdown
            for($i=0; $i<$data["totalDays"]; $i++){
              $val = date('Y-m-d', strtotime('+'.$data["daysFromToday"]+$i.' days', $data["now"]));
              $display = date('D d M', strtotime('+'.$data["daysFromToday"]+$i.' days', $data["now"]));
              $selected = "";
              if($_GET['data_id'] && $_SESSION['DATA_'.$_GET['data_id']]['date'] == $val) $selected = " selected";
              echo "<option value='$val'$selected>$display</option>";
            }
          ?>
        </select><br/>
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
        </select><br>
      </div>
      <div id='selected-text'></div>
      <br/><label for="sabai-salud-calendar-input-type">Type</label> <select name='type' id='sabai-salud-calendar-input-type' required>
        <?php
          $types = get_massage_types();
          foreach($types as $type){
            echo "<option data-massage-length='{$type["length"]}' value='{$type["name"]}'>";
            echo $type["name"];
            echo "</option>";
          }
        ?>
      </select><br/>
      <?php
      //TODO prettify form, labels
      ?>
      <br/><p>
      <label for="sabai-salud-calendar-input-name">Nombre</label> <input type='text' name='name' id='sabai-salud-calendar-input-name' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['name'];?>' required></input><br>
      <label for="sabai-salud-calendar-input-surnames">Apellidos</label> <input type='text' name='surnames' id='sabai-salud-calendar-input-surnames' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['surnames'];?>' required></input><br>
      <label for="sabai-salud-calendar-input-email">Email</label> <input type='email' name='email' id='sabai-salud-calendar-input-email' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['email'];?>' required></input><br>
      <label for="sabai-salud-calendar-input-phone">Phone</label> <input type='phone' name='phone' id='sabai-salud-calendar-input-phone' value='<?php if($_GET['data_id']) echo $_SESSION['DATA_'.$_GET['data_id']]['phone'];?>' required></input><br><br>
      <input type='checkbox' name='permission' id='sabai-salud-calendar-input-permission'></input> <label for="sabai-salud-calendar-input-permission">Permission to send marketing emails</label><br><br>
      <input type="submit" value="Request Booking"></input>
    </form>
  </div>
  <?php
  return ob_get_clean();
}

?>
