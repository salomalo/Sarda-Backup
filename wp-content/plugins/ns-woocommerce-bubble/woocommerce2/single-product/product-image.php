<?php

/**
 * Custom Product image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */


global $post, $product, $woocommerce, $flatsome_opt, $wc_cpdf;
$attachment_ids = $product->get_gallery_attachment_ids();
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
 <div class="product-image">
			<div class="product-gallery-slider <?php if(!empty($attachment_ids)) echo 'ux-slider slider-nav-circle-hover slider-nav-small js-flickity'; ?>"
				data-flickity-options='{ 
		            "cellAlign": "center",
		            "wrapAround": true,
		            "autoPlay": false,
		            "prevNextButtons":true,
		            "percentPosition": true,
		            "imagesLoaded": true,
		            "lazyLoad": 1,
		            "pageDots": false,
		            "selectedAttraction" : 0.1,
		            "friction": 0.6,
		            "rightToLeft": false
		        }'
			>
			<?php if ( has_post_thumbnail() ) { ?>
	    	
			<?php

				//Get the Thumbnail URL
				$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
				$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
				$image       	= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
					'title'	=> $image_title,
					'alt'	=> $image_title
					) );

				$attachment_count = count( $product->get_gallery_attachment_ids() );
				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="slide easyzoom first"><a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s">%s</a></div>', $image_link, $image_caption, $image ), $post->ID );
			?>
			
			<?php } else { echo '<div class="slide easyzoom"><img src="'.wc_placeholder_img_src().'" title="'.get_the_title().'" alt="'.get_the_title().'"/></div>';} ?>
	        
			<?php

				if ( $attachment_ids ) {
			
					$loop = 0;
					$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );						
					
					foreach ( $attachment_ids as $attachment_id ) {

						$image_link = wp_get_attachment_url( $attachment_id );

						if ( ! $image_link )
							continue;

						$src = wp_get_attachment_image_src( $attachment_id, false, '' );
						$image = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
						$image_small = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_thumbnail' ) );

						$srcset = '';
						if(function_exists('wp_get_attachment_image_srcset')){
							$srcset = 'srcset="'.wp_get_attachment_image_srcset( $attachment_id).'"';
						}

						$image_title = esc_attr( get_the_title( $attachment_id ) );
						?>
						<div class="slide easyzoom">
						  <a href="<?php echo $src[0]; ?>" title="<?php echo $image_title; ?>">
	    					<img src="<?php echo $image_small[0]; ?>" <?php echo $srcset; ?> data-flickity-lazyload="<?php echo $image[0] ?>" alt="<?php echo $image_title ?>" title="<?php echo $image_title ?>" />
	   				 	  </a>
	   				 	</div>
						<?php
					}
				}
			?>
	</div>
	<div class="product-image-tools">
		<?php do_action('flatsome_product_image_tools'); ?>
	</div>
	 <?php if(shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) { echo do_shortcode('[yith_wcwl_add_to_wishlist]'); } ?>
</div><!-- .product-image -->

<?php do_action( 'woocommerce_product_thumbnails' ); ?>

</div><!-- .images -->
