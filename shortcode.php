<?php
add_shortcode('day-names', 'day_names_func');

function day_names_func()
{
    $day_names = get_option('day_names');
    if ($day_names) {
        // Additional check if our database data is not for a 'yesterday' date
        if (date("m.d.y") != $day_names[1]) {
            update_day_names("https://day.lt");
        }

        $day_names = get_option('day_names');


        return $day_names[0];
    } else {
        return 'Something went wrong';
    }

}
