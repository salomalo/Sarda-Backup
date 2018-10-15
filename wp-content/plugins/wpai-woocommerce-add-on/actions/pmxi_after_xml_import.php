<?php

/**
 * @param $importID
 *
 * @throws \Exception
 */
function pmwi_pmxi_after_xml_import($importID) {
    $import = new PMXI_Import_Record();
    $import->getById($importID);

    if (!$import->isEmpty() and in_array($import->options['custom_type'], array(
            'product',
            'product_variation'
        ))
    ) {
        // Re-count WooCommerce Terms.
        $recount_terms_after_import = TRUE;
        $recount_terms_after_import = apply_filters('wp_all_import_recount_terms_after_import', $recount_terms_after_import, $importID);
        if ($recount_terms_after_import && (($import->options['create_new_records'] && $import->options['is_keep_former_posts'] == 'yes') || ($import->options['is_keep_former_posts'] == 'no' && ($import->options['update_all_data'] == 'yes' || $import->options['is_update_categories'])))) {
            $product_cats = get_terms('product_cat', array(
                'hide_empty' => FALSE,
                'fields' => 'id=>parent'
            ));
            _wc_term_recount($product_cats, get_taxonomy('product_cat'), TRUE, FALSE);
            $product_tags = get_terms('product_tag', array(
                'hide_empty' => FALSE,
                'fields' => 'id=>parent'
            ));
            _wc_term_recount($product_tags, get_taxonomy('product_tag'), TRUE, FALSE);
        }
        // Delete Missing Products.
        $maybe_to_delete = get_option('wp_all_import_products_maybe_to_delete_' . $importID);
        if (!empty($maybe_to_delete)) {
            foreach ($maybe_to_delete as $pid) {
                $children = get_posts(array(
                    'post_parent' => $pid,
                    'posts_per_page' => -1,
                    'post_type' => 'product_variation',
                    'fields' => 'ids',
                    'orderby' => 'ID',
                    'order' => 'ASC',
                    'post_status' => array(
                        'draft',
                        'publish',
                        'trash',
                        'pending',
                        'future',
                        'private'
                    )
                ));

                if (empty($children)) {
                    wp_delete_post($pid, TRUE);
                }
            }
            delete_option('wp_all_import_products_maybe_to_delete_' . $importID);
        }
        delete_option('wp_all_import_not_linked_products_' . $importID);
        // Make Products Simple.
        $productStack = get_option('wp_all_import_product_stack_' . $importID, array());
        foreach ($productStack as $parentProductID) {
            XmlImportWooCommerceService::getInstance()
                ->syncVariableProductData($parentProductID);
        }
        delete_option('wp_all_import_product_stack_' . $importID);
    }
}