# CLAUDE.md — yarri/mjml

PHP port of the [MJML 4.13.0](https://mjml.io/) email templating library. Converts MJML markup to responsive HTML email — without Node.js at runtime. Node.js is used only in tests (to compare output against the reference implementation).

## Spuštění testů

```bash
./vendor/bin/run_unit_tests test/              # všechny testy
./vendor/bin/run_unit_tests test/tc_mjml.php  # jeden soubor
```

Testy vyžadují Node.js (nainstalované balíčky přes `npm install`).

## Architektura

```
src/mjml.php          — vstupní bod: Yarri\Mjml::Mjml2Html($mjml)
src/parser.php        — parsuje MJML (XMole), sestavuje strom objektů _Tag, volá render()
src/global_data.php   — GlobalData: sdílený stav (media queries, fonty, headStyle, atributy...)
src/skeleton.php      — generuje celý HTML dokument (head, body, media queries, CSS)
src/tags/_tag.php     — základní třída všech tagů
src/tags/mj_*.php     — implementace jednotlivých MJML komponent
src/utils.php         — pomocné funkce (htmlspecialchars, atd.)
src/core/lib/         — Helpers (widthParser, atd.) portované z JS
```

### Tok zpracování

1. `Mjml2Html()` — pre-processing malformovaného HTML v content tazích (viz níže)
2. XMole zparsuje MJML jako XML
3. `Parser::parse()` — rekurzivně projde strom, pro každý `mj-*` element vytvoří instanci příslušné PHP třídy
4. Každý tag implementuje `render()` → vrací HTML string
5. `Skeleton::render()` — obalí výstup do kompletního HTML dokumentu
6. Post-processing: `mj-html-attributes` (DOMDocument), mergeOutlookConditionals

## Implementované komponenty

### Head
`mj-title`, `mj-preview`, `mj-font`, `mj-breakpoint`, `mj-style`, `mj-attributes` (včetně `mj-all` a `mj-class`), `mj-html-attributes`

### Body
`mj-body`, `mj-section`, `mj-column`, `mj-wrapper`, `mj-group`, `mj-hero`, `mj-text`, `mj-image`, `mj-button`, `mj-divider`, `mj-spacer`, `mj-table`, `mj-raw`, `mj-social` + `mj-social-element`, `mj-navbar` + `mj-navbar-link`, `mj-accordion` + `mj-accordion-element` + `mj-accordion-title` + `mj-accordion-text`, `mj-carousel` + `mj-carousel-image`

Všechny komponenty jsou implementovány. Výstup je ověřen proti referenční Node.js implementaci.

## Zásady pro nové tagy

1. Vytvořit `src/tags/mj_nazev_tagu.php`, třída `Yarri\Mjml\Tags\MjNazevTagu`
2. Definovat `static $componentName`, `static $allowedAttributes`, `static $defaultAttributes`
3. Implementovat `getStyles()` → vrací pole `['klíč' => ['css-vlastnost' => hodnota]]`
4. Implementovat `render()` → vrací HTML string
5. Po přidání souboru spustit `composer dump-autoload`

Při renderování potomků se používá `$this->renderChildren(callable $wrapper)` — wrapper dostane každý potomek a vrací HTML. Context se propaguje přes `getChildContext()`.

## Klíčové vzory a gotchas

### headStyle vs componentHeadStyle
- `headStyle($breakpoint)` — CSS registrované přes `GlobalData::addHeadStyle($componentName, $callable)`. **Deduplikováno per typ komponenty** — vhodné pro CSS, které je stejné pro všechny instance (accordion, navbar).
- `componentHeadStyle()` — CSS registrované přes `GlobalData::addComponentHeadStyle($callable)`. **Per-instance** — vhodné pro CSS s unikátními ID (carousel generuje unikátní `carouselId = bin2hex(random_bytes(6))`).

### Dot-notation v getStyles()
Carousel používá vnořené klíče v `getStyles()`: `['carousel' => ['div' => [...]]]`. V `_tag.php::styles()` se podporuje zápis `'carousel.div'` pro přístup do těchto vnořených polí.

### mj-class priorita atributů
Pořadí (od nejnižší po nejvyšší prioritu):
`mj-all` < typ komponenty (`mj-text`) < `mj-class` < explicitní atributy elementu

Implementováno v `parser.php::_buildTag()`:
```php
$attributes = array_merge($globalAttrs, $tagAttrs, $classAttrs, $attributes);
```

### mj-html-attributes
Post-processing přes DOMDocument + XPath. CSS selektory (`.třída`, `.a.b`, `.a .b`) se konvertují na XPath v `Parser::_cssToXPath()`. Conditional comments (`<!--[if ...]-->`) přežijí zpracování.

### Malformovaný HTML v content tazích
Tagy `mj-text`, `mj-button`, `mj-accordion-title`, `mj-accordion-text`, `mj-table`, `mj-raw`, `mj-social-element`, `mj-navbar-link` mohou obsahovat nevalidní XML (např. `<br>` bez uzavření). Řešení v `Mjml2Html()`:
1. Pre-processing: regex najde content těchto tagů, zkusí ho zparsovat XMole
2. Pokud je malformovaný, nahradí ho unikátním klíčem
3. Po render() se klíče zpětně dosadí přes `strtr()`

Regex v `mjml.php` musí správně přeskočit self-closing tagy (`<mj-text />`). Atributová část regexu: `(?:[^>"'\/]|"[^"]*"|'[^']*'|\/(?!>))*` — povolí `/` uvnitř uvozovek (URL), ale ne těsně před `>`.

### Náhodná ID a testovací normalizace
Komponenty generující náhodná ID při porovnávání s Node.js vyžadují normalizaci v `test/tc_base.php::_compare_html()`:
- **Carousel**: `bin2hex(random_bytes(6))` → 12 hex znaků v názvech tříd a atributech. Regex: `carousel-(?:[a-z]+-)*[0-9a-f]{12}` → `carousel-ID`
- **Navbar**: 16-znakový hex klíč v `id=` a `for=` atributech. Regex: `[0-9a-f]{16}` uvnitř těchto atributů → `MENU-KEY`

### Replikované bugy z JS
Některé chování PHP záměrně replikuje bugy Node.js implementace, aby výstup byl identický:
- **mj-navbar div style**: JS volá `this.htmlAttributes('div')` (string místo objektu) → vždy vrací `''` → `style=""`. PHP proto předává `'style' => ''` explicitně.
- **mj-carousel-image class**: JS template literal vždy přidá mezeru + cssClass, i když je prázdný → trailing space v class atributu.

### Compatibility PHP 7.1+
- Bez trailing čárek za posledním argumentem funkce (přidáno v PHP 7.3)
- Bez `??=` operátoru (PHP 7.4)
- Bez named arguments (PHP 8.0)

## Testovací infrastruktura

- `test/tc_base.php` — základní třída s `assertHtmlEquals()` a `_mjml_node()`
- `assertHtmlEquals($expected, $actual)` — porovná body část HTML přes XMole (XML parser); normalizuje whitespace, `&`, náhodná ID
- `_mjml_node($src)` — spustí Node.js MJML na vstupu a vrátí HTML pro srovnání
- XMole je expat-based XML parser — **nesnáší holé `&`** (musí být `&amp;`) a **HTML named entity** (`&copy;` → použít `&#169;`)

## Závislosti

- `atk14/xmole` — XML parser (expat) používaný jak v produkci (parsování MJML), tak v testech (porovnávání HTML)
- Node.js `mjml@4.13.0` — pouze pro testy (dev dependency)
