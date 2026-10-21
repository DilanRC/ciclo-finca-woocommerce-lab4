<?php
/**
 * Seed twelve simple demo products and their images in the isolated lab store.
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
    'bicicletas' => ['Bicicletas', 'Modelos de montaña y gravel de muestra para explorar precios, fichas de producto y existencias. Cada artículo de esta categoría es ilustrativo y sirve para practicar el flujo de compra.'],
    'accesorios' => ['Accesorios', 'Sillines, protección, hidratación y herramientas forman esta selección de accesorios de demostración. Combina artículos y revisa cómo cambian las cantidades del carrito.'],
    'nutricion' => ['Nutrición', 'Una selección ilustrativa para representar la categoría de nutrición e hidratación. Las fichas sirven para probar cantidades, disponibilidad y totales.'],
    'ropa' => ['Ropa', 'Prendas de muestra para completar el catálogo de ciclismo. No se publican tallas ni disponibilidad comercial; la información se usa en el laboratorio.'],
];

$products = [
    [
        'CF4-LAB4-BIKE-01',
        'Bicicleta MTB doble suspensión (muestra)',
        'bicicletas',
        '<p>Inspirada en las bicicletas de montaña de doble suspensión, esta pieza encabeza la selección de Ciclo Finca. La fotografía es ilustrativa y sirve para dar contexto al catálogo del laboratorio.</p><p>Revisa el precio, las unidades disponibles y el selector de cantidad para seguir el recorrido desde la ficha hasta el carrito. La tienda no ofrece una bicicleta real: talla, componentes, garantía y especificaciones técnicas no están definidos.</p>',
        1500000,
        6,
        '/product-assets/bicicleta-mtb-suspension.webp',
        'Una bicicleta MTB de muestra para explorar la ficha, revisar existencias y probar el checkout.',
    ],
    [
        'CF4-LAB4-BIKE-02',
        'Bicicleta MTB hardtail de muestra',
        'bicicletas',
        '<p>Una bicicleta rígida de montaña para imaginar rutas por caminos compactos y senderos. La imagen y el artículo forman parte del catálogo académico de Ciclo Finca.</p><p>Su ficha permite comparar el precio con otros productos y poner a prueba cantidades e inventario. No representa un modelo disponible para venta y no incluye ficha técnica real de cuadro, componentes o tallas.</p>',
        350000,
        8,
        '/product-assets/bicicleta-mtb-hardtail.webp',
        'Bicicleta de montaña rígida ilustrativa para comparar precios y probar cantidades en el carrito.',
    ],
    [
        'CF4-LAB4-BIKE-03',
        'Bicicleta gravel de demostración',
        'bicicletas',
        '<p>Una bicicleta gravel de demostración, pensada para evocar recorridos que combinan carretera secundaria y caminos de lastre. Su imagen ayuda a reconocer rápidamente la categoría de bicicletas.</p><p>Explora el detalle de producto, agrégala al carrito y revisa cómo WooCommerce calcula el total. El precio y las existencias son datos de práctica; no se publican especificaciones, talla ni disponibilidad comercial.</p>',
        425000,
        5,
        '/product-assets/bicicleta-gravel-demo.webp',
        'Una bicicleta gravel ilustrativa para armar una compra de práctica y comprobar los totales.',
    ],
    [
        'CF4-LAB4-SEAT-01',
        'Asiento MTB DDK R305',
        'accesorios',
        '<p>El nombre DDK R305 aparece como dato de muestra del catálogo. La ilustración presenta un sillín genérico en tonos negro y rojo; no es una fotografía verificada del fabricante ni confirma el diseño exacto del modelo.</p><p>Consulta su precio, cantidad disponible y categoría, y combínalo con una bicicleta de muestra. No se especifican medidas, compatibilidad con cuadros ni condiciones de uso para una compra real.</p>',
        18000,
        12,
        '/product-assets/asiento-mtb-rojo-demo.webp',
        'Sillín de muestra en tonos negro y rojo para explorar precios y existencias del catálogo.',
    ],
    [
        'CF4-LAB4-SEAT-02',
        'Asiento DDK Snake Ergonomics',
        'accesorios',
        '<p>El nombre DDK Snake Ergonomics se conserva como dato de muestra del catálogo. La ilustración muestra un sillín genérico en tonos negro y azul; no es una fotografía verificada del fabricante ni confirma el diseño exacto del modelo.</p><p>Esta ficha pertenece al catálogo demostrativo: las dimensiones, la compatibilidad y cualquier recomendación de ajuste deben confirmarse con el fabricante antes de un uso real.</p>',
        25000,
        10,
        '/product-assets/asiento-ergonomico-azul-demo.webp',
        'Sillín ilustrativo en tonos negro y azul para completar una compra de accesorios.',
    ],
    [
        'CF4-LAB4-NUTRITION-01',
        'Bebidas de hidratación deportiva (muestra)',
        'nutricion',
        '<p>Una selección ilustrativa de bebidas deportivas para representar la categoría de nutrición e hidratación de Ciclo Finca. La imagen de muestra agrupa varios envases de colores distintos.</p><p>Úsala para probar cantidades mayores a uno y revisar el cálculo del pedido. No se detallan marcas, ingredientes ni información nutricional y el producto no está a la venta.</p>',
        20000,
        35,
        '/product-assets/bebidas-hidratacion-demo.webp',
        'Bebidas deportivas ilustrativas para ensayar cantidades, subtotal y existencias en WooCommerce.',
    ],
    [
        'CF4-LAB4-ACC-03',
        'Casco MTB Trail',
        'accesorios',
        '<p>Un casco de montaña ilustrativo para representar el equipo de protección dentro del catálogo. Su acabado verde combina con la identidad visual de Ciclo Finca.</p><p>La ficha es exclusivamente académica y no certifica seguridad, ajuste ni protección. Para un uso real se deben revisar la certificación aplicable, la talla y las instrucciones del fabricante.</p>',
        42000,
        12,
        '/product-assets/casco-mtb-trail.webp',
        'Casco de montaña ilustrativo para completar la categoría de accesorios del catálogo.',
    ],
    [
        'CF4-LAB4-ACC-04',
        'Guantes MTB Trail',
        'accesorios',
        '<p>Guantes de demostración para añadir una prenda de protección a la selección de accesorios. La ficha describe un par de guantes y muestra un acabado con palma texturizada.</p><p>Revisa el inventario y combina este producto con una bicicleta o un casco para probar la compra de varios artículos. La imagen es ilustrativa; material, tallas y nivel de protección no están especificados.</p>',
        18500,
        20,
        '/product-assets/guantes-mtb-trail.webp',
        'Par de guantes MTB ilustrativos para añadir variedad a una compra de prueba.',
    ],
    [
        'CF4-LAB4-ACC-05',
        'Botella térmica 650 ml',
        'accesorios',
        '<p>Botella de muestra con cuerpo metálico y tapa verde, presentada como complemento para una salida en bicicleta. La capacidad indicada de 650 ml es parte de la descripción ilustrativa del catálogo.</p><p>Añádela a tu carrito para ver cómo se reflejan las cantidades y el subtotal. No se ha verificado el aislamiento térmico ni se ofrece una garantía de uso.</p>',
        14000,
        24,
        '/product-assets/botella-termica-650ml.webp',
        'Botella ilustrativa de 650 ml, con acabado metálico y tapa verde.',
    ],
    [
        'CF4-LAB4-ACC-06',
        'Kit de herramientas para ruta',
        'accesorios',
        '<p>Un kit compacto de herramientas de muestra que representa los accesorios de mantenimiento para una ruta. La imagen reúne piezas pequeñas en un estuche fácil de identificar en el catálogo.</p><p>La ficha sirve para practicar la selección de accesorios y revisar el total de una combinación. El contenido exacto del kit y su compatibilidad con reparaciones no están especificados.</p>',
        22500,
        15,
        '/product-assets/kit-herramientas-ruta.webp',
        'Kit compacto de herramientas ilustrativo para dar variedad a la selección de accesorios.',
    ],
    [
        'CF4-LAB4-ACC-07',
        'Luces LED recargables',
        'accesorios',
        '<p>Juego de luces delantera y trasera de demostración, pensado para representar un accesorio visible en una bicicleta. Su imagen ayuda a distinguir este artículo en la tienda.</p><p>Consulta la disponibilidad y añádelo junto con otros productos de muestra. La ficha no declara potencia luminosa, autonomía, resistencia al agua ni certificación para circular.</p>',
        28000,
        16,
        '/product-assets/luces-led-recargables.webp',
        'Juego ilustrativo de luces delantera y trasera para completar el catálogo de accesorios.',
    ],
    [
        'CF4-LAB4-APP-01',
        'Jersey de ciclismo verde',
        'ropa',
        '<p>Jersey de ciclismo de manga corta en color verde, incluido para dar variedad a la categoría de ropa. El diseño gráfico de la imagen es ilustrativo y combina con la paleta de Ciclo Finca.</p><p>Explora la ficha y agrégalo a una compra de prueba. No se ofrece una guía de tallas ni se especifican composición de la tela, ajuste o cuidados.</p>',
        36000,
        10,
        '/product-assets/jersey-ciclismo-verde.webp',
        'Jersey verde de manga corta como prenda de muestra para completar el catálogo.',
    ],
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
    if ($existingId) {
        $existing = wc_get_product($existingId);
        if (! $existing) {
            throw new RuntimeException('Could not load existing product with SKU ' . $sku);
        }

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

        $existingImageId = $existing->get_image_id();
        $existingAsset = $existingImageId
            ? get_post_meta($existingImageId, '_ciclo_finca_seed_asset', true)
            : '';
        if (! $existingImageId || $existingAsset !== basename($imagePath)) {
            attachProductImage($existing, $name, $imagePath);
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
    'bicicletas' => 'CF4-LAB4-BIKE-01',
    'accesorios' => 'CF4-LAB4-ACC-03',
    'nutricion' => 'CF4-LAB4-NUTRITION-01',
    'ropa' => 'CF4-LAB4-APP-01',
];
foreach ($categoryThumbnailSkus as $slug => $sku) {
    $productId = wc_get_product_id_by_sku($sku);
    $product = $productId ? wc_get_product($productId) : false;
    if ($product && $product->get_image_id()) {
        update_term_meta($termIds[$slug], 'thumbnail_id', $product->get_image_id());
    }
}

WP_CLI::success('The twelve-product demonstration catalog is ready.');

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
