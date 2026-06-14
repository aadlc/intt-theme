# INTT Theme — Claude Session Context

## What went wrong and why we are starting fresh

During development on the previous LocalWP instance (`intt`), a webpack build was run with `output.clean: true` combined with `output.path` pointing to the theme root directory. This caused webpack to delete every file in the theme folder that was not a webpack build artifact — effectively wiping the entire theme. All PHP, HTML, JSON, and CSS files were lost.

The theme was recovered from two sources: VS Code local file history and Claude Code JSONL session transcripts. After recovery, the WordPress database on that instance had saved copies of all templates (`wp_template` post type) that were overriding the recovered file-based templates. Every attempt to clear the DB templates (via Site Editor reset and WP-CLI) failed to produce clean file-based control.

**Decision:** Start a fresh LocalWP WordPress instance. Import only content (posts, tramites, media) via XML export — not the database. Rebuild the theme from source files on the new instance, piece by piece, with the new slug `intt-theme`.

The source files to reference during the rebuild are at:
```
C:\Users\aangulodelacruz\Local Sites\intt\app\public\wp-content\themes\intt-portal-theme\
```

The new theme lives at:
```
C:\Users\aangulodelacruz\Local Sites\<new-site>\app\public\wp-content\themes\intt-theme\
```

---

## Theme identity (new)

| Field | Value |
|---|---|
| Theme Name | INTT Theme |
| Theme Slug / Folder | `intt-theme` |
| Text Domain | `intt-theme` |
| Pattern prefix | `intt-theme/` |
| Template part theme attr | `"theme":"intt-theme"` |

**Important:** The old source files use `intt-portal-theme` everywhere. When copying content to the new theme, update all slug references to `intt-theme`.

---

## Site context

- **Client:** INTT — Instituto Nacional de Transporte Terrestre (Venezuela)
- **Goal:** Transform a department-organized archive into a citizen-task-organized service portal
- **Three pillars:** Trámites (action) / Consultas (verification) / Institucional (information)

---

## Custom Post Types & Taxonomies

- **CPT `tramite`** — government procedures. Registered in `inc/cpt-tramites.php`. Meta field: `descripcion_corta` (visible in admin list column and Quick Edit). Supports: title, editor, thumbnail, excerpt.
- **Taxonomy `tipo_tramite`** — hierarchical. Categories: Licencias (Conductores), Vehículos (Registro/Placas), Profesionales (Carga/Transporte Público). Drives hub pages.
- **CPT `alerta_intt`** — alert bar posts. Registered in `inc/alert-bar.php`. Meta field: `fecha_expiracion`.

---

## Design system (from theme.json)

**Layout:** contentSize `620px`, wideSize `1200px`

**Spacing tokens:**
`sp-0` `sp-8` `sp-16` `sp-24` `sp-32` `sp-40` `sp-48` `sp-64` `sp-80`
(clamp-based fluid values — see source `theme.json` for exact sizes)

**Typography:** Font family `Georama` — single variable TTF: `assets/fonts/Georama-VariableFont_wdth,wght.ttf`. Declared in `theme.json` as one `fontFace` entry with `"fontWeight": "100 900"`. WP 6.0+ supports variable font weight ranges natively. Font size slugs: `heading-1` through `heading-6`, `body1`, `body2`, `caption`, `overline`

**Font file note:** The original design referenced 4 static woff2 files (Regular/Medium/SemiBold/Bold). Those don't exist — switched to the variable TTF from the Google Fonts download. Impact: zero on blocks/patterns/templates (they use the CSS custom property, not the file). woff2 conversion is a deferred TODO.

**Color palettes:** `azul-marino-*` (50–900), `azul-electrico-*` (50–900), `amarillo-oro-*` (50–900), `rojo-carmesi-*` (50–900), `gris-*` (50–900), `white`

**Global styles:** Body text `gris-800`, background `white`, headings `azul-marino-600`, links `azul-electrico-500`, buttons `azul-electrico-500` bg with pill radius.

---

## Build approach — Stub first, then fill

**Do not copy files directly from the old theme.** Build stubs for every file first so the theme activates with zero errors, then fill each file with real content one at a time.

