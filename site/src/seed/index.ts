import { branches } from './branches.ts';
import { partners } from './partners.ts';
import { events } from './events.ts';
import { services } from './services.ts';
import { homePage } from './homePage.ts';
import { siteSettings } from './siteSettings.ts';

export { branches, partners, events, services, homePage, siteSettings };

/** Every seed document, in dependency order (branches and partners first). */
export const allSeedDocuments = [
  ...branches,
  ...partners,
  ...events,
  ...services,
  homePage,
  siteSettings,
];
