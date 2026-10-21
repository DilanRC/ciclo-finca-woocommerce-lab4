<?php
/**
 * Build the Spanish storefront pages and editable homepage.
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
    throw new RuntimeException('The Ciclo Finca block theme is not mounted in WordPress.');
}

switch_theme('ciclo-finca');
if (in_array(get_option('blogname'), ['', 'WordPress', 'My WordPress Site'], true)) {
    update_option('blogname', 'Ciclo Finca');
}
if ('' === trim((string) get_option('blogdescription'))) {
    update_option('blogdescription', 'Bicicletas y esenciales para disfrutar cada ruta.');
}
if ('' === (string) get_option('permalink_structure')) {
    update_option('permalink_structure', '/%postname%/');
}

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
$accountPageContent = '<!-- wp:ciclo-finca/account -->'
    . '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Mi cuenta</h2><!-- /wp:heading -->'
    . '<!-- wp:paragraph --><p>Accede para revisar tus pedidos y actualizar tus datos de cliente.</p><!-- /wp:paragraph -->'
    . '<!-- /wp:ciclo-finca/account -->';
$systemPageBlockContent = [
    'cart' => '<!-- wp:woocommerce/cart --><div class="wp-block-woocommerce-cart"></div><!-- /wp:woocommerce/cart -->',
    'checkout' => '<!-- wp:woocommerce/checkout --><div class="wp-block-woocommerce-checkout wc-block-checkout is-loading"></div><!-- /wp:woocommerce/checkout -->',
    'myaccount' => $accountPageContent,
];
$legacySystemPageContent = [
    'cart' => [
        '[woocommerce_cart]',
        '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->',
        '<!-- wp:woocommerce/cart /-->',
    ],
    'checkout' => [
        '[woocommerce_checkout]',
        '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->',
        '<!-- wp:woocommerce/checkout /-->',
    ],
    'myaccount' => ['<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->'],
];
foreach ($systemPageTitles as $pageKey => $title) {
    $pageId = wc_get_page_id($pageKey);
    if ($pageId > 0) {
        $systemPage = get_post($pageId);
        if ($systemPage && ('' === trim($systemPage->post_title) || 'Cart' === $systemPage->post_title || 'Checkout' === $systemPage->post_title || 'My account' === $systemPage->post_title)) {
            wp_update_post([
                'ID' => $pageId,
                'post_title' => $title,
            ]);
        }
        $currentContent = $systemPage ? trim($systemPage->post_content) : '';
        if ($systemPage && isset($systemPageBlockContent[$pageKey])
            && ('' === $currentContent || in_array($currentContent, $legacySystemPageContent[$pageKey], true))) {
            wp_update_post([
                'ID' => $pageId,
                'post_content' => $systemPageBlockContent[$pageKey],
            ]);
        }
    }
}

$aboutBlocks = cicloFincaEditorialContent([
    ['type' => 'eyebrow', 'text' => 'COSTA RICA · RUTAS QUE NOS MUEVEN'],
    ['type' => 'paragraph', 'text' => 'Ciclo Finca nace del gusto por salir a rodar, descubrir caminos nuevos y disfrutar el paisaje sin apuro. Reunimos bicicletas y esenciales para acompañar cada salida, desde la vuelta de todos los días hasta ese desvío que termina siendo la mejor parte del recorrido.'],
    ['type' => 'heading', 'text' => 'El camino marca el ritmo'],
    ['type' => 'paragraph', 'text' => 'Nos inspira la mezcla de caminos verdes, pueblos y montañas de Costa Rica. Por eso nuestra identidad se siente cercana a la naturaleza y lleva la energía de quien siempre está listo para arrancar.'],
    ['type' => 'heading', 'text' => 'Equipo para rodar a tu manera'],
    ['type' => 'paragraph', 'text' => 'Explora bicicletas, accesorios, nutrición y ropa en un solo lugar. Cada ficha reúne la información disponible para que puedas comparar y elegir con calma.'],
    ['type' => 'paragraph', 'text' => 'Los precios están expresados en colones costarricenses. Al finalizar el pedido, el pago es manual: el sitio no solicita datos de tarjeta ni inicia un cobro en línea.'],
    ['type' => 'links', 'items' => [
        ['label' => 'Explorar el catálogo', 'url' => $shopUrl],
        ['label' => 'Ver preguntas frecuentes', 'url' => home_url('/preguntas-frecuentes/')],
    ]],
]);
$aboutId = cicloFincaUpsertPage(
    'sobre-ciclo-finca',
    'Nuestra historia',
    $aboutBlocks['content'],
    'Bicicletas y esenciales para disfrutar cada ruta de Costa Rica.',
    $aboutBlocks['legacy']
);

$guideBlocks = cicloFincaEditorialContent([
    ['type' => 'eyebrow', 'text' => 'GUÍA RÁPIDA'],
    ['type' => 'paragraph', 'text' => 'Encuentra lo que necesitas, revisa tu pedido y elige la forma de pago disponible al finalizar.'],
    ['type' => 'heading', 'text' => '1. Elige productos'],
    ['type' => 'paragraph', 'text' => 'Explora las categorías y abre una ficha para ver su descripción, precio en colones y disponibilidad. Añade al carrito los artículos que quieras pedir.'],
    ['type' => 'heading', 'text' => '2. Revisa el carrito'],
    ['type' => 'paragraph', 'text' => 'Verifica los artículos, ajusta las cantidades y revisa el total antes de continuar al checkout.'],
    ['type' => 'heading', 'text' => '3. Completa tus datos'],
    ['type' => 'paragraph', 'text' => 'Indica la información necesaria para registrar el pedido y elige “Recogida local” si aparece disponible para tu dirección.'],
    ['type' => 'heading', 'text' => '4. Elige pago manual'],
    ['type' => 'paragraph', 'text' => 'El pedido queda pendiente mientras se confirma el pago. No ingreses datos de tarjeta ni compartas información bancaria en este sitio; el checkout no procesa cobros en línea.'],
    ['type' => 'links', 'items' => [['label' => 'Ir a la tienda', 'url' => $shopUrl]]],
]);
$guideId = cicloFincaUpsertPage(
    'como-comprar',
    'Cómo comprar',
    $guideBlocks['content'],
    'Pasos para elegir tus productos y finalizar un pedido.',
    $guideBlocks['legacy']
);

$faqBlocks = cicloFincaEditorialContent([
    ['type' => 'eyebrow', 'text' => 'INFORMACIÓN DE LA TIENDA'],
    ['type' => 'heading', 'text' => '¿En qué moneda están los precios?'],
    ['type' => 'paragraph', 'text' => 'Todos los precios aparecen en colones costarricenses (CRC).'],
    ['type' => 'heading', 'text' => '¿Cómo puedo pagar?'],
    ['type' => 'paragraph', 'text' => 'El checkout ofrece pago manual. No solicita datos de tarjeta ni procesa pagos en línea; el pedido queda pendiente mientras se confirma el pago.'],
    ['type' => 'heading', 'text' => '¿Puedo elegir recogida?'],
    ['type' => 'paragraph', 'text' => 'Si la opción aparece para tu dirección, puedes seleccionar “Recogida local” durante el checkout. El costo aparece antes de confirmar el pedido.'],
    ['type' => 'heading', 'text' => '¿Cómo sé si un producto está disponible?'],
    ['type' => 'paragraph', 'text' => 'La ficha de cada producto indica su disponibilidad. Si no permite añadirlo al carrito, no está disponible para ese pedido.'],
    ['type' => 'heading', 'text' => '¿Puedo cambiar o cancelar un pedido?'],
    ['type' => 'paragraph', 'text' => 'Los cambios dependen del estado del pedido. Revisa la información de confirmación que recibas para conocer las opciones disponibles.'],
    ['type' => 'heading', 'text' => '¿Qué pasa con mis datos?'],
    ['type' => 'paragraph-link', 'text' => 'Los datos que ingresas al finalizar el pedido se utilizan para registrarlo y gestionarlo en la tienda. Consulta la ', 'label' => 'política de privacidad', 'url' => get_privacy_policy_url(), 'suffix' => ' para más información.'],
    ['type' => 'links', 'items' => [['label' => 'Leer la guía de compra', 'url' => get_permalink($guideId)]]],
]);
$faqId = cicloFincaUpsertPage(
    'preguntas-frecuentes',
    'Preguntas frecuentes',
    $faqBlocks['content'],
    'Respuestas sobre productos, pagos, recogida y pedidos.',
    $faqBlocks['legacy']
);

$privacyBlocks = cicloFincaEditorialContent([
    ['type' => 'eyebrow', 'text' => 'TUS DATOS EN CICLO FINCA'],
    ['type' => 'paragraph', 'text' => 'Cuando finalizas un pedido, la tienda solicita los datos necesarios para registrarlo y gestionarlo, como tu nombre, correo electrónico y dirección de facturación o recogida.'],
    ['type' => 'heading', 'text' => 'Pedidos'],
    ['type' => 'paragraph', 'text' => 'Los datos del pedido se guardan en WordPress y WooCommerce para mostrar los artículos, el total y el estado del pedido a quien lo gestiona.'],
    ['type' => 'heading', 'text' => 'Pagos'],
    ['type' => 'paragraph', 'text' => 'El método disponible es manual. Este checkout no solicita datos de tarjeta ni envía información bancaria a una pasarela de pago.'],
    ['type' => 'heading', 'text' => 'Carrito y cuenta'],
    ['type' => 'paragraph', 'text' => 'El carrito necesita guardar temporalmente los artículos seleccionados para que puedas continuar con tu pedido. Si creas una cuenta, la tienda conserva los datos que ingreses en ella.'],
    ['type' => 'heading', 'text' => 'Actualización de datos'],
    ['type' => 'paragraph', 'text' => 'Para consultar o corregir los datos asociados a un pedido, utiliza la información de contacto facilitada junto con la confirmación del pedido.'],
    ['type' => 'links', 'items' => [['label' => 'Leer las preguntas frecuentes', 'url' => get_permalink($faqId)]]],
]);
cicloFincaUpsertPage(
    'privacy-policy',
    'Privacidad',
    $privacyBlocks['content'],
    'Cómo se utilizan los datos de cuenta, carrito y pedidos en Ciclo Finca.',
    $privacyBlocks['legacy']
);

$homeId = cicloFincaUpsertPage(
    'inicio',
    'Inicio',
    '',
    'Bicicletas, accesorios y esenciales para disfrutar cada ruta en Costa Rica.'
);

$homeBlocks = '<!-- wp:cover ' . wp_json_encode([
    'url' => $heroImageUrl,
    'id' => $heroImageId,
    'dimRatio' => 42,
    'overlayColor' => 'forest-deep',
    'isUserOverlayColor' => true,
    'contentPosition' => 'center left',
    'align' => 'full',
    'className' => 'ciclo-hero',
    'layout' => ['type' => 'constrained', 'contentSize' => '1320px'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ' -->'
    . '<div class="wp-block-cover alignfull has-custom-content-position is-position-center-left ciclo-hero">'
    . '<img class="wp-block-cover__image-background wp-image-' . (int) $heroImageId . '" alt="" src="' . esc_url($heroImageUrl) . '" data-object-fit="cover"/>'
    . '<span aria-hidden="true" class="wp-block-cover__background has-forest-deep-background-color has-background-dim-40 has-background-dim"></span>'
    . '<div class="wp-block-cover__inner-container">'
    . '<!-- wp:group {"className":"ciclo-hero__content","layout":{"type":"default"}} -->'
    . '<div class="wp-block-group ciclo-hero__content">'
    . '<!-- wp:paragraph {"className":"ciclo-eyebrow"} --><p class="ciclo-eyebrow">CICLO FINCA · COSTA RICA</p><!-- /wp:paragraph -->'
    . '<!-- wp:heading {"level":1,"className":"ciclo-hero__title","fontSize":"display"} --><h1 class="wp-block-heading ciclo-hero__title has-display-font-size">Tu próxima ruta empieza aquí.</h1><!-- /wp:heading -->'
    . '<!-- wp:paragraph {"className":"ciclo-hero__intro"} --><p class="ciclo-hero__intro">Bicicletas y esenciales para moverte con libertad. Encuentra tu equipo, prepara el próximo recorrido y sal a rodar.</p><!-- /wp:paragraph -->'
    . '<!-- wp:buttons {"className":"ciclo-hero__actions"} --><div class="wp-block-buttons ciclo-hero__actions">'
    . '<!-- wp:button {"url":"' . esc_url($shopUrl) . '","className":"is-style-fill"} --><div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="' . esc_url($shopUrl) . '">Explorar la tienda</a></div><!-- /wp:button -->'
    . '<!-- wp:button {"url":"' . esc_url(get_permalink($guideId)) . '","className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_url(get_permalink($guideId)) . '">Cómo comprar</a></div><!-- /wp:button -->'
    . '</div><!-- /wp:buttons -->'
    . '</div><!-- /wp:group -->'
    . '</div></div><!-- /wp:cover -->';

$homeBlocks .= '<!-- wp:columns {"className":"ciclo-trust-strip","align":"wide"} --><div class="wp-block-columns alignwide ciclo-trust-strip">';
$trustItems = [
    ['payment', 'Precios en colones', 'Moneda CRC · Costa Rica'],
    ['route', 'Pago manual', 'Sin datos de tarjeta en el checkout'],
    ['pin', 'Recogida local', 'Opción disponible en el checkout'],
];
foreach ($trustItems as [$icon, $title, $description]) {
    $homeBlocks .= '<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"ciclo-trust-item","layout":{"type":"default"}} -->'
        . '<div class="wp-block-group ciclo-trust-item"><!-- wp:ciclo-finca/icon {"icon":"' . esc_attr($icon) . '","size":24,"iconColor":"#d8f05a"} /-->'
        . '<!-- wp:group {"className":"ciclo-trust-item__copy","layout":{"type":"default"}} --><div class="wp-block-group ciclo-trust-item__copy">'
        . '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html($title) . '</h3><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . esc_html($description) . '</p><!-- /wp:paragraph -->'
        . '</div><!-- /wp:group --></div><!-- /wp:group --></div><!-- /wp:column -->';
}
$homeBlocks .= '</div><!-- /wp:columns -->';

$homeBlocks .= '<!-- wp:group {"align":"wide","className":"ciclo-home ciclo-category-list ciclo-section","layout":{"type":"default"}} --><div class="wp-block-group alignwide ciclo-home ciclo-category-list ciclo-section">'
    . '<!-- wp:group {"className":"ciclo-section__heading","layout":{"type":"default"}} --><div class="wp-block-group ciclo-section__heading">'
    . '<!-- wp:group {"layout":{"type":"default"}} --><div class="wp-block-group">'
    . '<!-- wp:paragraph {"className":"ciclo-eyebrow"} --><p class="ciclo-eyebrow">ENCUENTRA TU RITMO</p><!-- /wp:paragraph -->'
    . '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Todo para tu próxima rodada</h2><!-- /wp:heading -->'
    . '</div><!-- /wp:group -->'
    . '<!-- wp:paragraph --><p>Explora bicicletas, accesorios, nutrición y ropa para distintas formas de vivir el ciclismo.</p><!-- /wp:paragraph -->'
    . '</div><!-- /wp:group -->'
    . '<!-- wp:woocommerce/product-categories {"hasCount":false,"hasImage":true,"hasEmpty":false,"isDropdown":false,"isHierarchical":false,"align":"wide"} /-->'
    . '</div><!-- /wp:group -->';

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

$homeBlocks .= '<!-- wp:group {"align":"wide","className":"ciclo-home ciclo-section ciclo-products","layout":{"type":"default"}} --><div class="wp-block-group alignwide ciclo-home ciclo-section ciclo-products">'
    . '<!-- wp:group {"className":"ciclo-section__heading","layout":{"type":"default"}} --><div class="wp-block-group ciclo-section__heading">'
    . '<!-- wp:group {"layout":{"type":"default"}} --><div class="wp-block-group">'
    . '<!-- wp:paragraph {"className":"ciclo-eyebrow"} --><p class="ciclo-eyebrow">PARA TU PRÓXIMA SALIDA</p><!-- /wp:paragraph -->'
    . '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Equipo listo para acompañarte</h2><!-- /wp:heading -->'
    . '</div><!-- /wp:group -->'
    . '<!-- wp:paragraph --><p>Explora bicicletas y esenciales para encontrar lo que mejor se adapta a tu manera de rodar.</p><!-- /wp:paragraph -->'
    . '</div><!-- /wp:group -->';
$collectionQuery = [
    'perPage' => 8,
    'pages' => 1,
    'offset' => 0,
    'postType' => 'product',
    'order' => 'asc',
    'orderBy' => 'title',
    'search' => '',
    'exclude' => [],
    'inherit' => false,
    'taxQuery' => (object) [],
    'isProductCollectionBlock' => true,
    'featured' => false,
    'woocommerceOnSale' => false,
    'woocommerceStockStatus' => ['instock', 'onbackorder'],
    'woocommerceAttributes' => [],
    'woocommerceHandPickedProducts' => $featuredIds,
    'filterable' => false,
];
$homeBlocks .= '<!-- wp:woocommerce/product-collection ' . wp_json_encode([
    'align' => 'wide',
    'queryId' => 0,
    'query' => $collectionQuery,
    'tagName' => 'div',
    'displayLayout' => ['type' => 'flex', 'columns' => 4, 'shrinkColumns' => false],
    'dimensions' => ['widthType' => 'fill'],
    'collection' => 'woocommerce/product-collection/hand-picked',
    'hideControls' => ['inherit'],
    'queryContextIncludes' => ['collection'],
    'className' => 'ciclo-featured-collection',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ' -->'
    . '<div class="wp-block-woocommerce-product-collection alignwide ciclo-featured-collection">'
    . '<!-- wp:woocommerce/product-template -->'
    . '<!-- wp:woocommerce/product-image {"showSaleBadge":false,"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} --><!-- wp:woocommerce/product-sale-badge {"isDescendentOfQueryLoop":true,"align":"right"} /--><!-- /wp:woocommerce/product-image -->'
    . '<!-- wp:post-title {"level":3,"isLink":true,"__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->'
    . '<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->'
    . '<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /-->'
    . '<!-- /wp:woocommerce/product-template -->'
    . '</div><!-- /wp:woocommerce/product-collection -->'
    . '<!-- wp:paragraph {"className":"ciclo-catalog-link","align":"right"} --><p class="has-text-align-right ciclo-catalog-link"><a href="' . esc_url($shopUrl) . '">Ver todo el catálogo <span aria-hidden="true">→</span></a></p><!-- /wp:paragraph -->'
    . '</div><!-- /wp:group -->';

$homeBlocks .= '<!-- wp:columns {"align":"wide","className":"ciclo-how"} --><div class="wp-block-columns alignwide ciclo-how">';
$steps = [
    ['01', 'Encuentra tu equipo', 'Compara bicicletas y accesorios en las distintas categorías.'],
    ['02', 'Arma tu pedido', 'Revisa el carrito, ajusta cantidades y confirma el total.'],
    ['03', 'Finaliza con calma', 'Elige pago manual. No ingreses datos de tarjeta en el sitio.'],
];
foreach ($steps as [$number, $title, $description]) {
    $homeBlocks .= '<!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"ciclo-how-card","layout":{"type":"default"}} --><div class="wp-block-group ciclo-how-card">'
        . '<!-- wp:paragraph {"className":"ciclo-step-number"} --><p class="ciclo-step-number">' . esc_html($number) . '</p><!-- /wp:paragraph -->'
        . '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html($title) . '</h3><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . esc_html($description) . '</p><!-- /wp:paragraph -->'
        . '</div><!-- /wp:group --></div><!-- /wp:column -->';
}
$homeBlocks .= '</div><!-- /wp:columns -->'
    . '<!-- wp:group {"align":"wide","className":"ciclo-home ciclo-callout","layout":{"type":"default"}} --><div class="wp-block-group alignwide ciclo-home ciclo-callout">'
    . '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">¿Listo para salir?</h2><!-- /wp:heading -->'
    . '<!-- wp:paragraph --><p>Encuentra todo lo que necesitas para preparar tu próxima rodada.</p><!-- /wp:paragraph -->'
    . '<!-- wp:buttons --><div class="wp-block-buttons">'
    . '<!-- wp:button {"url":"' . esc_url($shopUrl) . '"} --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url($shopUrl) . '">Explorar el catálogo</a></div><!-- /wp:button -->'
    . '<!-- wp:button {"url":"' . esc_url(get_permalink($faqId)) . '","className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_url(get_permalink($faqId)) . '">Preguntas frecuentes</a></div><!-- /wp:button -->'
    . '</div><!-- /wp:buttons -->'
    . '</div><!-- /wp:group -->';

$homeEditorVersion = (int) get_post_meta($homeId, '_ciclo_finca_home_editor_version', true);
if ($homeEditorVersion < 2) {
    wp_update_post([
        'ID' => $homeId,
        'post_content' => $homeBlocks,
        'post_status' => 'publish',
    ]);
    update_post_meta($homeId, '_ciclo_finca_home_editor_version', 3);
} elseif ($homeEditorVersion < 3) {
    $homePost = get_post($homeId);
    if ($homePost) {
        $existingBlocks = parse_blocks($homePost->post_content);
        $updatedHero = false;

        foreach ($existingBlocks as &$block) {
            if (($block['blockName'] ?? '') !== 'core/cover'
                || ! in_array('ciclo-hero', (array) ($block['attrs']['className'] ?? []), true)
                || (int) ($block['attrs']['minHeight'] ?? 0) !== 680
                || ! str_contains((string) ($block['innerHTML'] ?? ''), 'style="min-height:680px"')) {
                continue;
            }

            unset($block['attrs']['minHeight'], $block['attrs']['minHeightUnit']);
            $block['innerHTML'] = str_replace(' style="min-height:680px"', '', $block['innerHTML']);
            foreach ($block['innerContent'] as &$contentPart) {
                if (is_string($contentPart)) {
                    $contentPart = str_replace(' style="min-height:680px"', '', $contentPart);
                }
            }
            unset($contentPart);
            $updatedHero = true;
            break;
        }
        unset($block);

        if ($updatedHero) {
            $updated = wp_update_post([
                'ID' => $homeId,
                'post_content' => serialize_blocks($existingBlocks),
            ], true);
            if (is_wp_error($updated)) {
                throw new RuntimeException($updated->get_error_message());
            }
        }
    }

    update_post_meta($homeId, '_ciclo_finca_home_editor_version', 3);
}
update_option('show_on_front', 'page');
update_option('page_on_front', $homeId);
update_option('page_for_posts', 0);

foreach (['sample-page'] as $sampleSlug) {
    $samplePage = get_page_by_path($sampleSlug, OBJECT, 'page');
    if ($samplePage && 'Sample Page' === $samplePage->post_title && str_contains($samplePage->post_content, 'This is an example page')) {
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
    if (str_contains($samplePost->post_content, 'Welcome to WordPress. This is your first post.')) {
        wp_delete_post($samplePost->ID, true);
    }
}

flush_rewrite_rules(false);
WP_CLI::success('Ciclo Finca block theme, storefront pages, and homepage are ready.');

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

function cicloFincaUpsertPage(string $slug, string $title, string $content, string $excerpt = '', ?string $legacyContent = null): int
{
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
        $isUntouchedLegacyPage = $legacyContent !== null
            && ! has_blocks($existing)
            && ! get_post_meta($existing->ID, '_ciclo_finca_page_editor_version', true)
            && $existing->post_title === $title
            && $existing->post_excerpt === $excerpt
            && trim($existing->post_content) === trim($legacyContent);
        if ($isUntouchedLegacyPage) {
            $updated = wp_update_post([
                'ID' => $existing->ID,
                'post_content' => $content,
            ], true);
            if (is_wp_error($updated)) {
                throw new RuntimeException($updated->get_error_message());
            }
            update_post_meta($existing->ID, '_ciclo_finca_page_editor_version', 1);
        }

        return (int) $existing->ID;
    }

    $pageData = [
        'post_type' => 'page',
        'post_name' => $slug,
        'post_title' => $title,
        'post_content' => $content,
        'post_excerpt' => $excerpt,
        'post_status' => 'publish',
    ];
    $pageId = wp_insert_post($pageData, true);
    if (is_wp_error($pageId)) {
        throw new RuntimeException($pageId->get_error_message());
    }
    update_post_meta($pageId, '_ciclo_finca_page_editor_version', 1);
    return (int) $pageId;
}

function cicloFincaEditorialContent(array $sections): array
{
    $blocks = [];
    $legacyContent = '<div class="ciclo-editorial">';

    foreach ($sections as $section) {
        $text = esc_html((string) ($section['text'] ?? ''));
        $legacyMarkup = '';
        $blockMarkup = '';
        $blockName = 'core/paragraph';
        $attributes = [];

        switch ($section['type'] ?? '') {
            case 'eyebrow':
                $attributes['className'] = 'ciclo-eyebrow';
                $legacyMarkup = '<p class="ciclo-eyebrow">' . $text . '</p>';
                $blockMarkup = $legacyMarkup;
                break;
            case 'heading':
                $blockName = 'core/heading';
                $attributes['level'] = 2;
                $legacyMarkup = '<h2>' . $text . '</h2>';
                $blockMarkup = '<h2 class="wp-block-heading">' . $text . '</h2>';
                break;
            case 'paragraph':
                $legacyMarkup = '<p>' . $text . '</p>';
                $blockMarkup = $legacyMarkup;
                break;
            case 'paragraph-link':
                $link = sprintf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url((string) ($section['url'] ?? '#')),
                    esc_html((string) ($section['label'] ?? ''))
                );
                $legacyMarkup = '<p>'
                    . esc_html((string) ($section['text'] ?? ''))
                    . $link
                    . esc_html((string) ($section['suffix'] ?? ''))
                    . '</p>';
                $blockMarkup = $legacyMarkup;
                break;
            case 'links':
                $links = [];
                foreach ($section['items'] ?? [] as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $links[] = sprintf(
                        '<a href="%1$s">%2$s</a>',
                        esc_url((string) ($item['url'] ?? '#')),
                        esc_html((string) ($item['label'] ?? ''))
                    );
                }
                if (! $links) {
                    continue 2;
                }
                $legacyMarkup = '<p>' . implode(' · ', $links) . '</p>';
                $blockMarkup = $legacyMarkup;
                break;
            default:
                continue 2;
        }

        $blocks[] = [
            'blockName' => $blockName,
            'attrs' => $attributes,
            'innerBlocks' => [],
            'innerHTML' => $blockMarkup,
            'innerContent' => [$blockMarkup],
        ];
        $legacyContent .= $legacyMarkup;
    }

    $groupWrapper = '<div class="wp-block-group ciclo-editorial">';
    $innerContent = [$groupWrapper];
    foreach ($blocks as $_block) {
        $innerContent[] = null;
    }
    $innerContent[] = '</div>';

    $group = [
        'blockName' => 'core/group',
        'attrs' => ['className' => 'ciclo-editorial', 'layout' => ['type' => 'default']],
        'innerBlocks' => $blocks,
        'innerHTML' => $groupWrapper . '</div>',
        'innerContent' => $innerContent,
    ];

    return [
        'content' => serialize_block($group),
        'legacy' => $legacyContent . '</div>',
    ];
}
