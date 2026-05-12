<?php
/**
 * Plugin Name: Lzon Project Tools
 * Description: Product Excel-compatible import/export and VNPay sandbox payment gateway for the Shop Lzon project.
 * Version: 1.0.0
 * Author: Shop Lzon
 */

if (!defined('ABSPATH')) {
    exit;
}

final class LZON_Project_Tools
{
    public static function init()
    {
        add_action('admin_menu', array(__CLASS__, 'register_admin_menu'));
        add_action('admin_post_lzon_export_products', array(__CLASS__, 'export_products'));
        add_action('admin_post_lzon_import_products', array(__CLASS__, 'import_products'));

        add_action('plugins_loaded', array(__CLASS__, 'init_payment_gateway'), 20);
    }

    public static function register_admin_menu()
    {
        add_submenu_page(
            'woocommerce',
            'Lzon Excel',
            'Lzon Excel',
            'manage_woocommerce',
            'lzon-excel',
            array(__CLASS__, 'render_excel_page')
        );
    }

    public static function render_excel_page()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'lzon-project-tools'));
        }

        $message = isset($_GET['lzon_message']) ? sanitize_text_field(wp_unslash($_GET['lzon_message'])) : '';
        ?>
        <div class="wrap">
            <h1>Lzon Excel Import / Export</h1>
            <p>Export products to a CSV file that opens in Excel, or import products from the same format.</p>

            <?php if ($message): ?>
                <div class="notice notice-success is-dismissible"><p><?php echo esc_html($message); ?></p></div>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:20px;max-width:1100px;">
                <div class="postbox" style="padding:20px;">
                    <h2>Export products</h2>
                    <p>The file includes: sku, name, prices, stock, categories, description, and image URL.</p>
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                        <?php wp_nonce_field('lzon_export_products'); ?>
                        <input type="hidden" name="action" value="lzon_export_products">
                        <button type="submit" class="button button-primary">Download Excel CSV</button>
                    </form>
                </div>

                <div class="postbox" style="padding:20px;">
                    <h2>Import products</h2>
                    <p>Upload a CSV exported from this tool. Existing products are updated by SKU; rows without an existing SKU create new products.</p>
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('lzon_import_products'); ?>
                        <input type="hidden" name="action" value="lzon_import_products">
                        <p><input type="file" name="products_file" accept=".csv,text/csv" required></p>
                        <button type="submit" class="button button-primary">Import products</button>
                    </form>
                </div>
            </div>

            <h2>CSV columns</h2>
            <code>sku,name,regular_price,sale_price,stock_quantity,categories,short_description,description,image_url</code>
        </div>
        <?php
    }

    public static function export_products()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to export products.', 'lzon-project-tools'));
        }

        check_admin_referer('lzon_export_products');

        if (!class_exists('WooCommerce')) {
            wp_die(esc_html__('WooCommerce is required.', 'lzon-project-tools'));
        }

        $filename = 'lzon-products-' . gmdate('Y-m-d-His') . '.csv';

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, array(
            'sku',
            'name',
            'regular_price',
            'sale_price',
            'stock_quantity',
            'categories',
            'short_description',
            'description',
            'image_url',
        ));

        $products = wc_get_products(array(
            'limit' => -1,
            'status' => array('publish', 'draft', 'private'),
            'type' => array('simple'),
        ));

        foreach ($products as $product) {
            $image_url = '';
            $image_id = $product->get_image_id();
            if ($image_id) {
                $image_url = wp_get_attachment_url($image_id);
            }

            $category_names = array();
            foreach ($product->get_category_ids() as $term_id) {
                $term = get_term($term_id, 'product_cat');
                if ($term && !is_wp_error($term)) {
                    $category_names[] = $term->name;
                }
            }

            fputcsv($output, array(
                $product->get_sku(),
                $product->get_name(),
                $product->get_regular_price(),
                $product->get_sale_price(),
                $product->get_stock_quantity(),
                implode('|', $category_names),
                wp_strip_all_tags($product->get_short_description()),
                wp_strip_all_tags($product->get_description()),
                $image_url,
            ));
        }

        fclose($output);
        exit;
    }

    public static function import_products()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to import products.', 'lzon-project-tools'));
        }

        check_admin_referer('lzon_import_products');

        if (!class_exists('WooCommerce')) {
            wp_die(esc_html__('WooCommerce is required.', 'lzon-project-tools'));
        }

        if (empty($_FILES['products_file']['tmp_name'])) {
            self::redirect_excel_page('No file uploaded.');
        }

        $file = $_FILES['products_file'];
        if (!empty($file['error'])) {
            self::redirect_excel_page('Upload failed.');
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            self::redirect_excel_page('Could not read uploaded file.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            self::redirect_excel_page('The file is empty.');
        }

        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        $map = array_flip($headers);
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = self::csv_row_to_assoc($map, $row);
            $name = trim($data['name']);

            if ($name === '') {
                continue;
            }

            $sku = trim($data['sku']);
            $product_id = $sku ? wc_get_product_id_by_sku($sku) : 0;
            $product = $product_id ? wc_get_product($product_id) : new WC_Product_Simple();

            if (!$product) {
                $product = new WC_Product_Simple();
            }

            $product->set_name($name);
            $product->set_status('publish');

            if ($sku !== '') {
                $product->set_sku($sku);
            }

            if ($data['regular_price'] !== '') {
                $product->set_regular_price(wc_format_decimal($data['regular_price']));
            }

            if ($data['sale_price'] !== '') {
                $product->set_sale_price(wc_format_decimal($data['sale_price']));
            } else {
                $product->set_sale_price('');
            }

            if ($data['stock_quantity'] !== '') {
                $product->set_manage_stock(true);
                $product->set_stock_quantity((int) $data['stock_quantity']);
                $product->set_stock_status(((int) $data['stock_quantity'] > 0) ? 'instock' : 'outofstock');
            }

            $product->set_short_description(wp_kses_post($data['short_description']));
            $product->set_description(wp_kses_post($data['description']));

            $category_ids = self::get_or_create_product_categories($data['categories']);
            if ($category_ids) {
                $product->set_category_ids($category_ids);
            }

            $product_id = $product->save();

            if ($product_id && !empty($data['image_url'])) {
                self::attach_remote_image($product_id, esc_url_raw($data['image_url']));
            }

            $imported++;
        }

        fclose($handle);
        self::redirect_excel_page('Imported ' . $imported . ' product rows.');
    }

    private static function csv_row_to_assoc($map, $row)
    {
        $keys = array(
            'sku',
            'name',
            'regular_price',
            'sale_price',
            'stock_quantity',
            'categories',
            'short_description',
            'description',
            'image_url',
        );

        $data = array();
        foreach ($keys as $key) {
            $data[$key] = isset($map[$key], $row[$map[$key]]) ? sanitize_text_field($row[$map[$key]]) : '';
        }

        $data['short_description'] = isset($map['short_description'], $row[$map['short_description']]) ? wp_kses_post($row[$map['short_description']]) : '';
        $data['description'] = isset($map['description'], $row[$map['description']]) ? wp_kses_post($row[$map['description']]) : '';

        return $data;
    }

    private static function get_or_create_product_categories($categories)
    {
        $ids = array();
        $names = array_filter(array_map('trim', explode('|', $categories)));

        foreach ($names as $name) {
            $term = term_exists($name, 'product_cat');
            if (!$term) {
                $term = wp_insert_term($name, 'product_cat');
            }

            if (!is_wp_error($term)) {
                $ids[] = (int) $term['term_id'];
            }
        }

        return $ids;
    }

    private static function attach_remote_image($product_id, $image_url)
    {
        if (!$image_url || !function_exists('media_sideload_image')) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        if (!$image_url) {
            return;
        }

        $attachment_id = media_sideload_image($image_url, $product_id, null, 'id');
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($product_id, $attachment_id);
        }
    }

    private static function redirect_excel_page($message)
    {
        wp_safe_redirect(add_query_arg(
            array(
                'page' => 'lzon-excel',
                'lzon_message' => rawurlencode($message),
            ),
            admin_url('admin.php')
        ));
        exit;
    }

    public static function init_payment_gateway()
    {
        if (!class_exists('WooCommerce') || !class_exists('WC_Payment_Gateway')) {
            return;
        }

        lzon_project_tools_register_vnpay_gateway_class();
        add_filter('woocommerce_payment_gateways', array(__CLASS__, 'add_vnpay_gateway'));
    }

    public static function add_vnpay_gateway($gateways)
    {
        $gateways[] = 'LZON_VNPay_Gateway';
        $gateways[] = 'LZON_QR_Demo_Gateway';
        return $gateways;
    }
}

