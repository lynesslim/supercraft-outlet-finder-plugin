# Task List: Supercraft Outlet Finder Enhancements

- [ ] **Feature 1: Admin Marker Settings & Custom Colors**
  - [ ] Add `marker_style` select field to `class-settings.php`
  - [ ] Add `_sc_outlet_marker_color` color picker to `class-post-type.php` (meta box and save routine)
  - [ ] Enqueue `wp-color-picker` for the post edit screen
  - [ ] Pass `marker_style` and custom `marker_color` to frontend JSON in `class-shortcode.php`
  - [ ] Update `mkIcon()` JS function to support "pinpoint" style and custom colors

- [x] **Feature 2: Image Hover & Fixed Height**
  - [x] Update `.d-img-wrap` to fixed height (`200px`) with `flex-shrink: 0;` in `class-shortcode.php`
  - [x] Add `transform: scale(1.05)` on `.d-img-wrap img:hover` with CSS transition

- [x] **Feature 3: Detail Panel & Sidebar Scroll Fix**
  - [x] Add `overflow-y: auto; overscroll-behavior: contain;` and styled scrollbar to `#det` in `class-shortcode.php`
  - [x] Add explicit `wheel` JS event listener to `#det` and `#list` to call `stopPropagation` on wheel events

- [x] **Feature 4: Optional Display Switches & Outlet Description**
  - [x] Add Display Options section and toggle switches (`show_hours`, `show_phone`, `show_call_btn`, `show_description`) to `class-settings.php`
  - [x] Add optional `_sc_outlet_description` textarea to outlet meta box and save routine in `class-post-type.php`
  - [x] Update frontend detail panel markup, styling, and JS in `class-shortcode.php` to conditionally render hours, phone, call button, and description
  - [x] Bump plugin version to `1.0.18` and rebuild zip packages

- [x] **Feature 5: Editable Titles & Text Labels**
  - [x] Add `sc_of_text_labels` section to `class-settings.php` with all frontend editable text fields
  - [x] Add defaults for all text fields to `defaults()` in `class-settings.php`
  - [x] Update `class-shortcode.php` to make eyebrow, headline, search placeholder, no results message, back button, detail labels, action buttons, sidebar footer counter, and HUD label dynamically editable
  - [x] Bump plugin version to `1.0.19` and rebuild zip packages

- [x] **Feature 6: Granular Typography & Font Size Control**
  - [x] Add `sc_of_typography` section to `class-settings.php` with font size and font family controls for all 26 text elements across sidebar header, search, list, detail panel, map HUD, and loader
  - [x] Add `field_typography_item` renderer supporting font size and font family (Inherit, Primary Heading, Body, Monospace, Elementor Fonts, or Custom Font)
  - [x] Add defaults for all font size and font family options to `defaults()` in `class-settings.php`
  - [x] Add subnav anchor pills in settings for smooth navigation between General, Titles, Typography, Display, and Styling
  - [x] Update `class-shortcode.php` `render_styles()` with resolvers and individual `--fs-*` and `--ff-*` CSS variables applied to each text element
  - [x] Bump plugin version to `1.0.20` and rebuild zip packages

- [x] **Feature 7: Numbering Customization & Map Marker Pin Number Typography**
  - [x] Confirm and support font size and typography controls for all numbering elements (Sidebar list number `.oi-num`, Detail watermark number `.d-bignum`, Sidebar footer total `.ftr-num`, Map HUD count `.hud-big`)
  - [x] Add `typo_marker_num` for Map Marker Pin Number (`fs_marker_num`, `ff_marker_num`) to `class-settings.php`
  - [x] Bind `.sc-outlet-widget .sc-core` and active marker state in `class-shortcode.php` to `--ff-marker-num` and `--fs-marker-num`
  - [x] Bump plugin version to `1.0.21` and rebuild zip packages
