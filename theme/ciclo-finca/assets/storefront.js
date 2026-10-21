(() => {
  const navigation = document.querySelector('.wp-block-ciclo-finca-navigation');
  const button = navigation?.querySelector('.ciclo-menu-toggle');
  const items = navigation?.querySelector('.ciclo-primary-nav__items');

  if (!navigation || !button || !items) {
    return;
  }

  const desktopQuery = window.matchMedia('(min-width: 86.0625rem)');

  const closeMenu = (returnFocus = false) => {
    navigation.classList.remove('is-open');
    items.hidden = !desktopQuery.matches;
    button.setAttribute('aria-expanded', 'false');

    if (returnFocus) {
      button.focus();
    }
  };

  const syncLayout = () => {
    document.body.classList.add('ciclo-menu-ready');

    if (desktopQuery.matches) {
      navigation.classList.remove('is-open');
      items.hidden = false;
      button.setAttribute('aria-expanded', 'false');
      return;
    }

    closeMenu();
  };

  button.addEventListener('click', () => {
    const shouldOpen = button.getAttribute('aria-expanded') !== 'true';
    button.setAttribute('aria-expanded', String(shouldOpen));
    navigation.classList.toggle('is-open', shouldOpen);
    items.hidden = !shouldOpen;

    if (shouldOpen) {
      items.querySelector('a')?.focus({ preventScroll: true });
    }
  });

  items.addEventListener('click', (event) => {
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
    if (button.getAttribute('aria-expanded') === 'true' && !navigation.contains(event.target)) {
      closeMenu();
    }
  });

  desktopQuery.addEventListener('change', syncLayout);
  syncLayout();
})();