function lzon_project_tools_register_vnpay_gateway_class()
{
    if (class_exists('LZON_VNPay_Gateway') || !class_exists('WC_Payment_Gateway')) {
        return;
    }

class LZON_VNPay_Gateway extends WC_Payment_Gateway
{
            public function __construct()
            {
                $this->id = 'lzon_vnpay';
                $this->method_title = 'VNPay Sandbox';
                $this->method_description = 'Redirect customers to VNPay sandbox for real payment-flow testing.';
                $this->has_fields = false;
                $this->supports = array('products');

                $this->init_form_fields();
                $this->init_settings();

                $this->title = $this->get_option('title');
                $this->description = $this->get_option('description');
                $this->enabled = $this->get_option('enabled');
                $this->tmn_code = $this->get_option('tmn_code');
                $this->hash_secret = $this->get_option('hash_secret');
                $this->payment_url = $this->get_option('payment_url');

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_api_lzon_vnpay_return', array($this, 'handle_return'));
            }

            public function init_form_fields()
            {
                $this->form_fields = array(
                    'enabled' => array(
                        'title' => 'Enable/Disable',
                        'type' => 'checkbox',
                        'label' => 'Enable VNPay sandbox payment',
                        'default' => 'no',
                    ),
                    'title' => array(
                        'title' => 'Title',
                        'type' => 'text',
                        'default' => 'Thanh toan VNPay',
                    ),
                    'description' => array(
                        'title' => 'Description',
                        'type' => 'textarea',
                        'default' => 'Thanh toan truc tuyen qua VNPay sandbox.',
                    ),
                    'tmn_code' => array(
                        'title' => 'vnp_TmnCode',
                        'type' => 'text',
                        'description' => 'Merchant terminal code from VNPay sandbox.',
                        'default' => '',
                    ),
                    'hash_secret' => array(
                        'title' => 'vnp_HashSecret',
                        'type' => 'password',
                        'description' => 'Secret key from VNPay sandbox.',
                        'default' => '',
                    ),
                    'payment_url' => array(
                        'title' => 'Payment URL',
                        'type' => 'text',
                        'default' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
                    ),
                );
            }

            public function process_payment($order_id)
            {
                $order = wc_get_order($order_id);

                if (!$order) {
                    wc_add_notice('Order not found.', 'error');
                    return array('result' => 'failure');
                }

                if (!$this->tmn_code || !$this->hash_secret) {
                    wc_add_notice('VNPay is not configured. Please contact the shop owner.', 'error');
                    return array('result' => 'failure');
                }

                $redirect_url = $this->build_payment_url($order);
                $order->update_status('pending', 'Customer redirected to VNPay sandbox.');

                return array(
                    'result' => 'success',
                    'redirect' => $redirect_url,
                );
            }

            private function build_payment_url($order)
            {
                $return_url = add_query_arg('wc-api', 'lzon_vnpay_return', home_url('/'));
                $amount = (int) round((float) $order->get_total() * 100);

                $params = array(
                    'vnp_Version' => '2.1.0',
                    'vnp_Command' => 'pay',
                    'vnp_TmnCode' => $this->tmn_code,
                    'vnp_Amount' => $amount,
                    'vnp_CurrCode' => 'VND',
                    'vnp_TxnRef' => (string) $order->get_id(),
                    'vnp_OrderInfo' => 'Thanh toan don hang #' . $order->get_id(),
                    'vnp_OrderType' => 'other',
                    'vnp_Locale' => 'vn',
                    'vnp_ReturnUrl' => $return_url,
                    'vnp_IpAddr' => self::get_customer_ip(),
                    'vnp_CreateDate' => current_time('YmdHis'),
                );

                ksort($params);

                $hash_data = array();
                $query = array();
                foreach ($params as $key => $value) {
                    $hash_data[] = urlencode($key) . '=' . urlencode($value);
                    $query[] = urlencode($key) . '=' . urlencode($value);
                }

                $secure_hash = hash_hmac('sha512', implode('&', $hash_data), $this->hash_secret);
                $query[] = 'vnp_SecureHash=' . $secure_hash;

                return $this->payment_url . '?' . implode('&', $query);
            }

            public function handle_return()
            {
                $params = array_map('sanitize_text_field', wp_unslash($_GET));
                $order_id = isset($params['vnp_TxnRef']) ? absint($params['vnp_TxnRef']) : 0;
                $order = $order_id ? wc_get_order($order_id) : false;

                if (!$order) {
                    wp_safe_redirect(wc_get_cart_url());
                    exit;
                }

                $is_valid = $this->validate_return_hash($params);
                $response_code = isset($params['vnp_ResponseCode']) ? $params['vnp_ResponseCode'] : '';

                if ($is_valid && $response_code === '00') {
                    $order->payment_complete(isset($params['vnp_TransactionNo']) ? $params['vnp_TransactionNo'] : '');
                    $order->add_order_note('VNPay sandbox payment completed.');
                    WC()->cart->empty_cart();
                    wp_safe_redirect($this->get_return_url($order));
                    exit;
                }

                $order->update_status('failed', 'VNPay sandbox payment failed or invalid signature.');
                wc_add_notice('VNPay payment failed. Please try again.', 'error');
                wp_safe_redirect(wc_get_checkout_url());
                exit;
            }

            private function validate_return_hash($params)
            {
                if (empty($params['vnp_SecureHash']) || !$this->hash_secret) {
                    return false;
                }

                $secure_hash = $params['vnp_SecureHash'];
                unset($params['vnp_SecureHash'], $params['vnp_SecureHashType'], $params['wc-api']);
                ksort($params);

                $hash_data = array();
                foreach ($params as $key => $value) {
                    if (strpos($key, 'vnp_') === 0 && $value !== '') {
                        $hash_data[] = urlencode($key) . '=' . urlencode($value);
                    }
                }

                $calculated = hash_hmac('sha512', implode('&', $hash_data), $this->hash_secret);
                return hash_equals($calculated, $secure_hash);
            }

            private static function get_customer_ip()
            {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    return sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
                }

                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ips = explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])));
                    return trim($ips[0]);
                }

                return !empty($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '127.0.0.1';
    }
}

