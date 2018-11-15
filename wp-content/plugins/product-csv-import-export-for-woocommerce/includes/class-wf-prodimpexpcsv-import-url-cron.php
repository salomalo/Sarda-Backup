<?php

if (!defined('WPINC'))
    exit; // Exit if accessed directly

class WF_ProdImpExpCsv_ImportUrlCron {

    public $settings;
    public $file_url;
    public $error_message;

    public function __construct() {
        add_filter('cron_schedules', array($this, 'wf_auto_import_url_schedule'), 11);
        add_action('init', array($this, 'wf_new_scheduled_import_url'));
        add_action('wf_woocommerce_csv_im_ex_auto_import_products_from_url', array($this, 'wf_scheduled_import_url_products'));
        $this->settings = get_option('woocommerce_' . WF_PROD_IMP_EXP_ID . '_settings', null);

        $this->imports_enabled = FALSE;
        if (isset($this->settings['pro_enable_url_ie']) && $this->settings['pro_enable_url_ie'] === TRUE)
            $this->imports_enabled = TRUE;
    }

    public function wf_auto_import_url_schedule($schedules) {
        if ($this->imports_enabled) {
            $import_interval = $this->settings['pro_auto_import_url_interval'];
            if ($import_interval) {
                $schedules['import_url_interval'] = array(
                    'interval' => (int) $import_interval * 60,
                    'display' => sprintf(__('Every %d minutes', 'wf_csv_import_export'), (int) $import_interval)
                );
            }
        }
        return $schedules;
    }

    public function wf_new_scheduled_import_url() {

        if ($this->imports_enabled) {
            if (!wp_next_scheduled('wf_woocommerce_csv_im_ex_auto_import_products_from_url')) {

                $start_time = $this->settings['pro_auto_import_url_start_time'];
                $current_time = current_time('timestamp');
                if ($start_time) {
                    if ($current_time > strtotime('today ' . $start_time, $current_time)) {
                        $start_timestamp = strtotime('tomorrow ' . $start_time, $current_time) - ( get_option('gmt_offset') * HOUR_IN_SECONDS );
                    } else {
                        $start_timestamp = strtotime('today ' . $start_time, $current_time) - ( get_option('gmt_offset') * HOUR_IN_SECONDS );
                    }
                } else {
                    $import_url_interval = $this->settings['pro_auto_import_url_interval'];
                    $start_timestamp = strtotime("now +{$import_url_interval} minutes");
                }
                wp_schedule_event($start_timestamp, 'import_url_interval', 'wf_woocommerce_csv_im_ex_auto_import_products_from_url');
            }
        }
    }

    public static function load_wp_importer() {
        // Load Importer API
        require_once ABSPATH . 'wp-admin/includes/import.php';

        if (!class_exists('WP_Importer')) {
            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
            if (file_exists($class_wp_importer)) {
                require $class_wp_importer;
            }
        }
    }

    public function wf_scheduled_import_url_products() {

        if (!defined('WP_LOAD_IMPORTERS'))
            define('WP_LOAD_IMPORTERS', true);

        if (!class_exists('WooCommerce')) :
            require ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php';
        endif;
        $delimiter = (!empty($this->settings['pro_auto_import_url_delimiter']) ) ? $this->settings['pro_auto_import_url_delimiter'] : ',';
        WF_ProdImpExpCsv_ImportUrlCron::product_importer($delimiter);

        if ($this->handle_url_for_autoimport()) {

            $mapping = '';
            $eval_field = '';
            $start_pos = 0;
            $end_pos = '';

            if ($this->settings['pro_auto_import_url_profile'] !== '') {
                $profile_array = get_option('wf_prod_csv_imp_exp_mapping');
                $mapping = $profile_array[$this->settings['pro_auto_import_url_profile']][0];
                $eval_field = $profile_array[$this->settings['pro_auto_import_url_profile']][1];
                $start_pos = 0;
                $end_pos = '';
            } else {
                $this->error_message = 'Please set a mapping profile';
                $GLOBALS['WF_CSV_Product_Import']->hf_log_data_change('csv-import', __('Failed processing import. Reason:' . $this->error_message, 'wf_csv_import_export'));
            }
            if ($this->settings['pro_auto_import_url_merge']) {
                $_GET['merge'] = 1;
            } else {
                $_GET['merge'] = 0;
            }
            if ($this->settings['pro_auto_import_url_skip']) {
                $_GET['skip_new'] = 1;
            } else {
                $_GET['skip_new'] = 0;
            }

            $_GET['delete_products'] = (($this->settings['pro_auto_import_url_delete_products']) ? 1 : 0);
            $GLOBALS['WF_CSV_Product_Import']->import_start(ABSPATH .$this->file_url, $mapping, $start_pos, $end_pos, $eval_field);
            $GLOBALS['WF_CSV_Product_Import']->import();
            $GLOBALS['WF_CSV_Product_Import']->import_end();

            if ($_GET['delete_products'] == 1) {
                $GLOBALS['WF_CSV_Product_Import']->delete_products_not_in_csv();
            }

            die();
        } else {
            $GLOBALS['WF_CSV_Product_Import']->hf_log_data_change('csv-import', __('Fetching file failed. Reason:' . $this->error_message, 'wf_csv_import_export'));
        }
    }

    public function clear_wf_scheduled_import_url() {
        wp_clear_scheduled_hook('wf_woocommerce_csv_im_ex_auto_import_products_from_url');
    }

    private function handle_url_for_autoimport() {

        if (filter_var($this->settings['pro_auto_import_url'], FILTER_VALIDATE_URL)) {
            $this->file_url = $GLOBALS['WF_CSV_Product_Import']->get_data_from_url($this->settings['pro_auto_import_url']);
            return true;
        } else {
            $this->error_message = "Sorry, The entered URL is not valid.";
            die($this->error_message);
        }
    }

    public static function product_importer($cron_delimiter) {
        if (!defined('WP_LOAD_IMPORTERS')) {
            return;
        }

        self::load_wp_importer();

        // includes
        require_once 'importer/class-wf-prodimpexpcsv-product-import.php';
        require_once 'importer/class-wf-csv-parser.php';

        if (!class_exists('WC_Logger')) {
            $class_wc_logger = ABSPATH . 'wp-content/plugins/woocommerce/includes/class-wc-logger.php';
            if (file_exists($class_wc_logger)) {
                require $class_wc_logger;
            }
        }

        $class_wc_logger = ABSPATH . 'wp-includes/pluggable.php';
        require_once($class_wc_logger);
        wp_set_current_user(1); // escape user access check while running cron

        $GLOBALS['WF_CSV_Product_Import'] = new WF_ProdImpExpCsv_Product_Import();
        $GLOBALS['WF_CSV_Product_Import']->import_page = 'woocommerce_csv_cron';
        $GLOBALS['WF_CSV_Product_Import']->delimiter = $cron_delimiter;
    }

}
