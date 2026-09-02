// @ts-check
import { defineConfig } from 'astro/config';
import sanity from '@sanity/astro';
import react from '@astrojs/react';
import sitemap from '@astrojs/sitemap';

const projectId = process.env.PUBLIC_SANITY_PROJECT_ID || 'pamoja-placeholder';
const dataset = process.env.PUBLIC_SANITY_DATASET || 'production';

export default defineConfig({
  site: process.env.PUBLIC_SITE_URL || 'https://pamoja-collective.netlify.app',
  output: 'static',
  integrations: [
    react(),
    sitemap({ filter: (page) => !page.includes('/studio') && !page.includes('/thank-you') }),
    sanity({
      projectId,
      dataset,
      useCdn: true,
      apiVersion: '2025-01-01',
      studioBasePath: '/studio',
    }),
  ],
});
