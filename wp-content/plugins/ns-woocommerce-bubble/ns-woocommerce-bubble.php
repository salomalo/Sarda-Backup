<?php
/*
Plugin Name: Your Custom Badge and Bubble for Woocommerce
Plugin URI: http://www.nsthemes.com
Description: This plugin allow to enable custom badge and bubble for each product in woocommerce
Version: 1.3.0
Author: NsThemes
Author URI: http://www.nsthemes.com
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! defined( 'BUBBLE_NS_PLUGIN_DIR' ) )
    define( 'BUBBLE_NS_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );


/* *** include admin js *** */
function ns_woocommerce_bubble_load_admin_js( $hook ) {
    if( is_admin() ) {        
        wp_enqueue_style( 'wp-color-picker' ); 
        wp_enqueue_script( 'ns-woocommerce-bubble-admin-script', plugins_url( '/assets/js/ns-woocommerce-bubble-admin-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
		wp_enqueue_style( 'ns-bubble-backend-style', WP_PLUGIN_URL .'/ns-woocommerce-bubble/assets/css/ns-bubble-backend-style.css', array(), '1.0.0' );  
    }
}
add_action( 'admin_enqueue_scripts', 'ns_woocommerce_bubble_load_admin_js' );




/* *** include frontend css *** */
function ns_woocommerce_bubble_load_frontend_css($hook) {
    	wp_enqueue_style( 'ns-bubble-frontend-style', WP_PLUGIN_URL .'/ns-woocommerce-bubble/assets/css/ns-bubble-frontend-style.css', array(), '1.0.0' );
    	wp_enqueue_style( 'ns-bubble-dynamic-style', WP_PLUGIN_URL .'/ns-woocommerce-bubble/assets/css/ns-bubble-dynamic-style.css', array(), '1.0.0' );  
}
add_action( 'wp_enqueue_scripts', 'ns_woocommerce_bubble_load_frontend_css' );



// Add the custom tab in woocommerce product page
require_once( BUBBLE_NS_PLUGIN_DIR.'/ns-woocommerce-bubble-add-tab.php');


/* *** include loop product bubble *** */
require_once( BUBBLE_NS_PLUGIN_DIR.'/ns-woocommerce-bubble-loop-product.php');

/* *** include single product bubble *** */
require_once( BUBBLE_NS_PLUGIN_DIR.'/ns-woocommerce-bubble-single-product.php');

function get_meta_values( $key = '', $type = 'product') {

    global $wpdb;

    if( empty( $key ) )
        return;

    $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.post_id AS META FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = '%s' 
        AND p.post_type = '%s'
    ", $key, $type ) );

    return $r;
}
?>