class LZON_QR_Demo_Gateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'lzon_qr_demo';
        $this->method_title = 'QR Demo';
        $this->method_description = 'Simulated QR payment for project demos. No real money is charged.';
        $this->has_fields = false;
        $this->supports = array('products');

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_lzon_qr_demo_confirm', array($this, 'confirm_payment'));
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Enable/Disable',
                'type' => 'checkbox',
                'label' => 'Enable QR demo payment',
                'default' => 'yes',
            ),
            'title' => array(
                'title' => 'Title',
                'type' => 'text',
                'default' => 'Thanh toan QR Demo',
            ),
            'description' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'default' => 'Quet ma QR mo phong va bam xac nhan de hoan tat don hang demo.',
            ),
        );
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            wc_add_notice('Order not found.', 'error');
            return array('result' => 'failure');
        }

        $order->update_status('on-hold', 'Awaiting QR demo confirmation.');

        return array(
            'result' => 'success',
            'redirect' => add_query_arg(
                array(
                    'lzon_qr_demo' => '1',
                    'order_id' => $order->get_id(),
                    'key' => $order->get_order_key(),
                ),
                home_url('/')
            ),
        );
    }

    public function confirm_payment()
    {
        $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
        $key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';
        $order = $order_id ? wc_get_order($order_id) : false;

        if (!$order || $order->get_order_key() !== $key) {
            wp_die('Invalid demo payment confirmation.');
        }

        if (!$order->is_paid()) {
            $order->payment_complete('QR-DEMO-' . $order->get_id());
            $order->add_order_note('QR demo payment confirmed manually.');
            WC()->cart->empty_cart();
        }

        wp_safe_redirect($this->get_return_url($order));
        exit;
    }
}

