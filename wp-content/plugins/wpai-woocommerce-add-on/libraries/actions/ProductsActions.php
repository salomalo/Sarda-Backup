<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\parser\Parser;
use wpai_woocommerce_add_on\libraries\parser\ProductsParser;

require_once dirname(__FILE__) . '/Actions.php';

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/16/17
 * Time: 3:44 PM
 */

class ProductsActions extends Actions {

    /**
     * ProductsActions constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser) {
        parent::__construct($parser);
    }

    /**
     * @return ProductsParser
     */
    public function getParser() {
        parent::getParser();
    }

    /**
     * @return ImporterInterface
     */
    public function getImporter() {
        return parent::getImporter();
    }
}