<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\helpers\ImporterOptions;

require_once dirname(__FILE__) . '/ImportProductBase.php';

/**
 * Class ImportProduct
 * @package wpai_woocommerce_add_on\libraries\importer
 */
abstract class ImportProduct extends ImportProductBase {

    /**
     * @var \WC_Product
     */
    public $product;

    /**
     * @var array
     */
    public $productProperties = array();

    /**
     * @var bool
     */
    public $isNewProduct;

    /**
     * @var bool
     */
    public $downloadable;

    /**
     * @var bool
     */
    public $virtual;

    /**
     * @var bool
     */
    public $featured;

    /**
     * @var string
     */
    protected $productType;

    /**
     * ImportProduct constructor.
     * @param \wpai_woocommerce_add_on\libraries\importer\ImporterIndex $index
     * @param \wpai_woocommerce_add_on\libraries\helpers\ImporterOptions $options
     * @param array $data
     */
    public function __construct(ImporterIndex $index, ImporterOptions $options, array $data) {
        parent::__construct($index, $options, $data);
        $this->isNewProduct = empty($this->getArticleData('ID'));
        $this->downloadable = $this->getValue('product_downloadable') == 'yes';
        $this->virtual = $this->getValue('product_virtual') == 'yes';
        $this->featured = $this->getValue('product_featured') == 'yes';
    }

    /**
     *
     * @return mixed
     */
    public function import() {
        $this->setProperties();
        $this->save();
    }

    /**
     *  Save product data into database.
     */
    public function save() {
        /**
         * @since 3.0.0 to set props before save.
         */
        do_action( 'woocommerce_admin_process_product_object', $this->product );
        $this->product->save();
        do_action( 'woocommerce_process_product_meta_' . $this->product->get_type(), $this->product->get_id() );
        wc_delete_product_transients($this->product->get_id());
    }

    /**
     * @return mixed
     */
    public function setProperties() {
        $this->prepareProperties();
        foreach ($this->productProperties as $property => $value) {
            $this->log(sprintf(__('Property `%s` updated with value `%s`', \PMWI_Plugin::TEXT_DOMAIN), $property, maybe_serialize($value)));
        }
        $errors = $this->product->set_props($this->productProperties);
        if (is_wp_error($errors)) {
            $this->log('<b>ERROR:</b> ' . $errors->get_error_message());
        }
    }

    /*
	|--------------------------------------------------------------------------
	| Product Properties Methods
	|--------------------------------------------------------------------------
	*/

    /**
     *
     * Define all product properties.
     *
     * @return mixed
     */
    public function prepareProperties(){
        $this->prepareGeneralProperties();
        $this->prepareInventoryProperties();
        $this->prepareShippingProperties();
        $this->prepareLinkedProducts();
        $this->prepareAttributesProperties();
        $this->prepareAdvancedProperties();
    }

    /**
     *  Define general properties.
     */
    public function prepareGeneralProperties(){
        // Prices.
        $this->setProperty('price', wc_clean( $this->getValue('product_regular_price') ));
        $this->setProperty('regular_price', wc_clean( $this->getValue('product_regular_price') ));
        $this->setProperty('sale_price', wc_clean( $this->getValue('product_sale_price') ));
        $this->setProperty('date_on_sale_from', wc_clean( $this->getValue('product_sale_price_dates_from') ));
        $this->setProperty('date_on_sale_to', wc_clean( $this->getValue('product_sale_price_dates_to') ));
        // Product properties.
        $this->setProperty('downloadable', $this->isDownloadable());
        $this->setProperty('virtual', $this->isVirtual());
        $this->setProperty('featured', $this->isFeatured());
        $this->setProperty('catalog_visibility', wc_clean( $this->getValue('product_visibility') ));
        $this->prepareDownloadableProperties();
        $this->prepareTaxProperties();
    }

