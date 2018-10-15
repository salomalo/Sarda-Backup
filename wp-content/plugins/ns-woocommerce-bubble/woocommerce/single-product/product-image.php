<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;
$class_type_bubb_var = get_post_meta($post->ID, 'custom_tab_type_ns', true);
$class_type_bubb = ( !empty($class_type_bubb_var)  ? $class_type_bubb_var : 'roundedNS');
$class_type_bubb_var2 = get_post_meta($post->ID, 'custom_tab_type_ns2', true);
$class_type_bubb2 = ( !empty($class_type_bubb_var2)  ? $class_type_bubb_var2 : 'roundedNS');
$bubble_enabled =  get_post_meta($post->ID, 'custom_tab_enabled', true);
$bubble_enabled2 =  get_post_meta($post->ID, 'custom_tab_enabled_two', true);
?>
<div class="images">
<?php if ($bubble_enabled == 'yes'){ ?>	
	<div id="Bubble1PosNS<?php echo $post->ID; ?>" class="bubbNS"><div id="colorNS<?php echo $post->ID; ?>" class="<?php echo $class_type_bubb; ?>"><div id="textBubleNS<?php echo $post->ID; ?>" class="textBubleNS"><?php echo get_post_meta($post->ID, 'custom_tab_title', true); ?></div></div></div>
<?php } ?>
<?php if ($bubble_enabled2 == 'yes'){ ?>	
	<div id="BubblePosNS<?php echo $post->ID; ?>" class="bubbleNS"><div id="color2NS<?php echo $post->ID; ?>" class="<?php echo $class_type_bubb2; ?>"><div id="text2BubleNS<?php echo $post->ID; ?>" class="textBubleNS"><?php echo get_post_meta($post->ID, 'custom_tab_title2', true); ?></div></div></div>
<?php } ?>
	<?php
		if ( has_post_thumbnail() ) {
			$image_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
			$image_link    = wp_get_attachment_url( get_post_thumbnail_id() );
			$image         = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title'	=> get_the_title( get_post_thumbnail_id() )
			) );

			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_caption, $image ), $post->ID );

		} else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

		}
	?>

	<?php do_action( 'woocommerce_product_thumbnails' ); ?>
</div>
