<?php 
    // Settings pane
    function generate_menu() {
        add_menu_page(
            'Pretix attendee list settings',
            'Attendee List settings',
            'manage_options',
            'pretix-settings',
            'pretix_settings_page'
        );
    }
    add_action('admin_menu', 'generate_menu');

    function pretix_settings_page() {
        ?>
        <div class="wrap">
            <h2>Pretix attendee list settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('pretix_settings_group');
                do_settings_sections('pretix-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function pretix_register_settings() {
        register_setting('pretix_settings_group', 'pretix_api_url');
        register_setting('pretix_settings_group', 'pretix_api_token');
        register_setting('pretix_settings_group', 'pretix_organizer');
    }
    add_action('admin_init', 'pretix_register_settings');

    function pretix_settings_sections() {
        add_settings_section('general_settings', 'General Settings', 'general_settings_callback', 'pretix-settings');
        add_settings_field('pretix_api_url', 'API URL', 'text_field_callback', 'pretix-settings', 'general_settings', ['field' => 'pretix_api_url']);
        add_settings_field('pretix_api_token', 'API Token', 'text_field_callback', 'pretix-settings', 'general_settings', ['field' => 'pretix_api_token']);
        add_settings_field('pretix_organizer', 'Organizer', 'text_field_callback', 'pretix-settings', 'general_settings', ['field' => 'pretix_organizer']);
    }
    add_action('admin_init', 'pretix_settings_sections');

    function general_settings_callback() {
        echo '<p>General settings for the Pretix attendee list</p>';
    }

    function text_field_callback($args) {
        $field_name = $args['field'];
        $value = get_option($field_name);
        echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" />';
    }
?>