        /**
         *  Define general -> downloadable properties.
         */
        public function prepareDownloadableProperties(){
            // Downloadable options.
            if ( $this->isDownloadable() ) {
                $_download_limit = absint( $this->getValue('product_download_limit') );
                if (!$_download_limit) {
                    $_download_limit = ''; // 0 or blank = unlimited
                }
                $this->setProperty('download_limit', $_download_limit);
                $_download_expiry = absint( $this->getValue('product_download_expiry') );
                if (!$_download_expiry) {
                    $_download_expiry = ''; // 0 or blank = unlimited
                }
                $this->setProperty('download_expiry', $_download_expiry);
                // File paths will be stored in an array keyed off md5(file path).
                if ($this->getValue('product_files')) {
                    $_file_paths = array();
                    $file_paths = explode( $this->getImport()->options['product_files_delim'] , $this->getValue('product_files') );
                    $file_names = explode( $this->getImport()->options['product_files_names_delim'] , $this->getValue('product_files_names') );
                    foreach ( $file_paths as $fn => $file_path ) {
                        $file_path = trim( $file_path );
                        $_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
                    }
                    $this->setProperty('downloads', $_file_paths);
                }
            }
        }

        /**
         *  Define general -> tax properties.
         */
        public function prepareTaxProperties(){
            $tax_status = wc_clean($this->getValue('product_tax_status'));
            $tax_class = strtolower($this->getValue('product_tax_class') == 'standard' ? '' : wc_clean($this->getValue('product_tax_class')));
            $this->setProperty('tax_status', $tax_status != '' ? $tax_status : null);
            $this->setProperty('tax_class', $tax_class != '' ? $tax_class : null);
        }

    /**
     *  Define inventory properties.
     */
    public function prepareInventoryProperties() {
        $this->prepareSKU();
        $this->setProperty('manage_stock', $this->getValue('product_manage_stock') == 'yes');
        $backorders = $this->getValue('product_allow_backorders');
        $this->setProperty('backorders', $backorders != '' ? wc_clean($backorders) : null);
        $this->setProperty('stock_status', wc_clean($this->getValue('product_stock_status')));
        $this->setProperty('stock_quantity', wc_stock_amount($this->getValue('product_stock_qty')));
        $this->setProperty('sold_individually', 'yes' == $this->getValue('product_sold_individually'));
    }

        /**
         *  Define product SKU.
         */
        public function prepareSKU() {
            $this->setProperty('sku', $this->generateSKU() );
        }

    /**
     *  Define shipping properties.
     */
    public function prepareShippingProperties() {
        $shippingClass = $this->getShippingClass();
        if (empty($shippingClass)) {
            $shippingClass = -1;
        }
        $this->setProperty('shipping_class_id', absint($shippingClass));
        $this->prepareDimensions();
    }

        /**
         *  Define dimensions properties.
         */
        protected function prepareDimensions() {
            if ( ! $this->isVirtual() ) {
                $this->setProperty('weight', stripslashes($this->getValue('product_weight')));
                $this->setProperty('length', stripslashes($this->getValue('product_length')));
                $this->setProperty('width', stripslashes($this->getValue('product_width')));
                $this->setProperty('height', stripslashes($this->getValue('product_height')));
            } else {
                $this->setProperty('weight', '' );
                $this->setProperty('length', '' );
                $this->setProperty('width', '' );
                $this->setProperty('height', '' );
            }
        }

