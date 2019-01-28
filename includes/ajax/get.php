<?php

  defined( 'ABSPATH' ) or die( 'Access denied.' );

  add_action( 'wp_ajax_get_masked', 'get_masked' );
  add_action( 'wp_ajax_nopriv_get_masked', 'get_masked' );

  function get_masked(){
    $data = get_masked_availability_with_prep_time();
    wp_send_json($data);
  }

  add_action( 'wp_ajax_get_detailed', 'get_detailed' );

  function get_detailed(){
    $data = get_detailed_data();
    wp_send_json($data);
  }

?>
