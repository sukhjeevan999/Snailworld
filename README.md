# SnailWorld — WordPress Theme

A custom, from-scratch WordPress theme for **SnailWorld**: a garden/snail
aesthetic, fully editable from the WP Customizer, mobile-first responsive,
AdSense-optimized, and built with Core Web Vitals in mind.

No page builder, no build step, and no runtime dependencies — plain PHP,
CSS custom properties, and vanilla JS. Compatible with Elementor/Elementor
Pro if you choose to use it, but not required.

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

1. Zip this theme folder (or upload it as-is via SFTP to `wp-content/themes/snailworld`).
2. In wp-admin, go to **Appearance → Themes** and activate **SnailWorld**.
3. Go to **Settings → Permalinks** and click **Save Changes** once (registers the virtual `/ads.txt` route).
4. Set up your menus: **Appearance → Menus** → assign a menu to **Primary Menu** and, optionally, **Footer Menu**.
5. Set your homepage: **Settings → Reading** → "A static page" with a Homepage set (the theme uses `front-page.php` regardless, so "Your latest posts" also works out of the box).

## What's editable from wp-admin (zero code edits)

Everything lives under **Appearance → Customize**:

- **Garden Design** panel
  - Colors — Light Mode (6 palette colors: base, primary, secondary, accent, text, highlight)
  - Colors — Dark Mode (dark variants of the same 6, + default appearance: auto/light/dark)
  - Typography (3 font pairs, base font-size slider)
  - Garden Texture (on/off subtle dew/texture background overlay)
- **Header** — logo position (left/center), sticky header, CTA button, dark-mode toggle, live search toggle
- **Footer** — column count (2–4), copyright text (`[year]` token), social links
- **Homepage Sections** — hero on/off + copy/image/CTA, featured posts count, category grid on/off, table-of-contents on/off
- **AdSense Zones** panel — 5 zones (header, 2× in-content, sidebar, footer), each with an on/off toggle and a code box for your AdSense/ad-unit snippet, plus an **Ads.txt / Publisher** field that auto-serves a valid `/ads.txt` once you enter your `pub-XXXXXXXXXXXXXXXX` ID

### Category icons & accent colors

Go to **Posts → Categories**, edit (or add) a category, and you'll find
**Garden Icon** (snail, snail shell, trowel, watering can, dew drop,
butterfly, pot, leaf) and **Accent Color** fields. These drive the icon +
color shown on that category's post cards, archive header, and the
homepage category grid.

### Widgets

**Appearance → Widgets**: one **Blog Sidebar** area (used on single posts,
archives, and search — alongside the sticky table of contents on single
posts) and four **Footer Column** areas (only as many are shown as the
footer-column count you set in the Customizer).

## Theme structure

```
snailworld/
├── style.css                  theme header + all site CSS (custom properties driven)
├── functions.php               setup, enqueues, includes
├── inc/
│   ├── customizer.php          all Customizer sections/settings
│   ├── customizer-controls.php custom range / font-pair / textarea controls
│   ├── customizer-output.php   turns settings into an inline CSS var block + dark-mode boot script
│   ├── icons.php                inline garden/snail SVG icon set (Lucide-style line art)
│   ├── category-meta.php       per-category icon + color (term meta) admin UI
│   ├── template-tags.php       entry meta, pagination, author box, social icons…
│   ├── ad-zones.php            ad zone rendering + in-content paragraph injection
│   ├── ajax-search.php         REST endpoint for the AJAX live search
│   ├── toc.php                 table-of-contents extraction + heading-id injection
│   ├── breadcrumbs.php         visual breadcrumbs (shared item list with schema-json-ld.php)
│   ├── schema-json-ld.php      Organization, BreadcrumbList, Article JSON-LD
│   ├── ads-txt.php             virtual /ads.txt served from the Publisher ID field
│   └── performance.php         Core Web Vitals hygiene (lazy-load, trimmed <head>, etc.)
├── template-parts/             post-card, content-single, content-page, content-none, hero, toc
├── assets/js/                  main.js, live-search.js, toc.js, customizer-preview.js
├── header.php / footer.php / sidebar.php / searchform.php / comments.php
└── front-page.php, home.php, index.php, single.php, page.php,
    archive.php, category.php, search.php, 404.php
```

## Notes on specific features

- **Dark mode** — toggled from the header, persisted via a first-party
  `sw_theme` cookie so it carries across pages, with an inline boot script
  in `<head>` that applies the right mode before first paint (no flash).
- **Live search** — debounced (300ms) fetch against a small custom REST
  route (`/wp-json/snailworld/v1/search`, registered in `inc/ajax-search.php`)
  that returns garden/snail-icon-ready result cards in one round trip.
- **Table of contents** — auto-built from `<h2>`/`<h3>` in the post body,
  sticky on desktop (≥1088px) next to the sidebar, a collapsible
  `<details>` element everywhere else, with scrollspy highlighting.
- **Reading progress** — a slim bar fixed to the top of singular posts,
  with a small snail riding the leading edge of the fill as you scroll
  (`inc/toc.php` pairs with the visual bar in `header.php`/`assets/js/main.js`).
- **Structured data** — `inc/schema-json-ld.php` prints Organization schema
  on every page, BreadcrumbList schema (built from the same items as the
  visual breadcrumbs) wherever breadcrumbs show, and Article schema on
  single posts. Validate with [Google's Rich Results Test](https://search.google.com/test/rich-results)
  after you launch.
- **Ad zones** — each zone only renders when toggled on **and** has code
  saved; the two in-content zones are injected after the 3rd and 8th
  paragraph via a content filter (skipped gracefully on shorter posts, so
  an ad never lands directly under the title).
- **ads.txt** — once you add your AdSense Publisher ID in Customize →
  AdSense Zones → Ads.txt / Publisher, `yourdomain.com/ads.txt` is served
  automatically (virtual, no file upload needed). Leave it blank and the
  route serves a reminder comment instead.
- **Performance** — native image lazy-loading, `fetchpriority=high` on the
  likely LCP image, deferred non-critical JS, emoji script/RSD/oEmbed
  discovery links removed from `<head>`, Google Fonts preconnect.

## Manual production step (optional)

There is no bundler in this theme by design (fewer moving parts, no
Node.js needed to deploy). If you want minified CSS/JS for a marginal
extra performance boost, run your minifier of choice over `style.css` and
the files in `assets/js/` as an extra build step in your deploy pipeline —
the theme will keep working with either the minified or unminified files
since they're referenced by their existing filenames.

## Elementor compatibility

`add_theme_support( 'elementor' )` / `'elementor-pro'` are declared in
`functions.php`. If you install Elementor, create an Elementor page/template
and assign it as needed — the theme's `header.php`/`footer.php` wrap
around it exactly like they do any other page.
