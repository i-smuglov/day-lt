<?php
// Get all website content
function get_contents($url)
{
    $content = file_get_contents($url);

    // Deal with windows-1257 charset
    return mb_convert_encoding($content, "UTF-8", "HTML-ENTITIES");
}


// Extract day names from website content
function get_day_names($contents)
{
    // could also use some PHP DOM or some library, but regexp is just fine here
    $exp = '/<a href="vardai\/.+" title=".+">(.+)<\/a>/';
    preg_match_all($exp, $contents, $matches);
    return $matches[1];
}


// Transform day names array to formatted list
function transform_day_names($day_names_arr)
{
    $list = '';
    foreach ($day_names_arr as $day) {
        $list .= '<li>' . $day . '</li>';
    }
    return '<ul>' . $list . '</ul>';
}


// Save dates to a database
// TODO: get date of scraping from a website instead of a local one
function save_day_names($day_names_list)
{
    /*
    * Saving data do a database with update_option() works 'like' a cache here, because we don't need to scrape it every time now.
    * From that point any kind of a database cache plugin will work just fine, but we also could just use wp_cache_***() instead.
    * Also, ***_transient() is theoretically better in this use-case, but requires more QA.
    */
    update_option('day_names', array($day_names_list, date("m.d.y"))); // [0] - data, [1] - timestamp
}


// Do everything together
function update_day_names($url)
{
    $contents = get_contents($url);
    $day_names_arr = get_day_names($contents);
    $day_names_list = transform_day_names($day_names_arr);
    save_day_names($day_names_list);
}

