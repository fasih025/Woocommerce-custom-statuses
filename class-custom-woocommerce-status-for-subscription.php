<?php

class Custom_Woocommerce_Status_For_Subscription
{

    /**
     * Initialize Hooks.
     *
     * @access public
     */
    public function run()
    {
        // a woocommerce function to register new woocommerce status
        add_action('init', array($this, 'register_dormant_order_statuses'), 100);
		add_action('init', array($this, 'register_inactive_order_statuses'), 101);

        /**
         * Following hooks are from woocommerce. You can find its implementation for on-hold status
         * in file `woocommerce-subscriptions/includes/class-wc-subscriptions-manager.php`
         */
        add_filter('wc_order_statuses', array($this, 'dormant_wc_order_statuses'), 100, 1);
		add_filter('wc_order_statuses', array($this, 'inactive_wc_order_statuses'), 101, 1);
        add_action('woocommerce_order_status_dormant', array($this, 'put_subscription_on_dormant_for_order'), 100);
		add_action('woocommerce_order_status_inactive', array($this, 'put_subscription_on_inactive_for_order'), 101);
    }

    /**
     * Registered new woocommerce status for `Dormant`.
     *
     * @access public
     *
     */
    public function register_dormant_order_statuses()
    {
        register_post_status('wc-dormant', array(
            'label' => _x('Dormant', 'Order status', 'custom-wcs-status-texts'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Dormant <span class="count">(%s)</span>', 'Dormant<span class="count">(%s)</span>', 'woocommerce'),
        ));
    }
	public function register_inactive_order_statuses()
    {
        register_post_status('wc-inactive', array(
            'label' => _x('InActive', 'Order status', 'custom-wcs-status-texts'),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('InActive <span class="count">(%s)</span>', 'InActive<span class="count">(%s)</span>', 'woocommerce'),
        ));
    }

    /**
     * Add new status `Dormant` to $order_statuses array.
     *
     * @access public
     *
     * @param array $order_statuses current order statuses array.
     * @return array $order_statuses with the new status added to it.
     */
    public function dormant_wc_order_statuses($order_statuses)
    {
        $order_statuses['wc-dormant'] = _x('Dormant', 'Order status', 'custom-wcs-status-texts');
        return $order_statuses;
    }
	
	public function inactive_wc_order_statuses($order_statuses)
    {
        $order_statuses['wc-inactive'] = _x('InActive', 'Order status', 'custom-wcs-status-texts');
        return $order_statuses;
    }

    /**
     * Change status of all the subscription in an order to `Dormant` when order status is changed to `Dormant`.
     *
     * @param object $order woocommerce order.
     * @access public
     */
    public function put_subscription_on_dormant_for_order($order)
    {
        $subscriptions = wcs_get_subscriptions_for_order($order, array('order_type' => 'parent'));

        if (!empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                try {
                    if (!$subscription->has_status(wcs_get_subscription_ended_statuses())) {
                        $subscription->update_status('dormant');
                    }
                } catch (Exception $e) {
                    // translators: $1: order number, $2: error message
                    $subscription->add_order_note(sprintf(__('Failed to update subscription status after order #%1$s was put to dormant: %2$s', 'woocommerce-subscriptions'), is_object($order) ? $order->get_order_number() : $order, $e->getMessage()));
                }
            }

            // Added a new action the same way subscription plugin has added for on-hold
            do_action('subscriptions_put_to_dormant_for_order', $order);
        }
    }
	public function put_subscription_on_inactive_for_order($order)
    {
        $subscriptions = wcs_get_subscriptions_for_order($order, array('order_type' => 'parent'));

        if (!empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                try {
                    if (!$subscription->has_status(wcs_get_subscription_ended_statuses())) {
                        $subscription->update_status('inactive');
                    }
                } catch (Exception $e) {
                    // translators: $1: order number, $2: error message
                    $subscription->add_order_note(sprintf(__('Failed to update subscription status after order #%1$s was put to inactive: %2$s', 'woocommerce-subscriptions'), is_object($order) ? $order->get_order_number() : $order, $e->getMessage()));
                }
            }

            // Added a new action the same way subscription plugin has added for on-hold
            do_action('subscriptions_put_to_inactive_for_order', $order);
        }
    }
}
