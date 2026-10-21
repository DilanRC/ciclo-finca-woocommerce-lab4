<?php
/**
 * Seed twelve simple products and their images in the isolated local store.
 * Run after configure-store.php:
 * wp eval-file /lab/seed-products.php
 */
if (! defined('ABSPATH') || ! class_exists('WC_Product_Simple')) {
    throw new RuntimeException('Run this file with WP-CLI after activating WooCommerce.');
}

$host = wp_parse_url(home_url(), PHP_URL_HOST);
if (! in_array($host, ['localhost', '127.0.0.1'], true)) {
    throw new RuntimeException('This script is restricted to a local WordPress site.');
}

$categories = [
    'bicicletas' => ['Bicicletas', 'Bicicletas de montaña y gravel para quienes disfrutan explorar caminos nuevos.'],
    'accesorios' => ['Accesorios', 'Sillines, protección, hidratación, herramientas y equipo para acompañarte en cada salida.'],
    'nutricion' => ['Nutrición', 'Bebidas y esenciales para llevar contigo durante tus recorridos.'],
    'ropa' => ['Ropa', 'Prendas de ciclismo con diseños inspirados en los caminos y paisajes de Costa Rica.'],
];

$products = [
    [
        'CIC-BIK-001',
        'Bicicleta MTB de doble suspensión',
        'bicicletas',
        '<p>Una bicicleta de montaña de doble suspensión para recorrer senderos, pendientes y caminos irregulares. Su silueta robusta está hecha para quienes buscan una rodada con carácter.</p><p>Consulta el precio y la disponibilidad que aparecen en esta ficha antes de añadirla al carrito.</p>',
        1500000,
        6,
        '/product-assets/bicicleta-mtb-suspension.webp',
        'Bicicleta de montaña de doble suspensión para explorar nuevos senderos.',
    ],
    [
        'CIC-BIK-002',
        'Bicicleta MTB hardtail',
        'bicicletas',
        '<p>Una bicicleta de montaña de cuadro rígido, con una presencia ligera y decidida. Ideal para quienes disfrutan los caminos compactos, las subidas y las vueltas espontáneas.</p><p>Revisa el precio y la disponibilidad indicada en la ficha para preparar tu pedido.</p>',
        350000,
        8,
        '/product-assets/bicicleta-mtb-hardtail.webp',
        'Una bicicleta rígida para caminos de montaña y recorridos cotidianos.',
    ],
    [
        'CIC-BIK-003',
        'Bicicleta gravel',
        'bicicletas',
        '<p>Una bicicleta gravel para enlazar tramos de asfalto y caminos de lastre en una misma salida. Su diseño invita a tomar el desvío y dejar que el recorrido se alargue.</p><p>Consulta el precio y la disponibilidad indicados antes de añadirla al carrito.</p>',
        425000,
        5,
        '/product-assets/bicicleta-gravel-demo.webp',
        'Bicicleta gravel para cambiar de terreno y seguir rodando.',
    ],
    [
        'CIC-ACC-001',
        'Sillín MTB negro y rojo',
        'accesorios',
        '<p>Sillín para bicicleta de montaña en una combinación deportiva de negro y rojo. Un detalle llamativo para renovar el aspecto de tu bicicleta.</p><p>Antes de comprar, verifica la compatibilidad con tu bicicleta y consulta la disponibilidad de este artículo.</p>',
        18000,
        12,
        '/product-assets/asiento-mtb-rojo-demo.webp',
        'Sillín para bicicleta de montaña con detalles en rojo.',
    ],
    [
        'CIC-ACC-002',
        'Sillín negro y azul',
        'accesorios',
        '<p>Sillín para bicicleta en tonos negro y azul, con una silueta estilizada y un diseño de inspiración deportiva.</p><p>Verifica las medidas y la compatibilidad que requiere tu bicicleta antes de elegirlo.</p>',
        25000,
        10,
        '/product-assets/asiento-ergonomico-azul-demo.webp',
        'Sillín negro y azul de estilo deportivo para bicicleta.',
    ],
    [
        'CIC-NUT-001',
        'Bebidas para ciclismo',
        'nutricion',
        '<p>Una selección de bebidas en envases individuales para llevar en tus recorridos. El formato compacto cabe fácilmente junto con el resto de tu equipo.</p><p>Consulta la etiqueta del producto para revisar ingredientes e indicaciones antes de consumir.</p>',
        20000,
        35,
        '/product-assets/bebidas-hidratacion-demo.webp',
        'Bebidas en envases individuales para acompañar tus salidas.',
    ],
    [
        'CIC-ACC-003',
        'Casco MTB Trail',
        'accesorios',
        '<p>Casco de ciclismo de montaña en color verde, con una estética aventurera y líneas marcadas.</p><p>Antes de elegirlo, revisa el ajuste, la talla y las indicaciones del fabricante.</p>',
        42000,
        12,
        '/product-assets/casco-mtb-trail.webp',
        'Casco MTB verde para completar tu equipo de ruta.',
    ],
    [
        'CIC-ACC-004',
        'Guantes MTB Trail',
        'accesorios',
        '<p>Guantes de ciclismo con palma texturizada y diseño en negro. Un accesorio cómodo de llevar en tus recorridos.</p><p>Consulta la disponibilidad indicada en esta ficha antes de añadir el par a tu pedido.</p>',
        18500,
        20,
        '/product-assets/guantes-mtb-trail.webp',
        'Guantes de ciclismo negros con palma texturizada.',
    ],
    [
        'CIC-ACC-005',
        'Botella de ciclismo',
        'accesorios',
        '<p>Botella para bicicleta con acabado metálico y tapa verde. Su forma alargada está pensada para llevarla en tus recorridos.</p><p>Revisa las dimensiones de tu portabotellas para confirmar que se ajuste a tu bicicleta.</p>',
        14000,
        24,
        '/product-assets/botella-termica-650ml.webp',
        'Botella metálica para bicicleta con tapa verde.',
    ],
    [
        'CIC-ACC-006',
        'Kit de herramientas para ruta',
        'accesorios',
        '<p>Kit compacto de herramientas para llevar en la bicicleta o guardar en casa. Su estuche mantiene las piezas reunidas y listas para acompañarte en el camino.</p><p>Consulta la imagen del producto para revisar el contenido incluido y confirmar que responda a tus necesidades.</p>',
        22500,
        15,
        '/product-assets/kit-herramientas-ruta.webp',
        'Kit compacto de herramientas de ciclismo con estuche.',
    ],
    [
        'CIC-ACC-007',
        'Luces LED para bicicleta',
        'accesorios',
        '<p>Juego de luces LED delantera y trasera para bicicleta. Su formato compacto permite mantener ambas luces a mano al preparar tu salida.</p><p>Consulta en el empaque del producto las instrucciones de instalación y uso.</p>',
        28000,
        16,
        '/product-assets/luces-led-recargables.webp',
        'Juego de luces LED delantera y trasera para bicicleta.',
    ],
    [
        'CIC-ROP-001',
        'Jersey de ciclismo verde',
        'ropa',
        '<p>Jersey de ciclismo verde de manga corta con un diseño deportivo inspirado en las rutas de montaña.</p><p>Revisa la información de talla y cuidado disponible antes de añadirlo a tu pedido.</p>',
        36000,
        10,
        '/product-assets/jersey-ciclismo-verde.webp',
        'Jersey de ciclismo verde de manga corta.',
    ],
];

