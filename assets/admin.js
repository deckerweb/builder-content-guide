/* Local progressive enhancement; native links and forms work without JavaScript. */
(() => {
  const opener = document.querySelector('[data-bcg-changelog]');
  const dialog = document.getElementById('bcg-changelog');
  if (opener && dialog && !dialog.dataset.bcgBound) {
    dialog.dataset.bcgBound = 'true';
    const prototype = window.HTMLDialogElement && HTMLDialogElement.prototype;
    const close = () => {
      if (prototype && typeof prototype.close === 'function' && dialog.open) {
        try { prototype.close.call(dialog); } catch (_) { dialog.removeAttribute('open'); }
      }
      dialog.removeAttribute('open');
      dialog.hidden = true;
      opener.focus();
    };
    opener.addEventListener('click', event => {
      if (!prototype || typeof prototype.showModal !== 'function') return;
      dialog.hidden = false;
      try {
        prototype.showModal.call(dialog);
        if (!dialog.open) throw new Error('Dialog did not open');
        event.preventDefault();
      } catch (_) { dialog.removeAttribute('open'); dialog.hidden = true; }
    });
    dialog.addEventListener('click', event => {
      if (event.target.closest('[data-bcg-close]')) { event.preventDefault(); close(); }
      else if (event.target === dialog) {
        const bounds = dialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close();
      }
    }, true);
    dialog.addEventListener('cancel', event => { event.preventDefault(); close(); });
    dialog.addEventListener('close', close);
  }

  const copy = document.querySelector('[data-bcg-copy]');
  const link = document.getElementById('bcg-guide-link');
  const feedback = document.querySelector('[data-bcg-copy-status]');
  if (copy && link && feedback) {
    copy.addEventListener('click', async () => {
      try {
        if (!navigator.clipboard || !window.isSecureContext) throw new Error('Clipboard unavailable');
        await navigator.clipboard.writeText(link.value);
        feedback.textContent = feedback.dataset.success;
      } catch (_) {
        link.focus();
        link.select();
        feedback.textContent = feedback.dataset.fallback;
      }
    });
  }

  const menuIcon = document.getElementById('bcg-menu-icon');
  const iconPreview = document.querySelector('[data-bcg-menu-icon]');
  if (menuIcon && iconPreview) menuIcon.addEventListener('change', () => { iconPreview.className = 'dashicons ' + menuIcon.value; });

  const picker = document.querySelector('.bcg-picker');
  const select = document.getElementById('bcg-reference');
  const data = document.getElementById('bcg-original-catalog');
  if (!picker || !select || !data) return;
  let catalog;
  try { catalog = JSON.parse(data.textContent); } catch (_) { return; }
  const options = Array.from(select.options).map(o => ({ value: o.value, label: o.textContent }));
  const search = document.getElementById('bcg-original-search');
  const provider = document.getElementById('bcg-original-provider');
  const type = document.getElementById('bcg-original-type');
  const status = document.querySelector('[data-bcg-picker-status]');
  const normalized = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase();
  let selected = select.value;
  const update = () => {
    const query = normalized(search.value.trim());
    let matches = 0;
    const filtered = options.filter(option => {
      if (!option.value) return true;
      const item = catalog[option.value];
      const match = item && (!query || normalized(option.label).includes(query)) &&
        (!provider.value || item.provider === provider.value) && (!type.value || item.type === type.value);
      if (match) matches++;
      // Keep the chosen original even when it lies outside the current search.
      return match || option.value === selected;
    });
    select.replaceChildren(...filtered.map(option => new Option(option.label, option.value, false, option.value === selected)));
    select.value = selected;
    status.textContent = matches ? '' : status.dataset.empty;
  };
  [search, provider, type].forEach(field => field.addEventListener('input', update));
  select.addEventListener('change', () => { selected = select.value; update(); });
  picker.hidden = false;
})();
