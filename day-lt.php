<?php
/**
 * Plugin Name: Day.Lt scraper
 * Plugin URI: https://github.com/i-smuglov/day-lt
 * Description: This is the test task for scraping.
 * Version: 1.0
 * Author: Yuri Smuglov
 * Author URI: https://github.com/i-smuglov/
 **/

define('DAY__PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(DAY__PLUGIN_DIR . 'scraper-functions.php');
require_once(DAY__PLUGIN_DIR . 'shortcode.php');


add_action('get_dates_hourly', 'get_dates_hourly_func');
function get_dates_hourly_func()
{
    $day_names = get_option('day_names');

    // Update only once a day
    if ($day_names && (date("m.d.y") != $day_names[1])) {
        update_day_names("https://day.lt");
    }
}

register_activation_hook(__FILE__, 'day_lt_activate');
function day_lt_activate()
{
    /*
    Schedule scraping
    This will show outdated results from 00:00 to ±01:00, so we have additional check in the shortcode itself.

    Possibly we don't even need schedules at all,
    but this is a nice example on how to deal with more frequently updated data.
    */
    wp_schedule_event(time(), 'hourly', 'get_dates_hourly');
    // Making sure that update_option() will fork without additional validations
    add_option('day_names', '', null, 'no');
    // Make first run
    update_day_names("https://day.lt");
}

register_deactivation_hook(__FILE__, 'day_lt_deactivate');
function day_lt_deactivate()
{
    wp_clear_scheduled_hook('my_hourly_event');
    delete_option('day_names');
}