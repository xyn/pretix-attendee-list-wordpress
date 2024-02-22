<?php
    /*
    Plugin Name: Pretix attendees list
    Description: Displays the attendees of different events that have agreed to be visible on the website
    Version: 1.0.0
    */
    require_once(plugin_dir_path(__FILE__) . 'api-calls-pretix-attendee-list.php');
    require_once(plugin_dir_path(__FILE__) . 'tools-pretix-attendee-list.php');
    require_once(plugin_dir_path(__FILE__) . 'widget-pretix-attendee-list.php');
    require_once(plugin_dir_path(__FILE__) . 'settings-pretix-attendee-list.php');
?>