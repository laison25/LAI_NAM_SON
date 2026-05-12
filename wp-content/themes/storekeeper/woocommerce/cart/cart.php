<?php
/**
 * Custom cart layout.
 *
 * @package Storekeeper
 * @version 10.1.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<form class="woocommerce-cart-form lns-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>

    <div class="lns-cart-topbar">
        <h2><?php echo esc_html__('Giỏ hàng', 'storekeeper'); ?></h2>
        <a class="lns-continue-shopping" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
            <?php echo esc_html__('Tiếp tục mua', 'storekeeper'); ?>
        </a>
    </div>

    <div class="lns-cart-card lns-cart-items-card">
        <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents lns-cart-table" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" class="product-name"><?php echo esc_html__('Sản phẩm', 'storekeeper'); ?></th>
                    <th scope="col" class="product-price"><?php echo esc_html__('Đơn giá', 'storekeeper'); ?></th>
                    <th scope="col" class="product-quantity"><?php echo esc_html__('Số lượng', 'storekeeper'); ?></th>
                    <th scope="col" class="product-subtotal"><?php echo esc_html__('Thành tiền', 'storekeeper'); ?></th>
                    <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'woocommerce'); ?></span></th>
                </tr>
            </thead>
            <tbody>
                <?php do_action('woocommerce_before_cart_contents'); ?>

                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        ?>
                        <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                            <td scope="row" role="rowheader" class="product-name" data-title="<?php echo esc_attr__('Sản phẩm', 'storekeeper'); ?>">
                                <div class="lns-cart-product">
                                    <div class="lns-cart-thumb">
                                        <?php
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                        if (!$product_permalink) {
                                            echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        } else {
                                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        }
                                        ?>
                                    </div>
                                    <div class="lns-cart-product-copy">
                                        <?php
                                        if (!$product_permalink) {
                                            echo wp_kses_post($product_name . '&nbsp;');
                                        } else {
                                            echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                        }

                                        do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
                                        echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                                        if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                            echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                        }
                                        ?>
                                    </div>
                                </div>
                            </td>

                            <td class="product-price" data-title="<?php echo esc_attr__('Đơn giá', 'storekeeper'); ?>">
                                <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </td>

                            <td class="product-quantity" data-title="<?php echo esc_attr__('Số lượng', 'storekeeper'); ?>">
                                <?php
                                if ($_product->is_sold_individually()) {
                                    $min_quantity = 1;
                                    $max_quantity = 1;
                                } else {
                                    $min_quantity = 0;
                                    $max_quantity = $_product->get_max_purchase_quantity();
                                }

                                $product_quantity = woocommerce_quantity_input(
                                    array(
                                        'input_name' => "cart[{$cart_item_key}][qty]",
                                        'input_value' => $cart_item['quantity'],
                                        'max_value' => $max_quantity,
                                        'min_value' => $min_quantity,
                                        'product_name' => $product_name,
                                    ),
                                    $_product,
                                    false
                                );

                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                ?>
                            </td>

                            <td class="product-subtotal" data-title="<?php echo esc_attr__('Thành tiền', 'storekeeper'); ?>">
                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </td>

                            <td class="product-remove">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a role="button" href="%s" class="remove lns-remove-link" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                                        esc_attr($product_id),
                                        esc_attr($_product->get_sku()),
                                        esc_html__('Xóa', 'storekeeper')
                                    ),
                                    $cart_item_key
                                ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>

                <?php do_action('woocommerce_cart_contents'); ?>

                <tr class="lns-cart-table-actions">
                    <td colspan="5" class="actions">
                        <button type="submit" class="button lns-update-cart<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                            <?php echo esc_html__('Cập nhật giỏ hàng', 'storekeeper'); ?>
                        </button>

                        <div class="lns-inline-total">
                            <span><?php echo esc_html__('Tổng cộng:', 'storekeeper'); ?></span>
                            <strong><?php wc_cart_totals_order_total_html(); ?></strong>
                            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button lns-inline-checkout">
                                <?php echo esc_html__('Tiến hành thanh toán', 'storekeeper'); ?>
                            </a>
                        </div>

                        <?php do_action('woocommerce_cart_actions'); ?>
                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                    </td>
                </tr>

                <?php do_action('woocommerce_after_cart_contents'); ?>
            </tbody>
        </table>
    </div>

    <div class="lns-cart-bottom-grid">
        <?php if (wc_coupons_enabled()) : ?>
            <section class="lns-cart-card lns-coupon-card">
                <h3><?php echo esc_html__('Mã giảm giá', 'storekeeper'); ?></h3>
                <p><?php echo esc_html__('Thử mã: LZON10, FIGURE500, FREESHIP', 'storekeeper'); ?></p>
                <div class="coupon">
                    <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
                    <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php echo esc_attr__('Nhập mã giảm giá', 'storekeeper'); ?>" />
                    <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
                        <?php echo esc_html__('Áp dụng', 'storekeeper'); ?>
                    </button>
                    <?php do_action('woocommerce_cart_coupon'); ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="lns-cart-card lns-summary-card">
            <h3><?php echo esc_html__('Tóm tắt thanh toán', 'storekeeper'); ?></h3>
            <div class="lns-summary-row">
                <span><?php echo esc_html__('Tạm tính', 'storekeeper'); ?></span>
                <strong><?php wc_cart_totals_subtotal_html(); ?></strong>
            </div>
            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                <div class="lns-summary-row">
                    <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
                    <strong><?php wc_cart_totals_coupon_html($coupon); ?></strong>
                </div>
            <?php endforeach; ?>
            <div class="lns-summary-row lns-summary-total">
                <span><?php echo esc_html__('Cần thanh toán', 'storekeeper'); ?></span>
                <strong><?php wc_cart_totals_order_total_html(); ?></strong>
            </div>
        </section>
    </div>

    <?php do_action('woocommerce_after_cart_table'); ?>
</form>

<?php
do_action('woocommerce_before_cart_collaterals');
do_action('woocommerce_after_cart');
