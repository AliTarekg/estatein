# Estatein — Custom WordPress Theme

A custom classic WordPress theme built from the Figma community file **Real Estate Business Website UI Template – Dark Theme** (Produce UI). It covers all six designed pages — Home, About Us, Properties, Property Details, Services and Contact — at desktop, laptop, tablet and mobile widths. Content is editable through custom post types, ACF fields and the Customizer, and forms work without plugins.

- **Live demo:** https://rebw.ifree.page/ (InfinityFree free hosting; first load can be slow)
- **Repository:** https://github.com/AliTarekg/estatein
- **Development notes:** [DOCUMENTATION.md](DOCUMENTATION.md) — process, architecture decisions, plugins and tools
- **Requires:** WordPress 6.4+ · PHP 7.4+. Tested on WordPress 7.1.3 / PHP 8.5 (WordPress Playground).
- **Recommended plugin:** [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (free). The theme renders fully without it, using the design's copy as defaults; ACF only adds the editing screens.

## Install

1. Download `estatein.zip` (or build it — see [Development](#development)).
2. In WordPress: **Appearance → Themes → Add New → Upload Theme**, choose the zip, install, **Activate**.
3. Install and activate **Advanced Custom Fields** (Plugins → Add New → search "Advanced Custom Fields").

On activation the theme creates sample content once, without overwriting anything that already exists:

- Pages with their templates: Home (set as the front page), About Us, Services, Contact Us, Terms & Conditions
- 6 properties (photos, gallery, details, pricing), 6 testimonials, 6 FAQs, 4 team members — images are imported into the Media Library
- The primary menu and the footer column menu
- Pretty permalinks (`/%postname%/`) if none were set

If a host activates themes without firing WordPress's activation hook, the same content is created on the first visit to the dashboard by an administrator.

## Editing content

| Content | Where |
|---|---|
| Home hero, stats, image, feature cards, section headings | **Pages → Home** (ACF "Home Page Sections") |
| About / Services / Contact copy, cards, offices, gallery | **Pages → About Us / Services / Contact Us** (ACF "… Page Sections"; empty fields show the design copy as placeholder) |
| Listings: price, beds, baths, area, year built, type, location, tagline, features, pricing | **Properties** (side panel + "Features & Pricing") |
| Property photo gallery | **Properties → (property) → Property Gallery** box |
| Client reviews | **Testimonials** |
| Questions (excerpt = card teaser, content = full answer) | **FAQs** |
| Team (name, role, photo, email, X link) | **Team** |
| Banner, header button, contact details, closing CTA, copyright, social links | **Appearance → Customize → Estatein** |
| Header links / footer columns | **Appearance → Menus** (Primary, Footer Columns — top-level items become headings) |
| Form submissions | **Inquiries** (also emailed to the admin address) |

Order of properties, testimonials, FAQs and team uses the **Order** field (Page Attributes).

## Structure

```
estatein/
├── style.css                    Theme header
├── functions.php                Loads the /inc modules
├── inc/
│   ├── helpers.php              Field reader, icons, images, formatting, feature cards
│   ├── setup.php                Theme supports, menus, image sizes, menu highlighting
│   ├── enqueue.php              Assets (minified unless SCRIPT_DEBUG), resource hints
│   ├── post-types.php           Property, Testimonial, FAQ, Team + archive search/filters
│   ├── acf-fields.php           ACF field groups for Home, properties, testimonials, team
│   ├── page-sections.php        About/Services/Contact sections: defaults + generated ACF fields
│   ├── property-gallery.php     Gallery meta box (core media modal, REST post meta)
│   ├── property-details.php     Features list and pricing breakdown
│   ├── customizer.php           Site-wide options
│   ├── forms.php                All forms: validation, nonce, honeypot, rate limit, Inquiries
│   ├── seo.php                  Meta description, Open Graph, canonical, JSON-LD
│   └── demo-content.php         Versioned, non-destructive sample content
├── template-parts/
│   ├── components/              logo, page-hero, section-heading, features, stats, cards, carousel-nav, cta
│   ├── forms/property-match.php "Let's Make it Happen" request form
│   └── home/                    hero, properties, testimonials, faq
├── front-page.php · page-about.php · page-services.php · page-contact.php
├── archive-property.php · single-property.php · page.php · single.php · index.php · 404.php · searchform.php
└── assets/
    ├── css/main.css (+ .min)    js/main.js (+ .min), js/admin-gallery.js
    ├── icons/*.svg              Inline SVG icons (Figma), inherit currentColor
    └── images/                  Figma photos (WebP), logo and line artwork (SVG)
```

## Development

```bash
npm install          # csso + terser
npm run build        # main.css → main.min.css, main.js → main.min.js
npm run package      # build, then dist/estatein.zip from the last commit (git archive)
```

Minified files are committed, so no build is needed to run the theme. Add `define( 'SCRIPT_DEBUG', true );` to `wp-config.php` to load the readable sources.

**Local WordPress without installing PHP or MySQL** (Node 20+):

```bash
npx @wp-playground/cli@latest server --auto-mount --port=9400
# then open http://127.0.0.1:9400/wp-admin (admin / password), install ACF, activate Estatein
```

## Deploying

Any host with PHP 7.4+ and MySQL/MariaDB works (shared hosting, managed WordPress, or a free host such as InfinityFree).

1. Create the WordPress site with the host's installer.
2. Upload `dist/estatein.zip` (Appearance → Themes → Upload), activate it, install ACF.
3. Visit the dashboard once as an administrator (this finishes the sample content if the host skipped the activation hook).
4. Settings → Permalinks: confirm "Post name" and save once.
5. Appearance → Customize → Estatein: set real social links (they default to `#`) and contact details.
6. Check that email works (Tools → Site Health), or rely on the Inquiries screen, which stores every submission.

## Known limitations

- Sample clients, offices and team links are demonstration content; client "Visit Website" buttons only appear when a URL is entered.
- Properties are listed as a paginated grid with filters, instead of the Figma carousel, so that search and filtering stay usable.
- The form rate limit (5 submissions per form per IP every 10 minutes) uses `REMOTE_ADDR`. Behind a reverse proxy, raise or disable it with the `estatein_form_rate_limit` filter.
- Post types live in the theme to keep the assessment self-contained; in production they would move to a small site plugin so content survives a theme change.
