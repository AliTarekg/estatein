# Estatein WordPress Theme — Development Notes

**Author:** Ali Tarek · **Theme:** custom classic theme (PHP templates), no page builder · **Design:** Real Estate Business Website UI Template – Dark Theme (Figma, Produce UI)

## 1. Process

1. **Read the design first.** Six pages (Home, About, Properties, Property Details, Services, Contact) in three frames each (1920, 1440, 390). I took colours, the Urbanist type scale and spacing from the Figma variables and mapped the frames to fluid `clamp()` values. Photos, the logo, the line artwork and the icons were exported from the file, so nothing is a stand-in.
2. **Modelled content before markup.** Repeatable things are post types (properties, testimonials, FAQs, team), page-specific copy is ACF fields, and site-wide details are Customizer options.
3. **Built shared pieces first** — header, footer, section heading, cards, carousels, forms — then the pages, home page first.
4. **Verified in a real WordPress** (WordPress Playground: WordPress 7.1.3, PHP 8.5): activated the theme from the dashboard with ACF installed, checked each page in Chromium at 375, 390, 768, 1280 and 1440px, ran scripted functional, form, accessibility and PHP-notice checks, and fixed what they found (see §6). The release zip was then installed on a clean WordPress **without** ACF, and every page rendered from the built-in defaults with no PHP notices.

## 2. Theme architecture

- **Classic theme with template parts.** `front-page`, `page-about`, `page-services`, `page-contact`, `archive-property`, `single-property`, plus `page`, `single`, `index` (blog and search) and `404`. Components such as the page hero, section heading, feature strip, stats, property/testimonial/FAQ/team/client cards, carousel controls and CTA are each written once and reused.
- **Modular `functions.php`.** It only loads `/inc` files, one concern each: setup, assets, post types and filters, fields, page sections, gallery, property details, Customizer, forms, SEO, sample content.
- **One source for page copy.** `inc/page-sections.php` declares each About/Services/Contact section once. That definition provides the Figma copy as defaults, generates the ACF fields (empty fields show the default as placeholder), and feeds the templates through `estatein_section()`. Fields cannot drift from templates, and pages render fully without ACF.
- **Property data.** ACF (free) fields for price, beds, baths, area, year built, type, location, tagline and features. The pricing breakdown computes totals from stored inputs, with sensible estimates when fields are empty. The gallery is a small native meta box using the core media modal, saved as REST-registered post meta, because ACF free has no gallery field.
- **Native queries.** The Properties page filters the main query in `pre_get_posts` (keyword, location, type, price, size, build year), so counts and pagination are standard WordPress.

## 3. Content-management experience

Editors use familiar screens. Pages have tabbed ACF sections. Properties have a details side panel, a features and pricing box, and a gallery box. Team, Testimonials and FAQs are simple post lists ordered by Page Attributes. Menus drive the header and footer, and the Customizer holds the banner, contact details, CTA and social links. Sample content, including images in the Media Library, is created once on activation. It never overwrites edits, and is versioned so older installs receive only new items.

## 4. Responsive, accessibility, SEO, performance, security

- **Responsive:** fluid type and spacing with `clamp()`; breakpoints at 1440, 1200, 1024 and 768px; carousels show 3/2/1 cards; no horizontal overflow at any tested width.
- **Accessibility:** landmarks, skip link, one `h1` per page, labelled fields with inline errors linked by `aria-describedby`, visible focus styles, `aria-expanded`/`aria-pressed`/`aria-current` where state changes, keyboard-scrollable carousels, and `prefers-reduced-motion` support. An automated axe-core scan (WCAG 2.1 A/AA rules) reports 0 violations on every page.
- **SEO:** meta description, Open Graph, canonical URLs (filtered listings point to the clean list), and JSON-LD (`RealEstateAgent`, `RealEstateListing` with price). These step aside when Yoast, Rank Math, SEOPress or AIOSEO is active.
- **Performance:** no jQuery or framework on the front end; one deferred script (5 KB minified, 2 KB gzipped) and one stylesheet (42 KB minified, 9 KB gzipped); inline SVG icons; WebP images (1.2 MB for all design images) with `srcset`, lazy loading, and an eagerly loaded, high-priority hero; only the Urbanist weights in use.
- **Security:** every form goes through one handler with a nonce, a honeypot, a per-IP rate limit, whitelisted choice values, sanitisation and length limits. Output is escaped everywhere. The gallery save checks a nonce and `edit_post`.

## 5. Tools and plugins

- **Plugins:** Advanced Custom Fields (free), optional at runtime. No form, SEO or page-builder plugin.
- **Tools:** Figma (MCP export of images, icons and specs), WordPress Playground CLI, Playwright + Chromium (screenshots, functional tests), axe-core, csso and terser.
- **AI:** Claude was used to speed up implementation, testing and review. I reviewed, ran and adjusted the code; the tests above were run against the real theme.

## 6. Issues found by testing and fixed

- Sample content never appeared when a host activated the theme without the activation hook; seeding now also runs once from the dashboard.
- The "Build Year" filter returned nothing, because `year` is a reserved WordPress query var; the parameter is now `built`.
- Clicking the terms checkbox right after fixing a field could miss: removing an error message on blur shifted the layout under the pointer. Errors now clear while typing.
- Gallery edits in the block editor did not save, because changes to a classic meta box don't mark the post as changed; the gallery is now REST post meta updated through the editor store.

## 7. Limitations and next steps

- Not yet deployed to a public host; the release zip and steps are ready (README, "Deploying").
- Properties are a filtered, paginated grid instead of the Figma carousel, which suits search better.
- Clients, offices and social links are sample content; replace them before launch.
- In production the post types would move to a small site plugin, so content survives a theme switch.
