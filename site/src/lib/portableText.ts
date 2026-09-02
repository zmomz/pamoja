import type { PortableText, PortableTextBlock, PortableTextSpan } from './data';

function escapeHtml(text: string): string {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function renderSpan(span: PortableTextSpan): string {
  let html = escapeHtml(span.text);
  for (const mark of span.marks ?? []) {
    if (mark === 'strong') html = `<strong>${html}</strong>`;
    else if (mark === 'em') html = `<em>${html}</em>`;
    else if (mark === 'underline') html = `<u>${html}</u>`;
  }
  return html;
}

function renderBlock(block: PortableTextBlock): string {
  const inner = (block.children ?? []).map(renderSpan).join('');
  switch (block.style) {
    case 'h2':
      return `<h2>${inner}</h2>`;
    case 'h3':
      return `<h3>${inner}</h3>`;
    case 'h4':
      return `<h4>${inner}</h4>`;
    case 'blockquote':
      return `<blockquote>${inner}</blockquote>`;
    default:
      return `<p>${inner}</p>`;
  }
}

/** Inner HTML of one block (spans only, no wrapping element). */
export function renderBlockInner(block: PortableTextBlock): string {
  return (block.children ?? []).map(renderSpan).join('');
}

/** Plain text of one block — handy for detecting editorial placeholder notes. */
export function blockPlainText(block: PortableTextBlock): string {
  return (block.children ?? []).map((span) => span.text).join('');
}

/**
 * Minimal portable text renderer: paragraph blocks with strong/em/underline
 * span marks. Returns an HTML string; render with set:html in Astro.
 */
export function renderPortableText(blocks: PortableText | undefined | null): string {
  return (blocks ?? [])
    .filter((block) => block && block._type === 'block')
    .map(renderBlock)
    .join('\n');
}
