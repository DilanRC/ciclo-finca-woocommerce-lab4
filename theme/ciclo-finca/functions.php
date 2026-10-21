<?php
/**
 * Register the Ciclo Finca block theme, editor controls, and storefront assets.
 */
add_action('after_setup_theme', static function (): void {
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

add_filter('register_block_type_args', static function (array $args, string $blockType): array {
    if (! in_array($blockType, [
        'core/group',
        'core/cover',
        'core/heading',
        'core/paragraph',
        'core/button',
        'core/buttons',
        'core/image',
    ], true)) {
        return $args;
    }

    $args['supports']['dimensions'] = array_merge(
        $args['supports']['dimensions'] ?? [],
        ['width' => true, 'height' => true]
    );

    return $args;
}, 10, 2);

add_action('init', static function (): void {
    $themeDirectory = get_stylesheet_directory();
    $themeUri = get_stylesheet_directory_uri();
    $editorScriptPath = $themeDirectory . '/assets/editor-blocks.js';
    $editorStylePath = $themeDirectory . '/assets/css/editor.css';
    $blockStylePath = $themeDirectory . '/assets/css/blocks.css';

    wp_register_script(
        'ciclo-finca-editor-blocks',
        $themeUri . '/assets/editor-blocks.js',
        ['wp-blocks', 'wp-hooks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-plugins', 'wp-edit-post'],
        is_readable($editorScriptPath) ? (string) filemtime($editorScriptPath) : wp_get_theme()->get('Version'),
        true
    );
    wp_add_inline_script(
        'wp-blocks',
        'wp.hooks.addFilter("blocks.registerBlockType", "ciclo-finca/block-dimensions", function (settings, name) {'
            . 'if (!["core/group", "core/cover", "core/heading", "core/paragraph", "core/button", "core/buttons", "core/image"].includes(name)) { return settings; }'
            . 'var supports = settings.supports || {};'
            . 'return { ...settings, supports: { ...supports, dimensions: { ...supports.dimensions, width: true, height: true } } };'
            . '});',
        'after'
    );
    wp_localize_script('ciclo-finca-editor-blocks', 'cicloFincaIconShapes', cicloFincaIconShapes());
    $templateEditorLinks = [];
    if (is_admin() && current_user_can('edit_theme_options')) {
        foreach (['header' => 'wp_template_part', 'footer' => 'wp_template_part'] as $slug => $postType) {
            $template = get_block_template(get_stylesheet() . '//' . $slug, $postType);
            if ($template) {
                $templateEditorLinks[$slug] = add_query_arg(
                    ['p' => '/' . $postType . '/' . $template->id, 'canvas' => 'edit'],
                    admin_url('site-editor.php')
                );
            }
        }
    }
    wp_localize_script('ciclo-finca-editor-blocks', 'cicloFincaTemplateEditorLinks', $templateEditorLinks);
    wp_register_style(
        'ciclo-finca-editor-blocks',
        $themeUri . '/assets/css/editor.css',
        [],
        is_readable($editorStylePath) ? (string) filemtime($editorStylePath) : wp_get_theme()->get('Version')
    );
    wp_register_style(
        'ciclo-finca-blocks',
        $themeUri . '/assets/css/blocks.css',
        [],
        is_readable($blockStylePath) ? (string) filemtime($blockStylePath) : wp_get_theme()->get('Version')
    );

    $renderCallbacks = [
        'icon' => 'cicloFincaRenderIconBlock',
        'icon-link' => 'cicloFincaRenderIconLinkBlock',
        'navigation' => 'cicloFincaRenderNavigationBlock',
        'account' => 'cicloFincaRenderAccountBlock',
        'shop-content' => 'cicloFincaRenderShopContentBlock',
    ];
    foreach (glob($themeDirectory . '/blocks/*/block.json') ?: [] as $blockMetadata) {
        $blockSlug = basename(dirname($blockMetadata));
        $registration = [
            'editor_script' => 'ciclo-finca-editor-blocks',
            'editor_style' => 'ciclo-finca-editor-blocks',
            'style' => 'ciclo-finca-blocks',
        ];
        if (isset($renderCallbacks[$blockSlug])) {
            $registration['render_callback'] = $renderCallbacks[$blockSlug];
        }
        register_block_type(dirname($blockMetadata), $registration);
    }
});

add_action('enqueue_block_editor_assets', static function (): void {
    wp_enqueue_script('ciclo-finca-editor-blocks');
    wp_enqueue_style('ciclo-finca-editor-blocks');
});

add_action('wp_enqueue_scripts', static function (): void {
    $themeDirectory = get_stylesheet_directory();
    $themeUri = get_stylesheet_directory_uri();
    $stylesheets = [
        ['ciclo-finca-base', 'assets/css/base.css', []],
        ['ciclo-finca-header', 'assets/css/header.css', ['ciclo-finca-base']],
        ['ciclo-finca-storefront', 'assets/css/storefront.css', ['ciclo-finca-header']],
        ['ciclo-finca-site', 'assets/css/site.css', ['ciclo-finca-storefront']],
        ['ciclo-finca-commerce', 'assets/css/commerce.css', ['ciclo-finca-site']],
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

function cicloFincaRenderIconBlock(array $attributes): string
{
    $icon = sanitize_key($attributes['icon'] ?? 'bike');
    $size = max(16, min(112, (int) ($attributes['size'] ?? 28)));
    $color = sanitize_hex_color($attributes['iconColor'] ?? '') ?: '#d8f05a';
    $svg = cicloFincaIconSvg($icon, $size);
    if ('' === $svg) {
        return '';
    }

    $dimensions = $attributes['style']['dimensions'] ?? [];
    $fallbackDimensions = '';
    if (empty($dimensions['width'])) {
        $fallbackDimensions .= sprintf('width:%dpx;', $size);
    }
    if (empty($dimensions['height'])) {
        $fallbackDimensions .= sprintf('height:%dpx;', $size);
    }
    $wrapper = get_block_wrapper_attributes([
        'class' => 'ciclo-icon',
        'style' => $fallbackDimensions . 'color:' . $color,
        'aria-hidden' => 'true',
    ]);
    return '<span ' . $wrapper . '>' . $svg . '</span>';
}

function cicloFincaRenderIconLinkBlock(array $attributes): string
{
    $label = trim(wp_strip_all_tags((string) ($attributes['label'] ?? 'Enlace')));
    if ('' === $label) {
        $label = 'Enlace';
    }
    $url = esc_url($attributes['url'] ?? '#');
    $icon = sanitize_key($attributes['icon'] ?? 'none');
    $iconSize = max(14, min(64, (int) ($attributes['iconSize'] ?? 20)));
    $iconColor = sanitize_hex_color($attributes['iconColor'] ?? '') ?: 'currentColor';
    $iconSvg = cicloFincaIconSvg($icon, $iconSize);
    $iconMarkup = '';
    if ('' !== $iconSvg) {
        $iconMarkup = sprintf(
            '<span class="ciclo-icon-link__icon" style="width:%1$dpx;height:%1$dpx;color:%2$s">%3$s</span>',
            $iconSize,
            esc_attr($iconColor),
            $iconSvg
        );
    }
    $target = ! empty($attributes['opensInNewTab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
    $ariaCurrent = cicloFincaIsCurrentUrl($url) ? ' aria-current="page"' : '';
    $wrapper = get_block_wrapper_attributes(['class' => 'ciclo-icon-link']);

    return sprintf('<a %1$s href="%2$s"%3$s%4$s>%5$s<span>%6$s</span></a>',
        $wrapper,
        $url,
        $target,
        $ariaCurrent,
        $iconMarkup,
        esc_html($label)
    );
}

function cicloFincaRenderNavigationBlock(array $attributes, string $content): string
{
    $ariaLabel = trim(sanitize_text_field($attributes['ariaLabel'] ?? 'Navegación principal')) ?: 'Navegación principal';
    $menuLabel = trim(sanitize_text_field($attributes['menuLabel'] ?? 'Menú')) ?: 'Menú';
    $menuIcon = sanitize_key($attributes['menuIcon'] ?? 'menu');
    $menuIconSize = max(16, min(40, (int) ($attributes['menuIconSize'] ?? 20)));
    $menuIconColor = sanitize_hex_color($attributes['menuIconColor'] ?? '') ?: 'currentColor';
    $itemsId = sanitize_html_class($attributes['itemsId'] ?? 'ciclo-primary-nav-items');
    $itemsId = '' !== $itemsId ? $itemsId : 'ciclo-primary-nav-items';
    $buttonIcon = cicloFincaIconSvg($menuIcon, $menuIconSize);
    $wrapper = get_block_wrapper_attributes(['class' => 'ciclo-primary-nav']);

    return sprintf(
        '<div %1$s><button class="ciclo-menu-toggle" type="button" aria-expanded="false" aria-controls="%2$s"><span class="ciclo-menu-toggle__icon" style="color:%3$s" aria-hidden="true">%4$s</span><span>%5$s</span></button><nav id="%2$s" class="ciclo-primary-nav__items" aria-label="%6$s">%7$s</nav></div>',
        $wrapper,
        esc_attr($itemsId),
        esc_attr($menuIconColor),
        $buttonIcon,
        esc_html($menuLabel),
        esc_attr($ariaLabel),
        $content
    );
}

function cicloFincaRenderAccountBlock(array $attributes, string $content): string
{
    $wrapper = get_block_wrapper_attributes(['class' => 'ciclo-editorial ciclo-account-block']);
    $accountContent = do_shortcode('[woocommerce_my_account]');

    return sprintf(
        '<section %1$s>%2$s<div class="ciclo-account-block__form">%3$s</div></section>',
        $wrapper,
        $content,
        $accountContent
    );
}

function cicloFincaRenderShopContentBlock(array $attributes, string $content, WP_Block $block): string
{
    static $renderDepth = 0;

    if ($renderDepth > 0 || ! function_exists('wc_get_page_id') || ! function_exists('is_shop') || ! is_shop()) {
        return '';
    }

    $shopPageId = wc_get_page_id('shop');
    $shopPage = $shopPageId > 0 ? get_post($shopPageId) : null;
    if (! $shopPage || 'publish' !== $shopPage->post_status || '' === trim($shopPage->post_content)) {
        return '';
    }

    ++$renderDepth;
    try {
        return apply_filters('the_content', $shopPage->post_content);
    } finally {
        --$renderDepth;
    }
}

function cicloFincaIconSvg(string $icon, int $size): string
{
    if ('none' === $icon) {
        return '';
    }

    $shapes = cicloFincaIconShapes();
    $iconShapes = $shapes[$icon] ?? $shapes['route'] ?? [];
    if (! is_array($iconShapes)) {
        return '';
    }

    $size = max(14, min(112, $size));
    $markup = '';
    $allowedAttributes = [
        'path' => ['d'],
        'circle' => ['cx', 'cy', 'r'],
        'rect' => ['x', 'y', 'width', 'height', 'rx'],
    ];
    foreach ($iconShapes as $shape) {
        if (! is_array($shape) || ! is_string($shape['tag'] ?? null) || ! isset($allowedAttributes[$shape['tag']]) || ! is_array($shape['attributes'] ?? null)) {
            continue;
        }
        $tag = $shape['tag'];
        $attributes = '';
        foreach ($allowedAttributes[$tag] as $name) {
            if (isset($shape['attributes'][$name]) && is_scalar($shape['attributes'][$name])) {
                $attributes .= sprintf(' %s="%s"', $name, esc_attr((string) $shape['attributes'][$name]));
            }
        }
        $markup .= '<' . $tag . $attributes . '/>';
    }

    return sprintf(
        '<svg viewBox="0 0 24 24" width="%1$d" height="%1$d" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
        $size,
        $markup
    );
}

function cicloFincaIconShapes(): array
{
    static $shapes;
    if (isset($shapes)) {
        return $shapes;
    }

    $path = get_stylesheet_directory() . '/assets/icons.json';
    $contents = is_readable($path) ? file_get_contents($path) : false;
    $decoded = is_string($contents) ? json_decode($contents, true) : null;
    $shapes = is_array($decoded) ? $decoded : [];

    return $shapes;
}

function cicloFincaIsCurrentUrl(string $url): bool
{
    $targetPath = wp_parse_url($url, PHP_URL_PATH);
    $requestPath = wp_parse_url(home_url(add_query_arg([], $GLOBALS['wp']->request ?? '')), PHP_URL_PATH);
    return is_string($targetPath) && is_string($requestPath)
        && untrailingslashit($targetPath) === untrailingslashit($requestPath);
}

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
