(() => {
  const cartLink = document.querySelector('.ciclo-header-cart');
  const cartCount = cartLink?.querySelector('.ciclo-header-cart__count');
  const cartPage = document.body.classList.contains('woocommerce-cart');
  let cartCountRequest = 0;
  let cartRefreshTimer;

  const setCartCount = (count) => {
    if (!cartLink || !cartCount) {
      return;
    }

    const itemCount = Math.max(0, Number(count) || 0);
    cartCount.textContent = String(itemCount);
    cartLink.setAttribute('aria-label', `Carrito: ${itemCount} ${itemCount === 1 ? 'artículo' : 'artículos'}`);
  };

  const refreshCartCount = async () => {
    const request = ++cartCountRequest;

    try {
      const response = await fetch(window.cicloFincaCartEndpoint, {
        cache: 'no-store',
        credentials: 'same-origin',
      });

      if (!response.ok) {
        return;
      }

      const cart = await response.json();
      const itemCount = Number(cart.items_count);

      if (request === cartCountRequest && Number.isInteger(itemCount)) {
        setCartCount(itemCount);
      }
    } catch {
      // Keep the server-rendered count if the session cart endpoint is unavailable.
    }
  };

  const scheduleCartCountRefresh = () => {
    window.clearTimeout(cartRefreshTimer);
    cartRefreshTimer = window.setTimeout(refreshCartCount, 180);
  };

  if (cartLink && cartCount) {
    // Blocks update the session cart without refreshing classic WooCommerce fragments.
    document.body.addEventListener('wc-blocks_removed_from_cart', scheduleCartCountRefresh);
    document.body.addEventListener('wc-blocks_added_to_cart', scheduleCartCountRefresh);
  }

  if (cartLink && cartCount && cartPage) {
    const cartContent = document.querySelector('main .woocommerce');

    if (cartContent) {
      new MutationObserver(scheduleCartCountRefresh).observe(cartContent, { childList: true, subtree: true });
      cartContent.addEventListener('change', (event) => {
        if (event.target.matches('.cart_item input.qty, .wc-block-components-quantity-selector__input')) {
          scheduleCartCountRefresh();
        }
      });
      cartContent.addEventListener('click', (event) => {
        if (event.target.closest('.wc-block-components-quantity-selector__button')) {
          scheduleCartCountRefresh();
        }
      });
    }

    scheduleCartCountRefresh();
  }

  const button = document.querySelector('.ciclo-menu-toggle');
  const navigation = document.querySelector('.ciclo-primary-nav');

  if (!button || !navigation) {
    return;
  }

  document.body.classList.add('ciclo-menu-ready');
  const desktopQuery = window.matchMedia('(min-width: 86.0625rem)');
  navigation.hidden = !desktopQuery.matches;

  const closeMenu = (returnFocus = false) => {
    navigation.hidden = true;
    navigation.classList.remove('is-open');
    button.setAttribute('aria-expanded', 'false');

    if (returnFocus) {
      button.focus();
    }
  };

  button.addEventListener('click', () => {
    const shouldOpen = button.getAttribute('aria-expanded') !== 'true';
    button.setAttribute('aria-expanded', String(shouldOpen));
    navigation.hidden = !shouldOpen;
    navigation.classList.toggle('is-open', shouldOpen);
  });

  navigation.addEventListener('click', (event) => {
    if (!desktopQuery.matches && event.target.closest('a')) {
      closeMenu();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
      closeMenu(true);
    }
  });

  document.addEventListener('click', (event) => {
    if (button.getAttribute('aria-expanded') === 'true'
      && !navigation.contains(event.target)
      && !button.contains(event.target)) {
      closeMenu();
    }
  });

  desktopQuery.addEventListener('change', (event) => {
    if (event.matches) {
      navigation.hidden = false;
      navigation.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
    } else {
      closeMenu();
    }
  });
})();
