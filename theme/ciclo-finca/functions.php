<?php
/**
 * Load Ciclo Finca's storefront styles and behavior after Twenty Twenty-Five.
 */
add_action('wp_enqueue_scripts', static function (): void {
    $themeDirectory = get_stylesheet_directory();
    $themeUri = get_stylesheet_directory_uri();
    $stylesheets = [
        ['ciclo-finca-base', 'assets/css/base.css', []],
        ['ciclo-finca-storefront', 'assets/css/storefront.css', ['ciclo-finca-base']],
        ['ciclo-finca-commerce', 'assets/css/commerce.css', ['ciclo-finca-storefront']],
        ['ciclo-finca-responsive', 'assets/css/responsive.css', ['ciclo-finca-commerce']],
    ];

    foreach ($stylesheets as [$handle, $relativePath, $dependencies]) {
        $stylePath = $themeDirectory . '/' . $relativePath;
        $styleVersion = is_readable($stylePath) ? (string) filemtime($stylePath) : wp_get_theme()->get('Version');
        wp_enqueue_style($handle, $themeUri . '/' . $relativePath, $dependencies, $styleVersion);
    }

    $scriptPath = get_stylesheet_directory() . '/assets/storefront.js';
    $scriptVersion = is_readable($scriptPath) ? (string) filemtime($scriptPath) : wp_get_theme()->get('Version');
    wp_enqueue_script(
        'ciclo-finca-storefront',
        get_stylesheet_directory_uri() . '/assets/storefront.js',
        [],
        $scriptVersion,
        ['in_footer' => true]
    );
    wp_add_inline_script(
        'ciclo-finca-storefront',
        'window.cicloFincaCartEndpoint = ' . wp_json_encode(rest_url('wc/store/v1/cart')) . ';',
        'before'
    );
}, 25);

add_action('wp_head', static function (): void {
    $fontBase = get_stylesheet_directory_uri() . '/assets/fonts/';
    printf(
        '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
        esc_url($fontBase . 'barlow-condensed-bold.woff2')
    );
    printf(
        '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
        esc_url($fontBase . 'dm-sans-variable.woff2')
    );
    echo '<script>(function(){try{var root=document.documentElement;root.classList.add("ciclo-fonts-pending");var reveal=function(){root.classList.remove("ciclo-fonts-pending");root.classList.add("ciclo-fonts-ready")};if(!document.fonts){reveal();return}Promise.all([document.fonts.load("700 1em Barlow Condensed"),document.fonts.load("400 1em DM Sans")]).then(reveal,reveal);window.setTimeout(reveal,2500)}catch(error){document.documentElement.classList.remove("ciclo-fonts-pending")}})();</script>' . "\n";
}, 1);

function cicloFincaIcon(string $name): string
{
    $icons = [
        'home' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3.5 10.5 8.5-7 8.5 7v9a1 1 0 0 1-1 1h-5v-6h-5v6h-5a1 1 0 0 1-1-1v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
        'shop' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 9.5h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-10Z" stroke="currentColor" stroke-width="1.8"/><path d="m3 9 2-5h14l2 5M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0M9 20.5v-6h6v6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
        'bike' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="6" cy="17" r="3.2" stroke="currentColor" stroke-width="1.7"/><circle cx="18" cy="17" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="m6 17 4-7 4 7H6Zm4-7h4l2 7m-6-7-1.5-2H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'gear' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5 14 5l2.5-.2.8 2.4 2.2 1.2-.7 2.4.7 2.4-2.2 1.2-.8 2.4L14 16.6l-2 1.5-2-1.5-2.5.2-.8-2.4-2.2-1.2.7-2.4-.7-2.4 2.2-1.2.8-2.4L10 5l2-1.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="11.5" r="2.8" stroke="currentColor" stroke-width="1.6"/></svg>',
        'account' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'cart' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 4h2.2l2.1 11.1a1.8 1.8 0 0 0 1.8 1.5h8.7a1.8 1.8 0 0 0 1.8-1.5L21 8H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.5" cy="20" r="1.2" fill="currentColor"/><circle cx="17.5" cy="20" r="1.2" fill="currentColor"/></svg>',
        'search' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3" stroke="currentColor" stroke-width="1.8"/><path d="m15.5 15.5 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'menu' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
    ];

    return $icons[$name] ?? '';
}

