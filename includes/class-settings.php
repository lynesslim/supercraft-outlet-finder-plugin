<?php

if (!defined('ABSPATH')) {
    exit;
}

class TS_TOF_Settings
{
    private $option_group = 'ts_tof_settings';
    private $option_name = 'ts_tof_options';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    public function enqueue_admin($hook)
    {
        if ('settings_page_ts-tyre-outlets' !== $hook) {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        add_action('admin_footer', function () {
            ?>
            <script>
            jQuery(function ($) {
                $('.ts-tof-color-picker').wpColorPicker();
            });
            </script>
            <?php
        });
    }

    public function add_menu_page()
    {
        add_options_page(
            __('TS Tyre Outlet Finder', 'ts-tof'),
            __('TS Tyre Outlets', 'ts-tof'),
            'manage_options',
            'ts-tyre-outlets',
            [$this, 'render_page']
        );
    }

    public function register_settings()
    {
        register_setting($this->option_group, $this->option_name, [$this, 'sanitize']);

        add_settings_section(
            'ts_tof_general',
            __('General Settings', 'ts-tof'),
            null,
            'ts-tyre-outlets'
        );

        add_settings_field(
            'brand_name',
            __('Brand Name', 'ts-tof'),
            [$this, 'field_text'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'brand_name', 'default' => 'TS TYRE']
        );

        add_settings_field(
            'brand_subtitle',
            __('Brand Subtitle', 'ts-tof'),
            [$this, 'field_text'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'brand_subtitle', 'default' => 'Premium Tyre Solutions']
        );

        add_settings_field(
            'region',
            __('Region Label', 'ts-tof'),
            [$this, 'field_text'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'region', 'default' => 'Klang Valley']
        );

        add_settings_field(
            'region_sub',
            __('Region Subtitle', 'ts-tof'),
            [$this, 'field_text'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'region_sub', 'default' => 'Klang Valley · Malaysia']
        );

        add_settings_field(
            'map_center_lat',
            __('Map Center Latitude', 'ts-tof'),
            [$this, 'field_text'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'map_center_lat', 'default' => '3.1']
        );

        add_settings_field(
            'map_center_lng',
            __('Map Center Longitude', 'ts-tof'),
            [$this, 'field_text'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'map_center_lng', 'default' => '101.62']
        );

        add_settings_field(
            'map_zoom',
            __('Default Map Zoom', 'ts-tof'),
            [$this, 'field_number'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'map_zoom', 'default' => '11', 'min' => 1, 'max' => 20]
        );

        add_settings_field(
            'fly_zoom',
            __('Fly-to Zoom Level', 'ts-tof'),
            [$this, 'field_number'],
            'ts-tyre-outlets',
            'ts_tof_general',
            ['key' => 'fly_zoom', 'default' => '15', 'min' => 1, 'max' => 20]
        );

        add_settings_section(
            'ts_tof_styling',
            __('Styling', 'ts-tof'),
            null,
            'ts-tyre-outlets'
        );

        $colors = [
            'color_primary'   => ['label' => __('Primary Accent', 'ts-tof'), 'default' => '#F2C94C'],
            'color_bg'        => ['label' => __('Background', 'ts-tof'), 'default' => '#070707'],
            'color_surface'   => ['label' => __('Surface / Card', 'ts-tof'), 'default' => '#181818'],
            'color_surface2'  => ['label' => __('Surface Light', 'ts-tof'), 'default' => '#202020'],
            'color_text'      => ['label' => __('Text', 'ts-tof'), 'default' => '#EDEAE4'],
            'color_muted'     => ['label' => __('Muted Text', 'ts-tof'), 'default' => '#555555'],
            'color_muted2'    => ['label' => __('Muted Text Light', 'ts-tof'), 'default' => '#777777'],
            'color_border'    => ['label' => __('Border', 'ts-tof'), 'default' => 'rgba(255,255,255,0.06)'],
            'color_border2'   => ['label' => __('Border Light', 'ts-tof'), 'default' => 'rgba(255,255,255,0.11)'],
            'color_dark'      => ['label' => __('Sidebar Background', 'ts-tof'), 'default' => '#101010'],
        ];

        foreach ($colors as $key => $cfg) {
            add_settings_field(
                $key,
                $cfg['label'],
                [$this, 'field_color'],
                'ts-tyre-outlets',
                'ts_tof_styling',
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
                $output[$key] = sanitize_text_field($input[$key]);
            } else {
                $output[$key] = $default;
            }
        }

        return $output;
    }

    private function defaults()
    {
        return [
            'brand_name'      => 'TS TYRE',
            'brand_subtitle'  => 'Premium Tyre Solutions',
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
        ];
    }

    public static function get($key)
    {
        $instance = new self();
        $defaults = $instance->defaults();
        $options = get_option('ts_tof_options', []);
        return isset($options[$key]) ? $options[$key] : $defaults[$key];
    }

    public function render_page()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('TS Tyre Outlet Finder Settings', 'ts-tof'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->option_group);
                do_settings_sections('ts-tyre-outlets');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function field_text($args)
    {
        $value = self::get($args['key']);
        ?>
        <input type="text" name="ts_tof_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>" class="regular-text">
        <?php
    }

    public function field_number($args)
    {
        $value = self::get($args['key']);
        ?>
        <input type="number" name="ts_tof_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>"
               min="<?php echo esc_attr($args['min'] ?? ''); ?>"
               max="<?php echo esc_attr($args['max'] ?? ''); ?>"
               step="0.1" class="small-text">
        <?php
    }

    public function field_color($args)
    {
        $value = self::get($args['key']);
        ?>
        <input type="text" name="ts_tof_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>"
               class="ts-tof-color-picker" data-default-color="<?php echo esc_attr($args['default']); ?>">
        <?php
    }
}
