<?php
// Add custom Theme Functions here
function update_menu_category_items($items) {
    //look through the menu for items with Label "Link Title"
    foreach($items as $item){

        var_dump( $item );
        echo '<br><br>';

        if($item->title === "Link Title") { // this is the link label your searching for
            $item->url = "http://www.google.com"; //this is the new link
        }
    }
    return $items;
}
//add_filter('wp_nav_menu_objects', 'update_menu_category_items', 10,2);