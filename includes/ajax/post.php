<?php

  defined( 'ABSPATH' ) or die( 'Access denied.' );

  add_action( 'wp_ajax_confirm_booking', 'confirm_booking' );
  add_action( 'wp_ajax_delete_booking', 'delete_booking' );
  add_action( 'wp_ajax_change_booking', 'change_booking' );
  add_action( 'wp_ajax_new_booking', 'new_booking' );
  add_action( 'wp_ajax_change_opening_hours', 'change_opening_hours' );
  add_action( 'wp_ajax_update_massage_types', 'update_massage_types' );
  add_action( 'wp_ajax_update_massage_openings', 'update_massage_openings' );
  add_action( 'wp_ajax_update_script_location', 'update_script_location' );

  function confirm_booking(){
    my_log(json_encode($_POST));
    if($_POST["emailCustomer"] == "true"){
      $data = get_single_booking_data($_POST["id"]);
      $data['date'] = explode(" ", $data['start'])[0];
      $data['time'] = explode(" ", $data['start'])[1];
      $data['name'] = $data['nombre'];
      send_email("confirm", $_POST["id"], $data);
    }
    wp_send_json(update_booking($_POST["id"], array(
      "confirmed"=>1
    )));
  }

  function delete_booking(){
    if($_POST["emailCustomer"] == "true"){
      $data = get_single_booking_data($_POST["id"]);
      $data['date'] = explode(" ", $data['start'])[0];
      $data['time'] = explode(" ", $data['start'])[1];
      $data['name'] = $data['nombre'];
      send_email("delete", $_POST["id"], $data);
    }
    wp_send_json(update_booking($_POST["id"], array(
      "deleted"=>1
    )));
  }

  function change_booking(){
    global $wpdb;
    $wpdb->query('START TRANSACTION');

    if(verify_booking_slot_valid($_POST, true, $_POST["id"])){
      $result = update_booking($_POST["id"], array(
          "confirmed"=>1,
          "start"=>$_POST["date"]." ".$_POST["time"].":00"
      ));
      $wpdb->query('COMMIT');
      if($_POST["emailCustomer"] == "true"){
        $data = get_single_booking_data($_POST["id"]);
        $data['name'] = $data['nombre'];
        $data["date"] = $_POST["date"];
        $data["time"] = $_POST["time"];
        send_email("change", $_POST["id"], $data);
      }
      wp_send_json($result);
    }
    else{
      $wpdb->query('ROLLBACK');
      wp_send_json(array("success"=>false, "error"=>"Error updating database."));
    }
  }

  function new_booking(){
    global $wpdb;
    $wpdb->query('START TRANSACTION');

    if(verify_booking_slot_valid($_POST, true, $_POST["id"])){
      $result = insert_booking($_POST);
      if($result == 1){
        $wpdb->query('COMMIT');
        if($_POST["emailCustomer"] == "true"){
          $data = $_POST;
          $data['id'] = $wpdb->insert_id;
          send_email("confirm", $data['id'], $data);
        }
        wp_send_json(array("success"=>true, "data"=>get_detailed_data(), "id"=>$wpdb->insert_id));
      }
      else{
        $wpdb->query('ROLLBACK');
        wp_send_json(array("success"=>false, "error"=>"Error inserting into database."));
      }
    }
    else{
      $wpdb->query('ROLLBACK');
      wp_send_json(array("success"=>false, "error"=>"Booking slot no longer valid."));
    }
  }

  function change_opening_hours(){
    $update = array();
    global $wpdb;
    foreach($_POST["data"] as $i => $row){
      $update = array(
        "windowsdate"=>$_POST["date"],
        "idWithinDay"=>$i,
        "start"=>$row["start"],
        "end"=>$row["end"]
      );
      $wpdb->replace("{$wpdb->prefix}ss_booking_windows",$update);
    }
    wp_send_json(array("success"=>true, "data"=>get_detailed_data()));
  }

  function update_massage_types(){
    global $wpdb;
    foreach($_POST["data"] as $row){
      $wpdb->replace("{$wpdb->prefix}ss_booking_types",$row);
    }
    wp_send_json(true);
  }

  function update_massage_openings(){
    global $wpdb;
    foreach($_POST["data"] as $row){
      $wpdb->replace("{$wpdb->prefix}ss_booking_default_windows",$row);
    }
    wp_send_json(true);
  }

  function update_script_location(){
    global $wpdb;

    $wpdb->update(
    	"{$wpdb->prefix}ss_booking_settings",
    	array(
    		'value' => $_POST["data"],
    	),
    	array( 'setting' => "script_location" )
    );
    wp_send_json(true);
  }

?>