    /**
     *  Define linked properties.
     */
    public function prepareLinkedProducts() {
        // Upsells.
        if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_upsell_ids')) {
            $linked = $this->getLinkedProducts($this->getPid(), $this->getValue('product_up_sells'), '_upsell_ids');
            $this->setProperty('upsell_ids', $linked );
        }
        // Cross sells.
        if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_crosssell_ids')) {
            $linked = $this->getLinkedProducts($this->getPid(), $this->getValue('product_cross_sells'), '_crosssell_ids');
            $this->setProperty('cross_sell_ids', $linked);
        }
        // Grouping.
        $this->importGrouping();
    }

    /**
     *  Define attributes properties.
     */
    public function prepareAttributesProperties() {
        if (!$this->getImportService()->isUpdateDataAllowed('is_update_attributes', $this->isNewProduct())) {
            return TRUE;
        }
        $attributes = $this->getAttributesProperties();
        $this->setProperty('attributes', $attributes);
    }

    /**
     * @return array \WC_Product_Attribute
     */
    public function getAttributesProperties() {
        $attributes = $this->getAttributesData();
        if (!empty($attributes['attribute_names'])) {
            $attributes = \WC_Meta_Box_Product_Data::prepare_attributes($attributes);
        }
        else {
            $attributes = array();
        }
        return $attributes;
    }

    /**
     * Get attributes data.
     *
     * @return array
     */
    public function getAttributesData() {
        $data = array(
            'attribute_names' => array(),
            'attribute_values' => array(),
            'attribute_visibility' => array(),
            'attribute_variation' => array(),
            'attribute_position' => array()
        );
        $max_attribute_length = apply_filters('wp_all_import_max_woo_attribute_term_length', 199);
        $parsedAttributes = array();
        $attributesToImport = $this->getParsedDataOption('serialized_attributes');
        if (!empty($attributesToImport)) {
            $attribute_position = 0;
            foreach ($attributesToImport as $attributeData) {
                $real_attr_name = $attributeData['names'][$this->getIndex()];
                if (empty($real_attr_name)) {
                    continue;
                }
                $isTaxonomy = intval($attributeData['in_taxonomy'][$this->getIndex()]);
                $attributeName = ($isTaxonomy) ? wc_attribute_taxonomy_name( $real_attr_name ) : $real_attr_name;
                $isUpdateAttributes = $this->getImportService()->isUpdateAttribute($attributeName, $this->isNewProduct());
                $attribute_position++;
                if ($isUpdateAttributes) {
                    $values = $attributeData['value'][$this->getIndex()];
                    if ( $isTaxonomy ) {
                        if ( isset( $attributeData['value'][$this->getIndex()] ) ) {
                            $values = array_map('stripslashes', array_map( 'strip_tags', explode( '|', $attributeData['value'][$this->getIndex()])));
                            // Remove empty items in the array.
                            $values = array_filter( $values, array($this, "filtering") );
                            if (intval($attributeData['is_create_taxonomy_terms'][$this->getIndex()])){
                                $real_attr_name = $this->getImportService()->getTaxonomiesService()->createTaxonomy($real_attr_name);
                            }
                            if ( ! empty($values) && taxonomy_exists( wc_attribute_taxonomy_name( $real_attr_name ) )){
                                $attr_values = array();
                                foreach ($values as $key => $val) {
                                    $value = substr($val, 0, $max_attribute_length);
                                    $term = get_term_by('name', $value, wc_attribute_taxonomy_name( $real_attr_name ), ARRAY_A);
                                    // For compatibility with WPML plugin.
                                    $term = apply_filters('wp_all_import_term_exists', $term, wc_attribute_taxonomy_name( $real_attr_name ), $value, null);
                                    if ( empty($term) && !is_wp_error($term) ){
                                        $term = is_exists_term($value, wc_attribute_taxonomy_name( $real_attr_name ));
                                        if ( empty($term) && !is_wp_error($term) ){
                                            $term = is_exists_term(htmlspecialchars($value), wc_attribute_taxonomy_name( $real_attr_name ));
                                            if ( empty($term) && !is_wp_error($term) && intval($attributeData['is_create_taxonomy_terms'][$this->getIndex()])){
                                                $term = wp_insert_term(
                                                    $value, // the term
                                                    wc_attribute_taxonomy_name( $real_attr_name ) // the taxonomy
                                                );
                                            }
                                        }
                                    }
                                    if (!is_wp_error($term)) {
                                        $attr_values[] = (int) $term['term_taxonomy_id'];
                                    }
                                }
                                $values = $attr_values;
                                $values = array_map( 'intval', $values );
                                $values = array_unique( $values );
                            }
                            else{
                                $values = array();
                            }
                        }
                    }
                    $parsedAttributes[$attributeName] = array(
                        'name' => $attributeName,
                        'value' => $values,
                        'is_visible' => intval($attributeData['is_visible'][$this->getIndex()]),
                        'in_variation' => intval($attributeData['in_variation'][$this->getIndex()]),
                        'position' => $attribute_position
                    );
                }
            }
        }

        if (!$this->getProduct() instanceof \WC_Product_Variation) {
            $productAttributes = array();
            $attributes = $this->getProduct()->get_attributes();
            /** @var \WC_Product_Attribute $attribute */
            foreach ($attributes as $attributeName => $attribute) {
                if (!$this->getImportService()->isUpdateAttribute($attributeName, $this->isNewProduct())) {
                    $productAttributes[$attributeName] = array(
                        'name' => $attributeName,
                        'value' => $attribute->get_options(),
                        'is_visible' => $attribute->get_visible(),
                        'in_variation' => $attribute->get_variation(),
                        'position' => $attribute->get_position()
                    );
                }
            }
            $parsedAttributes = array_merge($parsedAttributes, $productAttributes);
        }
        // Prepare attributes for response.
        foreach ($parsedAttributes as $parsedAttribute) {
            $data['attribute_names'][] = $parsedAttribute['name'];
            $data['attribute_values'][] = $parsedAttribute['value'];
            $data['attribute_visibility'][] = $parsedAttribute['is_visible'];
            $data['attribute_variation'][] = $parsedAttribute['in_variation'];
            $data['attribute_position'][] = $parsedAttribute['position'];
        }
        return $data;
    }

    /**
     *  Define advanced properties.
     */
    public function prepareAdvancedProperties() {
        // Import product comment status.
        $this->setProperty('purchase_note', wp_kses_post(stripslashes($this->getValue('product_purchase_note'))));
        $this->setProperty('reviews_allowed', $this->getValue('product_enable_reviews') == 'yes');
        // Import product menu order.
        $this->setProperty('menu_order', $this->getValue('product_menu_order') != '' ? (int) $this->getValue('product_menu_order') : 0);
        // Import total sales.
        $total_sales = get_post_meta($this->getPid(), 'total_sales', true);
        if ( empty($total_sales)) {
            update_post_meta($this->getPid(), 'total_sales', '0');
        }
    }

    /*
	|--------------------------------------------------------------------------
	| Product Import Methods
	|--------------------------------------------------------------------------
	*/

    /**
     *  Get shipping class.
     */
    private function getShippingClass() {
        $shipping_class = $this->getValue('product_shipping_class');
        if ( $shipping_class != '' ) {
            $term = false;
            if (!is_numeric($shipping_class)){
                $term = get_term_by('slug', $shipping_class, 'product_shipping_class');
                // For compatibility with WPML plugin.
                $term = apply_filters('wp_all_import_term_exists', $term, 'product_shipping_class', $shipping_class, null);
            }
            // The term to check. Accepts term ID, slug, or name.
            if (!empty($term) && !is_wp_error($term)){
                $term = is_exists_term( (int) $shipping_class, 'product_shipping_class');
                if (!empty($term) && !is_wp_error($term)) {
                    $shipping_class = (int) $term['term_taxonomy_id'];
                }
                else {
                    $term = wp_insert_term($shipping_class, 'product_shipping_class');
                    if (!empty($term) && !is_wp_error($term)) {
                        $shipping_class = (int) $term['term_taxonomy_id'];
                    }
                }
            }
            if (empty($term) || is_wp_error($term)) {
                $shipping_class = '';
            }
        }
        return $shipping_class;
    }

    /**
     *  Generate product SKU.
     */
    protected function generateSKU() {

        $generatedSKU = '';

        // Unique SKU
        $sku = $this->isNewProduct() ? '' : get_post_meta($this->getPid(), '_sku', true);
        $new_sku = wc_clean( trim( stripslashes( $this->getValue('product_sku') ) ) );

        if ( ( in_array($this->productType, array('variation', 'variable')) || $this->getValue('product_types') == "variable" ) && ! $this->getImport()->options['link_all_variations'] ){
            switch ($this->getImport()->options['matching_parent']){
                case 'first_is_parent_id':
                    if (!empty($this->getValue('single_product_first_is_parent_id_parent_sku'))){
                        update_post_meta($this->getPid(), '_parent_sku', $this->getValue('single_product_first_is_parent_id_parent_sku'));
                    }
                    break;
                case 'first_is_variation':
                    if (!empty($this->getValue('single_product_first_is_parent_title_parent_sku'))){
                        update_post_meta($this->getPid(), '_parent_sku', $this->getValue('single_product_first_is_parent_title_parent_sku'));
                    }
                    break;
            }
        }

        if ( $new_sku == '' and $this->getImport()->options['disable_auto_sku_generation'] ) {
            $generatedSKU = '';
        }
        elseif ( $new_sku == '' && ! $this->getImport()->options['disable_auto_sku_generation'] ) {
            if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_sku')){
                if (!empty($this->getImport()->options['unique_key'])) {
                    $cxpath = $this->getParser()
                            ->getXpath() . $this->getImport()->xpath;
                    $unique_keys = \XmlImportParser::factory($this->getParser()
                        ->getXml(), $cxpath, $this->getImport()->options['unique_key'], $file)
                        ->parse();
                    $tmp_files[] = $file;
                    foreach ($tmp_files as $file) { // remove all temporary files created
                        @unlink($file);
                    }
                    $new_sku = substr(md5($unique_keys[$this->getIndex()]), 0, 12);
                }

                if ( ( in_array($this->productType, array('variation', 'variable')) || $this->getValue('product_types') == "variable" ) && ! $this->getImport()->options['link_all_variations'] ){
                    switch ($this->getImport()->options['matching_parent']){
                        case 'first_is_parent_id':
                            if (empty($this->getValue('single_product_first_is_parent_id_parent_sku'))){
                                update_post_meta($this->getPid(), '_parent_sku', strrev($new_sku));
                            }
                            break;
                        case 'first_is_variation':
                            if (empty($this->getValue('single_product_first_is_parent_title_parent_sku'))){
                                update_post_meta($this->getPid(), '_parent_sku', strrev($new_sku));
                            }
                            break;
                    }
                }
            }
        }
        if ( $new_sku != '' && $new_sku !== $sku ) {
            if ( ! empty( $new_sku ) ) {
                if ( ! $this->getImport()->options['disable_sku_matching'] and
                    $this->wpdb->get_var( $this->wpdb->prepare("
						SELECT ".$this->wpdb->posts.".ID
					    FROM ".$this->wpdb->posts."
					    LEFT JOIN ".$this->wpdb->postmeta." ON (".$this->wpdb->posts.".ID = ".$this->wpdb->postmeta.".post_id)
					    WHERE (".$this->wpdb->posts.".post_type = 'product'
					    OR ".$this->wpdb->posts.".post_type = 'product_variation')
					    AND ".$this->wpdb->posts.".post_status = 'publish'
					    AND ".$this->wpdb->postmeta.".meta_key = '_sku' AND ".$this->wpdb->postmeta.".meta_value = '%s'
					 ", $new_sku ) )
                ) {
                    $this->getLogger() and call_user_func($this->getLogger(), sprintf(__('<b>WARNING</b>: Product SKU must be unique.', \PMWI_Plugin::TEXT_DOMAIN)));

                } else {
                    $generatedSKU = $new_sku;
                }
            }
        }
        return wc_clean($generatedSKU);
    }

    /**
     *  Import products grouping.
     */
    protected function importGrouping() {
        // Group products by Parent.
        if (in_array($this->productType, array( 'simple', 'external', 'variable' ))) {
            // Group all product to one parent ( no XPath provided ).
            if ($this->getImport()->options['is_multiple_grouping_product'] != 'yes') {
                // Trying to find parent product according to matching options.
                if ($this->getImport()->options['grouping_indicator'] == 'xpath' && !is_numeric($this->getValue('product_grouping_parent'))) {
                    $post = pmxi_findDuplicates(array(
                        'post_type' => 'product',
                        'ID' => $this->getPid(),
                        'post_parent' => $this->getArticleData('post_parent'),
                        'post_title' => $this->getValue('product_grouping_parent')
                    ));
                    if (!empty($post)) {
                        $this->setValue('product_grouping_parent', $post[0]);
                    }
                    else {
                        $this->setValue('product_grouping_parent', 0);
                    }
                }
                elseif ($this->getImport()->options['grouping_indicator'] != 'xpath') {
                    $post = pmxi_findDuplicates($this->getArticle(), $this->getValue('custom_grouping_indicator_name'), $this->getValue('custom_grouping_indicator_value'), 'custom field');
                    if (!empty($post)) {
                        $this->setValue('product_grouping_parent', array_shift($post));
                    }
                    else {
                        $this->setValue('product_grouping_parent', 0);
                    }
                }
            }
            // Update `children` property of parent product with new item (current product).
            if ($this->getValue('product_grouping_parent') != "" && absint($this->getValue('product_grouping_parent')) > 0) {
                $this->product->set_parent_id(absint( $this->getValue('product_grouping_parent') ));
                $all_grouped_products = get_post_meta($this->getValue('product_grouping_parent'), '_children', true);
                if (empty($all_grouped_products)) {
                    $all_grouped_products = array();
                }
                if (!in_array($this->getPid(), $all_grouped_products)) {
                    $all_grouped_products[] = $this->getPid();
                    update_post_meta($this->getValue('product_grouping_parent'), '_children', $all_grouped_products);
                }
            }
        }
    }

    /*
	|--------------------------------------------------------------------------
	| Getters & Setters
	|--------------------------------------------------------------------------
	*/

    /**
     * @return boolean
     */
    public function isNewProduct() {
        return $this->isNewProduct;
    }

    /**
     * @return boolean
     */
    public function isDownloadable() {
        return $this->downloadable;
    }

    /**
     * @return boolean
     */
    public function isVirtual() {
        return $this->virtual;
    }

    /**
     * @return boolean
     */
    public function isFeatured() {
        return $this->featured;
    }

    /**
     * @return \WC_Product
     */
    public function getProduct(){
        return $this->product;
    }

    /**
     * @param \WC_Product $product
     */
    public function setProduct($product){
        $this->product = $product;
    }

    /**
     * @param $property
     * @param $value
     */
    public function setProperty($property, $value) {
        switch ($property) {
            case 'attributes':
            case 'menu_order':
            case 'catalog_visibility':
                if ($this->getImportService()->isUpdateDataAllowed('is_update_' . $property, $this->isNewProduct())) {
                    $this->productProperties[$property] = $value;
                }
                break;
            case 'featured':
                if ($this->getImportService()->isUpdateDataAllowed('is_update_featured_status', $this->isNewProduct())) {
                    $this->productProperties[$property] = $value;
                }
                break;
            default:
                if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField($this->getPropertyMetaKey($property))) {
                    $this->productProperties[$property] = $value;
                }
                break;
        }
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    private function getPropertyMetaKey($property) {
        switch ($property) {
            case 'stock_quantity':
                $property = '_stock';
                break;
        }
        return '_' === substr( $property, 0, 1 ) ? $property : '_' . $property;
    }

    /**
     * @param $property
     * @return mixed|null
     */
    public function getProperty($property){
        return isset($this->productProperties[$property]) ? $this->productProperties[$property] : null;
    }
}
