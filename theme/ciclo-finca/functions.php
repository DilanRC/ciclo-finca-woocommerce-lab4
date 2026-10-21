<?php
/**
 * Load the child theme's storefront refinements after Twenty Twenty-Five.
 */
add_action('wp_enqueue_scripts', static function (): void {
    $stylePath = get_stylesheet_directory() . '/style.css';
    $styleVersion = is_readable($stylePath) ? (string) filemtime($stylePath) : wp_get_theme()->get('Version');
    wp_enqueue_style(
        'ciclo-finca-storefront',
        get_stylesheet_uri(),
        [],
        $styleVersion
    );
}, 25);

add_filter('gettext_woocommerce', static function (string $translation, string $text, string $domain): string {
    $translations = [
        'Showing the single result' => 'Mostrando el único resultado',
        'Your cart is currently empty!' => 'Tu carrito está vacío.',
        'Browse store' => 'Explorar la tienda',
        'New in store' => 'Novedades',
        'Order Confirmation' => 'Confirmación del pedido',
        'Shipment' => 'Envío',
        'Shipment:' => 'Envío:',
        'Country / Region' => 'País / Región',
        'Select a country / region…' => 'Elige un país / región…',
        'Select a country / region&hellip;' => 'Elige un país / región…',
        'Category:' => 'Categoría:',
        'Visit product category %1$s' => 'Ver la categoría de producto %1$s',
        'Remove %s from cart' => 'Quitar %s del carrito',
        'Enter your coupon code' => 'Ingresa tu cupón',
    ];
    return $translations[$text] ?? $translation;
}, 20, 3);

add_filter('ngettext_woocommerce', static function (string $translation, string $single, string $plural, int $number): string {
    if ($single === 'Category:' && $plural === 'Categories:') {
        return $number === 1 ? 'Categoría:' : 'Categorías:';
    }

    if ($single === 'Showing all %1$d result' && $plural === 'Showing all %1$d results') {
        return 'Mostrando los %1$d resultados';
    }

    if ($single === 'Showing %1$d&ndash;%2$d of %3$d result' && $plural === 'Showing %1$d&ndash;%2$d of %3$d results') {
        return 'Mostrando %1$d&ndash;%2$d de %3$d resultados';
    }

    return $translation;
}, 20, 4);

add_filter('render_block_core/post-terms', static function (string $content, array $block): string {
    $attributes = $block['attrs'] ?? [];
    if (($attributes['term'] ?? '') !== 'product_cat' || ($attributes['prefix'] ?? '') !== 'Category: ') {
        return $content;
    }

    return str_replace(
        '<span class="wp-block-post-terms__prefix">Category: </span>',
        '<span class="wp-block-post-terms__prefix">Categoría: </span>',
        $content
    );
}, 10, 2);

add_filter('gettext_with_context_woocommerce', static function (string $translation, string $text, string $context, string $domain): string {
    if ($text === 'Shipment' && $context === 'shipping packages') {
        return 'Envío';
    }

    return $translation;
}, 20, 4);

add_action('woocommerce_after_shop_loop_item_title', static function (): void {
    if (is_shop() || is_product_taxonomy()) {
        return;
    }

    global $product;

    if (! $product instanceof WC_Product) {
        return;
    }

    $summary = $product->get_short_description();
    if ($summary === '') {
        return;
    }

    echo '<p class="ciclo-product-summary">' . wp_kses_post($summary) . '</p>';
}, 12);

add_filter('render_block_woocommerce/product-price', static function (string $content): string {
    if (! is_shop() && ! is_product_taxonomy()) {
        return $content;
    }

    global $post;
    $product = isset($post->ID) ? wc_get_product($post->ID) : false;
    if (! $product) {
        return $content;
    }

    $summary = $product->get_short_description();
    if ($summary === '') {
        return $content;
    }

    return $content . '<p class="ciclo-product-summary">' . wp_kses_post($summary) . '</p>';
}, 20);

add_action('woocommerce_before_shop_loop', static function (): void {
    if (! is_shop()) {
        return;
    }

    $categorySlugs = ['bicicletas', 'accesorios', 'nutricion', 'ropa'];
    $categories = [];
    foreach ($categorySlugs as $slug) {
        $term = get_term_by('slug', $slug, 'product_cat');
        if (! $term || is_wp_error($term)) {
            continue;
        }

        $url = get_term_link($term);
        if (! is_wp_error($url)) {
            $categories[] = '<a href="' . esc_url($url) . '">' . esc_html($term->name) . '</a>';
        }
    }

    echo '<section class="ciclo-shop-intro" aria-label="Información del catálogo">'
        . '<p class="ciclo-eyebrow">CATÁLOGO DE MUESTRA · COSTA RICA</p>'
        . '<p>Doce productos simples para explorar bicicletas, accesorios, nutrición y ropa. Los precios y las existencias son datos de práctica en colones (CRC).</p>'
        . '<nav aria-label="Explorar categorías">' . implode('', $categories) . '</nav>'
        . '</section>';
}, 5);
