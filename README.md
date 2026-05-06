# yarri/mjml

PHP implementation of the [MJML](https://mjml.io/) email templating language. Converts MJML markup into responsive HTML emails — without Node.js at runtime.

## Requirements

- PHP 7.1+

## Installation

```bash
composer require yarri/mjml
```

## Usage

```php
$mjml = '
    <mjml>
        <mj-head>
            <mj-title>Hello World</mj-title>
            <mj-preview>Check this out!</mj-preview>
        </mj-head>
        <mj-body>
            <mj-section>
                <mj-column>
                    <mj-text font-size="20px" color="#333333">Hello World</mj-text>
                    <mj-button href="https://example.com">Click here</mj-button>
                </mj-column>
            </mj-section>
        </mj-body>
    </mjml>
';

$html = Yarri\Mjml::Mjml2Html($mjml);
```

## Supported components

### Head

| Component | Description |
|---|---|
| `mj-title` | Sets the `<title>` of the email |
| `mj-preview` | Adds a preview text (hidden preheader) |
| `mj-font` | Imports a web font (only if used in `font-family`) |
| `mj-breakpoint` | Sets the responsive breakpoint width (default: 480px) |
| `mj-style` | Injects custom CSS into the `<head>` |
| `mj-attributes` | Sets default attribute values for components (`mj-all` applies to all) |
| `mj-html-attributes` | Injects custom HTML attributes into rendered elements by CSS class selector |

### Body

| Component | Description |
|---|---|
| `mj-body` | Root body element |
| `mj-section` | A horizontal section; supports background color, image, full-width |
| `mj-column` | Column inside a section; supports border, padding, background |
| `mj-wrapper` | Wraps sections; useful for shared backgrounds |
| `mj-group` | Groups columns for Outlook compatibility |
| `mj-hero` | Full-width hero with background image |
| `mj-text` | A block of text |
| `mj-image` | An image |
| `mj-button` | A call-to-action button |
| `mj-divider` | A horizontal rule |
| `mj-spacer` | Vertical whitespace |
| `mj-table` | An HTML table |
| `mj-raw` | Raw HTML passthrough (not processed) |
| `mj-social` | A row of social media icons |
| `mj-social-element` | A single social media link |
| `mj-navbar` | A responsive navigation bar |
| `mj-navbar-link` | A link inside a navbar |
| `mj-accordion` | A collapsible accordion |
| `mj-accordion-element` | A single accordion item |
| `mj-accordion-title` | Title of an accordion item |
| `mj-accordion-text` | Body of an accordion item |
| `mj-carousel` | An image carousel |
| `mj-carousel-image` | A single image in a carousel |

## Testing

The test suite compares output against the reference Node.js MJML 4.13.0 implementation. Install Node.js dependencies first:

```bash
nvm use
npm install
```

Then run the tests:

```bash
cd test/
../vendor/bin/run_unit_tests
```

Run a single test case:

```bash
cd test/
../vendor/bin/run_unit_tests tc_mj_column.php

```
