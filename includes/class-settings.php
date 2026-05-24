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
            });
            </script>
            <?php
        });
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

        add_settings_field(
            'brand_name',
            __('Brand Name', 'supercraft-of'),
            [$this, 'field_text'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'brand_name', 'default' => 'SUPERCRAFT']
        );

        add_settings_field(
            'brand_subtitle',
            __('Brand Subtitle', 'supercraft-of'),
            [$this, 'field_text'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'brand_subtitle', 'default' => 'Premium Outlet Finder']
        );

        add_settings_field(
            'region',
            __('Region Label', 'supercraft-of'),
            [$this, 'field_text'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'region', 'default' => 'Klang Valley']
        );

        add_settings_field(
            'region_sub',
            __('Region Subtitle', 'supercraft-of'),
            [$this, 'field_text'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'region_sub', 'default' => 'Klang Valley · Malaysia']
        );

        add_settings_field(
            'map_center_lat',
            __('Map Center Latitude', 'supercraft-of'),
            [$this, 'field_text'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'map_center_lat', 'default' => '3.1']
        );

        add_settings_field(
            'map_center_lng',
            __('Map Center Longitude', 'supercraft-of'),
            [$this, 'field_text'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'map_center_lng', 'default' => '101.62']
        );

        add_settings_field(
            'map_zoom',
            __('Default Map Zoom', 'supercraft-of'),
            [$this, 'field_number'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'map_zoom', 'default' => '11', 'min' => 1, 'max' => 20]
        );

        add_settings_field(
            'fly_zoom',
            __('Fly-to Zoom Level', 'supercraft-of'),
            [$this, 'field_number'],
            'supercraft-outlet-finder',
            'sc_of_general',
            ['key' => 'fly_zoom', 'default' => '15', 'min' => 1, 'max' => 20]
        );

        add_settings_section(
            'sc_of_styling',
            __('Styling', 'supercraft-of'),
            null,
            'supercraft-outlet-finder'
        );

        $colors = [
            'color_primary'   => ['label' => __('Primary Accent', 'supercraft-of'), 'default' => '#F2C94C'],
            'color_bg'        => ['label' => __('Background', 'supercraft-of'), 'default' => '#070707'],
            'color_surface'   => ['label' => __('Surface / Card', 'supercraft-of'), 'default' => '#181818'],
            'color_surface2'  => ['label' => __('Surface Light', 'supercraft-of'), 'default' => '#202020'],
            'color_text'      => ['label' => __('Text', 'supercraft-of'), 'default' => '#EDEAE4'],
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
                [$this, 'field_color'],
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
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Supercraft Outlet Finder Settings', 'supercraft-of'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->option_group);
                do_settings_sections('supercraft-outlet-finder');
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

    public function field_color($args)
    {
        $value = self::get($args['key']);
        ?>
        <input type="text" name="sc_of_options[<?php echo esc_attr($args['key']); ?>]"
               value="<?php echo esc_attr($value); ?>"
               class="sc-of-color-picker" data-default-color="<?php echo esc_attr($args['default']); ?>">
        <?php
    }
}