$legacySkus = [
    'CIC-BIK-001' => 'CF4-LAB4-BIKE-01',
    'CIC-BIK-002' => 'CF4-LAB4-BIKE-02',
    'CIC-BIK-003' => 'CF4-LAB4-BIKE-03',
    'CIC-ACC-001' => 'CF4-LAB4-SEAT-01',
    'CIC-ACC-002' => 'CF4-LAB4-SEAT-02',
    'CIC-ACC-003' => 'CF4-LAB4-ACC-03',
    'CIC-ACC-004' => 'CF4-LAB4-ACC-04',
    'CIC-ACC-005' => 'CF4-LAB4-ACC-05',
    'CIC-ACC-006' => 'CF4-LAB4-ACC-06',
    'CIC-ACC-007' => 'CF4-LAB4-ACC-07',
    'CIC-NUT-001' => 'CF4-LAB4-NUTRITION-01',
    'CIC-ROP-001' => 'CF4-LAB4-APP-01',
];

foreach ($products as $productData) {
    if (! is_readable($productData[6])) {
        throw new RuntimeException('Missing product image: ' . $productData[6]);
    }
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$termIds = [];
foreach ($categories as $slug => [$name, $description]) {
    $term = term_exists($slug, 'product_cat');
    if (! $term) {
        $term = wp_insert_term($name, 'product_cat', [
            'slug' => $slug,
            'description' => $description,
        ]);
    }

    if (is_wp_error($term)) {
        throw new RuntimeException($term->get_error_message());
    }

    $termIds[$slug] = (int) (is_array($term) ? $term['term_id'] : $term);
    $updatedTerm = wp_update_term($termIds[$slug], 'product_cat', [
        'name' => $name,
        'description' => $description,
    ]);
    if (is_wp_error($updatedTerm)) {
        throw new RuntimeException($updatedTerm->get_error_message());
    }
}

update_option('default_product_cat', $termIds['accesorios']);
$uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
if ($uncategorized && (int) $uncategorized->count === 0) {
    $deleted = wp_delete_term((int) $uncategorized->term_id, 'product_cat');
    if (is_wp_error($deleted)) {
        throw new RuntimeException($deleted->get_error_message());
    }
}

foreach ($products as [$sku, $name, $categorySlug, $description, $price, $stock, $imagePath, $shortDescription]) {
    $existingId = wc_get_product_id_by_sku($sku);
    if (! $existingId && isset($legacySkus[$sku])) {
        $existingId = wc_get_product_id_by_sku($legacySkus[$sku]);
    }
    if ($existingId) {
        $existing = wc_get_product($existingId);
        if (! $existing) {
            throw new RuntimeException('Could not load existing product with SKU ' . $sku);
        }

        $existing->set_sku($sku);
        $existing->set_name($name);
        $existing->set_status('publish');
        $existing->set_catalog_visibility('visible');
        $existing->set_description($description);
        $existing->set_short_description($shortDescription);
        $existing->set_regular_price((string) $price);
        $existing->set_category_ids([$termIds[$categorySlug]]);
        if (! $existing->get_manage_stock()) {
            $existing->set_manage_stock(true);
            $existing->set_stock_quantity($stock);
            $existing->set_stock_status('instock');
        }
        $existing->save();

        $productSlug = sanitize_title($name);
        if (get_post_field('post_name', $existingId) !== $productSlug) {
            wp_update_post([
                'ID' => $existingId,
                'post_name' => $productSlug,
            ]);
        }

        $existingImageId = $existing->get_image_id();
        $existingAsset = $existingImageId
            ? get_post_meta($existingImageId, '_ciclo_finca_seed_asset', true)
            : '';
        if (! $existingImageId || $existingAsset !== basename($imagePath)) {
            attachProductImage($existing, $name, $imagePath);
        } else {
            wp_update_post(['ID' => $existingImageId, 'post_title' => $name]);
            update_post_meta($existingImageId, '_wp_attachment_image_alt', $name);
        }

        WP_CLI::log('Updated product without resetting its stock: ' . $name);
        continue;
    }

    $product = new WC_Product_Simple();
    $product->set_sku($sku);
    $product->set_name($name);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_description($description);
    $product->set_short_description($shortDescription);
    $product->set_regular_price((string) $price);
    $product->set_manage_stock(true);
    $product->set_stock_quantity($stock);
    $product->set_stock_status('instock');
    $product->set_category_ids([$termIds[$categorySlug]]);
    $product->set_virtual(false);
    $product->save();

    attachProductImage($product, $name, $imagePath);
    WP_CLI::success('Created simple product: ' . $name);
}

$categoryThumbnailSkus = [
    'bicicletas' => 'CIC-BIK-001',
    'accesorios' => 'CIC-ACC-003',
    'nutricion' => 'CIC-NUT-001',
    'ropa' => 'CIC-ROP-001',
];
foreach ($categoryThumbnailSkus as $slug => $sku) {
    $productId = wc_get_product_id_by_sku($sku);
    $product = $productId ? wc_get_product($productId) : false;
    if ($product && $product->get_image_id()) {
        update_term_meta($termIds[$slug], 'thumbnail_id', $product->get_image_id());
    }
}

WP_CLI::success('The Ciclo Finca twelve-product catalog is ready.');

function attachProductImage(WC_Product_Simple $product, string $name, string $imagePath): void
{
    $upload = wp_upload_bits(basename($imagePath), null, file_get_contents($imagePath));
    if (! empty($upload['error'])) {
        throw new RuntimeException('Image upload failed for ' . $name . ': ' . $upload['error']);
    }

    $fileType = wp_check_filetype($upload['file']);
    $attachmentId = wp_insert_attachment([
        'post_mime_type' => $fileType['type'],
        'post_title' => $name,
        'post_content' => '',
        'post_status' => 'inherit',
    ], $upload['file'], $product->get_id());

    if (is_wp_error($attachmentId)) {
        throw new RuntimeException($attachmentId->get_error_message());
    }

    $metadata = wp_generate_attachment_metadata($attachmentId, $upload['file']);
    wp_update_attachment_metadata($attachmentId, $metadata);
    update_post_meta($attachmentId, '_wp_attachment_image_alt', $name);
    update_post_meta($attachmentId, '_ciclo_finca_seed_asset', basename($imagePath));
    $product->set_image_id((int) $attachmentId);
    $product->save();
}
