<?php
$ns_theme = wp_get_theme();
$ns_theme_name = $ns_theme['Name'];

if( $ns_theme_name == 'Flatsome' || $ns_theme_name == 'Flatsome Child Theme' || $ns_theme_name == 'Avada' || $ns_theme_name == 'Avada Child'){
    $ns_priority  = 20;
} else {
    $ns_priority  = 10;
} 

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', $ns_priority);
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', $ns_priority);

 if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
	function woocommerce_template_loop_product_thumbnail() {
		echo woocommerce_get_product_thumbnail();
	} 
 }

if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {

    function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $deprecated1 = 0, $deprecated2 = 0 ) {
        global $post;
        $bubble_enabled =  get_post_meta($post->ID, 'custom_tab_enabled', true);
        $class_type_bubb_var = get_post_meta($post->ID, 'custom_tab_type_ns', true);
        $class_type_bubb = ( !empty($class_type_bubb_var)  ? $class_type_bubb_var : 'roundedNS');
        
        $bubble_enabled2 =  get_post_meta($post->ID, 'custom_tab_enabled_two', true);
        $class_type_bubb_var2 = get_post_meta($post->ID, 'custom_tab_type_ns2', true);
        $class_type_bubb2 = ( !empty($class_type_bubb_var2)  ? $class_type_bubb_var2 : 'roundedNS');

        if ($bubble_enabled2 == 'yes'){
            $nsbubble2 = '<div id="BubblePosNS'.$post->ID.'" class="bubbleNS"><div id="color2NS'.$post->ID.'" class="'.$class_type_bubb2.'"><div id="text2BubleNS'.$post->ID.'" class="textBubleNS">'.get_post_meta($post->ID, 'custom_tab_title2', true).'</div></div></div>';
        }else{
            $nsbubble2 = '';
        }


        if ($bubble_enabled == 'yes'){
            $nsbubble = '<div id="Bubble1PosNS'.$post->ID.'" class="bubbNS"><div id="colorNS'.$post->ID.'" class="'.$class_type_bubb.'"><div id="textBubleNS'.$post->ID.'" class="textBubleNS">'.get_post_meta($post->ID, 'custom_tab_title', true).'</div></div></div>';
        }else{
            $nsbubble = '';
        }

        $ns_theme = wp_get_theme();
        $ns_theme_name = $ns_theme['Name'];

        if ( has_post_thumbnail() ) {
                if( $ns_theme_name == 'Flatsome' || $ns_theme_name == 'Flatsome Child Theme' || $ns_theme_name == 'Avada' || $ns_theme_name == 'Avada Child'){
                    return $nsbubble.$nsbubble2;
                } else {
                    return $nsbubble.$nsbubble2.get_the_post_thumbnail( $post->ID, $size );
                }                            
        } elseif ( wc_placeholder_img_src() ) {
                if( $ns_theme_name == 'Flatsome' || $ns_theme_name == 'Flatsome Child Theme' || $ns_theme_name == 'Avada' || $ns_theme_name == 'Avada Child'){
                    return $nsbubble.$nsbubble2;
                } else {
                    return $nsbubble.$nsbubble2.wc_placeholder_img( $size );
                }
               
        }
    }

}
?>