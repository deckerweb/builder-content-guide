/* Native WordPress document panel; links only, no original content is read or saved. */
(() => {
  const wp = window.wp;
  const config = window.bcgEditorGuide;
  if (!config || !wp?.plugins || !wp?.editor?.PluginDocumentSettingPanel || !wp?.data?.useSelect) return;
  const el = wp.element.createElement;
  const types = { page: 'page', post: 'post', wp_block: 'pattern', wp_navigation: 'navigation', wp_template_part: 'template_part' };
  function GuidePanel() {
    const document = wp.data.useSelect(select => {
      const editor = select('core/editor');
      return { type: editor.getCurrentPostType(), id: editor.getCurrentPostId() };
    }, []);
    const source = types[document.type];
    if (!source || !document.id || (source === 'template_part' && !String(document.id).includes('//'))) return null;
    const reference = source + ':' + document.id;
    const href = url => url + '&reference=' + encodeURIComponent(reference);
    return el(wp.editor.PluginDocumentSettingPanel, { name: 'bcg-content-guide', title: config.title },
      el('p', null, el('a', { href: href(config.readUrl) }, config.related)),
      el('p', null, el('a', { href: href(config.addUrl) }, config.add)));
  }
  wp.plugins.registerPlugin('bcg-content-guide', { render: GuidePanel, icon: 'book' });
})();