### What a stub looks like

| File type | Stub content |
|---|---|
| `style.css` | Full theme header comment + empty (see Phase 1) |
| `index.php` | `<?php // Silence is golden.` |
| `theme.json` | Copy as-is from source (pure config, no logic errors) |
| `functions.php` | Minimal — enqueue style, register categories, require inc/, register blocks |
| `inc/*.php` | `<?php // stub` |
| `parts/*.html` | `<!-- stub -->` |
| `templates/index.html` | Minimal with header + footer parts (see Phase 2) |
| `templates/*.html` | Just header + footer template parts (renders a blank page, no error) |
| `patterns/*.php` | PHP header comment only — registers pattern with empty body |
| `blocks/*/block.json` | Copy as-is from source |
| `blocks/*/render.php` | `<?php // stub` |

---

## Phase 1 — Scaffold (create all stubs)

Create the full folder structure and every stub file. After this phase the theme should activate cleanly with no PHP errors and no missing-file warnings.

### Folder structure to create
```
intt-theme/
  assets/
    fonts/          ← copy Georama woff2 files from old theme
    js/
      megamenu.js   ← stub (empty)
      tramite-quick-edit.js ← stub (empty)
  blocks/
    alert-bar/
      block.json    ← copy from source
      render.php    ← stub
      alert-bar.js  ← stub (empty)
    hub-list/
      block.json    ← copy from source
      render.php    ← stub
    hub-sidebar/
      block.json    ← copy from source
      render.php    ← stub
    megamenu/
      block.json    ← copy from source
      render.php    ← stub
      index.js      ← stub (empty, requires npm build later)
      index.asset.php ← copy from source
    tramite-descripcion/
      block.json    ← copy from source
      render.php    ← stub
  inc/
    alert-bar.php   ← stub
    cpt-tramites.php ← stub
    default-pages.php ← stub
    footer.php      ← stub
    megamenu.php    ← stub
  parts/
    banner-ministerio.html ← stub
    footer.html     ← stub
    header.html     ← stub
    megamenu-panel.html ← stub
    site-header.html ← stub
  patterns/
    antetitulo-titulo.php
    hero-home.php
    megamenu-panel.php
    noticias-home.php
    otros-tramites.php
    radio-banner.php
    seccion-otros-tramites.php
    seccion-tramites-frecuentes.php
    tarjeta-tramite-frecuente.php
    tramite-proceso-renovacion-licencia.php
    tramite-requisitos-licencia.php
    tramites-ciudadanos.php
    tramites-empresas.php
    tramites-frecuentes.php
  templates/
    front-page.html
    index.html
    page-hub.html
    single-tramite.html
    single.html
    taxonomy-tipo_tramite.html
  functions.php
  index.php
  style.css
  theme.json
  package.json    ← copy from source
```

### style.css stub (theme header required)
```css
/*
Theme Name: INTT Theme
Description: Portal institucional del INTT — Instituto Nacional de Transporte Terrestre.
Version: 1.0.0
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.0
Text Domain: intt-theme
*/
```

### templates/index.html stub (minimal — required for Site Editor)
```html
<!-- wp:template-part {"slug":"site-header","theme":"intt-theme","tagName":"header"} /-->
<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group"></main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer","theme":"intt-theme","tagName":"footer"} /-->
```

### All other templates stub
```html
<!-- wp:template-part {"slug":"site-header","theme":"intt-theme","tagName":"header"} /-->
<!-- wp:template-part {"slug":"footer","theme":"intt-theme","tagName":"footer"} /-->
```

### Pattern stub (example — repeat for all patterns with correct title/slug)
```php
<?php
/**
 * Title: Hero Home
 * Slug: intt-theme/hero-home
 * Categories: intt-componentes
 * Inserter: false
 */
?>
```

