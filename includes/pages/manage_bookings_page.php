<?php
function manage_bookings_page(){
  ob_start();
?>
<div id='ss_booking_menu'>
  A TEST
  <div data-action='show-unconfirmed'>Unconfirmed</div>
  <div data-action='show-confirmed'>Confirmed</div>
  <div data-action='show-cancelled'>Cancelled</div>
  <div data-action='show-historical'>Historical</div>
  <div data-action='show-all'>All</div>
  <div data-action='show-calendar'>Calendar</div>
</div>
<div id='ss_booking_calendar_holder' style='display:none'>
  <div id='ss_booking_calendar'></div>
  <div id='ss_booking_current_action' style='display:none'></div>
  <div id='ss_booking_day_controls'>
    <div data-action='add-new'>Add New</div>
    <div data-action='change-opening-hours'>Change Opening Hours</div>
  </div>
  <div id='ss_booking_item_dialog' style='display:none'></div>
  <div class='overlay'></div>
</div>
<div id='ss_booking_list' style='display:none'>
  <div id='ss_booking_list_search_bar'><input type='text' id='ss_booking_list_search_box' placeholder='Search'></input></div><p>
  <div id='ss_booking_list_list'></div>
</div>
<?php
  return ob_get_clean();
}
?>
