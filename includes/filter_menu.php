<?php
function hide_manage_menu_item( $items, $menu, $args ) {
    foreach ( $items as $key => $item ) {
        if ( $item->attr_title == 'Manage Bookings' && is_user_logged_in() == false ) unset( $items[$key] );
    }
    return $items;
}

add_filter( 'wp_get_nav_menu_items', 'hide_manage_menu_item', null, 3 );
?>