### functions.php stub (minimal working version)
```php
<?php
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'intt-style', get_stylesheet_uri(), [], '1.0.0' );
} );

add_action( 'after_setup_theme', function () {
    add_editor_style( 'style.css' );
} );

add_action( 'init', function () {
    register_block_pattern_category( 'intt-tramites',    [ 'label' => 'INTT — Trámites' ] );
    register_block_pattern_category( 'intt-componentes', [ 'label' => 'INTT — Componentes' ] );
}, 1 );

require_once get_template_directory() . '/inc/cpt-tramites.php';
require_once get_template_directory() . '/inc/alert-bar.php';
require_once get_template_directory() . '/inc/megamenu.php';
require_once get_template_directory() . '/inc/footer.php';
require_once get_template_directory() . '/inc/default-pages.php';

add_action( 'init', function () {
    register_block_type( get_template_directory() . '/blocks/alert-bar' );
    register_block_type( get_template_directory() . '/blocks/hub-list' );
    register_block_type( get_template_directory() . '/blocks/hub-sidebar' );
    register_block_type( get_template_directory() . '/blocks/megamenu' );
    register_block_type( get_template_directory() . '/blocks/tramite-descripcion' );
}, 5 );
```

**After Phase 1:** Activate the theme. Confirm:
- Theme appears in WP Admin → Appearance → Themes ✓
- No PHP fatal errors ✓
- Site Editor is available (Appearance → Editor, not just Design) ✓
- All templates show a lock icon (file-based, not DB) ✓

---

## Phase 2 — Foundation files

Fill these files with real content from the source theme (updating `intt-portal-theme` → `intt-theme`):

1. `theme.json` — copy entire file from source, no slug changes needed
2. `style.css` — add real CSS from source
3. `inc/cpt-tramites.php` — CPT + taxonomy + meta box + Quick Edit
4. `inc/alert-bar.php` — CPT alerta_intt registration
5. `inc/megamenu.php` — megamenu REST endpoint
6. `inc/footer.php` — footer utilities
7. `inc/default-pages.php` — default page creation on activation
8. `assets/js/megamenu.js` — megamenu toggle behavior
9. `assets/js/tramite-quick-edit.js` — Quick Edit for descripcion_corta

**Verify:** CPT `tramite` appears in WP Admin menu. Taxonomy `tipo_tramite` is visible.

---

## Phase 3 — Template parts (site structure)

Fill in order — each one builds on the previous:

1. `parts/site-header.html` — logo, nav, megamenu toggle, alert-bar block
2. `parts/footer.html` — footer links + dynamic year
3. `parts/megamenu-panel.html` — 3-column panel
4. `parts/banner-ministerio.html` — ministry logos bar (top of header area)
5. `parts/header.html` — inner header block

**Verify:** Site header and footer render on front page.

---

## Phase 4 — Home page patterns (fill one by one)

Front page section order (must be preserved):
1. hero
2. tramites frecuentes
3. otros tramites
4. noticias
5. radio banner

Fill patterns in this order, verifying each renders before moving to the next:
1. `patterns/antetitulo-titulo.php` (used by other patterns — fill first)
2. `patterns/hero-home.php`
3. `patterns/tarjeta-tramite-frecuente.php`
4. `patterns/tramites-frecuentes.php`
5. `patterns/seccion-tramites-frecuentes.php`
6. `patterns/otros-tramites.php`
7. `patterns/seccion-otros-tramites.php`
8. `patterns/noticias-home.php`
9. `patterns/radio-banner.php`

Then fill `templates/front-page.html` with pattern references.

---

## Phase 5 — Inner page templates

1. `templates/single.html` — generic post
2. `templates/single-tramite.html` — individual tramite
3. `templates/taxonomy-tipo_tramite.html` — hub page (hub-sidebar + hub-list blocks)
4. `templates/page-hub.html` — static hub page variant

---

## Phase 6 — Remaining patterns

1. `patterns/tramites-ciudadanos.php`
2. `patterns/tramites-empresas.php`
3. `patterns/tramite-requisitos-licencia.php`
4. `patterns/tramite-proceso-renovacion-licencia.php`
5. `patterns/megamenu-panel.php`

---

## Phase 7 — Custom blocks (requires npm)

1. Fill `blocks/*/render.php` files with real content from source
2. Fill `blocks/megamenu/index.js` with real content
3. Run `npm install && npm run build` in theme folder
4. Verify all 5 blocks register without errors

