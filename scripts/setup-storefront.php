<?php
/**
 * Build the Spanish storefront pages, navigation, and homepage.
 * Run after WooCommerce and the catalog are installed:
 * wp eval-file /lab/setup-storefront.php
 */
if (! defined('ABSPATH') || ! class_exists('WooCommerce')) {
    throw new RuntimeException('Run this file with WP-CLI after activating WooCommerce.');
}

$host = wp_parse_url(home_url(), PHP_URL_HOST);
if (! in_array($host, ['localhost', '127.0.0.1'], true)) {
    throw new RuntimeException('This script is restricted to a local WordPress site.');
}

$theme = wp_get_theme('ciclo-finca');
if (! $theme->exists()) {
    throw new RuntimeException('The Ciclo Finca child theme is not mounted in WordPress.');
}

switch_theme('ciclo-finca');
update_option('blogname', 'Ciclo Finca');
update_option('blogdescription', 'Bicicletas y esenciales para disfrutar cada ruta.');
update_option('permalink_structure', '/%postname%/');

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$heroImageId = cicloFincaEnsureImage(
    '/site-assets/rodada-costa-rica.webp',
    'Ciclista en una ruta de montaña de Costa Rica',
    'rodada-costa-rica.webp'
);
$heroImageUrl = wp_get_attachment_url($heroImageId);
if (! $heroImageUrl) {
    throw new RuntimeException('Could not resolve the storefront hero image URL.');
}

$shopUrl = wc_get_page_permalink('shop');
$systemPageTitles = [
    'shop' => 'Tienda',
    'cart' => 'Carrito',
    'checkout' => 'Finalizar compra',
    'myaccount' => 'Mi cuenta',
];
$systemPageContent = [
    'cart' => '[woocommerce_cart]',
    'checkout' => '[woocommerce_checkout]',
];
foreach ($systemPageTitles as $pageKey => $title) {
    $pageId = wc_get_page_id($pageKey);
    if ($pageId > 0) {
        $pageData = [
            'ID' => $pageId,
            'post_title' => $title,
        ];
        if (isset($systemPageContent[$pageKey])) {
            $pageData['post_content'] = $systemPageContent[$pageKey];
        }
        wp_update_post($pageData);
    }
}

$aboutId = cicloFincaUpsertPage(
    'sobre-ciclo-finca',
    'Nuestra historia',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">COSTA RICA · RUTAS QUE NOS MUEVEN</p>'
        . '<p>Ciclo Finca nace del gusto por salir a rodar, descubrir caminos nuevos y disfrutar el paisaje sin apuro. Reunimos bicicletas y esenciales para acompañar cada salida, desde la vuelta de todos los días hasta ese desvío que termina siendo la mejor parte del recorrido.</p>'
        . '<h2>El camino marca el ritmo</h2>'
        . '<p>Nos inspira la mezcla de caminos verdes, pueblos y montañas de Costa Rica. Por eso nuestra identidad se siente cercana a la naturaleza y lleva la energía de quien siempre está listo para arrancar.</p>'
        . '<h2>Equipo para rodar a tu manera</h2>'
        . '<p>Explora bicicletas, accesorios, nutrición y ropa en un solo lugar. Cada ficha reúne la información disponible para que puedas comparar y elegir con calma.</p>'
        . '<p>Los precios están expresados en colones costarricenses. Al finalizar el pedido, el pago es manual: el sitio no solicita datos de tarjeta ni inicia un cobro en línea.</p>'
        . '<p><a href="' . esc_url($shopUrl) . '">Explorar el catálogo</a> · <a href="' . esc_url(home_url('/preguntas-frecuentes/')) . '">Ver preguntas frecuentes</a></p>'
        . '</div>',
    'Bicicletas y esenciales para disfrutar cada ruta de Costa Rica.'
);

$guideId = cicloFincaUpsertPage(
    'como-comprar',
    'Cómo comprar',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">GUÍA RÁPIDA</p>'
        . '<p>Encuentra lo que necesitas, revisa tu pedido y elige la forma de pago disponible al finalizar.</p>'
        . '<h2>1. Elige productos</h2>'
        . '<p>Explora las categorías y abre una ficha para ver su descripción, precio en colones y disponibilidad. Añade al carrito los artículos que quieras pedir.</p>'
        . '<h2>2. Revisa el carrito</h2>'
        . '<p>Verifica los artículos, ajusta las cantidades y revisa el total antes de continuar al checkout.</p>'
        . '<h2>3. Completa tus datos</h2>'
        . '<p>Indica la información necesaria para registrar el pedido y elige “Recogida local” si aparece disponible para tu dirección.</p>'
        . '<h2>4. Elige pago manual</h2>'
        . '<p>El pedido queda pendiente mientras se confirma el pago. No ingreses datos de tarjeta ni compartas información bancaria en este sitio; el checkout no procesa cobros en línea.</p>'
        . '<p><a href="' . esc_url($shopUrl) . '">Ir a la tienda</a></p>'
        . '</div>',
    'Pasos para elegir tus productos y finalizar un pedido.'
);

