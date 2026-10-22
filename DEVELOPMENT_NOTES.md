# Estatein WordPress Theme — Development Notes

**Author:** Ali Tarek · **Theme:** custom classic theme (PHP templates), no page builder · **Design source:** Real Estate Business Website UI Template – Dark Theme (Figma)

## 1. Process

1. **Read the design first.** I took the Figma variables (colours Grey/08–60, Purple/60–75), typography (Urbanist 500/600/700) and the exact spacing of each Home section from Figma Dev Mode, and mapped the 1920px desktop, 1440px laptop and 390px mobile frames to fluid CSS values.
2. **Prioritised by the brief's guidance:** a complete, polished Home page (header, hero, feature strip, properties, testimonials, FAQs, CTA, footer) plus working Properties archive, Property detail and Contact pages, rather than every page half-built.
3. **Modelled content before markup:** decided which content is repeatable (post types), which is page-specific (ACF on the Home page) and which is site-wide (Customizer).
4. **Built and verified on a real WordPress install,** checking rendered pages at 1920, 1440 and 390px, PHP warnings, form submissions and 404s.

## 2. Theme architecture and choices

- **Custom classic theme.** Templates are plain PHP with reusable template parts (`template-parts/components/*`). Header, footer, section heading, cards, carousel controls and the CTA are each written once and reused.
- **Modular `functions.php`.** It only loads files in `/inc`, one concern per file (setup, assets, post types, fields, customizer, forms, SEO, demo content).
- **Content model.**
  - *Custom post types:* **Property** (public, archive at `/properties/`), **Testimonial**, **FAQ** (non-public, editable in the dashboard), and **Inquiry** (stores form submissions).
  - *ACF (free version only):* property details (price, beds, baths, type, location, featured), testimonial client details, and a "Home Page Sections" group with hero, stats, feature cards and section headings. Fields are **registered in PHP**, so they are version-controlled and deploy with the theme.
  - *Customizer:* banner, header button, closing CTA, copyright and social links, because they appear on every page.
  - *Menus:* primary navigation, and footer columns built from one menu (top-level items become column headings).
- **Graceful defaults.** Every field is read through `estatein_field()` with the Figma copy as the default, so the site looks right on first activation and does not break if ACF is deactivated.
- **One-time demo content.** On activation the theme creates the pages, menus and sample listings once, and never overwrites existing content, so reviewers see a complete site immediately.

## 3. Responsiveness, UX and accessibility

- Fluid type and spacing with `clamp()`, tuned to the three Figma frames, plus breakpoints at 1440, 1200, 1024 and 768px. Carousels show 3, 2 or 1 card per view.
- Semantic landmarks, a skip link, one `h1` per page, `aria-labelledby` on sections, labelled form fields, visible focus styles, `aria-expanded` on the mobile menu and FAQ toggles, and screen-reader text on repeated links ("View Property Details: Seaside Serenity Villa").
- Carousels are native scroll-snap containers, so touch, trackpad and keyboard (arrow keys) work, and the prev/next buttons and "01 of 06" counter stay in sync.
- All motion respects `prefers-reduced-motion`.

## 4. Performance and SEO

- No jQuery and no front-end framework: one small deferred script (4 KB minified) and one stylesheet (25 KB minified). Minified builds load in production; sources load with `SCRIPT_DEBUG`.
- Icons are inline SVG (no icon font, no extra requests). Images use registered sizes, `srcset`, lazy loading, and the hero image loads eagerly with `fetchpriority="high"`.
- Only the Urbanist weights in use are loaded, with `display=swap` and preconnect hints. Emoji scripts and block-library CSS are removed where not needed.
- Meta description, Open Graph tags, and JSON-LD (`RealEstateAgent` on Home, `RealEstateListing` with price on property pages). These switch off automatically if Yoast, Rank Math, SEOPress or AIOSEO is installed.

## 5. Forms

The contact and newsletter forms use WordPress's `admin-post.php` with a nonce, a honeypot, server-side sanitisation and validation, and accessible inline client-side validation. Each submission is **saved as a private Inquiry in the dashboard and emailed to the admin**, so nothing is lost on free hosts where PHP mail is disabled. No form plugin is needed.

## 6. Enhancements beyond the design

- Rotating "Discover Your Dream Property" badge with a hover state, linking to the listings.
- Hero stats count up on load.
- FAQ "Read More" expands the full answer in place, without a page load.
- Property-type filter on the Properties archive.
- "Send an Inquiry" on a property pre-fills the contact form with that property's name.
- Dismissible announcement banner that remembers the choice, without a layout jump on reload.
- Sections fade in on scroll.

## 7. Plugins and tools

- **Advanced Custom Fields (free)** — field UI for editors. Optional at runtime.
- No other plugins.
- Tools: Figma (variables and Dev Mode specs), PHP lint, headless Chromium screenshots at desktop, laptop and mobile widths, csso and terser for minification. Claude (AI) was used to speed up scaffolding and review; all code was checked, tested on a live WordPress install, and adjusted by me.

## 8. Known limitations and next steps

- About Us and Services use the default page template; their dedicated Figma layouts are the next pages to build.
- Property pages use one featured image; a gallery (ACF gallery or core gallery block) would match the design's detail page more closely.
- In a production project, the post types would move to a small site plugin so content survives a theme change.
