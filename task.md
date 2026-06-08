# Task List: Supercraft Outlet Finder Enhancements

- [ ] **Feature 1: Admin Marker Settings & Custom Colors**
  - [ ] Add `marker_style` select field to `class-settings.php`
  - [ ] Add `_sc_outlet_marker_color` color picker to `class-post-type.php` (meta box and save routine)
  - [ ] Enqueue `wp-color-picker` for the post edit screen
  - [ ] Pass `marker_style` and custom `marker_color` to frontend JSON in `class-shortcode.php`
  - [ ] Update `mkIcon()` JS function to support "pinpoint" style and custom colors

- [ ] **Feature 2: Image Hover & Ratio (3:2)**
  - [ ] Update `.d-img-wrap` CSS `aspect-ratio` to `3 / 2` in `class-shortcode.php`
  - [ ] Add `transform: scale(1.05)` on `.d-img-wrap img:hover` with CSS transition

- [ ] **Feature 3: Sidebar List Scroll Fix**
  - [ ] Add `overscroll-behavior: contain;` to `#list` CSS in `class-shortcode.php`
  - [ ] Add explicit `wheel` JS event listener to `#list` to `stopPropagation` of scroll events to the window
