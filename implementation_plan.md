# Goal Description

Implement three new enhancements for the Supercraft Outlet Finder plugin:
1. **Admin Marker Settings & Custom Colors**: Add a global setting to choose marker styles (Numbered vs. Pinpoint) and a custom color picker for each location's marker (falling back to the global primary color).
2. **Image Hover Effect & Ratio**: Change the image aspect ratio to 3:2 and apply an enlarge (scale) effect when hovered.
3. **Sidebar Scroll Fix**: Ensure the sidebar location list responds to mouse scroll events intuitively without scrolling the entire page.

## User Review Required

> [!IMPORTANT]
> **Image Hover Effect Clarification**: The implementation plan assumes the "image on each location" refers to the featured image displayed in the location detail panel (`.d-img-wrap`). If you meant the image inside the map marker tooltip, please clarify before execution.

## Proposed Changes

### Plugin Includes

#### [MODIFY] [class-settings.php](file:///Users/lynesslim/Library/CloudStorage/GoogleDrive-lynesslim@gmail.com/.shortcut-targets-by-id/1tdRwUUrLZ8ISnTgPBALicsop9_kB_lNQ/Supercraft%20Drive/03_RESOURCES/Custom%20Tool/Outlet%20Selector/supercraft-outlet-finder/includes/class-settings.php)
- Add a new setting `marker_style` in the `sc_of_general` section (Dropdown: "Numbered (Default)" vs "Pinpoint").
- Update `defaults()` to include `marker_style` => `numbered`.
- Add `field_select()` to render the dropdown field.

#### [MODIFY] [class-post-type.php](file:///Users/lynesslim/Library/CloudStorage/GoogleDrive-lynesslim@gmail.com/.shortcut-targets-by-id/1tdRwUUrLZ8ISnTgPBALicsop9_kB_lNQ/Supercraft%20Drive/03_RESOURCES/Custom%20Tool/Outlet%20Selector/supercraft-outlet-finder/includes/class-post-type.php)
- In `render_meta_box()`, add a new color picker input for `_sc_outlet_marker_color`.
- Enqueue the WordPress color picker script in the admin for the outlet post type.
- In `save_meta()`, ensure `_sc_outlet_marker_color` is sanitized and saved properly.

#### [MODIFY] [class-shortcode.php](file:///Users/lynesslim/Library/CloudStorage/GoogleDrive-lynesslim@gmail.com/.shortcut-targets-by-id/1tdRwUUrLZ8ISnTgPBALicsop9_kB_lNQ/Supercraft%20Drive/03_RESOURCES/Custom%20Tool/Outlet%20Selector/supercraft-outlet-finder/includes/class-shortcode.php)
- **Data Export**: Update `get_outlets()` to fetch and include `_sc_outlet_marker_color` in the JSON data passed to the frontend.
- **Frontend JS (Map Markers)**: 
  - Read the global `marker_style` setting.
  - Modify `mkIcon()` function to render either the default numbered marker HTML or a new pinpoint marker HTML.
  - Apply the custom `marker_color` (if provided for the outlet) to the marker's CSS properties, falling back to the global `--gold` variable.
- **CSS Styles**:
  - Add CSS for the new pinpoint marker style.
  - Change `.d-img-wrap` aspect ratio from `4 / 3` to `3 / 2`.
  - Add `overflow: hidden;` to `.d-img-wrap` and a `:hover` pseudo-class for the `img` inside it to trigger a `transform: scale(1.05);` with smooth transitions.
  - Add CSS `overscroll-behavior: contain;` to `#list` to prevent mouse wheel events from bubbling up and scrolling the main page.
  - Add an explicit `wheel` event listener in the JS for `#list` that calls `e.stopPropagation()` to absolutely guarantee the page doesn't scroll when the mouse is over the sidebar.

## Verification Plan

### Manual Verification
1. **Admin**: Go to Supercraft Outlet Finder Settings, verify the marker style dropdown exists and saves.
2. **Admin**: Edit an outlet, verify the custom color picker exists and saves.
3. **Frontend Map**: Verify markers render as "Pinpoints" or "Numbered" based on the global setting. Ensure outlets with custom colors reflect those colors accurately on the map.
4. **Frontend Image**: Click an outlet, view the detail panel, hover over the image to ensure it enlarges smoothly, and visually confirm the 3:2 ratio.
5. **Frontend Scroll**: Place the mouse over the sidebar location list and use the scroll wheel. Verify the list scrolls smoothly and the parent webpage does not scroll.