$faqId = cicloFincaUpsertPage(
    'preguntas-frecuentes',
    'Preguntas frecuentes',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">INFORMACIÓN DE LA TIENDA</p>'
        . '<h2>¿En qué moneda están los precios?</h2>'
        . '<p>Todos los precios aparecen en colones costarricenses (CRC).</p>'
        . '<h2>¿Cómo puedo pagar?</h2>'
        . '<p>El checkout ofrece pago manual. No solicita datos de tarjeta ni procesa pagos en línea; el pedido queda pendiente mientras se confirma el pago.</p>'
        . '<h2>¿Puedo elegir recogida?</h2>'
        . '<p>Si la opción aparece para tu dirección, puedes seleccionar “Recogida local” durante el checkout. El costo aparece antes de confirmar el pedido.</p>'
        . '<h2>¿Cómo sé si un producto está disponible?</h2>'
        . '<p>La ficha de cada producto indica su disponibilidad. Si no permite añadirlo al carrito, no está disponible para ese pedido.</p>'
        . '<h2>¿Puedo cambiar o cancelar un pedido?</h2>'
        . '<p>Los cambios dependen del estado del pedido. Revisa la información de confirmación que recibas para conocer las opciones disponibles.</p>'
        . '<h2>¿Qué pasa con mis datos?</h2>'
        . '<p>Los datos que ingresas al finalizar el pedido se utilizan para registrarlo y gestionarlo en la tienda. Consulta la <a href="' . esc_url(get_privacy_policy_url()) . '">política de privacidad</a> para más información.</p>'
        . '<p><a href="' . esc_url(get_permalink($guideId)) . '">Leer la guía de compra</a></p>'
        . '</div>',
    'Respuestas sobre productos, pagos, recogida y pedidos.'
);

cicloFincaUpsertPage(
    'privacy-policy',
    'Privacidad',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">TUS DATOS EN CICLO FINCA</p>'
        . '<p>Cuando finalizas un pedido, la tienda solicita los datos necesarios para registrarlo y gestionarlo, como tu nombre, correo electrónico y dirección de facturación o recogida.</p>'
        . '<h2>Pedidos</h2>'
        . '<p>Los datos del pedido se guardan en WordPress y WooCommerce para mostrar los artículos, el total y el estado del pedido a quien lo gestiona.</p>'
        . '<h2>Pagos</h2>'
        . '<p>El método disponible es manual. Este checkout no solicita datos de tarjeta ni envía información bancaria a una pasarela de pago.</p>'
        . '<h2>Carrito y cuenta</h2>'
        . '<p>El carrito necesita guardar temporalmente los artículos seleccionados para que puedas continuar con tu pedido. Si creas una cuenta, la tienda conserva los datos que ingreses en ella.</p>'
        . '<h2>Actualización de datos</h2>'
        . '<p>Para consultar o corregir los datos asociados a un pedido, utiliza la información de contacto facilitada junto con la confirmación del pedido.</p>'
        . '<p><a href="' . esc_url(get_permalink($faqId)) . '">Leer las preguntas frecuentes</a></p>'
        . '</div>',
    'Cómo se utilizan los datos de cuenta, carrito y pedidos en Ciclo Finca.'
);

$homeId = cicloFincaUpsertPage(
    'inicio',
    'Inicio',
    '',
    'Bicicletas, accesorios y esenciales para disfrutar cada ruta en Costa Rica.'
);

