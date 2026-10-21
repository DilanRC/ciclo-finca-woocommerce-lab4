(() => {
  const { registerBlockType } = wp.blocks;
  const { createElement: el, Fragment } = wp.element;
  const { InnerBlocks, InspectorControls, PanelColorSettings, RichText, useBlockProps } = wp.blockEditor;
  const { PanelBody, RangeControl, SelectControl, TextControl } = wp.components;

  const iconOptions = [
    { label: 'Sin icono', value: 'none' },
    { label: 'Inicio', value: 'home' },
    { label: 'Tienda', value: 'shop' },
    { label: 'Bicicleta', value: 'bike' },
    { label: 'Accesorios', value: 'gear' },
    { label: 'Cuenta', value: 'account' },
    { label: 'Carrito', value: 'cart' },
    { label: 'Buscar', value: 'search' },
    { label: 'Ruta', value: 'route' },
    { label: 'Pago', value: 'payment' },
    { label: 'Ubicación', value: 'pin' },
    { label: 'Casco', value: 'helmet' },
    { label: 'Herramientas', value: 'tools' },
    { label: 'Botella', value: 'bottle' },
    { label: 'Ropa', value: 'jersey' },
    { label: 'Menú', value: 'menu' },
    { label: 'Cerrar', value: 'close' },
  ];

  const iconShapes = window.cicloFincaIconShapes || {};

  const renderIcon = (name, size = 24) => {
    const shapes = iconShapes[name] || iconShapes.route;
    return el(
      'svg',
      {
        viewBox: '0 0 24 24',
        width: size,
        height: size,
        fill: 'none',
        stroke: 'currentColor',
        strokeWidth: 1.8,
        strokeLinecap: 'round',
        strokeLinejoin: 'round',
        'aria-hidden': 'true',
        focusable: 'false',
      },
      shapes.map(({ tag, attributes }, index) => el(tag, { ...attributes, key: index })),
    );
  };

  const iconControl = (icon, setAttributes, label = 'Icono', attribute = 'icon') =>
    el(SelectControl, {
      label,
      value: icon,
      options: iconOptions,
      onChange: (value) => setAttributes({ [attribute]: value }),
    });

  const colorControl = (title, value, onChange) =>
    el(PanelColorSettings, {
      title,
      colorSettings: [{ label: 'Color', value, onChange }],
    });

  wp.hooks.addFilter('blocks.registerBlockType', 'ciclo-finca/block-dimensions', (settings, name) => {
    if (!['core/group', 'core/cover', 'core/heading', 'core/paragraph', 'core/button', 'core/buttons', 'core/image'].includes(name)) {
      return settings;
    }

    return {
      ...settings,
      supports: {
        ...(settings.supports || {}),
        dimensions: {
          ...(settings.supports?.dimensions || {}),
          width: true,
          height: true,
        },
      },
    };
  });

  registerBlockType('ciclo-finca/icon', {
    edit: ({ attributes, setAttributes }) => {
      const { icon, size, iconColor } = attributes;
      const dimensions = attributes.style?.dimensions || {};
      const blockProps = useBlockProps({
        className: 'ciclo-icon',
        style: { width: dimensions.width || size + 'px', height: dimensions.height || size + 'px', color: iconColor },
      });

      return el(
        Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: 'Ajustes del icono', initialOpen: true },
            iconControl(icon, setAttributes),
            el(RangeControl, {
              label: 'Tamaño',
              value: size,
              min: 16,
              max: 112,
              step: 2,
              onChange: (value) => setAttributes({ size: value }),
            }),
          ),
          colorControl('Color del icono', iconColor, (value) => setAttributes({ iconColor: value || '#d8f05a' })),
        ),
        el('span', { ...blockProps, 'aria-hidden': 'true' }, renderIcon(icon, size)),
      );
    },
    save: () => null,
  });

  registerBlockType('ciclo-finca/icon-link', {
    edit: ({ attributes, setAttributes }) => {
      const { label, url, icon, iconSize, iconColor, opensInNewTab } = attributes;
      const blockProps = useBlockProps({
        className: 'ciclo-icon-link',
        href: url,
        onClick: (event) => event.preventDefault(),
      });

      return el(
        Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: 'Enlace', initialOpen: true },
            el(TextControl, { label: 'Dirección', type: 'url', value: url, onChange: (value) => setAttributes({ url: value }) }),
            iconControl(icon, setAttributes),
            el(RangeControl, {
              label: 'Tamaño del icono',
              value: iconSize,
              min: 14,
              max: 64,
              step: 2,
              onChange: (value) => setAttributes({ iconSize: value }),
            }),
            el('label', { className: 'ciclo-block-checkbox' },
              el('input', {
                type: 'checkbox',
                checked: opensInNewTab,
                onChange: (event) => setAttributes({ opensInNewTab: event.target.checked }),
              }),
              ' Abrir en una pestaña nueva',
            ),
          ),
          colorControl('Color del icono', iconColor, (value) => setAttributes({ iconColor: value || '' })),
        ),
        el(
          'a',
          blockProps,
          icon !== 'none' && el('span', {
            className: 'ciclo-icon-link__icon',
            style: { width: iconSize + 'px', height: iconSize + 'px', color: iconColor || 'currentColor' },
          }, renderIcon(icon, iconSize)),
          el(RichText, {
            tagName: 'span',
            value: label,
            allowedFormats: [],
            placeholder: 'Texto del enlace',
            onChange: (value) => setAttributes({ label: value }),
          }),
        ),
      );
    },
    save: () => null,
  });

  registerBlockType('ciclo-finca/navigation', {
    edit: ({ attributes, setAttributes }) => {
      const { ariaLabel, menuLabel, menuIcon, menuIconSize, menuIconColor, itemsId } = attributes;
      const blockProps = useBlockProps({ className: 'ciclo-primary-nav' });
      const template = [
        ['ciclo-finca/icon-link', { label: 'Inicio', url: '/', icon: 'home', className: 'ciclo-nav-link' }],
        ['ciclo-finca/icon-link', { label: 'Tienda', url: '/shop/', icon: 'shop', className: 'ciclo-nav-link' }],
        ['ciclo-finca/icon-link', { label: 'Bicicletas', url: '/product-category/bicicletas/', icon: 'bike', className: 'ciclo-nav-link' }],
        ['ciclo-finca/icon-link', { label: 'Accesorios', url: '/product-category/accesorios/', icon: 'gear', className: 'ciclo-nav-link' }],
      ];

      return el(
        Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: 'Ajustes de navegación', initialOpen: true },
            el(TextControl, { label: 'Etiqueta accesible', value: ariaLabel, onChange: (value) => setAttributes({ ariaLabel: value }) }),
            el(TextControl, { label: 'Texto del botón móvil', value: menuLabel, onChange: (value) => setAttributes({ menuLabel: value }) }),
            iconControl(menuIcon, setAttributes, 'Icono del botón móvil', 'menuIcon'),
            el(RangeControl, {
              label: 'Tamaño del icono móvil',
              value: menuIconSize,
              min: 16,
              max: 40,
              step: 2,
              onChange: (value) => setAttributes({ menuIconSize: value }),
            }),
            colorControl('Color del icono móvil', menuIconColor, (value) => setAttributes({ menuIconColor: value || '#f7f9f4' })),
            el(TextControl, { label: 'Identificador de enlaces', value: itemsId, onChange: (value) => setAttributes({ itemsId: value }) }),
          ),
        ),
        el(
          'div',
          blockProps,
          el('div', { className: 'ciclo-menu-toggle ciclo-menu-toggle--editor', style: { color: menuIconColor }, 'aria-hidden': 'true' },
            renderIcon(menuIcon, menuIconSize),
            el('span', null, menuLabel),
          ),
          el('nav', { className: 'ciclo-primary-nav__items ciclo-primary-nav__items--editor', 'aria-label': ariaLabel },
            el(InnerBlocks, { allowedBlocks: ['ciclo-finca/icon-link'], template }),
          ),
        ),
      );
    },
    save: () => el(InnerBlocks.Content),
  });
})();
