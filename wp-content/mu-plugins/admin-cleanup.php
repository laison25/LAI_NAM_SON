<?php
/**
 * Plugin Name: Admin Cleanup
 * Description: Hides unrelated WordPress admin menus, dashboard boxes, and promotional notices for this store.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'lns_admin_cleanup_menu', 999);
function lns_admin_cleanup_menu()
{
    $menu_pages = array(
        'edit.php',
        'edit-comments.php',
        'edit.php?post_type=elementor_library',
        'edit.php?post_type=bs_templates',
        'edit.php?post_type=elespare_builder',
        'templatespare-main-dashboard',
        'af-companion',
        'blockspare',
        'elespare_dashboard',
        'woocommerce-marketing',
        'wc-admin&path=/analytics/overview',
    );

    foreach ($menu_pages as $menu_page) {
        remove_menu_page($menu_page);
    }

    remove_submenu_page('themes.php', 'storecommerce-details');
    remove_submenu_page('themes.php', 'storekeeper-details');
    remove_submenu_page('woocommerce', 'wc-admin&path=/marketing');
}

add_action('wp_dashboard_setup', 'lns_admin_cleanup_dashboard', 999);
function lns_admin_cleanup_dashboard()
{
    remove_action('welcome_panel', 'wp_welcome_panel');

    $dashboard_boxes = array(
        'dashboard_activity' => 'normal',
        'dashboard_quick_press' => 'side',
        'dashboard_primary' => 'side',
        'dashboard_right_now' => 'normal',
        'dashboard_site_health' => 'normal',
        'woocommerce_dashboard_recent_reviews' => 'normal',
    );

    foreach ($dashboard_boxes as $box_id => $context) {
        remove_meta_box($box_id, 'dashboard', $context);
    }
}

add_action('admin_init', 'lns_admin_cleanup_dismiss_promotional_notices');
function lns_admin_cleanup_dismiss_promotional_notices()
{
    update_option('templatespare_notice_dismissed', 'yes');
    update_option('aft_notice_dismissed', 'yes');
}

add_action('admin_head', 'lns_admin_cleanup_hide_leftover_promotions');
function lns_admin_cleanup_hide_leftover_promotions()
{
    ?>
    <style>
        .templatespare-notice-content-wrapper,
        .aft-notice-content-wrapper,
        .fs-notice,
        .notice[data-dismissible*="templatespare"],
        .notice[data-dismissible*="blockspare"],
        .notice[data-dismissible*="elespare"],
        .notice[data-dismissible*="af-companion"] {
            display: none !important;
        }
    </style>
    <?php
}

add_action('admin_bar_menu', 'lns_admin_cleanup_admin_bar', 999);
function lns_admin_cleanup_admin_bar($wp_admin_bar)
{
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('new-post');
}
