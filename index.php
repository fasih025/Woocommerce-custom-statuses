<?php

/**
 * Plugin Name:       Custom Subscription Statuses
 * Description:       Simple & Custom plugin to add 2 new statuses to CraftCannabis WooCommerce Subscriptions.
 * Version:           1.0.0
 * Author:            HnZSoft
 * Author URI:        https://www.hnzsoft.com
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require plugin_dir_path(__FILE__) . 'class-custom-woocommerce-subscriptions-status.php';
$CWSS = new Custom_Woocommerce_Subscription_Status();
$CWSS->run(); // initiate the status hooks from woocommerce subscription

require plugin_dir_path(__FILE__) . 'class-custom-woocommerce-status-for-subscription.php';
$CWSFS = new Custom_Woocommerce_Status_For_Subscription();
$CWSFS->run(); // initiate the status hooks from woocommerce
