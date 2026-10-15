<?php

if (!defined('ABSPATH')) {
    exit;
}

class SC_OF_Settings
{
    private $option_group = 'sc_of_settings';
    private $option_name = 'sc_of_options';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu_page'], 20);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    public function enqueue_admin($hook)
    {
        if (strpos($hook, 'supercraft-outlet-finder') === false) {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        add_action('admin_footer', function () {
            ?>
            <style>
            .sc-of-switch {
                position: relative;
                display: inline-block;
                width: 44px;
                height: 24px;
                vertical-align: middle;
            }
            .sc-of-switch input {
                opacity: 0;
                width: 0;
                height: 0;
                position: absolute;
            }
            .sc-of-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #cbd5e1;
                transition: .25s ease;
                border-radius: 24px;
            }
            .sc-of-slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: #ffffff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.25);
                transition: .25s ease;
                border-radius: 50%;
            }
            .sc-of-switch input:checked + .sc-of-slider {
                background-color: #2271b1;
            }
            .sc-of-switch input:focus-visible + .sc-of-slider {
                outline: 2px solid #2271b1;
                outline-offset: 2px;
            }
            .sc-of-switch input:checked + .sc-of-slider:before {
                transform: translateX(20px);
            }
            </style>
            <script>
            jQuery(function ($) {
                $('.sc-of-color-picker').wpColorPicker();
                $('.sc-of-color-mapper').on('change', function() {
                    var $wrapper = $(this).siblings('.sc-of-custom-color-wrapper');
                    if ($(this).val() === 'custom') {
                        $wrapper.show();
                    } else {
                        $wrapper.hide();
                    }
                });
                $('.sc-of-font-mapper').on('change', function() {
                    var $wrapper = $(this).siblings('.sc-of-custom-font-wrapper');
                    if ($(this).val() === 'custom') {
                        $wrapper.show();
                    } else {
                        $wrapper.hide();
                    }
                });
            });
            </script>
            <?php
        });
    }

    public function get_elementor_colors() {
        $colors = ['custom' => __('Custom Color', 'supercraft-of')];
        if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
            try {
                $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
                if ($kit) {
                    $system_colors = $kit->get_settings_for_display( 'system_colors' );
                    $custom_colors = $kit->get_settings_for_display( 'custom_colors' );
                    if (is_array($system_colors)) {
                        foreach ($system_colors as $color) {
                            $colors['e-global-color-' . $color['_id']] = 'Elementor: ' . $color['title'];
                        }
                    }
                    if (is_array($custom_colors)) {
                        foreach ($custom_colors as $color) {
                            $colors['e-global-color-' . $color['_id']] = 'Elementor: ' . $color['title'];
                        }
                    }
                }
            } catch (\Exception $e) {}
        }
        return $colors;
    }

    public function get_elementor_fonts() {
        $fonts = ['custom' => __('Custom Font', 'supercraft-of')];
        if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
            try {
                $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
                if ($kit) {
                    $system_typography = $kit->get_settings_for_display( 'system_typography' );
                    $custom_typography = $kit->get_settings_for_display( 'custom_typography' );
                    if (is_array($system_typography)) {
                        foreach ($system_typography as $typo) {
                            $fonts['e-global-typography-' . $typo['_id']] = 'Elementor: ' . $typo['title'];
                        }
                    }
                    if (is_array($custom_typography)) {
                        foreach ($custom_typography as $typo) {
                            $fonts['e-global-typography-' . $typo['_id']] = 'Elementor: ' . $typo['title'];
                        }
                    }
                }
            } catch (\Exception $e) {}
        }
        return $fonts;
    }

    public function add_menu_page()
    {
        global $menu;

        $supercraft_parent_slug = '';

        // 1. Check if Supercraft Master Plugin is active (slug: 'supercraft-dashboard')
        if (is_array($menu)) {
            foreach ($menu as $item) {
                if (isset($item[2]) && $item[2] === 'supercraft-dashboard') {
                    $supercraft_parent_slug = 'supercraft-dashboard';
                    break;
                }
            }
        }

        // 2. Check if a generic 'supercraft' parent menu exists
        if (!$supercraft_parent_slug && is_array($menu)) {
            foreach ($menu as $item) {
                if (isset($item[2]) && $item[2] === 'supercraft') {
                    $supercraft_parent_slug = 'supercraft';
                    break;
                }
            }
        }

        if ($supercraft_parent_slug) {
            add_submenu_page(
                $supercraft_parent_slug,
                __('Supercraft Outlet Finder', 'supercraft-of'),
                __('Outlet Finder', 'supercraft-of'),
                'manage_options',
                'supercraft-outlet-finder',
                [$this, 'render_page']
            );
        } else {
            add_menu_page(
                __('Supercraft Outlet Finder', 'supercraft-of'),
                __('Outlet Finder', 'supercraft-of'),
                'manage_options',
                'supercraft-outlet-finder',
                [$this, 'render_page'],
                'dashicons-location-alt',
                30
            );
        }
    }

    public function register_settings()
    {
        register_setting($this->option_group, $this->option_name, [$this, 'sanitize']);

        add_settings_section(
            'sc_of_general',
            __('General Settings', 'supercraft-of'),
            function () {
                echo '<span id="sc_sec_general"></span>';
            },
            'supercraft-outlet-finder'
        );

        add_settings_field('brand_name', __('Brand Name', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'brand_name', 'default' => 'SUPERCRAFT']);
        add_settings_field('brand_subtitle', __('Brand Subtitle', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'brand_subtitle', 'default' => 'Premium Outlet Finder']);
        add_settings_field('region', __('Region Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'region', 'default' => 'Klang Valley']);
        add_settings_field('region_sub', __('Region Subtitle', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'region_sub', 'default' => 'Klang Valley · Malaysia']);
        add_settings_field('map_center_lat', __('Map Center Latitude', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'map_center_lat', 'default' => '3.1']);
        add_settings_field('map_center_lng', __('Map Center Longitude', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'map_center_lng', 'default' => '101.62']);
        add_settings_field('map_zoom', __('Default Map Zoom', 'supercraft-of'), [$this, 'field_number'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'map_zoom', 'default' => '11', 'min' => 1, 'max' => 20]);
        add_settings_field('fly_zoom', __('Fly-to Zoom Level', 'supercraft-of'), [$this, 'field_number'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'fly_zoom', 'default' => '15', 'min' => 1, 'max' => 20]);
        add_settings_field('marker_style', __('Marker Style', 'supercraft-of'), [$this, 'field_select'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'marker_style', 'default' => 'numbered', 'options' => ['numbered' => __('Numbered (Default)', 'supercraft-of'), 'pinpoint' => __('Pinpoint', 'supercraft-of')]]);
        add_settings_field('map_style', __('Map Tile Style', 'supercraft-of'), [$this, 'field_select'], 'supercraft-outlet-finder', 'sc_of_general', [
            'key' => 'map_style',
            'default' => 'voyager',
            'options' => [
                'voyager'      => __('Modern Colorful (CARTO Voyager - Recommended)', 'supercraft-of'),
                'dark'         => __('Dark Matter (CARTO Dark)', 'supercraft-of'),
                'light'        => __('Minimal Light (CARTO Positron)', 'supercraft-of'),
                'esri_streets' => __('Esri World Street Map (High Detail)', 'supercraft-of'),
                'satellite'    => __('Satellite Imagery (Esri)', 'supercraft-of'),
                'topo'         => __('Topographic / Terrain (OpenTopoMap)', 'supercraft-of'),
                'streets'      => __('Standard OpenStreetMap', 'supercraft-of'),
                'custom'       => __('Custom Tile URL', 'supercraft-of'),
            ]
        ]);
        add_settings_field('custom_tile_url', __('Custom Tile URL (If "Custom Tile URL" selected)', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'custom_tile_url', 'default' => 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png']);
        add_settings_field('custom_tile_attr', __('Custom Tile Attribution', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_general', ['key' => 'custom_tile_attr', 'default' => '&copy; OpenStreetMap &copy; CARTO']);

        add_settings_section(
            'sc_of_text_labels',
            __('Titles & Text Labels', 'supercraft-of'),
            function () {
                echo '<span id="sc_sec_text_labels"></span>';
                echo '<p class="description">' . esc_html__('Customize all titles, headings, placeholders, and action labels displayed across the outlet finder.', 'supercraft-of') . '</p>';
            },
            'supercraft-outlet-finder'
        );

        add_settings_field('text_eyebrow', __('Eyebrow Text', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_eyebrow',
            'default' => 'Service Network',
            'placeholder' => 'e.g. Service Network or Outlet Finder',
            'description' => __('Small category tag displayed above the main headline in the sidebar header.', 'supercraft-of')
        ]);
        add_settings_field('text_headline', __('Main Title / Headline', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_headline',
            'default' => 'Find an Outlet',
            'placeholder' => 'e.g. Find an Outlet',
            'description' => __('Main title headline displayed in the sidebar header.', 'supercraft-of')
        ]);
        add_settings_field('text_search_placeholder', __('Search Input Placeholder', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_search_placeholder',
            'default' => 'Search area or outlet…',
            'placeholder' => 'Search area or outlet…',
            'description' => __('Placeholder text for the search filter input field.', 'supercraft-of')
        ]);
        add_settings_field('text_no_results', __('No Results Found Message', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_no_results',
            'default' => 'No outlets found',
            'placeholder' => 'No outlets found',
            'description' => __('Message displayed in the outlet list when no search matches are found.', 'supercraft-of')
        ]);
        add_settings_field('text_back_button', __('Back Button Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_back_button',
            'default' => 'All Outlets',
            'placeholder' => 'All Outlets',
            'description' => __('Label on the back button in the outlet detail view to return to the list.', 'supercraft-of')
        ]);
        add_settings_field('text_label_address', __('Address Field Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_label_address',
            'default' => 'Address',
            'placeholder' => 'Address',
            'description' => __('Label displayed above the outlet address in the detail panel.', 'supercraft-of')
        ]);
        add_settings_field('text_label_hours', __('Operating Hours Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_label_hours',
            'default' => 'Operating Hours',
            'placeholder' => 'Operating Hours',
            'description' => __('Label displayed above operating hours in the detail panel.', 'supercraft-of')
        ]);
        add_settings_field('text_label_phone', __('Phone Field Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_label_phone',
            'default' => 'Phone',
            'placeholder' => 'Phone',
            'description' => __('Label displayed above the phone number in the detail panel.', 'supercraft-of')
        ]);
        add_settings_field('text_btn_directions', __('"Get Directions" Button Text', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_btn_directions',
            'default' => 'Get Directions ↗',
            'placeholder' => 'Get Directions ↗',
            'description' => __('Button text for opening directions in Google Maps.', 'supercraft-of')
        ]);
        add_settings_field('text_btn_call', __('"Call Outlet" Button Text', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_btn_call',
            'default' => 'Call Outlet',
            'placeholder' => 'Call Outlet',
            'description' => __('Button text for initiating a phone call.', 'supercraft-of')
        ]);
        add_settings_field('text_footer_label', __('Sidebar Count Footer Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_footer_label',
            'default' => 'Outlets in',
            'placeholder' => 'Outlets in',
            'description' => __('Label displayed above the region name in the sidebar bottom counter.', 'supercraft-of')
        ]);
        add_settings_field('text_hud_label', __('Map HUD Counter Label', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_hud_label',
            'default' => 'Outlets',
            'placeholder' => 'Outlets',
            'description' => __('Label displayed below the total outlet number on the map HUD card.', 'supercraft-of')
        ]);
        add_settings_field('text_loading', __('Loader Subtitle Text', 'supercraft-of'), [$this, 'field_text'], 'supercraft-outlet-finder', 'sc_of_text_labels', [
            'key' => 'text_loading',
            'default' => 'Locating Outlets',
            'placeholder' => 'Locating Outlets',
            'description' => __('Subtitle text displayed on the initial loading overlay.', 'supercraft-of')
        ]);

        add_settings_section(
            'sc_of_typography',
            __('Typography & Font Sizes', 'supercraft-of'),
            function () {
                echo '<span id="sc_sec_typography"></span>';
                echo '<p class="description">' . esc_html__('Fine-tune the typography (font family) and font size for every individual text element across the outlet finder widget. Enter sizes with units (e.g. 16px, 1.2rem) or plain numbers for px.', 'supercraft-of') . '</p>';
            },
            'supercraft-outlet-finder'
        );

        $typo_items = [
            'typo_brand'        => [
                'label'       => __('Brand Name', 'supercraft-of'),
                'fs_key'      => 'fs_brand',
                'fs_default'  => '26px',
                'ff_key'      => 'ff_brand',
                'ff_default'  => 'primary',
                'description' => __('Sidebar header brand title.', 'supercraft-of'),
            ],
            'typo_brand_sub'    => [
                'label'       => __('Brand Subtitle', 'supercraft-of'),
                'fs_key'      => 'fs_brand_sub',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_brand_sub',
                'ff_default'  => 'body',
                'description' => __('Sidebar brand subtitle.', 'supercraft-of'),
            ],
            'typo_eyebrow'      => [
                'label'       => __('Eyebrow Text', 'supercraft-of'),
                'fs_key'      => 'fs_eyebrow',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_eyebrow',
                'ff_default'  => 'body',
                'description' => __('Small category tag above main title.', 'supercraft-of'),
            ],
            'typo_headline'     => [
                'label'       => __('Main Title / Headline', 'supercraft-of'),
                'fs_key'      => 'fs_headline',
                'fs_default'  => '20px',
                'ff_key'      => 'ff_headline',
                'ff_default'  => 'primary',
                'description' => __('Main heading in sidebar header.', 'supercraft-of'),
            ],
            'typo_search'       => [
                'label'       => __('Search Input & Placeholder', 'supercraft-of'),
                'fs_key'      => 'fs_search',
                'fs_default'  => '14px',
                'ff_key'      => 'ff_search',
                'ff_default'  => 'body',
                'description' => __('Search bar input text and placeholder.', 'supercraft-of'),
            ],
            'typo_no_res'       => [
                'label'       => __('No Results Message', 'supercraft-of'),
                'fs_key'      => 'fs_no_res',
                'fs_default'  => '11px',
                'ff_key'      => 'ff_no_res',
                'ff_default'  => 'body',
                'description' => __('Text displayed when no matching outlets are found.', 'supercraft-of'),
            ],
            'typo_item_name'    => [
                'label'       => __('Outlet List: Name', 'supercraft-of'),
                'fs_key'      => 'fs_item_name',
                'fs_default'  => '15px',
                'ff_key'      => 'ff_item_name',
                'ff_default'  => 'body',
                'description' => __('Outlet name in the sidebar list.', 'supercraft-of'),
            ],
            'typo_item_area'    => [
                'label'       => __('Outlet List: Area', 'supercraft-of'),
                'fs_key'      => 'fs_item_area',
                'fs_default'  => '10px',
                'ff_key'      => 'ff_item_area',
                'ff_default'  => 'body',
                'description' => __('Area label in the sidebar list.', 'supercraft-of'),
            ],
            'typo_item_num'     => [
                'label'       => __('Outlet List: Number', 'supercraft-of'),
                'fs_key'      => 'fs_item_num',
                'fs_default'  => '11px',
                'ff_key'      => 'ff_item_num',
                'ff_default'  => 'primary',
                'description' => __('Numbered badge in the sidebar list item.', 'supercraft-of'),
            ],
            'typo_footer_label' => [
                'label'       => __('Sidebar Footer: Label', 'supercraft-of'),
                'fs_key'      => 'fs_footer_label',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_footer_label',
                'ff_default'  => 'body',
                'description' => __('Bottom sidebar counter label ("Outlets in").', 'supercraft-of'),
            ],
            'typo_footer_num'   => [
                'label'       => __('Sidebar Footer: Count Number', 'supercraft-of'),
                'fs_key'      => 'fs_footer_num',
                'fs_default'  => '22px',
                'ff_key'      => 'ff_footer_num',
                'ff_default'  => 'primary',
                'description' => __('Total outlet number in sidebar footer.', 'supercraft-of'),
            ],
            'typo_back_btn'     => [
                'label'       => __('Detail Panel: Back Button', 'supercraft-of'),
                'fs_key'      => 'fs_back_btn',
                'fs_default'  => '10px',
                'ff_key'      => 'ff_back_btn',
                'ff_default'  => 'body',
                'description' => __('Return to list button in outlet detail view.', 'supercraft-of'),
            ],
            'typo_det_title'    => [
                'label'       => __('Detail Panel: Outlet Title', 'supercraft-of'),
                'fs_key'      => 'fs_det_title',
                'fs_default'  => '34px',
                'ff_key'      => 'ff_det_title',
                'ff_default'  => 'primary',
                'description' => __('Large outlet title heading in detail view.', 'supercraft-of'),
            ],
            'typo_det_tag'      => [
                'label'       => __('Detail Panel: Area Badge', 'supercraft-of'),
                'fs_key'      => 'fs_det_tag',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_det_tag',
                'ff_default'  => 'body',
                'description' => __('Golden area pill badge in detail view.', 'supercraft-of'),
            ],
            'typo_det_bignum'   => [
                'label'       => __('Detail Panel: Background Number', 'supercraft-of'),
                'fs_key'      => 'fs_det_bignum',
                'fs_default'  => '100px',
                'ff_key'      => 'ff_det_bignum',
                'ff_default'  => 'primary',
                'description' => __('Watermark outlet number behind the detail header.', 'supercraft-of'),
            ],
            'typo_det_desc'     => [
                'label'       => __('Detail Panel: Description', 'supercraft-of'),
                'fs_key'      => 'fs_det_desc',
                'fs_default'  => '13px',
                'ff_key'      => 'ff_det_desc',
                'ff_default'  => 'body',
                'description' => __('Outlet description paragraph in detail view.', 'supercraft-of'),
            ],
            'typo_det_lbl'      => [
                'label'       => __('Detail Panel: Section Labels', 'supercraft-of'),
                'fs_key'      => 'fs_det_lbl',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_det_lbl',
                'ff_default'  => 'body',
                'description' => __('Address, Hours, and Phone heading labels in detail view.', 'supercraft-of'),
            ],
            'typo_det_val'      => [
                'label'       => __('Detail Panel: Section Values', 'supercraft-of'),
                'fs_key'      => 'fs_det_val',
                'fs_default'  => '12px',
                'ff_key'      => 'ff_det_val',
                'ff_default'  => 'mono',
                'description' => __('Address, Hours, and Phone data values in detail view.', 'supercraft-of'),
            ],
            'typo_btn_dir'      => [
                'label'       => __('Button: "Get Directions"', 'supercraft-of'),
                'fs_key'      => 'fs_btn_dir',
                'fs_default'  => '15px',
                'ff_key'      => 'ff_btn_dir',
                'ff_default'  => 'primary',
                'description' => __('Primary action button for Google Maps directions.', 'supercraft-of'),
            ],
            'typo_btn_call'     => [
                'label'       => __('Button: "Call Outlet"', 'supercraft-of'),
                'fs_key'      => 'fs_btn_call',
                'fs_default'  => '12px',
                'ff_key'      => 'ff_btn_call',
                'ff_default'  => 'body',
                'description' => __('Secondary action button for calling outlet.', 'supercraft-of'),
            ],
            'typo_topbar_badge' => [
                'label'       => __('Map: Topbar Badge', 'supercraft-of'),
                'fs_key'      => 'fs_topbar_badge',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_topbar_badge',
                'ff_default'  => 'body',
                'description' => __('Top right floating region pill badge over the map.', 'supercraft-of'),
            ],
            'typo_hud_num'      => [
                'label'       => __('Map HUD: Counter Number', 'supercraft-of'),
                'fs_key'      => 'fs_hud_num',
                'fs_default'  => '28px',
                'ff_key'      => 'ff_hud_num',
                'ff_default'  => 'primary',
                'description' => __('Bottom right floating HUD outlet counter number.', 'supercraft-of'),
            ],
            'typo_hud_sm'       => [
                'label'       => __('Map HUD: "Outlets" Label', 'supercraft-of'),
                'fs_key'      => 'fs_hud_sm',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_hud_sm',
                'ff_default'  => 'body',
                'description' => __('Floating HUD "Outlets" label.', 'supercraft-of'),
            ],
            'typo_hud_loc'      => [
                'label'       => __('Map HUD: Location Tag', 'supercraft-of'),
                'fs_key'      => 'fs_hud_loc',
                'fs_default'  => '10px',
                'ff_key'      => 'ff_hud_loc',
                'ff_default'  => 'body',
                'description' => __('Floating HUD selected location / region name.', 'supercraft-of'),
            ],
            'typo_ldr_brand'    => [
                'label'       => __('Loader: Brand Title', 'supercraft-of'),
                'fs_key'      => 'fs_ldr_brand',
                'fs_default'  => '52px',
                'ff_key'      => 'ff_ldr_brand',
                'ff_default'  => 'primary',
                'description' => __('Initial loading screen brand title.', 'supercraft-of'),
            ],
            'typo_loader'       => [
                'label'       => __('Loader: Subtitle', 'supercraft-of'),
                'fs_key'      => 'fs_loader',
                'fs_default'  => '9px',
                'ff_key'      => 'ff_loader',
                'ff_default'  => 'body',
                'description' => __('Initial loading screen subtitle.', 'supercraft-of'),
            ],
        ];

        foreach ($typo_items as $field_id => $cfg) {
            add_settings_field(
                $field_id,
                $cfg['label'],
                [$this, 'field_typography_item'],
                'supercraft-outlet-finder',
                'sc_of_typography',
                $cfg
            );
        }

        add_settings_section(
            'sc_of_display',
            __('Display Options', 'supercraft-of'),
            function () {
                echo '<span id="sc_sec_display"></span>';
            },
            'supercraft-outlet-finder'
        );

        add_settings_field('show_hours', __('Operating Hours', 'supercraft-of'), [$this, 'field_toggle'], 'supercraft-outlet-finder', 'sc_of_display', ['key' => 'show_hours', 'description' => __('Display operating hours in the outlet detail panel.', 'supercraft-of')]);
        add_settings_field('show_phone', __('Phone Number', 'supercraft-of'), [$this, 'field_toggle'], 'supercraft-outlet-finder', 'sc_of_display', ['key' => 'show_phone', 'description' => __('Display phone number in the outlet detail panel.', 'supercraft-of')]);
        add_settings_field('show_call_btn', __('"Call Outlet" Button', 'supercraft-of'), [$this, 'field_toggle'], 'supercraft-outlet-finder', 'sc_of_display', ['key' => 'show_call_btn', 'description' => __('Display the direct call action button in the outlet detail panel.', 'supercraft-of')]);
        add_settings_field('show_description', __('Outlet Description', 'supercraft-of'), [$this, 'field_toggle'], 'supercraft-outlet-finder', 'sc_of_display', ['key' => 'show_description', 'description' => __('Display outlet description in the outlet detail panel.', 'supercraft-of')]);

        add_settings_section(
            'sc_of_styling',
            __('Styling', 'supercraft-of'),
            function () {
                echo '<span id="sc_sec_styling"></span>';
            },
            'supercraft-outlet-finder'
        );

        $colors = [
            'color_primary'   => ['label' => __('Theme Primary Color', 'supercraft-of'), 'default' => '#F2C94C'],
            'color_marker'    => ['label' => __('Default Marker Color', 'supercraft-of'), 'default' => '#F2C94C'],
            'color_bg'        => ['label' => __('Background', 'supercraft-of'), 'default' => '#070707'],
            'color_surface'   => ['label' => __('Surface / Card', 'supercraft-of'), 'default' => '#181818'],
            'color_surface2'  => ['label' => __('Surface Light', 'supercraft-of'), 'default' => '#202020'],
            'color_text'      => ['label' => __('Theme Text Color', 'supercraft-of'), 'default' => '#EDEAE4'],
            'color_muted'     => ['label' => __('Muted Text', 'supercraft-of'), 'default' => '#555555'],
            'color_muted2'    => ['label' => __('Muted Text Light', 'supercraft-of'), 'default' => '#777777'],
            'color_border'    => ['label' => __('Border', 'supercraft-of'), 'default' => 'rgba(255,255,255,0.06)'],
            'color_border2'   => ['label' => __('Border Light', 'supercraft-of'), 'default' => 'rgba(255,255,255,0.11)'],
            'color_dark'      => ['label' => __('Sidebar Background', 'supercraft-of'), 'default' => '#101010'],
        ];

        foreach ($colors as $key => $cfg) {
            add_settings_field(
                $key,
                $cfg['label'],
                [$this, 'field_color_mapping'],
                'supercraft-outlet-finder',
                'sc_of_styling',
                ['key' => $key, 'default' => $cfg['default']]
            );
        }

        $fonts = [
            'font_heading' => ['label' => __('Heading Font', 'supercraft-of'), 'default' => 'inherit'],
            'font_body'    => ['label' => __('Body Font', 'supercraft-of'), 'default' => 'inherit'],
        ];

        foreach ($fonts as $key => $cfg) {
            add_settings_field(
                $key,
                $cfg['label'],
                [$this, 'field_font_mapping'],
                'supercraft-outlet-finder',
                'sc_of_styling',
                ['key' => $key, 'default' => $cfg['default']]
            );
        }
    }

    public function sanitize($input)
    {
        $defaults = $this->defaults();
        $output = [];
        $toggle_keys = ['show_hours', 'show_phone', 'show_call_btn', 'show_description'];

        foreach ($defaults as $key => $default) {
            if (in_array($key, $toggle_keys, true)) {
                $output[$key] = (!empty($input[$key]) && $input[$key] === '1') ? '1' : '0';
                continue;
            }

            if (isset($input[$key])) {
                $val = sanitize_text_field($input[$key]);
                if ($val === 'custom' && isset($_POST['sc_of_options_custom_' . $key])) {
                    $output[$key] = sanitize_text_field($_POST['sc_of_options_custom_' . $key]);
                } else {
                    $output[$key] = $val;
                }
            } else {
                $output[$key] = $default;
            }
        }
        return $output;
    }

    private function defaults()
    {
        return [
            'brand_name'      => 'SUPERCRAFT',
            'brand_subtitle'  => 'Premium Outlet Finder',
            'region'          => 'Klang Valley',
            'region_sub'      => 'Klang Valley · Malaysia',
            'map_center_lat'  => '3.1',
            'map_center_lng'  => '101.62',
            'map_zoom'        => '11',
            'fly_zoom'        => '15',
            'color_primary'   => '#F2C94C',
            'color_marker'    => '#F2C94C',
            'color_bg'        => '#070707',
            'color_surface'   => '#181818',
            'color_surface2'  => '#202020',
            'color_text'      => '#EDEAE4',
            'color_muted'     => '#555555',
            'color_muted2'    => '#777777',
            'color_border'    => 'rgba(255,255,255,0.06)',
            'color_border2'   => 'rgba(255,255,255,0.11)',
            'color_dark'      => '#101010',
            'font_heading'    => 'inherit',
            'font_body'       => 'inherit',
            'marker_style'     => 'numbered',
            'map_style'        => 'voyager',
            'custom_tile_url'  => 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            'custom_tile_attr' => '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            'show_hours'       => '1',
            'show_phone'       => '1',
            'show_call_btn'    => '1',
            'show_description' => '1',
            'text_eyebrow'            => 'Service Network',
            'text_headline'           => 'Find an Outlet',
            'text_search_placeholder' => 'Search area or outlet…',
            'text_no_results'         => 'No outlets found',
            'text_loading'            => 'Locating Outlets',
            'text_back_button'        => 'All Outlets',
            'text_label_address'      => 'Address',
            'text_label_hours'        => 'Operating Hours',
            'text_label_phone'        => 'Phone',
            'text_btn_directions'     => 'Get Directions ↗',
            'text_btn_call'           => 'Call Outlet',
            'text_footer_label'       => 'Outlets in',
            'text_hud_label'          => 'Outlets',
            // Typography: Font Sizes & Font Families
            'fs_brand'         => '26px',
            'ff_brand'         => 'primary',
            'fs_brand_sub'     => '9px',
            'ff_brand_sub'     => 'body',
            'fs_eyebrow'       => '9px',
            'ff_eyebrow'       => 'body',
            'fs_headline'      => '20px',
            'ff_headline'      => 'primary',
            'fs_search'        => '14px',
            'ff_search'        => 'body',
            'fs_no_res'        => '11px',
            'ff_no_res'        => 'body',
            'fs_item_name'     => '15px',
            'ff_item_name'     => 'body',
            'fs_item_area'     => '10px',
            'ff_item_area'     => 'body',
            'fs_item_num'      => '11px',
            'ff_item_num'      => 'primary',
            'fs_footer_label'  => '9px',
            'ff_footer_label'  => 'body',
            'fs_footer_num'    => '22px',
            'ff_footer_num'    => 'primary',
            'fs_back_btn'      => '10px',
            'ff_back_btn'      => 'body',
            'fs_det_title'     => '34px',
            'ff_det_title'     => 'primary',
            'fs_det_tag'       => '9px',
            'ff_det_tag'       => 'body',
            'fs_det_bignum'    => '100px',
            'ff_det_bignum'    => 'primary',
            'fs_det_desc'      => '13px',
            'ff_det_desc'      => 'body',
            'fs_det_lbl'       => '9px',
            'ff_det_lbl'       => 'body',
            'fs_det_val'       => '12px',
            'ff_det_val'       => 'mono',
            'fs_btn_dir'       => '15px',
            'ff_btn_dir'       => 'primary',
            'fs_btn_call'      => '12px',
            'ff_btn_call'      => 'body',
            'fs_topbar_badge'  => '9px',
            'ff_topbar_badge'  => 'body',
            'fs_hud_num'       => '28px',
            'ff_hud_num'       => 'primary',
            'fs_hud_sm'        => '9px',
            'ff_hud_sm'        => 'body',
            'fs_hud_loc'       => '10px',
            'ff_hud_loc'       => 'body',
            'fs_ldr_brand'     => '52px',
            'ff_ldr_brand'     => 'primary',
            'fs_loader'        => '9px',
            'ff_loader'        => 'body',
        ];
    }

    public static function get($key)
    {
        $instance = new self();
        $defaults = $instance->defaults();
        $options = get_option('sc_of_options', []);
        return isset($options[$key]) ? $options[$key] : $defaults[$key];
    }

    public static function is_validated()
    {
        if (defined('SC_OF_ALLOW_UNVALIDATED') && SC_OF_ALLOW_UNVALIDATED) {
            return true;
        }

        $local_status = get_option('sc_of_validation_status', 'valid') === 'valid';

        return apply_filters('supercraft_is_plugin_validated', $local_status, 'supercraft-outlet-finder');
    }

    public function render_page()
    {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
        $is_master_active = has_filter('supercraft_is_plugin_validated');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Supercraft Outlet Finder Settings', 'supercraft-of'); ?></h1>
            
            <?php if ($is_master_active): ?>
                <div class="notice notice-info">
                    <p><?php esc_html_e('License validation is managed globally by the Supercraft Master Plugin.', 'supercraft-of'); ?></p>
                </div>
            <?php endif; ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=supercraft-outlet-finder&tab=settings" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'supercraft-of'); ?></a>
                <a href="?page=supercraft-outlet-finder&tab=outlets" class="nav-tab <?php echo $active_tab === 'outlets' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Manage Outlets', 'supercraft-of'); ?></a>
            </h2>

            <?php if ($active_tab === 'settings') : ?>
                <div class="sc-of-nav-pills" style="margin: 20px 0 12px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="#sc_sec_general" class="button button-secondary"><?php esc_html_e('General', 'supercraft-of'); ?></a>
                    <a href="#sc_sec_text_labels" class="button button-secondary"><?php esc_html_e('Titles & Labels', 'supercraft-of'); ?></a>
                    <a href="#sc_sec_typography" class="button button-secondary"><?php esc_html_e('Typography & Font Sizes', 'supercraft-of'); ?></a>
                    <a href="#sc_sec_display" class="button button-secondary"><?php esc_html_e('Display Options', 'supercraft-of'); ?></a>
                    <a href="#sc_sec_styling" class="button button-secondary"><?php esc_html_e('Colors & Styling', 'supercraft-of'); ?></a>
                </div>
                <form method="post" action="options.php">
                    <?php
                    settings_fields($this->option_group);
                    do_settings_sections('supercraft-outlet-finder');
                    submit_button();
                    ?>
                </form>
            <?php else : ?>
                <?php $this->render_outlets_table(); ?>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_outlets_table() {
        $outlets = get_posts([
            'post_type' => 'sc_outlet',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ]);
        ?>
        <div class="sc-outlets-actions" style="margin: 20px 0;">
            <a href="<?php echo admin_url('post-new.php?post_type=sc_outlet'); ?>" class="page-title-action"><?php esc_html_e('Add New Outlet', 'supercraft-of'); ?></a>
        </div>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th class="manage-column column-thumbnail" style="width: 60px;"></th>
                    <th class="manage-column column-title column-primary"><?php esc_html_e('Title', 'supercraft-of'); ?></th>
                    <th class="manage-column column-area"><?php esc_html_e('Area', 'supercraft-of'); ?></th>
                    <th class="manage-column column-phone"><?php esc_html_e('Phone', 'supercraft-of'); ?></th>
                    <th class="manage-column column-order" style="width: 80px;"><?php esc_html_e('Order', 'supercraft-of'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($outlets)) : ?>
                    <tr><td colspan="5"><?php esc_html_e('No outlets found.', 'supercraft-of'); ?></td></tr>
                <?php else : ?>
                    <?php foreach ($outlets as $post) : ?>
                        <tr>
                            <td>
                                <?php if (has_post_thumbnail($post->ID)) : ?>
                                    <?php echo get_the_post_thumbnail($post->ID, [50, 50], ['style' => 'border-radius: 4px; display: block;']); ?>
                                <?php endif; ?>
                            </td>
                            <td class="title column-title has-row-actions column-primary page-title">
                                <strong><a class="row-title" href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a></strong>
                                <div class="row-actions">
                                    <span class="edit"><a href="<?php echo get_edit_post_link($post->ID); ?>"><?php esc_html_e('Edit', 'supercraft-of'); ?></a> | </span>
                                    <span class="trash"><a href="<?php echo get_delete_post_link($post->ID); ?>" class="submitdelete"><?php esc_html_e('Trash', 'supercraft-of'); ?></a></span>
                                </div>
                            </td>
                            <td><?php echo esc_html(get_post_meta($post->ID, '_sc_outlet_area', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($post->ID, '_sc_outlet_phone', true)); ?></td>
                            <td><?php echo esc_html($post->menu_order); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
    }

    public function field_text($args)
    {
        $value = self::get($args['key']);
        $placeholder = $args['placeholder'] ?? '';
        ?>
        <input type="text" name="sc_of_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>" class="regular-text"
               <?php echo !empty($placeholder) ? 'placeholder="' . esc_attr($placeholder) . '"' : ''; ?>>
        <?php if (!empty($args['description'])) : ?>
            <p class="description" style="margin-top: 6px;"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }

    public function field_number($args)
    {
        $value = self::get($args['key']);
        ?>
        <input type="number" name="sc_of_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min'] ?? ''); ?>"
               max="<?php echo esc_attr($args['max'] ?? ''); ?>"
               step="0.1" class="small-text">
        <?php
    }

    public function field_select($args)
    {
        $value = self::get($args['key']);
        $options = $args['options'] ?? [];
        ?>
        <select name="sc_of_options[<?php echo esc_attr($args['key']); ?>]">
            <?php foreach ($options as $k => $label) : ?>
                <option value="<?php echo esc_attr($k); ?>" <?php selected($value, $k); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function field_color_mapping($args)
    {
        $value = self::get($args['key']);
        $is_custom = (strpos($value, 'e-global-color') === false);
        $mapped_value = $is_custom ? 'custom' : $value;
        $custom_value = $is_custom ? $value : $args['default'];
        
        $elementor_colors = $this->get_elementor_colors();
        ?>
        <select name="sc_of_options[<?php echo esc_attr($args['key']); ?>]" class="sc-of-color-mapper" style="margin-bottom: 8px;">
            <?php foreach ($elementor_colors as $k => $label) : ?>
                <option value="<?php echo esc_attr($k); ?>" <?php selected($mapped_value, $k); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="sc-of-custom-color-wrapper" style="<?php echo $mapped_value === 'custom' ? '' : 'display:none;'; ?>">
            <input type="text" name="sc_of_options_custom_<?php echo esc_attr($args['key']); ?>"
                   value="<?php echo esc_attr($custom_value); ?>"
                   class="sc-of-color-picker" data-default-color="<?php echo esc_attr($args['default']); ?>">
        </div>
        <?php
    }

    public function field_font_mapping($args)
    {
        $value = self::get($args['key']);
        $is_custom = (strpos($value, 'e-global-typography') === false);
        $mapped_value = $is_custom ? 'custom' : $value;
        $custom_value = $is_custom ? $value : $args['default'];

        $elementor_fonts = $this->get_elementor_fonts();
        ?>
        <select name="sc_of_options[<?php echo esc_attr($args['key']); ?>]" class="sc-of-font-mapper" style="margin-bottom: 8px;">
            <?php foreach ($elementor_fonts as $k => $label) : ?>
                <option value="<?php echo esc_attr($k); ?>" <?php selected($mapped_value, $k); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="sc-of-custom-font-wrapper" style="<?php echo $mapped_value === 'custom' ? '' : 'display:none;'; ?>">
            <input type="text" name="sc_of_options_custom_<?php echo esc_attr($args['key']); ?>"
                   value="<?php echo esc_attr($custom_value); ?>"
                   class="regular-text" placeholder="e.g. 'Montserrat', sans-serif">
        </div>
        <?php
    }

    public function field_toggle($args)
    {
        $value = self::get($args['key']);
        $checked = ($value === '1' || $value === true || $value === 1);
        ?>
        <label class="sc-of-switch">
            <input type="checkbox" name="sc_of_options[<?php echo esc_attr($args['key']); ?>]" value="1" <?php checked($checked, true); ?>>
            <span class="sc-of-slider"></span>
        </label>
        <?php if (!empty($args['description'])) : ?>
            <p class="description" style="margin-top: 6px;"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }

    public function field_typography_item($args)
    {
        $fs_key     = $args['fs_key'];
        $fs_default = $args['fs_default'] ?? '';
        $ff_key     = $args['ff_key'];
        $ff_default = $args['ff_default'] ?? 'body';

        $fs_val = self::get($fs_key);
        $ff_val = self::get($ff_key);

        $builtins = ['inherit', 'primary', 'body', 'mono'];
        $is_elementor = (strpos($ff_val, 'e-global-typography') === 0);
        $is_custom = (!in_array($ff_val, $builtins, true) && !$is_elementor);

        $mapped_ff = $is_custom ? 'custom' : $ff_val;
        $custom_ff = $is_custom ? $ff_val : '';

        $elementor_fonts = $this->get_elementor_fonts();
        ?>
        <div class="sc-of-typo-control" style="display:inline-flex; align-items:center; gap:14px; flex-wrap:wrap; background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:6px 12px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <label style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:600; color:#475569;"><?php esc_html_e('Size', 'supercraft-of'); ?>:</label>
                <input type="text" name="sc_of_options[<?php echo esc_attr($fs_key); ?>]"
                       value="<?php echo esc_attr($fs_val); ?>"
                       placeholder="<?php echo esc_attr($fs_default); ?>"
                       class="small-text" style="width:75px; text-align:center;">
            </div>
            <div style="height:18px; width:1px; background:#cbd5e1;"></div>
            <div style="display:flex; align-items:center; gap:6px;">
                <label style="font-size:11px; text-transform:uppercase; letter-spacing:0.5px; font-weight:600; color:#475569;"><?php esc_html_e('Font', 'supercraft-of'); ?>:</label>
                <select name="sc_of_options[<?php echo esc_attr($ff_key); ?>]" class="sc-of-font-mapper" style="min-width:150px;">
                    <option value="inherit" <?php selected($mapped_ff, 'inherit'); ?>><?php esc_html_e('Default / Inherit', 'supercraft-of'); ?></option>
                    <option value="primary" <?php selected($mapped_ff, 'primary'); ?>><?php esc_html_e('Heading Font', 'supercraft-of'); ?></option>
                    <option value="body" <?php selected($mapped_ff, 'body'); ?>><?php esc_html_e('Body Font', 'supercraft-of'); ?></option>
                    <option value="mono" <?php selected($mapped_ff, 'mono'); ?>><?php esc_html_e('Monospace', 'supercraft-of'); ?></option>
                    <?php foreach ($elementor_fonts as $k => $label) : ?>
                        <?php if ($k !== 'custom') : ?>
                            <option value="<?php echo esc_attr($k); ?>" <?php selected($mapped_ff, $k); ?>><?php echo esc_html($label); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <option value="custom" <?php selected($mapped_ff, 'custom'); ?>><?php esc_html_e('Custom Font…', 'supercraft-of'); ?></option>
                </select>
                <div class="sc-of-custom-font-wrapper" style="<?php echo $mapped_ff === 'custom' ? '' : 'display:none;'; ?>">
                    <input type="text" name="sc_of_options_custom_<?php echo esc_attr($ff_key); ?>"
                           value="<?php echo esc_attr($custom_ff); ?>"
                           placeholder="e.g. 'Inter', sans-serif"
                           class="regular-text" style="width:160px;">
                </div>
            </div>
        </div>
        <?php if (!empty($args['description'])) : ?>
            <p class="description" style="margin-top:4px; font-size:12px;"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }
}