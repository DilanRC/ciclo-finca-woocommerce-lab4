<?php
/**
 * Configure local WooCommerce settings for the EIF4280 lab.
 * Run after installing and activating WooCommerce:
 * wp eval-file /lab/configure-store.php
 */
if (! defined('ABSPATH') || ! class_exists('WC_Shipping_Zones')) {
    throw new RuntimeException('Run this file with WP-CLI after activating WooCommerce.');
}

$host = wp_parse_url(home_url(), PHP_URL_HOST);
if (! in_array($host, ['localhost', '127.0.0.1'], true)) {
    throw new RuntimeException('This script is restricted to a local WordPress site.');
}

update_option('timezone_string', 'America/Costa_Rica');

$storeOptions = [
    'woocommerce_default_country' => 'CR',
    'woocommerce_store_country' => 'CR',
    'woocommerce_currency' => 'CRC',
    'woocommerce_price_num_decimals' => '0',
    'woocommerce_price_thousand_sep' => '.',
    'woocommerce_price_decimal_sep' => ',',
    'woocommerce_calc_taxes' => 'no',
    'woocommerce_enable_guest_checkout' => 'yes',
    'woocommerce_enable_signup_and_login_from_checkout' => 'no',
    'woocommerce_checkout_privacy_policy_text' => 'Tus datos personales se utilizarán para gestionar tu pedido y facilitar tu experiencia en este sitio, de acuerdo con nuestra [privacy_policy].',
    'woocommerce_coming_soon' => 'no',
];

foreach ($storeOptions as $name => $value) {
    update_option($name, $value);
}

$bacs = get_option('woocommerce_bacs_settings', []);
$bacs = is_array($bacs) ? $bacs : [];
$bacs = array_merge($bacs, [
    'enabled' => 'yes',
    'title' => 'Pago manual de prueba',
    'description' => 'Simulación académica. No se realiza ningún cobro ni transferencia.',
    'instructions' => 'Pedido de laboratorio: no realice pagos ni comparta datos bancarios.',
    'account_details' => [],
]);
update_option('woocommerce_bacs_settings', $bacs);

foreach (['cod', 'cheque'] as $gateway) {
    $optionName = 'woocommerce_' . $gateway . '_settings';
    $settings = get_option($optionName, []);
    $settings = is_array($settings) ? $settings : [];
    $settings['enabled'] = 'no';
    update_option($optionName, $settings);
}

$targetZoneName = 'EIF4280 — Costa Rica (recogida local)';
$zone = null;
foreach (WC_Shipping_Zones::get_zones() as $zoneData) {
    if (($zoneData['zone_name'] ?? '') === $targetZoneName) {
        $zone = new WC_Shipping_Zone((int) $zoneData['zone_id']);
        break;
    }
}

if (! $zone) {
    $zone = new WC_Shipping_Zone();
}

$zone->set_zone_name($targetZoneName);
$hasCostaRica = false;
foreach ($zone->get_zone_locations() as $location) {
    if ($location->type === 'country' && $location->code === 'CR') {
        $hasCostaRica = true;
        break;
    }
}
if (! $hasCostaRica) {
    $zone->add_location('CR', 'country');
}

$zoneId = (int) $zone->save();
$zone = new WC_Shipping_Zone($zoneId);

$pickupInstanceId = 0;
foreach ($zone->get_shipping_methods() as $method) {
    if ($method->id === 'local_pickup') {
        $pickupInstanceId = (int) $method->instance_id;
        break;
    }
}

if ($pickupInstanceId === 0) {
    $pickupInstanceId = (int) $zone->add_shipping_method('local_pickup');
}

if ($pickupInstanceId < 1) {
    throw new RuntimeException('WooCommerce could not create the free local-pickup method.');
}

$pickupOption = 'woocommerce_local_pickup_' . $pickupInstanceId . '_settings';
$pickupSettings = get_option($pickupOption, []);
$pickupSettings = is_array($pickupSettings) ? $pickupSettings : [];
$pickupSettings = array_merge($pickupSettings, [
    'enabled' => 'yes',
    'title' => 'Recogida local gratuita (simulada)',
    'tax_status' => 'none',
    'cost' => '0',
]);
update_option($pickupOption, $pickupSettings);

$defaultZone = new WC_Shipping_Zone(0);
foreach ($defaultZone->get_shipping_methods() as $method) {
    if ($method->id === 'local_pickup' && $method->title === 'Recogida local gratuita (simulada)') {
        $defaultZone->delete_shipping_method((int) $method->instance_id);
        delete_option('woocommerce_local_pickup_' . (int) $method->instance_id . '_settings');
    }
}

WP_CLI::success('Tienda configurada para Costa Rica/CRC, pago manual de prueba y recogida gratuita.');
