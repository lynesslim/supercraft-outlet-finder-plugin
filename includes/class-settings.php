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
        add_action('admin_menu', [$this, 'ensure_supercraft_menu'], 9);
        add_action('admin_menu', [$this, 'add_menu_page'], 10);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    public function ensure_supercraft_menu()
    {
        global $menu;
        $has_supercraft = false;
        if (is_array($menu)) {
            foreach ($menu as $item) {
                if (isset($item[2]) && $item[2] === 'supercraft') {
                    $has_supercraft = true;
                    break;
                }
            }
        }
        if (!$has_supercraft) {
            add_menu_page(
                __('Supercraft', 'supercraft-of'),
                __('Supercraft', 'supercraft-of'),
                'manage_options',
                'supercraft',
                function () {
                    echo '<div class="wrap"><h1>' . esc_html__('Supercraft Dashboard', 'supercraft-of') . '</h1><p>' . esc_html__('Welcome to Supercraft tools.', 'supercraft-of') . '</p></div>';
                },
                'dashicons-admin-generic',
                30
            );
        }
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
        add_submenu_page(
            'supercraft',
            __('Supercraft Outlet Finder Settings', 'supercraft-of'),
            __('Outlet Finder Settings', 'supercraft-of'),
            'manage_options',
            'supercraft-outlet-finder',
            [$this, 'render_page']
        );
    }

    public function register_settings()
    {
        register_setting($this->option_group, $this->option_name, [$this, 'sanitize']);

        add_settings_section(
            'sc_of_general',
            __('General Settings', 'supercraft-of'),
            null,
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

        add_settings_section(
            'sc_of_styling',
            __('Styling', 'supercraft-of'),
            null,
            'supercraft-outlet-finder'
        );

        $colors = [
            'color_primary'   => ['label' => __('Theme Primary Color', 'supercraft-of'), 'default' => '#F2C94C'],
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

        foreach ($defaults as $key => $default) {
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
            'marker_style'    => 'numbered',
        ];
    }

    public static function get($key)
    {
        $instance = new self();
        $defaults = $instance->defaults();
        $options = get_option('sc_of_options', []);
        return isset($options[$key]) ? $options[$key] : $defaults[$key];
    }

    public function render_page()
    {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Supercraft Outlet Finder Settings', 'supercraft-of'); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=supercraft-outlet-finder&tab=settings" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Settings', 'supercraft-of'); ?></a>
                <a href="?page=supercraft-outlet-finder&tab=outlets" class="nav-tab <?php echo $active_tab === 'outlets' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Manage Outlets', 'supercraft-of'); ?></a>
            </h2>

            <?php if ($active_tab === 'settings') : ?>
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
        ?>
        <input type="text" name="sc_of_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>" class="regular-text">
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
}