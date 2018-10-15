<?php
/**
 * Main class
 *
 * @author Yithemes
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.2
 */

if ( !defined( 'YITH_WCAS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCAS' ) ) {
    /**
     * WooCommerce Ajax Search
     *
     * @since 1.0.0
     */
    class YITH_WCAS {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCAS_VERSION;

        /**
         * Plugin object
         *
         * @var string
         * @since 1.0.0
         */
        public $obj = null;

        private $search_string = '';
        private $search_order  = '';
        private $post_type     = 'any';

        private $search_options = array();

        /**
         * Constructor
         *
         * @return mixed|YITH_WCAS_Admin|YITH_WCAS_Frontend
         * @since 1.0.0
         */
        public function __construct() {

	        $this->obj = false;

            // Load Plugin Framework
	        if( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != 'yith_ajax_search_products' ) {
	        	add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

		        if ( is_admin() ) {
			        $this->obj = new YITH_WCAS_Admin( $this->version );
		        }else {
			        $this->obj = new YITH_WCAS_Frontend( $this->version );
		        }
	        }
	        include_once( YITH_WCAS_DIR.'plugin-fw/yit-woocommerce-compatibility.php' );

            // actions
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'widgets_init', array( $this, 'registerWidgets' ) );
            add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
            add_action( 'wp', array( $this, 'remove_pre_get_posts' ) );


			if( ! isset($_REQUEST['wc-ajax']) ){
				add_action( 'wp_ajax_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
				add_action( 'wp_ajax_nopriv_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
			}else{
				add_action( 'wc_ajax_yith_ajax_search_products', array( $this, 'ajax_search_products' ) );
			}


            // YITH WooCommerce Brands Compatibility
            add_filter( 'yith_wcas_search_options', array( $this, 'add_brands_search_option' ) );
            add_filter( 'yith_wcas_search_params', array( $this, 'add_brands_search_params' ) );

	        // YITH WooCommerce Multi Vendor Premium Compatibility
	        if ( class_exists( 'YITH_Vendors' ) ) {
		        add_filter( 'yith_wcas_search_options', array( $this, 'add_vendor_search_option' ) );
		        add_filter( 'yith_wcas_search_params', array( $this, 'add_vendor_search_params' ) );
	        }

            //register shortcode
            add_shortcode( 'yith_woocommerce_ajax_search', array( $this, 'add_woo_ajax_search_shortcode' ) );

            return $this->obj;
        }


        /**
         * Init method:
         *  - default options
         *
         * @access public
         * @since  1.0.0
         */
        public function init() {

            $ordering_args = WC()->query->get_catalog_ordering_args('menu_order');

			$search_by_cf  = get_option( 'yith_wcas_cf_name' );
            if( $search_by_cf != ''){
                $search_by_cf  = array_map('trim',explode(",",$search_by_cf));
            }

            $this->search_options = apply_filters( 'yith_wcas_search_params', array(
                'search_by_excerpt'        => apply_filters( 'yith_wcas_search_in_excerpt', get_option( 'yith_wcas_search_in_excerpt' ) ),
                'search_by_content'        => apply_filters( 'yith_wcas_search_in_content', get_option( 'yith_wcas_search_in_content' ) ),
                'search_by_cat'            => apply_filters( 'yith_wcas_search_in_product_categories', get_option( 'yith_wcas_search_in_product_categories' ) ),
                'search_by_tag'            => apply_filters( 'yith_wcas_search_in_product_tags', get_option( 'yith_wcas_search_in_product_tags' ) ),
                'search_by_sku'            => apply_filters( 'yith_wcas_search_by_sku', get_option( 'yith_wcas_search_by_sku' ) ),
                'search_by_sku_variations' => apply_filters( 'yith_wcas_search_by_sku_variations', get_option( 'yith_wcas_search_by_sku_variations' ) ),
                'search_by_cf'             => ( is_array($search_by_cf)) ? implode("','",$search_by_cf) : '',
                'posts_per_page'           => apply_filters( 'yith_wcas_search_posts_per_page', get_option( 'yith_wcas_posts_per_page' ) ),
                'orderby'                  => apply_filters( 'yith_wcas_search_orderby', $ordering_args['orderby'] ),
                'order'                    => apply_filters( 'yith_wcas_search_order', $ordering_args['order'] ),
	            'like'                     => apply_filters( 'yith_wcas_search_with_like', false ),
            ) );

            if( isset( $ordering_args['meta_key'] ) && $ordering_args['meta_key'] != '' ){
                $this->search_options['meta_key'] = apply_filters( 'yith_wcas_search_meta_key', $ordering_args['meta_key'] );
            }


        }

        /**
         * Load Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Load template for [yith_woocommerce_ajax_search] shortcode
         *
         * @access public
         *
         * @param $args array
         *
         * @return void
         * @since  1.0.0
         */
        public function add_woo_ajax_search_shortcode( $args = array() ) {

	        $template_default = get_option('yith_wcas_search_default_template', '');

	        $args = shortcode_atts( array( 'template' => $template_default, 'class'=>''), $args );
	        $template = ! empty( $args['template'] ) ? '-wide' : '';

	        ob_start();
	        $wc_get_template = function_exists( 'wc_get_template' ) ? 'wc_get_template' : 'woocommerce_get_template';
	        $wc_get_template( 'yith-woocommerce-ajax-search' . $template . '.php', $args, '', YITH_WCAS_DIR . 'templates/' );

	        return ob_get_clean();
        }

        /**
         * Load and register widgets
         *
         * @access public
         * @since  1.0.0
         */
        public function registerWidgets() {
            register_widget( 'YITH_WCAS_Ajax_Search_Widget' );
        }


        function extend_search_join( $join ) {
            global $wpdb;

            if ( isset( $this->search_options['meta_key'] ) && ! empty( $this->search_options['meta_key'] ) ) {
                $join .= " LEFT JOIN {$wpdb->postmeta} as ywomk ON ( {$wpdb->posts}.ID = ywomk.post_id AND  ywomk.meta_key = '_visibility') ";
            }

            $join .= " LEFT JOIN {$wpdb->postmeta} as ywmk ON ( {$wpdb->posts}.ID = ywmk.post_id AND  ywmk.meta_key = '_visibility') ";

            // YITH WooCommerce Brands Compatibility
            $search_by_brand = isset( $this->search_options['search_by_brand'] ) && $this->search_options['search_by_brand'] == 'yes';

            if ( $this->search_options['search_by_cat'] == 'yes' || $this->search_options['search_by_tag'] == 'yes' || $search_by_brand || apply_filters('yith_wcas_search_for_taxonomy', false ) ) {
                $join .= " LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id LEFT JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id LEFT JOIN {$wpdb->terms} tm ON tm.term_id = tt.term_id";
            }

	        if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {
		        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
		        $join .= " LEFT JOIN {$wpdb->term_relationships} tr_v ON {$wpdb->posts}.ID = tr_v.object_id LEFT JOIN {$wpdb->term_taxonomy} tt_v ON ( tt_v.term_taxonomy_id=tr_v.term_taxonomy_id AND tt_v.taxonomy LIKE 'product_visibility' AND tt_v.term_taxonomy_id NOT IN ( " . $product_visibility_term_ids['exclude-from-search'] . " ) ) LEFT JOIN {$wpdb->terms} tm_v ON tm_v.term_id = tt_v.term_id";
	        }

            if( $this->search_options['search_by_cf'] != ''){
                $join .= " LEFT JOIN {$wpdb->postmeta} as cf1 ON ( {$wpdb->posts}.ID = cf1.post_id AND cf1.meta_key IN ('{$this->search_options['search_by_cf']}' ) ) ";
            }

            return $join;
        }

        function extend_search_where( $where = '' ) {
            global $wpdb;

            $terms = array();

           $where .= " AND ( ( ywmk.meta_key = '_visibility' AND CAST( ywmk.meta_value AS CHAR) IN ('search','visible') ) OR ywmk.meta_id IS NULL ) ";

            if ( $this->search_options['search_by_cat'] == 'yes' ) {
                if($this->post_type == 'product' ){
                    $terms[] = 'product_cat';
                }else{
                    $terms[] = 'category';
                    $terms[] = 'product_cat';
                }
            }

            if ( $this->search_options['search_by_tag'] == 'yes' ) {
                if($this->post_type == 'product' ){
                    $terms[] = 'product_tag';
                }else{
                    $terms[] = 'product_tag';
                    $terms[] = 'post_tag';
                }
            }

            // YITH WooCommerce Brands Compatibility
            if ( isset( $this->search_options['search_by_brand'] ) && $this->search_options['search_by_brand'] == 'yes' && $this->post_type == 'product' ) {
                if( ! in_array( YITH_WCBR::$brands_taxonomy, $terms ) ){
                    $terms[] = YITH_WCBR::$brands_taxonomy;
                }
            }

	        // YITH WooCommerce Multi Vendor Compatibility
	        if ( class_exists( 'YITH_Vendors' ) ) {
		        if ( isset( $this->search_options['search_by_vendor'] ) && $this->search_options['search_by_vendor'] == 'yes' && $this->post_type == 'product' ) {
			        if ( ! in_array( YITH_Vendors()->get_taxonomy_name(), $terms ) ) {
				        $terms[] = YITH_Vendors()->get_taxonomy_name();
			        }
		        }
	        }

	        // YITH WooCommerce Brands Compatibility
            $terms = apply_filters('yith_wcas_search_taxonomy_terms', $terms );


                $where .= " AND (";
                $addor = false;
	            if ( $this->search_options['like'] ) {

		                $where .= " ( LOWER( {$wpdb->posts}.post_title ) LIKE '" . $this->search_string . "') ";

		            if ( $this->search_options['search_by_excerpt'] == 'yes' ) {
			            $where .= " OR ( LOWER({$wpdb->posts}.post_excerpt) LIKE '" . $this->search_string . "') ";
		            }

		            if ( $this->search_options['search_by_content'] == 'yes' ) {
			            $where .= " OR ( LOWER({$wpdb->posts}.post_content) LIKE '" . $this->search_string . "')  ";
		            }

		            if ( $this->search_options['search_by_cf'] != '' ) {
			            $where .= " OR ( LOWER(cf1.meta_value) LIKE '{$this->search_string}' )  ";
		            }

		            $addor = true;

		            if ( ! empty( $terms ) ) {
			            $where .= ( $addor ) ? ' OR ' : '';
			            $where .= " (( LOWER(tm.name) LIKE '" . $this->search_string . "' OR LOWER(tm.slug) LIKE '" . $this->search_string . "') AND tt.taxonomy IN ('" . implode( "','", $terms ) . "')) ";
		            }

		            $where .= " ) ";

	            } else {

			            $where .= " ( LOWER( {$wpdb->posts}.post_title ) REGEXP '" . $this->search_string . "') ";

		            if ( $this->search_options['search_by_excerpt'] == 'yes' ) {
			            $where .= " OR ( LOWER({$wpdb->posts}.post_excerpt) REGEXP '" . $this->search_string . "') ";
		            }

		            if ( $this->search_options['search_by_content'] == 'yes' ) {
			            $where .= " OR ( LOWER({$wpdb->posts}.post_content) REGEXP '" . $this->search_string . "')  ";
		            }

		            if ( $this->search_options['search_by_cf'] != '' ) {
			            $where .= " OR ( LOWER(cf1.meta_value) REGEXP '{$this->search_string}' )  ";
		            }

		            $addor = true;


		            if ( ! empty( $terms ) ) {
			            $where .= ( $addor ) ? ' OR ' : '';
			            $where .= " (( LOWER(tm.name) REGEXP '" . $this->search_string . "' OR LOWER(tm.slug) REGEXP '" . $this->search_string . "') AND tt.taxonomy IN ('" . implode( "','", $terms ) . "')) ";
		            }

		            $where .= " ) ";
	            }


            $where = apply_filters('yith_wcas_search_where', $where );

            return $where;
        }


	    public function query_string_changes( $search_query ) {

        	$this->search_string = preg_replace( '/\s+/', ' ', $search_query );
		    $this->search_string = str_replace( '\\', '', $this->search_string );
		    $this->search_string = str_replace( '\'', ' ', $this->search_string );

		    if ( $this->search_options['like'] ) {
			    $this->search_string = '%' . $this->search_string . '%';
			    $this->search_string = str_replace( '&', '%', $this->search_string );
			    $this->search_string = str_replace( '&amp;', '%', $this->search_string );
			    $this->search_string = str_replace( ' ', '%', $this->search_string );
		    } else {
			    //search both or singular
			    if ( get_option( 'yith_wcas_search_type_more_words' ) == 'and' ) {
				    $this->search_string = str_replace( '&', '', $this->search_string );
				    $this->search_string = str_replace( '°', '', $this->search_string );
				    $this->search_string = str_replace( ' ', '?(.*)', $this->search_string );
			    } else {
				    $this->search_string = str_replace( '&amp;', ' ', $this->search_string );
				    $this->search_string = str_replace( '°', ' ', $this->search_string );
				    $this->search_string = str_replace( ' ', '|', trim( $this->search_string ) );
			    }
		    }

		    return apply_filters( 'yith_wcas_search_string_manipulation', $this->search_string );
	    }
        /**
         * Perform jax search products
         */
        public function ajax_search_products() {
	        $time_start         = getmicrotime();
	        $transient_enabled  = get_option( 'yith_wcas_enable_transient', 'no' );
	        $transient_duration = get_option( 'yith_wcas_transient_duration', 12 );

	        $this->search_string = apply_filters( 'yith_wcas_ajax_search_products_search_query', ( trim( $_REQUEST['query'] ) ) );
	        $this->search_string = apply_filters( 'yith_wcas_ajax_lower_search_query', strtolower( $this->search_string ), $this->search_string );


	        $have_results = true;
	        $transient_name = 'ywcas_' . $this->search_string;
	        if ( $transient_enabled == 'no' || false === ( $suggestions = get_transient( $transient_name ) ) ) {

		        //get the order by filter
		        $search_strings     = $this->parse_search_string( $this->search_string );
		        $this->search_order = $this->parse_search_order( $this->search_string, $search_strings );
		        $this->search_string = $this->query_string_changes($this->search_string);

				$post_type = ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'any' ) ? 'any' : 'product';
		        $this->post_type = apply_filters( 'yith_wcas_ajax_search_products_post_type', esc_attr( $post_type ) );

		        $suggestions = array();

		        $args = array(
			        'post_type'           => $this->post_type,
			        'post_status'         => 'publish',
			        'ignore_sticky_posts' => 1,
			        'orderby'             => $this->search_options['orderby'],
			        'order'               => $this->search_options['order'],
			        'posts_per_page'      => $this->search_options['posts_per_page'] + 1,
			        'suppress_filters'    => false
		        );



		        if ( $this->post_type == 'product' ) {
			        if ( version_compare( WC()->version, '2.7.0', '<' ) ) {
				        $args['meta_query'] = array(
					        array(
						        'key'     => '_visibility',
						        'value'   => array( 'search', 'visible' ),
						        'compare' => 'IN'
					        ),
				        );
			        }else{
				        $product_visibility_term_ids = wc_get_product_visibility_term_ids();
				        $args['tax_query'][] = array(
					        'taxonomy' => 'product_visibility',
					        'field'    => 'term_taxonomy_id',
					        'terms'    => $product_visibility_term_ids['exclude-from-search'],
					        'operator' => 'NOT IN',
				        );
			        }

			        /* perform the research if there's a request with a specific category */
			        if ( isset( $_REQUEST['product_cat'] ) && !empty($_REQUEST['product_cat']) ) {
				        $args['tax_query'][] = array(
					        'relation' => 'AND',
					        array(
						        'taxonomy' => 'product_cat',
						        'field'    => 'slug',
						        'terms'    => $_REQUEST['product_cat']
					        )
				        );
			        }
		        }

		        add_filter( 'posts_where', array( $this, 'extend_search_where' ) );
		        add_filter( 'posts_join', array( $this, 'extend_search_join' ) );
		        add_filter( 'posts_groupby', array( $this, 'search_post_groupby' ) );
		        add_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
		        global $wpdb;
		        $results = apply_filters( 'ywrac_results', get_posts( $args ), $_REQUEST['query'] );

		        if ( count( $results ) < $this->search_options['posts_per_page'] ) {

			        //collect the id of results
			        $is_posts = array();
			        if ( $results ) {
				        foreach ( $results as $key => $value ) {
					        $is_posts[] = intval( $value->ID );
				        }
			        }

			        $product_in     = $this->extend_to_sku();
			        $product_by_sku = array();

			        if ( ! empty( $product_in ) ) {
				        $product_in       = array_map( 'intval', $product_in );
				        $args['post__in'] = array_diff( $product_in, $is_posts );

				        if ( $args['post__in'] ) {
					        remove_filter( 'posts_where', array( $this, 'extend_search_where' ) );
					        remove_filter( 'posts_join', array( $this, 'extend_search_join' ) );
					        remove_filter( 'posts_groupby', array( $this, 'search_post_groupby' ) );
					        remove_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
					        $product_by_sku = get_posts( $args );
				        }

			        }

			        $results = array_merge( $results, $product_by_sku );
		        }

		        if ( ! empty( $results ) ) {

			        $max_number   = get_option( 'yith_wcas_posts_per_page' );
			        $have_results = ( ( count( $results ) - $max_number ) > 0 ) ? true : false;
			        $i            = 0;
			        foreach ( $results as $post ) {
				        if ( $i ++ == $max_number ) {
					        break;
				        }
				        if ( $post->post_type == 'product' ) {

					        $product = wc_get_product( $post );

					        if ( $product->is_visible() ) {
						        $suggest = apply_filters( 'yith_wcas_suggestion', array(
							        'id'    => $product->get_id(),
							        'value' => strip_tags( $product->get_title() ),
							        'url'   => $product->get_permalink(),
						        ), $product );


						        if ( get_option( 'yith_wcas_show_thumbnail' ) === 'left' || get_option( 'yith_wcas_show_thumbnail' ) === 'right' ) {
							        $thumb          = $product->get_image( 'shop_thumbnail', array( 'class' => 'ywcas_img '.esc_attr( 'align-' . get_option( 'yith_wcas_show_thumbnail' ) ) ) );
							        $suggest['img'] = sprintf( '<div class="yith_wcas_result_image %s">%s</div>', esc_attr( 'align-' . get_option( 'yith_wcas_show_thumbnail' ) ), $thumb );
						        }


						        if ( ( $product->is_on_sale() && get_option( 'yith_wcas_show_sale_badge' ) != 'no')  || ( $product->is_featured() && get_option( 'yith_wcas_show_featured_badge' ) != 'no') || ( ! $product->is_in_stock() && get_option( 'yith_wcas_show_outofstock_badge' ) != 'no' )){
							        $suggest['div_badge_open'] = '<div class="badges">';
							        if ( $product->is_on_sale() && get_option( 'yith_wcas_show_sale_badge' ) != 'no' ) {
								        $suggest['on_sale'] = '<span class="yith_wcas_result_on_sale">'.__('sale', 'yith-woocommerce-ajax-search').'</span>';
							        }


							        if ( ! $product->is_in_stock() && get_option( 'yith_wcas_show_outofstock_badge' ) != 'no' ) {
								        $suggest['outofstock'] = '<span class="yith_wcas_result_outofstock">'.__('Out of stock', 'yith-woocommerce-ajax-search').'</span>';
							        }

							        if ( $product->is_featured() && get_option( 'yith_wcas_show_featured_badge' ) != 'no' && ! ( get_option( 'yith_wcas_hide_feature_if_on_sale' ) == 'yes' && $product->is_on_sale()) ) {
								        $suggest['featured'] = '<span class="yith_wcas_result_featured">'.__('featured', 'yith-woocommerce-ajax-search').'</span>';
							        }
							        $suggest['div_badge_close'] = '</div>';
						        }

						        if ( get_option( 'yith_wcas_show_excerpt' ) != 'no' ) {
						        	$short_description = yit_get_prop( $product, 'short_description', true );
						        	$description = yit_get_prop( $product, 'description', true );
							        $excerpt = ( $short_description != '' ) ? $short_description : $description;
							        $num_of_words = ( get_option('yith_wcas_show_excerpt_num_words') ) ? get_option('yith_wcas_show_excerpt_num_words') : 10;
							        $excerpt = strip_tags(strip_shortcodes(preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $excerpt)));
							        $suggest['excerpt'] = sprintf( '<p class="yith_wcas_result_excerpt">%s</p>', wp_trim_words( $excerpt, $num_of_words ) );
						        }

						        if ( get_option( 'yith_wcas_categories' ) == 'yes' ) {
							        $categories = array();
							        $terms      = get_the_terms( $product->get_id(), 'product_cat' );
							        if ( $terms ) {
								        foreach ( $terms as $term ) {
									        $categories[] = $term->name;
								        }
								        $suggest['product_categories'] = sprintf( '<div class="yith_wcas_result_categories">%s</div>', implode( ', ', $categories ) );
							        }
						        }

						        if ( get_option( 'yith_wcas_show_price' ) != 'no' ) {
							        $suggest['price'] = $product->get_price_html();
						        }


						        $suggestions[] = $suggest;
					        }

				        } else {
					        $suggest = apply_filters( 'yith_wcas_suggestion', array(
						        'id'    => $post->ID,
						        'value' => strip_tags( $post->post_title ),
						        'url'   => get_permalink( $post->ID ),
					        ), $post );


					        if ( has_post_thumbnail( $post->ID ) && ( get_option( 'yith_wcas_show_thumbnail' ) === 'left' || get_option( 'yith_wcas_show_thumbnail' ) === 'right' ) ) {
						        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
						        if( $thumb ){
							        $suggest['img'] = sprintf( '<div class="yith_wcas_result_image %s"><img src="%s" alt="%s"></div>', esc_attr( 'align-' . get_option( 'yith_wcas_show_thumbnail' ) ), $thumb['0'], $post->post_title );
						        }
					        }

					        if ( get_option( 'yith_wcas_show_excerpt' ) != 'no' ) {
						        $excerpt = ( $post->post_excerpt != '' ) ? $post->post_excerpt : $post->post_content;
						        $num_of_words = ( get_option('yith_wcas_show_excerpt_num_words') ) ? get_option('yith_wcas_show_excerpt_num_words') : 10;
						        $excerpt = strip_tags(strip_shortcodes(preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $excerpt)));
						        $suggest['excerpt'] = sprintf( '<p class="yith_wcas_result_excerpt">%s</p>', wp_trim_words( $excerpt, $num_of_words ) );
					        }

					        $suggestions[] = $suggest;
				        }

			        }
		        } else {
			        $have_results  = false;
			        $suggestions[] = array(
				        'id'    => - 1,
				        'value' => get_option( 'yith_wcas_search_show_no_results_text' ) ? get_option( 'yith_wcas_search_show_no_results_text' ) : __( 'No results', 'yith-woocommerce-ajax-search' ),
				        'url'   => '',
			        );

		        }
		        wp_reset_postdata();

		        if( $transient_enabled == 'yes' ){
			        set_transient( $transient_name, $suggestions, $transient_duration * HOUR_IN_SECONDS );
		        }

	        }

	        $time_end = getmicrotime();

	        $time = $time_end - $time_start;
	        $suggestions = array(
		        'results'     => $have_results,
		        'suggestions' => $suggestions,
		        'time'        => $time
	        );

	        wp_send_json($suggestions);
        }


        public function pre_get_posts( $q ) {

            global $wp_the_query;

            //if ( ! is_admin() && is_search() && !empty( $wp_the_query->query_vars['s'] ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX )) {
            if ( ! is_admin() &&  !empty( $wp_the_query->query_vars['s'] ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX )) {

                $pt = isset( $wp_the_query->query_vars['post_type'] )  ? $wp_the_query->query_vars['post_type'] : 'any' ;
                $this->post_type = apply_filters( 'yith_wcas_ajax_search_products_post_type', esc_attr( $pt ) );

                $qv = apply_filters( 'yith_wcas_ajax_search_products_search_query', esc_attr( trim($wp_the_query->query_vars['s']) ) );

                //get the order by filter
                $search_strings = $this->parse_search_string($qv);
                $this->search_order = $this->parse_search_order($qv,$search_strings);
	            $this->search_string = $this->query_string_changes($qv);

                set_query_var ( 's', $this->search_string );
                add_filter( 'posts_join',    array( $this, 'search_post_join' ) );
                add_filter( 'posts_where',   array( $this, 'search_post_where' ) );
	            if ( ! isset( $_GET['orderby'] ) ) {
		            add_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
	            }

                add_filter( 'posts_groupby', array( $this, 'search_post_groupby' ) );

                set_query_var ( 's', $qv );

            }
        }

        public function search_post_orderby( $orderby ){

            return $this->search_order;
        }

        public function search_post_join( $join ) {
            global $wpdb;

            $join = $this->extend_search_join( $join );
            return $join;
        }

        public function search_post_where( $where ) {

            
            if ( $where != '' ) {
                $where_array = array_filter( array_map( 'trim', explode( 'AND', $where ) ) );

                $ands = $where_array;
                foreach ( $ands as $key => $value ) {
                    if ( strpos( $value, 'post_content' ) !== false ) {
                        unset( $where_array[$key] );
                    }
                }
                $where = ' AND ' . implode( ' AND ', $where_array );
            }

         
            global $wpdb;

            $where = $this->extend_search_where( $where );

            /* search by sku */
            $product_by_sku = $this->extend_to_sku();


            if( ! empty($product_by_sku) ){
                $where .= ' OR '.$wpdb->posts.'.ID IN (' . implode( ',', $product_by_sku ) . ') ';
            }


            return $where;
        }

        public function search_post_groupby( $groupby ) {
            global $wpdb;
            $groupby = "{$wpdb->posts}.ID";
            return $groupby;
        }


	    /**
	     * Return a list of product id if the option search by sku is active
	     * @return array
	     * @internal param bool $only_visible
	     *
	     * @since    1.3.0
	     * @author   Emanuela Castorina
	     */
	    public function extend_to_sku() {
		    global $wpdb;

		    $product_in = array();

		    $this->remove_pre_get_posts();

		    remove_filter( 'posts_where', array( $this, 'extend_search_where' ) );
		    remove_filter( 'posts_join', array( $this, 'extend_search_join' ) );
		    remove_filter( 'posts_groupby', array( $this, 'search_post_groupby' ) );
		    remove_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );

		    if ( $this->search_options['search_by_sku'] == 'yes' ) {

			    $args = array(
				    'post_type'        => 'product',
				    'fields'           => 'ids',
				    'meta_query'       => array(
					    array(
						    'key'     => '_sku',
						    'value'   => $this->search_string,
						    'compare' => 'LIKE',
					    ),
				    ),
				    'suppress_filters' => false
			    );


			    if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {

				    $product_visibility_term_ids = wc_get_product_visibility_term_ids();
				    $args['tax_query'][]         = array(
					    'taxonomy' => 'product_visibility',
					    'field'    => 'term_taxonomy_id',
					    'terms'    => $product_visibility_term_ids['exclude-from-search'],
					    'operator' => 'NOT IN',
				    );

				    $product_in = new WP_Query( $args );
				    $product_in = $product_in->posts;

				    if ( $this->search_options['search_by_sku_variations'] == 'yes' ) {
					    $args['post_type'] = 'product_variation';
					    $args['fields']    = 'id=>parent';
					    $skus_posts        = new WP_Query( $args );
					    $skus_posts        = $skus_posts->posts;
					    $sku_to_id         = array();
					    if ( $skus_posts ) {
						    foreach ( $skus_posts as $skus_post ) {
							    $sku_to_id[] = $skus_post->post_parent;
						    }
					    }
					    $sku_to_id = array_filter( $sku_to_id );
				    }

			    } else {
				    $args['meta_query'][] = array(
					    'key'     => '_visibility',
					    'value'   => 'hidden',
					    'compare' => '!=',
				    );
				    $product_in           = new WP_Query( $args );
				    $product_in           = $product_in->posts;

				    if ( $this->search_options['search_by_sku_variations'] == 'yes' ) {
					    $args['post_type'] = 'product_variation';
					    $args['fields']    = 'id=>parent';
					    unset( $args['meta_query'][1] );
					    $skus_posts = new WP_Query( $args );
					    $skus_posts = $skus_posts->posts;
					    $sku_to_id  = array();
					    if ( $skus_posts ) {
						    foreach ( $skus_posts as $skus_post ) {
							    $sku_to_id[] = $skus_post->post_parent;
						    }
					    }
					    $sku_to_id = array_filter( $sku_to_id );
				    }

			    }

			    if ( ! empty ( $sku_to_id ) ) {
				    $product_in = array_merge( $sku_to_id, $product_in );
			    }
		    }

		    add_filter( 'posts_join', array( $this, 'search_post_join' ) );

		    return $product_in;
	    }



        /**
         * Parse the search string
         *
         * @param $s string
         *
         * @return array
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         */
        protected function parse_search_string( $s ) {
            // added slashes screw with quote grouping when done early, so done later
            $s = stripslashes( $s );
            $s = str_replace( array( "\r", "\n" ), '', $s );

            if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $s, $matches ) ) {
                $search_terms = $this->parse_search_terms( $matches[0] );
                // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
                if ( empty( $search_terms ) || count( $search_terms ) > 9 ) {
                    $search_terms = array( $s );
                }
            }
            else {
                $search_terms = array( $s );
            }

            return $search_terms;
        }

        /**
         * Parse the search terms
         *
         * @param $terms array
         *
         * @return array
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         */
        protected function parse_search_terms( $terms ) {
            global $wp_query;

            $strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
            $checked    = array();


            foreach ( $terms as $term ) {
                // keep before/after spaces when term is for exact match
                if ( preg_match( '/^".+"$/', $term ) ) {
                    $term = trim( $term, "\"'" );
                }
                else {
                    $term = trim( $term, "\"' " );
                }

                // Avoid single A-Z.
                if ( !$term || ( 1 === strlen( $term ) && preg_match( '/^[a-z]$/i', $term ) ) ) {
                    continue;
                }

                $checked[] = $term;
            }

            return $checked;
        }

        /**
         * Parse the search order
         *
         * @param $s            string
         * @param $search_terms array
         *
         * @return string
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         */
        protected function parse_search_order( $s, $search_terms ) {
            global $wpdb;

            $search_orderby = '';

            if( get_option('yith_wcas_order_by_post_type') == 'yes' ){
                $post_type_order = ( get_option('yith_wcas_order_by_post_type_select') == 'product') ? "'product', 'post', 'page'" : "'post', 'page', 'product'";
                $search_orderby = apply_filters('yith_wcas_filter_by_post_type', " FIELD(".$wpdb->posts.".post_type, ". $post_type_order .") ASC,  " , $s, $search_terms);
            }

            if ( isset( $this->search_options['meta_key'] ) && ! empty( $this->search_options['meta_key'] ) ) {
                $search_orderby .= "ywomk.meta_value " . $this->search_options['order'];
            } else {
                $search_orderby_title = array();
                foreach ( $search_terms as $term ) {
                    $like                   = '%' . $wpdb->esc_like( $term ) . '%';
                    $search_orderby_title[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $like );
                }

                if ( count( $search_terms ) > 1 ) {

                    $num_terms = count( $search_orderby_title );
                    $like      = '%' . $wpdb->esc_like( $s ) . '%';

                    $search_orderby .= '(CASE ';
                    // sentence match in 'post_title'
                    $search_orderby .= $wpdb->prepare( "WHEN $wpdb->posts.post_title LIKE %s THEN 1 ", $like );

                    // sanity limit, sort as sentence when more than 6 terms
                    // (few searches are longer than 6 terms and most titles are not)
                    if ( $num_terms < 7 ) {
                        // all words in title
                        $search_orderby .= 'WHEN ' . implode( ' AND ', $search_orderby_title ) . ' THEN 2 ';
                        // any word in title, not needed when $num_terms == 1
                        if ( $num_terms > 1 ) {
                            $search_orderby .= 'WHEN ' . implode( ' OR ', $search_orderby_title ) . ' THEN 3 ';
                        }
                    }

                    // sentence match in 'post_content'
                    $search_orderby .= $wpdb->prepare( "WHEN $wpdb->posts.post_content LIKE %s THEN 4 ", $like );
                    $search_orderby .= 'ELSE 5 END)';
                } else {
                    // single word or sentence search
                    $search_orderby .= reset( $search_orderby_title ) . ' DESC';
                }
            }

            return apply_filters( 'ywcas_parse_search_order', $search_orderby, $s, $search_terms) ;

        }

        /**
         * Remove Filter after the query
         *
         * @return void
         *
         * @since 1.3.6
         * @author Emanuela Castorina
         */

        public function remove_pre_get_posts() {
            global $wp_the_query;
            if ( ! is_admin() &&  !empty( $wp_the_query->query_vars['s'] ) && !( defined( 'DOING_AJAX' ) && DOING_AJAX )) {
                remove_filter( 'posts_join', array( $this, 'search_post_join' ) );
                remove_filter( 'posts_where', array( $this, 'search_post_where' ) );
               // add_filter( 'posts_orderby', array( $this, 'search_post_orderby' ) );
                add_filter( 'posts_groupby', array( $this, 'search_post_groupby' ) );
                remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
            }

        }


        /* === YITH WooCommerce Brands Compatibility === */

        /**
         * Filters search options, to add brands to search
         *
         * @param $search_options mixed Original array of option
         * @return mixed Filtered array of options
         *
         * @since 1.3.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.com>
         */
        public function add_brands_search_option( $search_options ){
            if( defined( 'YITH_WCBR' ) ) {
                $options_chunk_1 = array_splice( $search_options['search'], 0, 6 );
                $options_chunk_2 = $search_options['search'];

                $brand_option = array(
                    'search_in_product_brands' => array(
                        'name'    => __( 'Search in product brands', 'yith-woocommerce-ajax-search' ),
                        'desc'    => __( 'Extend search in product brands' ),
                        'id'      => 'yith_wcas_search_in_product_brands',
                        'default' => 'yes',
                        'type'    => 'checkbox'
                    )
                );

                $search_options['search'] = array_merge( $options_chunk_1, $brand_option, $options_chunk_2 );
            }

            return $search_options;
        }

        /**
         * Filters search params, to add brands to search
         *
         * @param $search_params mixed Original array of params
         * @return mixed Filtered array of params
         *
         * @since 1.3.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.com>
         */
        public function add_brands_search_params( $search_params ){
            if( defined( 'YITH_WCBR' ) ) {
                $search_params['search_by_brand'] = apply_filters( 'yith_wcas_search_in_product_brands', get_option( 'yith_wcas_search_in_product_brands' ) );
            }

            return $search_params;
        }


	    /* === YITH WooCommerce Multi Vendor Compatibility === */

	    /**
	     * Filters search options, to add brands to search
	     *
	     * @param $search_options mixed Original array of option
	     * @return mixed Filtered array of options
	     *
	     * @since 1.4.5
	     * @author Emanuela Castorina
	     */
	    public function add_vendor_search_option( $search_options ) {

		    $options_chunk_1 = array_splice( $search_options['search'], 0, 6 );
		    $options_chunk_2 = $search_options['search'];

		    $brand_option = array(
			    'search_in_product_vendors' => array(
				    'name'    => __( 'Search by vendor', 'yith-woocommerce-ajax-search' ),
				    'desc'    => __( 'Extend search in vendors\' products' ),
				    'id'      => 'yith_wcas_search_in_vendor',
				    'default' => 'yes',
				    'type'    => 'checkbox'
			    )
		    );

		    $search_options['search'] = array_merge( $options_chunk_1, $brand_option, $options_chunk_2 );

		    return $search_options;
	    }

	    /**
	     * Filters search params, to add brands to search
	     *
	     * @param $search_params mixed Original array of params
	     * @return mixed Filtered array of params
	     *
	     * @since 1.4.5
	     * @author Emanuela Castorina
	     */
	    public function add_vendor_search_params( $search_params ) {

		    $search_params['search_by_vendor'] = apply_filters( 'yith_wcas_search_in_vendors', get_option( 'yith_wcas_search_in_vendor' ) );

		    return $search_params;
	    }

    }
}