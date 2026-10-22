# Estatein — Custom WordPress Theme

A custom WordPress theme built from the **Real Estate Business Website UI Template – Dark Theme** Figma file (Produce UI). Dark, fully responsive, editable through custom post types, ACF fields and the Customizer.

- **Live demo:** `https://YOUR-SITE.example` ← replace after deploying
- **Requires:** WordPress 6.4+ (tested on 6.6), PHP 7.4+
- **Recommended plugin:** [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (free). The theme still renders without it, using the design's copy as defaults.

## Install

1. Upload the `estatein` folder to `wp-content/themes/` (or zip it and use **Appearance → Themes → Add New → Upload**).
2. Install and activate **Advanced Custom Fields** (free).
3. Activate **Estatein**. On first activation the theme creates, once and without overwriting anything:
   - Pages: Home (set as the front page), About Us, Services, Contact Us (Contact template), Terms & Conditions
   - 6 sample properties, 6 testimonials, 6 FAQs (copy from the Figma file)
   - The primary menu and the footer column menu
   - Pretty permalinks (`/%postname%/`) if none were set
4. Add images (see below).

## Images to export from Figma

The Figma asset server cannot be reached from a build script, so images are added by hand. Every image is optional — the layout has designed fallbacks — but add these for full fidelity:

| Where | What to export (Figma layer) | How to add it |
|---|---|---|
| Logo | `Header › Navigation Bar › Logo` → SVG | Save as `assets/images/logo.svg`, or upload in **Appearance → Customize → Site Identity** |
| Home hero building | `Home Page – Desktop › Container › Image` → PNG 2x | Upload in **Pages → Home → Hero image** (ACF). Or save a WebP as `assets/images/hero-building.webp` |
| Property photos | The 3 card images in *Featured Properties* → PNG/JPG 2x | **Properties → (property) → Featured image** |
| Client photos | The 3 testimonial avatars → PNG 2x | **Testimonials → (testimonial) → Featured image** |
| Icons (optional) | Any icon → SVG | Overwrite the file with the same name in `assets/icons/` |

## Editing content

| Content | Where |
|---|---|
| Hero text, buttons, stats, image; feature cards; section headings | **Pages → Home** (ACF "Home Page Sections") |
| Listings (price, beds, baths, type, location, featured flag) | **Properties** |
| Client reviews (headline, quote, name, location, rating, photo) | **Testimonials** |
| Questions (excerpt = card teaser, content = full answer) | **FAQs** |
| Announcement banner, header button, closing CTA, copyright, social links | **Appearance → Customize → Estatein** |
| Header links | **Appearance → Menus → Primary** |
| Footer columns (top-level items = headings, children = links) | **Appearance → Menus → Footer Columns** |
| Contact & newsletter submissions | **Inquiries** (also emailed to the admin address) |

Drag-and-drop order uses the **Order** field (Page Attributes) on properties, testimonials and FAQs.

## Structure

```
estatein/
├── style.css                 Theme header only
├── functions.php             Loads /inc modules
├── inc/
│   ├── helpers.php           estatein_field(), icons, images, links, formatting
│   ├── setup.php             Theme supports, menus, image sizes
│   ├── enqueue.php           Assets (minified in production), resource hints
│   ├── post-types.php        Property, Testimonial, FAQ + archive query/filter
│   ├── acf-fields.php        ACF field groups registered in PHP
│   ├── customizer.php        Site-wide options
│   ├── forms.php             Contact & newsletter handlers, Inquiries post type
│   ├── seo.php               Meta description, Open Graph, JSON-LD
│   └── demo-content.php      One-time seeding on activation
├── template-parts/
│   ├── components/           logo, section-heading, cards, carousel-nav, cta, page-hero
│   └── home/                 hero (+ features), properties, testimonials, faq
├── front-page.php · page.php · page-contact.php · single.php · index.php · 404.php
├── archive-property.php · single-property.php · searchform.php
└── assets/
    ├── css/main.css (+ main.min.css)
    ├── js/main.js (+ main.min.js)
    ├── icons/*.svg           Inline SVG icons (inherit currentColor)
    └── images/               Exported Figma images go here
```

## Build

The minified files are committed, so no build step is needed to run the theme. After editing CSS or JS:

```bash
npm install
npm run build
```

Set `define( 'SCRIPT_DEBUG', true );` in `wp-config.php` to load the readable sources instead of the minified files.

## Deploying to free hosting (InfinityFree / Wasmer)

1. Create a WordPress site from the host's installer.
2. Upload the theme zip under **Appearance → Themes → Add New → Upload**, install ACF, activate the theme.
3. Upload the images listed above.
4. Check **Settings → Permalinks** shows "Post name", and save once.
