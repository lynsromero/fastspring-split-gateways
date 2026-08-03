# Phase 3 — Branded Header Assets & Gateway Icon Picker

Date: 2026-08-03

## Goal

Complete the branded admin experience for the FastSpring Split Gateways plugin:

1. Eliminate the two 404 header assets (`fastspring-logo.svg`, `fastspring-mark.svg`).
2. Replace the per-gateway text-input icon field with a preset SVG picker (with a custom-upload option) for the four string-icon gateways.
3. Fix stale icon URLs stored in the DB that point at the old `fastspring-unified` plugin path.

## Current State (verified)

- `includes/admin/views/html-settings-nav.php` references `assets/img/fastspring-logo.svg` and `assets/img/fastspring-mark.svg` — both files are missing (404s in the admin console).
- Header CSS already sizes the two slots: left logo `height: 26px`, right "Powered by" mark `height: 18px` (`assets/css/admin.css`).
- The 4 string-icon gateways (`fastspring_paypal`, `fastspring_amazon`, `fastspring_googlepay`, `fastspring_wire`) use field type `icon_upload` (`fssg_WC_FS_Manual_Gateway::init_form_fields()` + `generate_icon_upload_html()`): a text input + media-upload button.
- `fastspring_card` uses `multiselect` of card slugs — kept as-is.
- Stored icon options point to `https://microprokey.com/wp-content/plugins/fastspring-unified/assets/icons/<slug>.svg` (stale plugin-folder path from when the plugin was named `fastspring-unified`). Card stores slugs only.
- Plugin version is `1.1.0` (`FSSG_WC_FASTSPRING_VERSION`); a version-gated upgrade path already exists: `check_environment()` on `admin_init` detects a version change, calls `install()`, then fires `woocommerce_fastspring_updated`.
- The user provided an official FastSpring logo: `7980de6-small-logo3x_1.png` (546x80 px, RGBA/transparent), to be used for both header slots.

## Design

### 1. Header assets

- Copy `7980de6-small-logo3x_1.png` into the plugin as `assets/img/fastspring-logo.png` (committed to the repo).
- Update `includes/admin/views/html-settings-nav.php` to reference `assets/img/fastspring-logo.png` for both the 26px logo and the 18px "Powered by" mark.
- No SVG files are created; the two 404s are eliminated. No CSS changes needed (sizes already defined).

### 2. Icon picker (slug data model)

- Change the `icon` field type from `icon_upload` to `gateway_icon` for the four string-icon gateways in `fssg_WC_FS_Manual_Gateway::init_form_fields()`. Field definition gains `options` (slug => label) and keeps a sensible per-gateway default slug.
- Implement `generate_gateway_icon_html( $key, $data )` that renders a view partial `includes/admin/views/html-gateway-icon-picker.php`:
  - Preset SVG thumbnail cards (radios) sourced from `assets/icons/<slug>.svg`.
  - A "Custom image" option that reveals the existing media-uploader input (URL text field + upload button).
- Small admin JS (enqueued only on FastSpring settings screens) toggles the custom input and sets the hidden field value to either the preset slug or `custom:<url>`.
- Remove the old `generate_icon_upload_html()` method.
- **Stored value:** preset slug (e.g. `paypal`) or `custom:<url>`.
- `fssg_WC_FS_Manual_Gateway::get_icon()` resolution:
  - array value → existing inline card-icon rendering (unchanged);
  - string matching a known preset slug → `FS_SPLIT_GATEWAY_URL . 'assets/icons/<slug>.svg'`;
  - string starting with `custom:` → strip prefix, use the URL;
  - any other string → use as-is (backward-compatible with legacy absolute URLs).
- Add `validate_icon_field()` on the base gateway so saving sanitizes slug / `custom:` values (gateways save through WC core `process_admin_options()`).
- Card gateway unchanged (`multiselect` of slugs).

### 3. Migration / DB fix

- Bump `FSSG_WC_FASTSPRING_VERSION` to `1.2.0`.
- New PHP-only upgrade class (e.g. `includes/admin/class-wc-fastspring-upgrade.php`) hooked to `woocommerce_fastspring_updated`:
  - For each of the five gateway option rows (`woocommerce_fastspring_{paypal,card,amazon,wire,googlepay}_settings`):
    - if `icon` is a string matching `/assets/icons/<slug>.svg` (any domain/plugin folder), replace it with the bare slug;
    - array `icon` (card) is left untouched.
  - Idempotent: only rewrites values that match the legacy URL pattern; safe to run repeatedly.
- This fixes the stale `fastspring-unified` URLs on any site, independent of domain.

### 4. Verification

- `php -l` on all changed files.
- Browser:
  - Header 404s gone; PNG logo renders in both slots (26px / 18px).
  - All 4 gateway sections render the picker with SVG thumbnails.
  - Select a preset → save → option stores the slug; custom upload stores `custom:<url>`.
  - Card section unchanged.
  - Checkout `get_icon()` still renders icons for all gateways.
- DB options normalized to slugs after upgrade routine runs.
- Commit Phase 3 (bump to 1.2.0) and push.

## Out of Scope

- Apple Pay / Venmo / AliPay / Cash App gateways and their marks (gateways were removed in Phase 2).
- Card gateway icon picker (stays `multiselect`).
- Main `fastspring` gateway `icons` checkout field.
