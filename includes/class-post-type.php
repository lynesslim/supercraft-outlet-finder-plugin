<?php

if (!defined('ABSPATH')) {
    exit;
}

class SC_OF_Post_Type
{
    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_sc_outlet', [$this, 'save_meta'], 10, 2);
        add_filter('manage_sc_outlet_posts_columns', [$this, 'columns']);
        add_action('manage_sc_outlet_posts_custom_column', [$this, 'column_data'], 10, 2);
        add_filter('manage_edit-sc_outlet_sortable_columns', [$this, 'sortable_columns']);
        add_action('pre_get_posts', [$this, 'sort_backend_outlets']);
        add_action('admin_menu', [$this, 'ensure_supercraft_menu'], 9);
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

    public function register()
    {
        $labels = [
            'name'               => __('Outlets', 'supercraft-of'),
            'singular_name'      => __('Outlet', 'supercraft-of'),
            'add_new'            => __('Add New', 'supercraft-of'),
            'add_new_item'       => __('Add New Outlet', 'supercraft-of'),
            'edit_item'          => __('Edit Outlet', 'supercraft-of'),
            'new_item'           => __('New Outlet', 'supercraft-of'),
            'view_item'          => __('View Outlet', 'supercraft-of'),
            'search_items'       => __('Search Outlets', 'supercraft-of'),
            'not_found'          => __('No outlets found', 'supercraft-of'),
            'not_found_in_trash' => __('No outlets found in Trash', 'supercraft-of'),
            'all_items'          => __('All Outlets', 'supercraft-of'),
            'menu_name'          => __('Outlets', 'supercraft-of'),
        ];

        register_post_type('sc_outlet', [
            'labels'       => $labels,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'menu_icon'    => 'dashicons-location',
            'supports'     => ['title', 'thumbnail', 'page-attributes'],
            'rewrite'      => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'sc_outlet_details',
            __('Outlet Details', 'supercraft-of'),
            [$this, 'render_meta_box'],
            'sc_outlet',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('sc_outlet_save', 'sc_outlet_nonce');

        $fields = [
            '_sc_outlet_area' => __('Area', 'supercraft-of'),
            '_sc_outlet_address' => __('Address', 'supercraft-of'),
            '_sc_outlet_phone' => __('Phone', 'supercraft-of'),
            '_sc_outlet_hours' => __('Operating Hours', 'supercraft-of'),
            '_sc_outlet_lat' => __('Latitude', 'supercraft-of'),
            '_sc_outlet_lng' => __('Longitude', 'supercraft-of'),
            '_sc_outlet_maps_url' => __('Google Maps URL', 'supercraft-of'),
        ];

        $values = [];
        foreach ($fields as $key => $label) {
            $values[$key] = get_post_meta($post->ID, $key, true);
        }

        ?>
        <style>
            .sc-of-field {
                margin-bottom: 14px;
            }
            .sc-of-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 4px;
            }
            .sc-of-field input,
            .sc-of-field textarea {
                width: 100%;
                max-width: 500px;
            }
            .sc-of-field textarea {
                min-height: 70px;
            }
            .sc-of-row {
                display: flex;
                gap: 14px;
            }
            .sc-of-row .sc-of-field {
                flex: 1;
            }
        </style>

        <div class="sc-of-field">
            <label for="_sc_outlet_area"><?php esc_html_e('Area', 'supercraft-of'); ?></label>
            <input type="text" id="_sc_outlet_area" name="_sc_outlet_area"
                   value="<?php echo esc_attr($values['_sc_outlet_area']); ?>">
        </div>

        <div class="sc-of-field">
            <label for="_sc_outlet_address"><?php esc_html_e('Address', 'supercraft-of'); ?></label>
            <textarea id="_sc_outlet_address" name="_sc_outlet_address"><?php echo esc_textarea($values['_sc_outlet_address']); ?></textarea>
        </div>

        <div class="sc-of-field">
            <label for="_sc_outlet_phone"><?php esc_html_e('Phone', 'supercraft-of'); ?></label>
            <input type="text" id="_sc_outlet_phone" name="_sc_outlet_phone"
                   value="<?php echo esc_attr($values['_sc_outlet_phone']); ?>">
        </div>

        <div class="sc-of-field">
            <label for="_sc_outlet_hours"><?php esc_html_e('Operating Hours', 'supercraft-of'); ?></label>
            <textarea id="_sc_outlet_hours" name="_sc_outlet_hours"><?php echo esc_textarea($values['_sc_outlet_hours']); ?></textarea>
        </div>

        <div class="sc-of-row">
            <div class="sc-of-field">
                <label for="_sc_outlet_lat"><?php esc_html_e('Latitude', 'supercraft-of'); ?></label>
                <input type="text" id="_sc_outlet_lat" name="_sc_outlet_lat"
                       value="<?php echo esc_attr($values['_sc_outlet_lat']); ?>">
            </div>
            <div class="sc-of-field">
                <label for="_sc_outlet_lng"><?php esc_html_e('Longitude', 'supercraft-of'); ?></label>
                <input type="text" id="_sc_outlet_lng" name="_sc_outlet_lng"
                       value="<?php echo esc_attr($values['_sc_outlet_lng']); ?>">
            </div>
        </div>

        <div class="sc-of-field">
            <label for="_sc_outlet_maps_url"><?php esc_html_e('Google Maps URL', 'supercraft-of'); ?></label>
            <input type="url" id="_sc_outlet_maps_url" name="_sc_outlet_maps_url"
                   value="<?php echo esc_attr($values['_sc_outlet_maps_url']); ?>">
        </div>
        <?php
    }

    public function save_meta($post_id, $post)
    {
        if (!isset($_POST['sc_outlet_nonce']) ||
            !wp_verify_nonce($_POST['sc_outlet_nonce'], 'sc_outlet_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = [
            '_sc_outlet_area',
            '_sc_outlet_address',
            '_sc_outlet_phone',
            '_sc_outlet_hours',
            '_sc_outlet_lat',
            '_sc_outlet_lng',
            '_sc_outlet_maps_url',
        ];

        foreach ($fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
            }
        }
    }

    public function columns($columns)
    {
        $new = [];
        foreach ($columns as $k => $v) {
            $new[$k] = $v;
            if ('title' === $k) {
                $new['image'] = __('Image', 'supercraft-of');
                $new['area'] = __('Area', 'supercraft-of');
                $new['phone'] = __('Phone', 'supercraft-of');
                $new['coordinates'] = __('Coordinates', 'supercraft-of');
                $new['menu_order'] = __('Order', 'supercraft-of');
            }
        }
        return $new;
    }

    public function column_data($column, $post_id)
    {
        switch ($column) {
            case 'image':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, [50, 50]);
                }
                break;
            case 'area':
                echo esc_html(get_post_meta($post_id, '_sc_outlet_area', true));
                break;
            case 'phone':
                echo esc_html(get_post_meta($post_id, '_sc_outlet_phone', true));
                break;
            case 'coordinates':
                $lat = get_post_meta($post_id, '_sc_outlet_lat', true);
                $lng = get_post_meta($post_id, '_sc_outlet_lng', true);
                if ($lat && $lng) {
                    echo esc_html("{$lat}, {$lng}");
                }
                break;
            case 'menu_order':
                $post = get_post($post_id);
                echo esc_html($post->menu_order);
                break;
        }
    }

    public function sortable_columns($columns)
    {
        $columns['area'] = 'area';
        $columns['menu_order'] = 'menu_order';
        return $columns;
    }

    public function sort_backend_outlets($query)
    {
        if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'sc_outlet') {
            $orderby = $query->get('orderby');
            if (!$orderby) {
                $query->set('orderby', 'menu_order');
                $query->set('order', 'ASC');
            }
        }
    }
}

