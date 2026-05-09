# CLAUDE.md — yarri/mjml

PHP port of the [MJML 4.18.0](https://mjml.io/) email templating library. Converts MJML markup to responsive HTML email — without Node.js at runtime. Node.js is used only in tests (to compare output against the reference implementation).

## Running tests

```bash
./vendor/bin/run_unit_tests test/              # all tests
./vendor/bin/run_unit_tests test/tc_mjml.php  # single file
```

Tests require Node.js (install packages via `npm install`).

## Architecture

```
src/mjml.php          — entry point: Yarri\Mjml::Mjml2Html($mjml)
src/parser.php        — parses MJML (XMole), builds a tree of _Tag objects, calls render()
src/global_data.php   — GlobalData: shared state (media queries, fonts, headStyle, attributes...)
src/skeleton.php      — generates the full HTML document (head, body, media queries, CSS)
src/tags/_tag.php     — base class for all tags
src/tags/mj_*.php     — implementations of individual MJML components
src/utils.php         — helper functions (htmlspecialchars, etc.)
src/core/lib/         — Helpers (widthParser, etc.) ported from JS
```

### Processing flow

1. `Mjml2Html()` — pre-processing of malformed HTML in content tags (see below)
2. XMole parses MJML as XML
3. `Parser::parse()` — recursively walks the tree; for each `mj-*` element creates an instance of the corresponding PHP class
4. Each tag implements `render()` → returns an HTML string
5. `Skeleton::render()` — wraps the output in a complete HTML document
6. Post-processing: `mj-html-attributes` (DOMDocument), mergeOutlookConditionals

## Implemented components

### Head
`mj-title`, `mj-preview`, `mj-font`, `mj-breakpoint`, `mj-style`, `mj-attributes` (including `mj-all` and `mj-class`), `mj-html-attributes`

### Body
`mj-body`, `mj-section`, `mj-column`, `mj-wrapper`, `mj-group`, `mj-hero`, `mj-text`, `mj-image`, `mj-button`, `mj-divider`, `mj-spacer`, `mj-table`, `mj-raw`, `mj-social` + `mj-social-element`, `mj-navbar` + `mj-navbar-link`, `mj-accordion` + `mj-accordion-element` + `mj-accordion-title` + `mj-accordion-text`, `mj-carousel` + `mj-carousel-image`

All components are implemented. Output is verified against the reference Node.js implementation.

## Guidelines for new tags

1. Create `src/tags/mj_tag_name.php`, class `Yarri\Mjml\Tags\MjTagName`
2. Define `static $componentName`, `static $allowedAttributes`, `static $defaultAttributes`
3. Implement `getStyles()` → returns an array `['key' => ['css-property' => value]]`
4. Implement `render()` → returns an HTML string
5. After adding the file, run `composer dump-autoload`

To render children, use `$this->renderChildren(callable $wrapper)` — the wrapper receives each child and returns HTML. Context is propagated via `getChildContext()`.

## Key patterns and gotchas

### headStyle vs componentHeadStyle
- `headStyle($breakpoint)` — CSS registered via `GlobalData::addHeadStyle($componentName, $callable)`. **Deduplicated per component type** — suitable for CSS that is the same for all instances (accordion, navbar).
- `componentHeadStyle()` — CSS registered via `GlobalData::addComponentHeadStyle($callable)`. **Per-instance** — suitable for CSS with unique IDs (carousel generates a unique `carouselId = bin2hex(random_bytes(6))`).

### Dot-notation in getStyles()
Carousel uses nested keys in `getStyles()`: `['carousel' => ['div' => [...]]]`. In `_tag.php::styles()`, the notation `'carousel.div'` is supported to access these nested arrays.

### mj-class attribute priority
Order (lowest to highest priority):
`mj-all` < component type (`mj-text`) < `mj-class` < explicit element attributes

Implemented in `parser.php::_buildTag()`:
```php
$attributes = array_merge($globalAttrs, $tagAttrs, $classAttrs, $attributes);
```

### mj-html-attributes
Post-processing via DOMDocument + XPath. CSS selectors (`.class`, `.a.b`, `.a .b`) are converted to XPath in `Parser::_cssToXPath()`. Conditional comments (`<!--[if ...]-->`) survive processing.

### Malformed HTML in content tags
Tags `mj-text`, `mj-button`, `mj-accordion-title`, `mj-accordion-text`, `mj-table`, `mj-raw`, `mj-social-element`, `mj-navbar-link` may contain invalid XML (e.g. `<br>` without closing). Solution in `Mjml2Html()`:
1. Pre-processing: regex finds the content of these tags and tries to parse it with XMole
2. If malformed, replaces it with a unique placeholder key
3. After render(), keys are substituted back via `strtr()`

The regex in `mjml.php` must correctly skip self-closing tags (`<mj-text />`). The attribute part of the regex: `(?:[^>"'\/]|"[^"]*"|'[^']*'|\/(?!>))*` — allows `/` inside quotes (URLs) but not immediately before `>`.

### Random IDs and test normalization
Components that generate random IDs require normalization in `test/tc_base.php::_compare_html()` before comparing with Node.js output:
- **Carousel**: PHP generates 12-char IDs (`bin2hex(random_bytes(6))`), Node.js generates 16-char IDs (`genRandomHexString(16)`). Regex: `carousel-(?:[a-z]+-)*[0-9a-f]{12,16}` → `carousel-ID`
- **Navbar**: 16-char hex key in `id=` and `for=` attributes. Regex: `[0-9a-f]{16}` inside these attributes → `MENU-KEY`

### Changes from MJML 4.13.0 (implemented for 4.18.0)

- **mj-body**: `aria-label` from `<mj-title>` is added to the body wrapper `<div>` (`$this->context->globalData->title`).
- **mj-section**: `border-radius` moved from `table` style to `td` style; when `border-radius` is set, `border-collapse: separate` is added to `table` and `overflow: hidden` to `div`.
- **mj-column**: when `border-radius` or `inner-border-radius` is set, `border-collapse: separate` is added to the outer table in `renderGutter()`; the `vertical-align` HTML attribute is removed from the inner `<td>` in `renderColumn()`.
- **mj-hero**: in `fixed-height` mode, the height is also written to the CSS style (`height: Xpx`), not only as an HTML attribute.
- **mj-social-element**: `height` attribute removed from `<img>`; `text-align` added to the `tdText` style; `alt=""` is the default value.
- **mj-social**: propagation of parent attributes to `mj-social-element` skips `null` values (otherwise it would override the element's default values).
- **mj-accordion**: `font-family` is propagated via context (`accordionFontFamily`), not via childrenAttrs — to prevent it from reaching the `<label>` element. `mj-accordion-element` propagates it further as `elementFontFamily`. `mj-accordion-title` and `mj-accordion-text` use `resolveFontFamily()` with the order: own attribute → elementFontFamily → accordionFontFamily.

### Replicated JS bugs
Some PHP behaviour intentionally replicates Node.js implementation bugs to produce identical output:
- **mj-navbar div style**: JS calls `this.htmlAttributes('div')` (string instead of object) → always returns `''` → `style=""`. PHP therefore passes `'style' => ''` explicitly.
- **mj-carousel-image class**: JS template literal always appends a space + cssClass, even when empty → trailing space in the class attribute.

### PHP 7.1+ compatibility
- No trailing commas after the last function argument (added in PHP 7.3)
- No `??=` operator (PHP 7.4)
- No named arguments (PHP 8.0)

## Test infrastructure

- `test/tc_base.php` — base class with `assertHtmlEquals()` and `_mjml_node()`
- `assertHtmlEquals($expected, $actual)` — compares the body part of HTML via XMole (XML parser); normalizes whitespace, `&`, and random IDs
- `_mjml_node($src)` — runs Node.js MJML on the input and returns HTML for comparison
- XMole is an expat-based XML parser — **does not tolerate bare `&`** (must be `&amp;`) and **HTML named entities** (`&copy;` → use `&#169;`)

## Dependencies

- `atk14/xmole` — XML parser (expat) used both in production (MJML parsing) and in tests (HTML comparison)
- Node.js `mjml@4.18.0` — for tests only (dev dependency)
