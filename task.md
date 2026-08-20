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