**Known issue from old theme:** `hub-list`, `hub-sidebar`, and `alert-bar` blocks show an "unsupported" message in the block editor (no editor script). This is a cosmetic issue — they render correctly on the front end. Fix is deferred.

**Critical note:** `tramite-descripcion` render.php uses `$block->context['postId']` not `$context['postId']`.

---

## Convención: marcadores de posición en partes de plantilla

Los archivos `.html` de partes de plantilla son estáticos — no pueden ejecutar PHP directamente. Para valores dinámicos se usa un sistema de marcadores de posición reemplazados por filtros PHP en `render_block_core/template-part`.

| Marcador | Reemplazado por | Dónde se aplica |
|----------|----------------|-----------------|
| `{year}` | Año actual (`date('Y')`) | `parts/footer.html` — filtro en `inc/footer.php` |

**Patrón disponible para uso futuro:** Si una parte de plantilla necesita la URL del tema, agregar un filtro `render_block_core/template-part` en `functions.php` que reemplace un marcador como `{theme_uri}` con `get_template_directory_uri()`. No está implementado actualmente (el caso de uso —logo móvil— se delegó al bloque `intt/logo` pendiente).

---

## Decisiones de diseño registradas

### Logo en la cabecera (`parts/site-header.html`)
Actualmente usa `wp:site-logo` estándar de WordPress (un solo logo, asignable desde el Editor de Sitio). El redimensionamiento en móvil lo maneja el navegador.

**TODO pendiente — bloque `intt/logo` personalizado:**
Crear un bloque dedicado que soporte logo escritorio + logo móvil y renderice un elemento `<picture>` (el navegador descarga solo la versión necesaria). El editor sube ambos logos desde el inspector del bloque. Esto también habilita variantes para cabecera transparente, sticky y modo oscuro. Ver `blocks/` para el patrón de bloques existentes.

---

## Language

All documentation, comments, inline PHP docblocks, and any user-facing strings in the theme must be written in **Spanish**. This applies to README files, code comments, pattern titles, block descriptions, and admin labels. Exception: block JSON `$schema` and WordPress API keys are always in English.

---

## Hard rules — never break these

- **No DB-saved templates.** File-based only. If any template in Site Editor lacks a lock icon it is stored in the DB and must be reset (Site Editor → Templates → ⋮ → Reset to default).
- **No inline colors or typography in block markup.** Style decisions live in `theme.json` and `style.css` only.
- **Component CSS: structural only.** Custom classes carry layout and spacing only — never redefine colors, hover states, or typography the theme already sets globally.
- **Never make design decisions unilaterally.** Always ask before choosing layout, colors, font sizes, spacing values, or graphic elements.
- **Always use WordPress-native APIs.** Flag missing core functionality rather than hacking around it.

---

## Pending TODOs (carry these forward)

### Must verify on new instance
- [ ] `descripcion_corta` meta box visible on tramite edit screen
- [ ] Quick Edit shows "Descripción" field and saves correctly
- [ ] `tipo_tramite` taxonomy visible and working
- [ ] All templates show lock icon (file-based, not DB)
- [ ] All 5 custom blocks render without fatal errors

### Design decisions pending (need client input)
- [ ] `single.html` — 740px content width feels narrow, confirm with client
- [ ] `single.html` — breadcrumbs: add or skip?
- [ ] Alert bar: reopen affordance (currently dismissed forever via localStorage)
- [ ] `radio-banner.php` — design is a placeholder, needs real design
- [ ] Column count for tramite grids — needs client sign-off

### Deferred
- [ ] Set up GitHub remote (no remote exists — do this as soon as new instance is stable)
- [ ] Editor scripts for `hub-list`, `hub-sidebar`, `alert-bar` blocks (remove "unsupported" editor message)
- [ ] `<main>` landmark on `front-page.html`
- [ ] Review `single-tramite.html` inline styles
- [ ] Database cleanup: confirm no leftover `visibilidad-tramite` terms in imported content

### Post-import cleanup on new instance
- [ ] Delete `wp-content/mu-plugins/allow-svg.php` (was added temporarily to allow SVG media upload)
- [ ] Remove `define( 'ALLOW_UNFILTERED_UPLOADS', true )` from `wp-config.php`