$homeContent = '<div class="ciclo-home">'
    . '<section class="ciclo-hero" aria-labelledby="ciclo-home-heading">'
    . '<img class="ciclo-hero__image" src="' . esc_url($heroImageUrl) . '" alt="Ciclista recorre un sendero de montaña entre colinas verdes de Costa Rica" fetchpriority="high">'
    . '<div class="ciclo-hero__overlay" aria-hidden="true"></div>'
    . '<div class="ciclo-hero__content">'
    . '<p class="ciclo-eyebrow">CICLO FINCA · COSTA RICA</p>'
    . '<h1 id="ciclo-home-heading">Tu próxima ruta empieza aquí.</h1>'
    . '<p class="ciclo-hero__intro">Bicicletas y esenciales para moverte con libertad. Encuentra tu equipo, prepara el próximo recorrido y sal a rodar.</p>'
    . '<div class="ciclo-hero__actions"><a href="' . esc_url($shopUrl) . '">Explorar la tienda</a><a href="' . esc_url(get_permalink($guideId)) . '">Cómo comprar</a></div>'
    . '</div></section>'
    . '<div class="ciclo-trust-strip" aria-label="Información de la tienda">'
    . '<div><span class="ciclo-trust-strip__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 7.5h16v10H4zM4 10.5h16M7 14.5h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span><div><strong>Precios en colones</strong><span>Moneda CRC · Costa Rica</span></div></div>'
    . '<div><span class="ciclo-trust-strip__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7"/><path d="m8 8 8 8m0-8-8 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span><div><strong>Pago manual</strong><span>Sin datos de tarjeta en el checkout</span></div></div>'
    . '<div><span class="ciclo-trust-strip__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg></span><div><strong>Recogida local</strong><span>Opción disponible en el checkout</span></div></div>'
    . '</div></div>';

$homeBlocks = '<!-- wp:html -->' . $homeContent . '<!-- /wp:html -->';
$homeBlocks .= '<!-- wp:group {"className":"ciclo-home ciclo-category-list"} --><div class="wp-block-group ciclo-home ciclo-category-list">';
$homeBlocks .= '<!-- wp:html --><div class="ciclo-section__heading"><div><p class="ciclo-eyebrow">ENCUENTRA TU RITMO</p><h2>Todo para tu próxima rodada</h2></div><p>Explora bicicletas, accesorios, nutrición y ropa para distintas formas de vivir el ciclismo.</p></div><!-- /wp:html -->';
$homeBlocks .= '<!-- wp:shortcode -->[product_categories number="4" columns="4" orderby="name" order="ASC" hide_empty="0"]<!-- /wp:shortcode -->';
$homeBlocks .= '</div><!-- /wp:group -->';

$featuredSkus = [
    'CIC-BIK-001',
    'CIC-BIK-002',
    'CIC-ACC-001',
    'CIC-ACC-003',
    'CIC-ACC-004',
    'CIC-ACC-006',
    'CIC-NUT-001',
    'CIC-ROP-001',
];
$featuredIds = [];
foreach ($featuredSkus as $sku) {
    $productId = wc_get_product_id_by_sku($sku);
    if ($productId) {
        $featuredIds[] = (int) $productId;
    }
}
if (count($featuredIds) !== count($featuredSkus)) {
    throw new RuntimeException('The full demonstration catalog must exist before the storefront is configured.');
}

$homeBlocks .= '<!-- wp:group {"className":"ciclo-home ciclo-section ciclo-products"} --><div class="wp-block-group ciclo-home ciclo-section ciclo-products">';
$homeBlocks .= '<!-- wp:html --><div class="ciclo-section__heading"><div><p class="ciclo-eyebrow">PARA TU PRÓXIMA SALIDA</p><h2>Equipo listo para acompañarte</h2></div><p>Explora bicicletas y esenciales para encontrar lo que mejor se adapta a tu manera de rodar.</p></div><!-- /wp:html -->';
$homeBlocks .= '<!-- wp:shortcode -->[products ids="' . esc_attr(implode(',', $featuredIds)) . '" limit="8" columns="4" orderby="post__in"]<!-- /wp:shortcode -->';
$homeBlocks .= '<!-- wp:html --><p class="ciclo-catalog-link"><a href="' . esc_url($shopUrl) . '">Ver todo el catálogo →</a></p><!-- /wp:html -->';
$homeBlocks .= '</div><!-- /wp:group -->';

$homeBlocks .= '<!-- wp:html -->'
    . '<div class="ciclo-home ciclo-how"><article><span>01</span><h3>Encuentra tu equipo</h3><p>Compara bicicletas y accesorios en las distintas categorías.</p></article>'
    . '<article><span>02</span><h3>Arma tu pedido</h3><p>Revisa el carrito, ajusta cantidades y confirma el total.</p></article>'
    . '<article><span>03</span><h3>Finaliza con calma</h3><p>Elige pago manual. No ingreses datos de tarjeta en el sitio.</p></article></div>'
    . '<aside class="ciclo-home ciclo-callout"><h2>¿Listo para salir?</h2><p>Encuentra todo lo que necesitas para preparar tu próxima rodada.</p><p><a href="' . esc_url($shopUrl) . '">Explorar el catálogo</a> · <a href="' . esc_url(get_permalink($faqId)) . '">Preguntas frecuentes</a></p></aside>'
    . '<!-- /wp:html -->';

wp_update_post([
    'ID' => $homeId,
    'post_content' => $homeBlocks,
    'post_status' => 'publish',
]);
update_option('show_on_front', 'page');
update_option('page_on_front', $homeId);
update_option('page_for_posts', 0);
delete_post_thumbnail($homeId);