function cicloFincaPrimaryNavigation(): string
{
    $bikeUrl = get_term_link('bicicletas', 'product_cat');
    $accessoryUrl = get_term_link('accesorios', 'product_cat');
    $productId = get_queried_object_id();
    $bikeActive = is_product_category('bicicletas') || (is_product() && has_term('bicicletas', 'product_cat', $productId));
    $accessoryActive = is_product_category('accesorios') || (is_product() && has_term('accesorios', 'product_cat', $productId));
    $items = [
        ['Inicio', home_url('/'), 'home', is_front_page()],
        ['Tienda', wc_get_page_permalink('shop'), 'shop', is_shop()],
        ['Bicicletas', is_wp_error($bikeUrl) ? home_url('/shop/') : $bikeUrl, 'bike', $bikeActive],
        ['Accesorios', is_wp_error($accessoryUrl) ? home_url('/shop/') : $accessoryUrl, 'gear', $accessoryActive],
    ];

    $links = '';
    foreach ($items as [$label, $url, $icon, $active]) {
        $current = $active ? ' aria-current="page"' : '';
        $links .= '<a class="ciclo-nav-link' . ($active ? ' is-current' : '') . '" href="' . esc_url($url) . '"' . $current . '>'
            . '<span class="ciclo-nav-link__icon">' . cicloFincaIcon($icon) . '</span>'
            . '<span>' . esc_html($label) . '</span></a>';
    }

    return '<nav id="ciclo-primary-nav" class="ciclo-primary-nav" aria-label="Navegación principal">' . $links . '</nav>';
}
add_shortcode('ciclo_primary_navigation', 'cicloFincaPrimaryNavigation');

function cicloFincaHeaderSearch(): string
{
    return '<form class="ciclo-header-search" role="search" method="get" action="' . esc_url(home_url('/')) . '">'
        . '<label class="screen-reader-text" for="ciclo-header-search-input">Buscar productos</label>'
        . '<input id="ciclo-header-search-input" type="search" name="s" placeholder="Buscar en la tienda" value="' . esc_attr(get_search_query()) . '">'
        . '<input type="hidden" name="post_type" value="product">'
        . '<button type="submit" aria-label="Buscar productos">' . cicloFincaIcon('search') . '</button>'
        . '</form>';
}
add_shortcode('ciclo_header_search', 'cicloFincaHeaderSearch');

function cicloFincaCartLink(): string
{
    $count = function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
    $countLabel = sprintf(_n('%d artículo', '%d artículos', $count, 'ciclo-finca'), $count);

    return '<a class="ciclo-header-cart" href="' . esc_url(wc_get_page_permalink('cart')) . '" aria-label="Carrito: ' . esc_attr($countLabel) . '">'
        . '<span class="ciclo-header-action__icon">' . cicloFincaIcon('cart') . '</span>'
        . '<span class="ciclo-header-action__label">Carrito</span>'
        . '<span class="ciclo-header-cart__count" aria-live="polite" aria-atomic="true">' . esc_html((string) $count) . '</span>'
        . '</a>';
}

function cicloFincaHeaderActions(): string
{
    $account = '<a class="ciclo-header-account" href="' . esc_url(wc_get_page_permalink('myaccount')) . '">'
        . '<span class="ciclo-header-action__icon">' . cicloFincaIcon('account') . '</span>'
        . '<span class="ciclo-header-action__label">Cuenta</span></a>';

    return '<div class="ciclo-header-actions">' . $account . cicloFincaCartLink() . '</div>';
}
add_shortcode('ciclo_header_actions', 'cicloFincaHeaderActions');

add_filter('woocommerce_add_to_cart_fragments', static function (array $fragments): array {
    $fragments['a.ciclo-header-cart'] = cicloFincaCartLink();
    return $fragments;
});

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
        . '<p class="ciclo-eyebrow">EQUIPO PARA CADA RODADA · COSTA RICA</p>'
        . '<p>Encuentra bicicletas, accesorios, nutrición y ropa para tu próxima salida. Precios en colones y disponibilidad indicada en cada producto.</p>'
        . '<nav aria-label="Explorar categorías">' . implode('', $categories) . '</nav>'
        . '</section>';
}, 5);
