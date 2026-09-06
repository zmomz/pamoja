import { pt } from './helpers.ts';
import type { SeedBlock } from './helpers.ts';

export interface SeedSiteSettings {
  _id: string;
  _type: 'siteSettings';
  contactEmail: string;
  socialLinks: { label: string; url: string }[];
  landAcknowledgment: SeedBlock[];
  plausibleDomain?: string;
}

export const siteSettings: SeedSiteSettings = {
  _id: 'siteSettings',
  _type: 'siteSettings',
  contactEmail: 'hello@pamoja.example',
  socialLinks: [],
  landAcknowledgment: pt('[Land acknowledgment wording to be confirmed.]'),
  plausibleDomain: 'pamoja.example',
};
