# Estatein WordPress Theme — Development Notes

**Author:** Ali Tarek · **Type:** custom classic theme (PHP templates, no page builder) · **Design:** "Real Estate Business Website UI Template – Dark Theme" (Figma, Produce UI) · **Live:** https://rebw.ifree.page/ · **Repository:** https://github.com/AliTarekg/estatein

## 1. Development process

I started in Figma, not the editor. The file has six pages (Home, About, Properties, Property Details, Services, Contact), each at 1920, 1440 and 390px. I took the colour variables, the Urbanist type scale and the spacing from it, and exported the real photos, logo, line artwork and icons.

Before writing markup I decided where each piece of content lives. Repeating content is a post type, page copy is fields on that page, and site-wide details are Customizer options. With that settled, templates only render data and never own it.

I built the shared layer first (header, footer, cards, carousels, forms), then Home, then the inner pages, finishing each before starting the next. Testing ran on a real install (WordPress 7.1.3, PHP 8.5) at 375–1440px, with scripted browser checks of navigation, filters, forms and links, plus an axe-core scan. It caught real bugs, all fixed: a filter colliding with a core query var, a layout shift that made clicks miss, and gallery changes not saving in the block editor.

## 2. Theme architecture and decisions

- **Modular bootstrap.** `functions.php` only loads `/inc` files, one concern each (setup, assets, post types, fields, forms, SEO, sample content).
- **Template hierarchy.** `front-page.php`, the page templates `page-about.php`, `page-services.php` and `page-contact.php`, plus `archive-property.php` and `single-property.php`. `index.php`, `page.php`, `single.php` and `404.php` cover the rest. All use the standard Loop.
- **Reusable parts.** `template-parts/components/` holds the page hero, section heading, feature strip, stats, cards, carousel controls and CTA cards. They take data through `get_template_part()` arguments, so one property card serves both the Home carousel and the Properties grid.
- **Post types.** `register_post_type` for Property (public archive at `/properties/`), and for Testimonial, FAQ and Team, which are editable but not public because they only appear inside pages. A private Inquiry type stores submissions. No taxonomies: property type is a fixed list, simpler as a select field.
- **ACF registered in PHP.** `acf_add_local_field_group` keeps fields in version control and deploys them with the theme. Only free ACF features are used. For About, Services and Contact, `inc/page-sections.php` declares each section once; that definition supplies the Figma copy as defaults, generates the fields, and feeds `estatein_section()`, so fields and templates cannot drift.
- **Fallbacks.** Every field read has a design default, so a fresh install looks finished and nothing breaks if ACF is deactivated.
- **Gallery.** ACF free has no gallery field, so a small meta box uses the core media modal. It saves to post meta registered with `register_post_meta` (`show_in_rest`), which the block editor recognises as a change.
- **Customizer and menus.** Banner, contact details, closing CTA and social links are site-wide options. Two menu locations, primary and footer columns, each with a design-matching fallback.
- **Sample content.** `after_switch_theme` creates the pages with their templates, menus, listings, testimonials, FAQs and team, and imports images into the Media Library. It never overwrites existing content, is versioned, and also runs once from `admin_init`, because some hosts skip the activation hook.

## 3. Responsiveness, UX and accessibility

- Fluid type and spacing with `clamp()`, and breakpoints at 1440, 1200, 1024 and 768px. Carousels show three, two or one card.
- Carousels are native scroll-snap containers, so touch, trackpad and arrow keys work without a slider library.
- Landmarks, a skip link, one `h1` per page, and labelled fields with errors linked through `aria-describedby`. Focus is visible, and `aria-expanded`, `aria-pressed` and `aria-current` reflect state. All motion stops under `prefers-reduced-motion`.

## 4. Performance and SEO

- No jQuery on the front end: one vanilla script loaded with the `defer` strategy, and one stylesheet. Minified builds load in production and the sources under `SCRIPT_DEBUG`. Assets are versioned with `filemtime`.
- Icons are inline SVG. Photos are WebP, with registered sizes, `srcset`, lazy loading, and an eager, high-priority hero.
- Only the Urbanist weights in use are loaded, with `display=swap` and preconnect hints. Emoji scripts are removed, and block-library CSS is dequeued on the home page and the Properties archive.
- Meta description, Open Graph tags, a canonical for filtered listings, and JSON-LD (`RealEstateAgent`, and `RealEstateListing` with price). All of it steps aside when a major SEO plugin is active.

## 5. Forms and security

All four forms (contact, property inquiry, property request, newsletter) post to `admin-post.php` and go through one spec-driven handler. It checks a `wp_nonce_field` nonce, a honeypot and a per-IP rate limit (filterable for proxies). It sanitises fields by type, accepts select values only from the offered options, and caps lengths. Submissions are saved as private Inquiries before `wp_mail` runs, so nothing is lost on hosts without mail. Output is escaped throughout.

## 6. Enhancements beyond the design

- Keyword search and five filters (location, type, price, size, build year) in `pre_get_posts`, with native pagination.
- A pricing breakdown calculated from the listing price, with estimates for empty fields.
- Prefilled contact messages from the pricing "Learn More" and team "Say Hello" links.
- A dismissible banner that remembers the choice, FAQs that expand in place, count-up stats, scroll reveal, and Google Maps directions on office cards.

## 7. Plugins and tools

- **Advanced Custom Fields (free):** the editing screens; optional at runtime thanks to the fallbacks. No form, SEO or builder plugins.
- **Tools:** Figma, WordPress Playground CLI, Playwright, axe-core, and csso and terser (`npm run build`, `npm run package`).

AI assistance (Claude) was used to speed up scaffolding and review; all code was reviewed, tested and adjusted by me.

## 8. Known limitations and next steps

- Properties are a filtered, paginated grid rather than the Figma carousel, a deliberate trade-off for search.
- Tested in Chromium only; Safari and Firefox still need a pass. PHP 7.4 support is by code review; I tested on PHP 8.5.
- Clients, offices and social links are sample content and need real data before launch.
- In production the post types would move to a small site plugin, so content survives a theme change.