$bikeTerm = get_term_by('slug', 'bicicletas', 'product_cat');
$accessoryTerm = get_term_by('slug', 'accesorios', 'product_cat');
if (! $bikeTerm || ! $accessoryTerm) {
    throw new RuntimeException('The seeded product categories are required for the main navigation.');
}

$navigationItems = [
    ['Inicio', home_url('/'), 'page', $homeId],
    ['Tienda', $shopUrl, 'custom', 0],
    ['Bicicletas', get_term_link($bikeTerm), 'custom', 0],
    ['Accesorios', get_term_link($accessoryTerm), 'custom', 0],
    ['Cómo comprar', get_permalink($guideId), 'page', $guideId],
    ['Nuestra historia', get_permalink($aboutId), 'page', $aboutId],
];
$navigationContent = '';
foreach ($navigationItems as [$label, $url, $type, $pageId]) {
    $attributes = [
        'label' => $label,
        'type' => $type,
        'url' => $url,
    ];
    if ($pageId > 0) {
        $attributes['id'] = $pageId;
    }
    $navigationContent .= '<!-- wp:navigation-link ' . wp_json_encode($attributes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ' /-->' . "\n";
}
$navigationPosts = get_posts([
    'post_type' => 'wp_navigation',
    'post_status' => 'any',
    'posts_per_page' => 1,
    'orderby' => 'ID',
    'order' => 'ASC',
]);
if ($navigationPosts) {
    wp_update_post([
        'ID' => $navigationPosts[0]->ID,
        'post_title' => 'Menú principal Ciclo Finca',
        'post_content' => $navigationContent,
        'post_status' => 'publish',
    ]);
} else {
    wp_insert_post([
        'post_type' => 'wp_navigation',
        'post_title' => 'Menú principal Ciclo Finca',
        'post_content' => $navigationContent,
        'post_status' => 'publish',
    ]);
}

foreach (['sample-page'] as $sampleSlug) {
    $samplePage = get_page_by_path($sampleSlug, OBJECT, 'page');
    if ($samplePage) {
        wp_delete_post($samplePage->ID, true);
    }
}
$samplePosts = get_posts([
    'post_type' => 'post',
    'post_status' => 'any',
    'title' => 'Hello world!',
    'numberposts' => 10,
]);
foreach ($samplePosts as $samplePost) {
    wp_delete_post($samplePost->ID, true);
}

flush_rewrite_rules(false);
WP_CLI::success('Ciclo Finca storefront pages, navigation, and homepage are ready.');

function cicloFincaEnsureImage(string $path, string $title, string $assetKey): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_ciclo_finca_asset_key',
        'meta_value' => $assetKey,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);
    if ($existing) {
        return (int) $existing[0];
    }
    if (! is_readable($path)) {
        throw new RuntimeException('Missing storefront image: ' . $path);
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        throw new RuntimeException('Could not read storefront image: ' . $path);
    }
    $upload = wp_upload_bits($assetKey, null, $contents);
    if (! empty($upload['error'])) {
        throw new RuntimeException('Storefront image upload failed: ' . $upload['error']);
    }
    $fileType = wp_check_filetype($upload['file']);
    $attachmentId = wp_insert_attachment([
        'post_mime_type' => $fileType['type'],
        'post_title' => $title,
        'post_content' => '',
        'post_status' => 'inherit',
    ], $upload['file'], 0, true);
    if (is_wp_error($attachmentId)) {
        throw new RuntimeException($attachmentId->get_error_message());
    }

    wp_update_attachment_metadata($attachmentId, wp_generate_attachment_metadata($attachmentId, $upload['file']));
    update_post_meta($attachmentId, '_wp_attachment_image_alt', $title);
    update_post_meta($attachmentId, '_ciclo_finca_asset_key', $assetKey);
    return (int) $attachmentId;
}

function cicloFincaUpsertPage(string $slug, string $title, string $content, string $excerpt = ''): int
{
    $existing = get_page_by_path($slug, OBJECT, 'page');
    $pageData = [
        'post_type' => 'page',
        'post_name' => $slug,
        'post_title' => $title,
        'post_content' => $content,
        'post_excerpt' => $excerpt,
        'post_status' => 'publish',
    ];
    if ($existing) {
        $pageData['ID'] = $existing->ID;
        $pageId = wp_update_post($pageData, true);
    } else {
        $pageId = wp_insert_post($pageData, true);
    }
    if (is_wp_error($pageId)) {
        throw new RuntimeException($pageId->get_error_message());
    }
    return (int) $pageId;
}
