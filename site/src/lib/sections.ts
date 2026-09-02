export type SectionId =
  | 'home'
  | 'ground'
  | 'roots'
  | 'who-we-are'
  | 'our-work'
  | 'what-we-offer'
  | 'who-we-grow-with'
  | 'seeds'
  | 'contact';

export interface SiteSection {
  id: Exclude<SectionId, 'home' | 'contact'>;
  /** Tree part name, e.g. "Soil" */
  part: string;
  /** Nav label, e.g. "The Ground We Share" */
  label: string;
  path: string;
  anchor: string;
}

export const SECTIONS: SiteSection[] = [
  { id: 'ground', part: 'Soil', label: 'The Ground We Share', path: '/ground', anchor: '#ground' },
  { id: 'roots', part: 'Roots', label: 'Our Roots', path: '/roots', anchor: '#roots' },
  { id: 'who-we-are', part: 'Trunk', label: 'Who We Are', path: '/who-we-are', anchor: '#who-we-are' },
  { id: 'our-work', part: 'Branches', label: 'Our Work', path: '/our-work', anchor: '#our-work' },
  { id: 'what-we-offer', part: 'Canopy', label: 'What We Offer', path: '/what-we-offer', anchor: '#what-we-offer' },
  { id: 'who-we-grow-with', part: 'Grove', label: 'Who We Grow With', path: '/who-we-grow-with', anchor: '#who-we-grow-with' },
  { id: 'seeds', part: 'Seeds', label: 'Seeds', path: '/seeds', anchor: '#seeds' },
];

export const CONTACT = { id: 'contact' as const, label: 'Contact', path: '/contact' };
