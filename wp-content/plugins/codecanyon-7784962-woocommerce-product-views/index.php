<?php
/*
Plugin Name: WooCommerce Product Views
Plugin URI: http://www.danyob.org/plugins/woocommerce-product-views
Description: WooCommerce Product Views - An extension for WooCommerce which allows you see which users have looked at your products and whether or not they have purchased them.
Version: 1.0
Author: Danyo Borg
Author URI: http://www.danyob.org
*/

add_action('admin_menu', 'wpv_settings_page',99);
function wpv_settings_page() {
	global $wpv_settings_page;
	$wpv_settings_page = add_submenu_page( 'woocommerce', 'Product Views', 'Product Views', 'read', 'manage-product-views', 'wpv_page_call' ); 
}

add_action('admin_enqueue_scripts', 'wpv_load_scripts', 20, 1);
function wpv_load_scripts($hook) {
	global $wpv_settings_page;
	if( $hook != $wpv_settings_page ) 
	return;
	wp_enqueue_script('searchplug', plugins_url('search.js', __FILE__), array('jquery'), null, true);
}

add_action('woocommerce_after_main_content', 'wpv_add_product_views');
function wpv_add_product_views() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		$product_id = get_the_id();
		$product_views = get_option('product_views');
		$product_views[] = array('user_id'=>$current_user->ID, 'date'=>date('d-m-Y'), 'time'=>date('H:i:s'), 'product'=>$product_id);
		update_option('product_views', $product_views);
	}
}

function wpv_page_call() {
	global $wpdb;
	$views = get_option('product_views', true);
	//array_reverse($views);
?>

<div class="wrap">
  <div id="icon-plugins" class="icon32"><br />
  </div>
  <h2>WooCommerce Product Views</h2>
  <br />
  <?php if($views){ ?>
  <form id="live-search" method="post">
    <fieldset>
      <input size="56" type="text" class="text-input" id="filter" value="" placeholder="Search for users" />
      <span id="filter-count"></span>
    </fieldset>
  </form>
  <br />
  
  <table class="widefat fixed" cellspacing="0">
    <thead>
      <tr>
        <th>User</th>
        <th>Product</th>
        <th>Purchased</th>
        <th>Date / Time</th>
      </tr>
    </thead>
    <?php 

foreach ($views as $view){ $ui = get_userdata($view['user_id']); $userid = $view['user_id'];
	$sql = "SELECT * FROM wp_postmeta WHERE meta_key = '_customer_user' AND meta_value = '$userid'";
	$results = $wpdb->get_results($sql);
		$orders = array();
		foreach( $results as $result ) {
			$orders[] = $result->post_id;
		}
		
	$sql2 = "SELECT * FROM wp_woocommerce_order_items WHERE order_id  IN (".implode(',',$orders).")";
	$results2 = $wpdb->get_results($sql2);
		$items = array();
		foreach( $results2 as $result2 ) {
		   $items[] = $result2->order_item_name;
		}
		?>
    <tbody>
      <tr class="simple">
        <td><?php echo $ui->user_email. ' (#'.$view['user_id'].')';?></td>
        <td><a target="_blank" href="<?php echo get_permalink($view['product']);?>"><?php echo get_the_title($view['product']);?></a></td>
        <td><?php if (in_array(get_the_title($view['product']), $items)){	echo 'Yes';} else { echo '<strong>NO</strong>';}  ?></td>
        <td><?php echo $view['date'].' @ '.$view['time'];?></td>
      </tr>
    </tbody>
    <?php  } } else { echo 'No product views yet. Go and view one of your products and then check back here!'; }  ?>
  </table>
</div>
<?php } ?>
