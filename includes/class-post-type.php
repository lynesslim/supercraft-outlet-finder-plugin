<?php

if (!defined('ABSPATH')) {
    exit;
}

class TS_TOF_Post_Type
{
    public function __construct()
    {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_ts_outlet', [$this, 'save_meta'], 10, 2);
        add_filter('manage_ts_outlet_posts_columns', [$this, 'columns']);
        add_action('manage_ts_outlet_posts_custom_column', [$this, 'column_data'], 10, 2);
        add_filter('manage_edit-ts_outlet_sortable_columns', [$this, 'sortable_columns']);
    }

    public function register()
    {
        $labels = [
            'name'               => __('Outlets', 'ts-tof'),
            'singular_name'      => __('Outlet', 'ts-tof'),
            'add_new'            => __('Add New', 'ts-tof'),
            'add_new_item'       => __('Add New Outlet', 'ts-tof'),
            'edit_item'          => __('Edit Outlet', 'ts-tof'),
            'new_item'           => __('New Outlet', 'ts-tof'),
            'view_item'          => __('View Outlet', 'ts-tof'),
            'search_items'       => __('Search Outlets', 'ts-tof'),
            'not_found'          => __('No outlets found', 'ts-tof'),
            'not_found_in_trash' => __('No outlets found in Trash', 'ts-tof'),
            'all_items'          => __('All Outlets', 'ts-tof'),
            'menu_name'          => __('TS Outlets', 'ts-tof'),
        ];

        register_post_type('ts_outlet', [
            'labels'       => $labels,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-location',
            'supports'     => ['title'],
            'rewrite'      => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'ts_outlet_details',
            __('Outlet Details', 'ts-tof'),
            [$this, 'render_meta_box'],
            'ts_outlet',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('ts_outlet_save', 'ts_outlet_nonce');

        $fields = [
            '_ts_outlet_area' => __('Area', 'ts-tof'),
            '_ts_outlet_address' => __('Address', 'ts-tof'),
            '_ts_outlet_phone' => __('Phone', 'ts-tof'),
            '_ts_outlet_hours' => __('Operating Hours', 'ts-tof'),
            '_ts_outlet_lat' => __('Latitude', 'ts-tof'),
            '_ts_outlet_lng' => __('Longitude', 'ts-tof'),
            '_ts_outlet_maps_url' => __('Google Maps URL', 'ts-tof'),
        ];

        $values = [];
        foreach ($fields as $key => $label) {
            $values[$key] = get_post_meta($post->ID, $key, true);
        }

        ?>
        <style>
            .ts-tof-field {
                margin-bottom: 14px;
            }
            .ts-tof-field label {
                display: block;
                font-weight: 600;
                margin-bottom: 4px;
            }
            .ts-tof-field input,
            .ts-tof-field textarea {
                width: 100%;
                max-width: 500px;
            }
            .ts-tof-field textarea {
                min-height: 70px;
            }
            .ts-tof-row {
                display: flex;
                gap: 14px;
            }
            .ts-tof-row .ts-tof-field {
                flex: 1;
            }
        </style>

        <div class="ts-tof-field">
            <label for="_ts_outlet_area"><?php esc_html_e('Area', 'ts-tof'); ?></label>
            <input type="text" id="_ts_outlet_area" name="_ts_outlet_area"
                   value="<?php echo esc_attr($values['_ts_outlet_area']); ?>">
        </div>

        <div class="ts-tof-field">
            <label for="_ts_outlet_address"><?php esc_html_e('Address', 'ts-tof'); ?></label>
            <textarea id="_ts_outlet_address" name="_ts_outlet_address"><?php echo esc_textarea($values['_ts_outlet_address']); ?></textarea>
        </div>

        <div class="ts-tof-field">
            <label for="_ts_outlet_phone"><?php esc_html_e('Phone', 'ts-tof'); ?></label>
            <input type="text" id="_ts_outlet_phone" name="_ts_outlet_phone"
                   value="<?php echo esc_attr($values['_ts_outlet_phone']); ?>">
        </div>

        <div class="ts-tof-field">
            <label for="_ts_outlet_hours"><?php esc_html_e('Operating Hours', 'ts-tof'); ?></label>
            <textarea id="_ts_outlet_hours" name="_ts_outlet_hours"><?php echo esc_textarea($values['_ts_outlet_hours']); ?></textarea>
        </div>

        <div class="ts-tof-row">
            <div class="ts-tof-field">
                <label for="_ts_outlet_lat"><?php esc_html_e('Latitude', 'ts-tof'); ?></label>
                <input type="text" id="_ts_outlet_lat" name="_ts_outlet_lat"
                       value="<?php echo esc_attr($values['_ts_outlet_lat']); ?>">
            </div>
            <div class="ts-tof-field">
                <label for="_ts_outlet_lng"><?php esc_html_e('Longitude', 'ts-tof'); ?></label>
                <input type="text" id="_ts_outlet_lng" name="_ts_outlet_lng"
                       value="<?php echo esc_attr($values['_ts_outlet_lng']); ?>">
            </div>
        </div>

        <div class="ts-tof-field">
            <label for="_ts_outlet_maps_url"><?php esc_html_e('Google Maps URL', 'ts-tof'); ?></label>
            <input type="url" id="_ts_outlet_maps_url" name="_ts_outlet_maps_url"
                   value="<?php echo esc_attr($values['_ts_outlet_maps_url']); ?>">
        </div>
        <?php
    }

    public function save_meta($post_id, $post)
    {
        if (!isset($_POST['ts_outlet_nonce']) ||
            !wp_verify_nonce($_POST['ts_outlet_nonce'], 'ts_outlet_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = [
            '_ts_outlet_area',
            '_ts_outlet_address',
            '_ts_outlet_phone',
            '_ts_outlet_hours',
            '_ts_outlet_lat',
            '_ts_outlet_lng',
            '_ts_outlet_maps_url',
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
                $new['area'] = __('Area', 'ts-tof');
                $new['phone'] = __('Phone', 'ts-tof');
                $new['coordinates'] = __('Coordinates', 'ts-tof');
            }
        }
        return $new;
    }

    public function column_data($column, $post_id)
    {
        switch ($column) {
            case 'area':
                echo esc_html(get_post_meta($post_id, '_ts_outlet_area', true));
                break;
            case 'phone':
                echo esc_html(get_post_meta($post_id, '_ts_outlet_phone', true));
                break;
            case 'coordinates':
                $lat = get_post_meta($post_id, '_ts_outlet_lat', true);
                $lng = get_post_meta($post_id, '_ts_outlet_lng', true);
                if ($lat && $lng) {
                    echo esc_html("{$lat}, {$lng}");
                }
                break;
        }
    }

    public function sortable_columns($columns)
    {
        $columns['area'] = 'area';
        return $columns;
    }
}
