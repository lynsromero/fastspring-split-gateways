<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('woocommerce_fastspring_settings');
delete_option('woocommerce_fs_advanced_settings');
delete_option('FSSG_WC_FASTSPRING_VERSION');

// Per-gateway options for the split payment methods.
$fs_gateways = array(
    'fastspring_card',
    'fastspring_paypal',
    'fastspring_googlepay',
    'fastspring_amazon',
    'fastspring_applepay',
    'fastspring_wire',
    'fastspring_venmo',
    'fastspring_alipay',
    'fastspring_cashapp',
);

foreach ($fs_gateways as $gateway_id) {
    delete_option('woocommerce_' . $gateway_id . '_settings');
}
