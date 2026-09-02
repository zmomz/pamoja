// Shared shapes and helpers for seed documents.
// Kept to erasable TypeScript syntax so scripts/import-seed.mjs can load these
// modules directly with Node's type stripping.

export interface SeedSlug {
  _type: 'slug';
  current: string;
}

export interface SeedRef {
  _type: 'reference';
  _ref: string;
}

export interface SeedSpan {
  _type: 'span';
  _key: string;
  text: string;
  marks: string[];
}

export interface SeedBlock {
  _type: 'block';
  _key: string;
  style: 'normal';
  markDefs: unknown[];
  children: SeedSpan[];
}

let keyCounter = 0;
const nextKey = () => `k${(keyCounter++).toString(36)}`;

export const slug = (current: string): SeedSlug => ({ _type: 'slug', current });

export const ref = (_ref: string): SeedRef => ({ _type: 'reference', _ref });

/** Build a portable text array from plain paragraphs. */
export const pt = (...paragraphs: string[]): SeedBlock[] =>
  paragraphs.map((text) => ({
    _type: 'block',
    _key: nextKey(),
    style: 'normal',
    markDefs: [],
    children: [{ _type: 'span', _key: nextKey(), text, marks: [] }],
  }));
