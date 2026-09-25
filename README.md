# Women's Fight Agency — WordPress Theme

A dark, Bangla, multi-page agency theme. Content lives in real WordPress
Pages, so it can be edited two ways — pick whichever is easier for you.

## First activation

1. Upload and activate this theme (`Appearance → Themes`).
2. WordPress will offer to install **Elementor** automatically (the theme
   declares it as a recommended plugin) — install and activate it if you
   want drag-and-drop editing. It is optional; the site works without it.
3. On activation the theme automatically creates every page (Home, the
   five service pages, Case Study, Portfolio, Action Plan, Package,
   AI Agent, Contact Us), builds the navigation menu, and sets Home as
   the site's front page. A green confirmation notice appears in
   `wp-admin` once this is done, with a direct link to open the Home
   page for editing.
4. If that notice never appears (e.g. the theme was uploaded and
   activated before this happened), go to `Appearance → Themes` — a blue
   notice with a **"এখনই সেটআপ করুন"** button lets you run it manually.

## Editing content

- **Block editor (no plugin needed):** `Pages → All Pages → [pick a
  page] → Edit`. Text, images and links can be changed directly; larger
  structural changes are easiest as raw HTML there.
- **Elementor (optional, visual drag-and-drop):** open any page and
  click **"Edit with Elementor."** The page's real design loads
  immediately — it is not blank — because every page is pre-saved as a
  single full-width Elementor **HTML** widget holding that page's
  markup. From there you can drag in ordinary Elementor widgets above,
  below, or around it, or click the HTML widget itself to edit its code
  directly from Elementor's built-in code panel.

## Troubleshooting: a page (often Home) shows blank

Some hosting providers' one-click WordPress installers pre-create a
placeholder page at a common slug — most often `home` — before this theme
is ever activated. Older versions of this theme saw that placeholder,
assumed it was already set up, and skipped filling it in, leaving it
permanently empty. This is now fixed: on every setup run the theme fills
in any page that is genuinely empty (and only if nobody has since built
it out in Elementor), and republishes it if it was sitting in Draft or
Trash.

If a page is still showing blank on a site created with an older version
of this theme, go to `Appearance → Themes` and click **"পেজ ও মেনু আবার
সিঙ্ক করুন"** (visible once setup has already run once). It is safe to
run any number of times — it only ever fills in genuinely empty pages or
republishes an unpublished one, and never touches a page with real
content or real Elementor work.

## Contact form

The form on the Contact page is layout-only (see `inc/content/contact.php`)
— it shows a "thank you" message on submit but does not send email yet.
Wire it up with a plugin such as **Contact Form 7** or **WPForms**, or
replace the `<form>` markup with a shortcode from either plugin.

## Structure

- `functions.php` — theme setup, the one-time page/menu installer, and
  the Elementor-data helper that keeps pages pre-loaded in Elementor.
- `header.php` / `footer.php` / `page.php` / `front-page.php` — templates.
- `inc/content/{slug}.php` — each page's real HTML, one `return` per file.
- `assets/` — logo images and `main.js` (mobile menu, AI Agent
  calculator, contact-form demo handler).
