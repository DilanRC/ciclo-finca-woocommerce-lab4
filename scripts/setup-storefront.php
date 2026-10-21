<?php
/**
 * Build the Spanish storefront pages, navigation, and homepage for the lab.
 * Run after WooCommerce and the demonstration catalog are installed:
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
    'Sobre Ciclo Finca',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">EIF4280 · FUNDAMENTOS DE PROGRAMACIÓN WEB</p>'
        . '<p>Ciclo Finca es una tienda de demostración inspirada en el ciclismo y los paisajes rurales de Costa Rica. Se preparó para recorrer de principio a fin una tienda en línea construida con WordPress y WooCommerce.</p>'
        . '<h2>Una tienda para aprender haciendo</h2>'
        . '<p>El laboratorio permite practicar tareas que se conectan entre sí:</p>'
        . '<ul><li>Organizar un catálogo en categorías y completar fichas de producto.</li><li>Revisar imágenes, precios en colones e inventario administrado.</li><li>Probar cantidades, carrito, checkout y pedidos de visitante.</li><li>Consultar los pedidos en WooCommerce y actualizar sus estados.</li></ul>'
        . '<h2>Inspirada en las rutas de Costa Rica</h2>'
        . '<p>La identidad visual mezcla tonos de bosque, vegetación y caminos de montaña. Los productos y las fotografías se presentan como material ilustrativo para que el catálogo se sienta como una tienda completa sin representar una oferta comercial.</p>'
        . '<h2>Un entorno controlado</h2>'
        . '<p>La instalación de práctica corre en Docker y guarda sus datos en una base local del laboratorio. El pago manual no mueve dinero, la recogida local es simulada y las existencias no representan inventario real.</p>'
        . '<p><a href="' . esc_url($shopUrl) . '">Explorar el catálogo</a> · <a href="' . esc_url(home_url('/preguntas-frecuentes/')) . '">Leer las preguntas frecuentes</a></p>'
        . '</div>',
    'Proyecto académico de tienda con WordPress y WooCommerce.'
);

$guideId = cicloFincaUpsertPage(
    'como-comprar',
    'Cómo comprar',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">GUÍA RÁPIDA</p>'
        . '<p>El flujo de compra está preparado para practicar cada paso de una tienda en línea. Usa datos ficticios durante la demostración.</p>'
        . '<h2>1. Elige productos</h2>'
        . '<p>Explora las categorías y abre una ficha para revisar la imagen, descripción, precio en colones y cantidad disponible. Añade uno o más productos simples al carrito.</p>'
        . '<h2>2. Revisa el carrito</h2>'
        . '<p>Confirma los artículos y las cantidades. Puedes actualizar el carrito para comprobar cómo cambian el subtotal y el total de la compra.</p>'
        . '<h2>3. Finaliza el pedido de prueba</h2>'
        . '<p>Completa el checkout como visitante con un nombre y una dirección ficticios. Para el correo, usa un ejemplo como cliente1@example.test. Selecciona “Pago manual de prueba” y “Recogida local gratuita (simulada)”.</p>'
        . '<h2>4. Revisa y gestiona el pedido</h2>'
        . '<p>En el escritorio de WordPress, entra a WooCommerce → Pedidos. Abre el pedido para comprobar sus productos, cantidades, datos sintéticos, método de pago y total. Puedes practicar los estados En espera, Procesando y Completado.</p>'
        . '<div class="ciclo-practice-note"><h2>Sin cobros ni transferencias</h2><p>Este sitio es un ejercicio académico. No ingreses datos financieros, no realices transferencias y no uses información personal real. Los pedidos sirven únicamente para practicar la gestión de estados en WooCommerce.</p></div>'
        . '<p><a href="' . esc_url($shopUrl) . '">Ir a la tienda</a></p>'
        . '</div>',
    'Pasos para completar una compra de prueba sin dinero real.'
);

$faqId = cicloFincaUpsertPage(
    'preguntas-frecuentes',
    'Preguntas frecuentes',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">ANTES DE PROBAR EL CHECKOUT</p>'
        . '<h2>¿Los productos se venden de verdad?</h2>'
        . '<p>No. El catálogo, los precios y el inventario son datos ilustrativos para una práctica universitaria.</p>'
        . '<h2>¿El pago manual cobra o transfiere dinero?</h2>'
        . '<p>No. Es una opción de pago simulada, sin cuentas bancarias ni integración con servicios financieros.</p>'
        . '<h2>¿Cómo se entrega un pedido?</h2>'
        . '<p>La tienda solo incluye recogida local gratuita simulada para permitir el ejercicio. No se coordina ninguna entrega real.</p>'
        . '<h2>¿Por qué puede cambiar el inventario?</h2>'
        . '<p>WooCommerce administra existencias para que puedas revisar cómo el catálogo responde a las compras de prueba. Los números disponibles son datos de práctica, no representan unidades físicas.</p>'
        . '<h2>¿Qué significan los estados del pedido?</h2>'
        . '<p>Un pedido con pago manual puede quedar En espera mientras se revisa. Para la práctica, puedes cambiar otros pedidos a Procesando o Completado desde el panel de WooCommerce. Ningún estado implica que haya ocurrido una transferencia real.</p>'
        . '<h2>¿Debo usar mi correo o dirección personal?</h2>'
        . '<p>No. Utiliza datos ficticios y un correo de ejemplo como cliente1@example.test. El dominio example.test se usa aquí para evitar enviar información de prueba a una dirección personal.</p>'
        . '<h2>¿Voy a recibir una confirmación por correo?</h2>'
        . '<p>El entorno local no incluye un servicio de correo. Los pedidos y sus cambios de estado se consultan en WooCommerce → Pedidos; no dependas de un mensaje por email para confirmar el ejercicio.</p>'
        . '<div class="ciclo-practice-note"><h2>Uso académico</h2><p>No compartas información personal real en este entorno. Para la grabación de la práctica, utiliza datos sintéticos con el dominio reservado example.test.</p></div>'
        . '<p><a href="' . esc_url(get_permalink($guideId)) . '">Leer la guía de compra</a></p>'
        . '</div>',
    'Respuestas sobre el catálogo, el pago y la entrega de prueba.'
);

cicloFincaUpsertPage(
    'privacy-policy',
    'Privacidad del laboratorio',
    '<div class="ciclo-editorial">'
        . '<p class="ciclo-eyebrow">SITIO DE DEMOSTRACIÓN</p>'
        . '<p>Ciclo Finca es un entorno académico de WordPress y WooCommerce para EIF4280 — Fundamentos de programación web. No es una tienda física ni recibe pagos reales.</p>'
        . '<h2>Datos de los pedidos</h2>'
        . '<p>WooCommerce guarda los pedidos de práctica en la base de datos local de este laboratorio. Usa únicamente nombres, correos y direcciones sintéticas reservadas para pruebas, como example.test.</p>'
        . '<h2>Pagos y servicios externos</h2>'
        . '<p>El método “Pago manual de prueba” no solicita cuentas bancarias ni se conecta con una pasarela de pago. La recogida local también es simulada.</p>'
        . '<h2>Uso del entorno</h2>'
        . '<p>No ingreses contraseñas, datos financieros ni información personal real. El sitio se conserva o elimina junto con los volúmenes locales de Docker.</p>'
        . '<p><a href="' . esc_url(get_permalink($faqId)) . '">Leer las preguntas frecuentes</a></p>'
        . '</div>',
    'Información sobre el uso de datos sintéticos en el laboratorio.'
);

$homeId = cicloFincaUpsertPage(
    'inicio',
    'Inicio',
    '',
    'Bicicletas, accesorios y equipo para disfrutar cada ruta. Catálogo académico de demostración.'
);

$homeContent = '<div class="ciclo-home">'
    . '<section class="ciclo-hero" aria-labelledby="ciclo-home-heading">'
    . '<img class="ciclo-hero__image" src="' . esc_url($heroImageUrl) . '" alt="Ciclista recorre un sendero de montaña entre colinas verdes de Costa Rica" fetchpriority="high">'
    . '<div class="ciclo-hero__overlay" aria-hidden="true"></div>'
    . '<div class="ciclo-hero__content">'
    . '<p class="ciclo-eyebrow">CICLO FINCA · COSTA RICA</p>'
    . '<h1 id="ciclo-home-heading">Tu próxima ruta empieza aquí.</h1>'
    . '<p class="ciclo-hero__intro">Bicicletas y esenciales para moverte con libertad. Explora el catálogo de muestra y descubre una compra de principio a fin.</p>'
    . '<div class="ciclo-hero__actions"><a href="' . esc_url($shopUrl) . '">Explorar la tienda</a><a href="' . esc_url(get_permalink($guideId)) . '">Cómo comprar</a></div>'
    . '</div></section>'
    . '<div class="ciclo-trust-strip" aria-label="Información de la tienda">'
    . '<div><strong>Precios en colones</strong><span>Moneda CRC · Costa Rica</span></div>'
    . '<div><strong>Pago manual de prueba</strong><span>Sin cobros ni transferencias</span></div>'
    . '<div><strong>Recogida local</strong><span>Entrega gratuita simulada</span></div>'
    . '</div></div>';

$homeBlocks = '<!-- wp:html -->' . $homeContent . '<!-- /wp:html -->';
$homeBlocks .= '<!-- wp:group {"className":"ciclo-home ciclo-category-list"} --><div class="wp-block-group ciclo-home ciclo-category-list">';
$homeBlocks .= '<!-- wp:html --><div class="ciclo-section__heading"><div><p class="ciclo-eyebrow">ENCUENTRA TU RITMO</p><h2>Todo para tu próxima rodada</h2></div><p>Explora bicicletas, accesorios, nutrición y ropa para distintas formas de vivir el ciclismo.</p></div><!-- /wp:html -->';
$homeBlocks .= '<!-- wp:shortcode -->[product_categories number="4" columns="4" orderby="name" order="ASC" hide_empty="0"]<!-- /wp:shortcode -->';
$homeBlocks .= '</div><!-- /wp:group -->';

$featuredSkus = [
    'CF4-LAB4-BIKE-01',
    'CF4-LAB4-BIKE-02',
    'CF4-LAB4-SEAT-01',
    'CF4-LAB4-ACC-03',
    'CF4-LAB4-ACC-04',
    'CF4-LAB4-ACC-06',
    'CF4-LAB4-NUTRITION-01',
    'CF4-LAB4-APP-01',
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
$homeBlocks .= '<!-- wp:html --><div class="ciclo-section__heading"><div><p class="ciclo-eyebrow">SELECCIÓN DE LA TIENDA</p><h2>Equipo listo para acompañarte</h2></div><p>Ocho productos destacados de un catálogo de doce artículos simples con existencias administradas.</p></div><!-- /wp:html -->';
$homeBlocks .= '<!-- wp:shortcode -->[products ids="' . esc_attr(implode(',', $featuredIds)) . '" limit="8" columns="4" orderby="post__in"]<!-- /wp:shortcode -->';
$homeBlocks .= '<!-- wp:html --><p class="ciclo-catalog-link"><a href="' . esc_url($shopUrl) . '">Ver los 12 productos del catálogo →</a></p><!-- /wp:html -->';
$homeBlocks .= '</div><!-- /wp:group -->';

$homeBlocks .= '<!-- wp:html -->'
    . '<div class="ciclo-home ciclo-how"><article><span>01</span><h3>Elige tus productos</h3><p>Revisa cada ficha y añade al carrito los artículos de muestra que quieras probar.</p></article>'
    . '<article><span>02</span><h3>Completa el checkout</h3><p>Usa datos ficticios y selecciona el pago manual de prueba con recogida local simulada.</p></article>'
    . '<article><span>03</span><h3>Gestiona el pedido</h3><p>En el panel de WooCommerce, revisa los detalles y cambia el estado del pedido.</p></article></div>'
    . '<aside class="ciclo-home ciclo-practice-note"><h2>Una tienda de demostración, sin dinero real</h2><p>Ciclo Finca se creó para EIF4280 — Fundamentos de programación web. Los productos, precios, existencias y pedidos son información de práctica. Usa datos sintéticos y no ingreses información financiera.</p><p><a href="' . esc_url(get_permalink($faqId)) . '">Lee las preguntas frecuentes</a></p></aside>'
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
    ['Carrito', wc_get_page_permalink('cart'), 'custom', 0],
    ['Cómo comprar', get_permalink($guideId), 'page', $guideId],
    ['Sobre el proyecto', get_permalink($aboutId), 'page', $aboutId],
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
