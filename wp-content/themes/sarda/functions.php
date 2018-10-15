<?php
// Add custom Theme Functions here
function update_menu_category_items($items) {

    $parent_slug = "";
    $parent_category = "";

    //look through the menu for items with Label "Link Title"
    foreach($items as $item) {

        if(true/*$item->type === "taxonomy"*/) {

            echo $item->title . '<br>';
            echo '$item->url: ' . $item->url . '<br>';
            echo '$item->menu_item_parent: ' . $item->menu_item_parent . '<br>';
            echo '$item->object: ' . $item->object . '<br>';
            echo '$item->type: ' . $item->type . '<br>';
            if( in_array( 'menu-item-has-children', $item->classes) ) {
                echo 'Menu Item has Children<br>';
                $parent_category = $item->object;
                $parent_slug = $item->title;
            }
            echo 'Parent category is ' . $parent_category . '<br>';
            echo 'Parent slug is ' . $parent_slug . '<br>';

            if ($item->menu_item_parent > 0) { // this is the link label your searching for
                echo 'Change It<br>';
                $item->url = $item->url . '?' . $parent_category . '=' . $parent_slug; //this is the new link
                echo 'New URL is: ' . $item->url . '<br>';
            }

            //var_dump($item);
        }
        echo '<br><br>';


    }
    return $items;
}
//add_filter('wp_nav_menu_objects', 'update_menu_category_items', 10,2);

/* Hook to alter related products to related taxonomies (collections, stone type, stone color) */
add_filter( 'woocommerce_product_related_posts', 'wpse_change_related_products_to_custom_taxonomies' );
function wpse_change_related_products_to_custom_taxonomies() {
        $get_related_products_args = array(
            'orderby' => 'rand',
            'posts_per_page' => $limit,
            'post_type' => 'product',
            'fields' => 'ids',
            'meta_query' => $meta_query,
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'product_collection',
                    'field' => 'id',
                    'terms' => $col_array
                ),/*
                array(
                    'taxonomy' => 'product_tag',
                    'field' => 'id',
                    'terms' => $tags_array
                )*/
            )
        );
        return $get_related_products_args;
}