add_action('template_redirect', 'lzon_project_tools_render_qr_demo_page');
function lzon_project_tools_render_qr_demo_page()
{
    if (empty($_GET['lzon_qr_demo'])) {
        return;
    }

    if (!class_exists('WooCommerce')) {
        return;
    }

    $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
    $key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';
    $order = $order_id ? wc_get_order($order_id) : false;

    if (!$order || $order->get_order_key() !== $key) {
        wp_die('Invalid QR demo order.');
    }

    $confirm_url = add_query_arg(
        array(
            'wc-api' => 'lzon_qr_demo_confirm',
            'order_id' => $order->get_id(),
            'key' => $order->get_order_key(),
        ),
        home_url('/')
    );

    $qr_text = 'SHOP LZON DEMO|' . $order->get_id() . '|' . $order->get_total() . '|' . get_woocommerce_currency();
    $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' . rawurlencode($qr_text);

    status_header(200);
    nocache_headers();
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Thanh toan QR Demo - Shop Lzon</title>
        <?php wp_head(); ?>
        <style>
            body.lzon-qr-demo-page {
                background: #f4f6f8;
                color: #121820;
                font-family: Arial, sans-serif;
                margin: 0;
            }
            .lzon-qr-demo-wrap {
                align-items: center;
                display: flex;
                justify-content: center;
                min-height: 100vh;
                padding: 28px;
            }
            .lzon-qr-demo-card {
                background: #fff;
                border: 1px solid #e6e9ee;
                border-radius: 10px;
                box-shadow: 0 18px 48px rgba(18, 24, 32, 0.12);
                max-width: 460px;
                padding: 30px;
                text-align: center;
                width: 100%;
            }
            .lzon-qr-demo-card h1 {
                font-size: 28px;
                margin: 0 0 10px;
            }
            .lzon-qr-demo-card p {
                color: #667085;
                line-height: 1.6;
                margin: 0 0 20px;
            }
            .lzon-qr-demo-qr {
                background: #f8fafc;
                border-radius: 10px;
                display: inline-block;
                margin: 8px 0 20px;
                padding: 16px;
            }
            .lzon-qr-demo-qr img {
                display: block;
                height: 260px;
                width: 260px;
            }
            .lzon-qr-demo-info {
                border-top: 1px solid #e6e9ee;
                margin-top: 8px;
                padding-top: 18px;
                text-align: left;
            }
            .lzon-qr-demo-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
            }
            .lzon-qr-demo-row strong {
                color: #121820;
            }
            .lzon-qr-demo-actions {
                display: grid;
                gap: 10px;
                grid-template-columns: 1fr;
                margin-top: 22px;
            }
            .lzon-qr-demo-button {
                background: #c8102e;
                border-radius: 8px;
                color: #fff;
                display: block;
                font-weight: 800;
                padding: 14px 18px;
                text-decoration: none;
            }
            .lzon-qr-demo-button.secondary {
                background: #fff;
                border: 1px solid #e6e9ee;
                color: #121820;
            }
        </style>
    </head>
    <body <?php body_class('lzon-qr-demo-page'); ?>>
        <main class="lzon-qr-demo-wrap">
            <section class="lzon-qr-demo-card">
                <h1>Thanh toan QR Demo</h1>
                <p>Day la QR mo phong de demo quy trinh thanh toan. Khong tru tien that.</p>
                <div class="lzon-qr-demo-qr">
                    <img src="<?php echo esc_url($qr_url); ?>" alt="QR demo payment">
                </div>
                <div class="lzon-qr-demo-info">
                    <div class="lzon-qr-demo-row">
                        <span>Ma don hang</span>
                        <strong>#<?php echo esc_html($order->get_id()); ?></strong>
                    </div>
                    <div class="lzon-qr-demo-row">
                        <span>So tien</span>
                        <strong><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong>
                    </div>
                    <div class="lzon-qr-demo-row">
                        <span>Nha cung cap</span>
                        <strong>Shop Lzon Demo</strong>
                    </div>
                </div>
                <div class="lzon-qr-demo-actions">
                    <a class="lzon-qr-demo-button" href="<?php echo esc_url($confirm_url); ?>">Xac nhan da thanh toan</a>
                    <a class="lzon-qr-demo-button secondary" href="<?php echo esc_url(wc_get_checkout_url()); ?>">Quay lai thanh toan</a>
                </div>
            </section>
        </main>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
    exit;
}

}

LZON_Project_Tools::init();
