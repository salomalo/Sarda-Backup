<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


return apply_filters( 'yith_wcas_search_options', array(
    'search' => array(

        'search_option_section'         => array(
            'name'              => __( 'Search settings', 'yith-woocommerce-ajax-search' ),
            'type'              => 'title',
            'id'                => 'yith_wcas_search_options'
        ),

        //from 1.2.7
        'default_research' => array(
            'name'    => __( 'Choose element types to search', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Choose if you want to extend search also to posts and pages', 'yith-woocommerce-ajax-search'),
            'id'      => 'yith_wcas_default_research',
            'class'   => 'yith-wcas-chosen',
            'default' => 'product',
            'type'    => 'select',
            'options' => array(
                'any'     => __( 'All', 'yith-woocommerce-ajax-search' ),
                'product' => __( 'Products', 'yith-woocommerce-ajax-search' ),
            ),
        ),

        'search_in_excerpt' => array(
            'name'    => __( 'Search in excerpt', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search in the excerpt of the product','yith-woocommerce-ajax-search' ),
            'id'      => 'yith_wcas_search_in_excerpt',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),


        'search_in_content' => array(
            'name'    => __( 'Search in content', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search in the content of the product','yith-woocommerce-ajax-search' ),
            'id'      => 'yith_wcas_search_in_content',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),

        'search_in_product_categories' => array(
            'name'    => __( 'Search in product categories', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search in product categories','yith-woocommerce-ajax-search' ),
            'id'      => 'yith_wcas_search_in_product_categories',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),

        'search_in_product_tags' => array(
            'name'    => __( 'Search in product tags', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search in product tags','yith-woocommerce-ajax-search' ),
            'id'      => 'yith_wcas_search_in_product_tags',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),


        'search_type_more_words' => array(
            'name'    => __( 'Multiple Word Search', 'yith-woocommerce-ajax-search' ),
            'desc'    => '',
            'id'      => 'yith_wcas_search_type_more_words',
            'default' => 'or',
            'type'    => 'select',
            'options' => array(
                'and'  => __( 'Show items containing all words typed', 'yith-woocommerce-ajax-search' ),
                'or' => __( 'Show items containing al least one of the words typed', 'yith-woocommerce-ajax-search' ),
            ),
        ),

        'enable_transient' => array(
	        'name'    => __( 'Enable transients to cache autocomplete results', 'yith-woocommerce-ajax-search' ),
	        'desc'    => __( 'Save the results of a query in a transient','yith-woocommerce-ajax-search' ),
	        'id'      => 'yith_wcas_enable_transient',
	        'default' => 'no',
	        'type'    => 'checkbox'
        ),


        'transient_duration' => array(
	        'name'    => __( 'Set the duration of transient', 'yith-woocommerce-ajax-search' ),
	        'desc'    => __( '(hours)','yith-woocommerce-ajax-search' ),
	        'id'      => 'yith_wcas_transient_duration',
	        'default' => 12,
	        'type'    => 'text'
        ),

/*
        'search_mode' => array(
            'name'    => __( 'Search Mode', 'yith-woocommerce-ajax-search' ),
            'desc'    => '',
            'id'      => 'yith_wcas_search_mode',
            'default' => 'regex',
            'type'    => 'select',
            'options' => array(
                'regex'  => __( 'Regex', 'yith-woocommerce-ajax-search' ),
                'like' => __( 'Like', 'yith-woocommerce-ajax-search' ),
            ),
        ),
*/

        'search_option_section_end' => array(
            'type' => 'sectionend',
            'id'   => 'yith_wcas_search_options_end'
        ),

        //from 1.3.9
        'order_option_section'         => array(
            'name'              => __( 'Order Options', 'yith-woocommerce-ajax-search' ),
            'desc'    => '',
            'type'              => 'title',
            'id'                => 'yith_wcas_order_option_section'
        ),
        
        //from 1.3.9
        'order_by_post_type'               => array(
            'name'    => __( 'Enable order by post type', 'yith-woocommerce-ajax-search' ),
            'type'    => 'checkbox',
            'desc'    => '',
            'id'      => 'yith_wcas_order_by_post_type',
            'default' => 'no',
        ),

        //from 1.3.9
        'order_by_post_type_select' => array(
            'name'    => __( 'Show first: ', 'yith-woocommerce-ajax-search' ),
            'desc'    => '',
            'id'      => 'yith_wcas_order_by_post_type_select',
            'default' => 'product',
            'type'    => 'select',
            'options' => array(
                'product' => __( 'Product', 'yith-woocommerce-ajax-search' ),
                'post'    => __( 'Post & Pages', 'yith-woocommerce-ajax-search' ),
            ),
        ),

        //from 1.3.9
        'order_option_section_end' => array(
            'type' => 'sectionend',
            'id'   => 'yith_wcas_order_option_section_end'
        ),



        //from 1.3.2
        'search_by_cf_option_section'         => array(
            'name'              => __( 'Search by Custom Field', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search functionality to custom fields. Enter custom fields comma separated. Attention: this feature may slow down the search process on some servers.','yith-woocommerce-ajax-search' ),
            'type'              => 'title',
            'id'                => 'yith_wcas_search_by_custom_field'
        ),

        'cf_name'               => array(
            'name'    => __( 'Custom field name', 'yith-woocommerce-ajax-search' ),
            'type'    => 'text',
            'desc'    => '',
            'id'      => 'yith_wcas_cf_name',
            'default' => '',
        ),

        //from 1.3.2
        'search_by_cf_option_section_end' => array(
            'type' => 'sectionend',
            'id'   => 'yith_wcas_search_by_custom_field_end'
        ),

        //from 1.2.3
        'search_by_sku_option_section'         => array(
            'name'              => __( 'Search by Sku Settings', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search functionality so that search includes also sku. Attention: this feature may slow down the search process on some servers.','yith-woocommerce-ajax-search' ),
            'type'              => 'title',
            'id'                => 'yith_wcas_search_by_sku_options'
        ),


        'search_by_sku' => array(
            'name'    => __( 'Search by sku', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend search functionality so that search includes also sku','yith-woocommerce-ajax-search' ),
            'id'      => 'yith_wcas_search_by_sku',
            'default' => 'no',
            'type'    => 'checkbox'
        ),

        //from 1.2.3
        'search_by_sku_variations' => array(
            'name'    => __( 'Search by sku variable products', 'yith-woocommerce-ajax-search' ),
            'desc'    => __( 'Extend sku search including variable products.','yith-woocommerce-ajax-search' ),
            'id'      => 'yith_wcas_search_by_sku_variations',
            'default' => 'no',
            'type'    => 'checkbox'
        ),


        //from 1.2.3
        'search_by_sku_option_section_end' => array(
            'type' => 'sectionend',
            'id'   => 'yith_wcas_search_by_sku_options_end'
        ),



    )
) );