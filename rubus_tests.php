<?php
require_once('rubus_functions.php');

$abbreviations = json_decode(file_get_contents('abbreviations.json'), true);
$index = build_inverted_index($abbreviations);

$merge_stops = $abbreviations['merge'];

$route_config_raw = json_decode(file_get_contents('routeconfig.json'), true);
$route_config = build_route_config($route_config_raw);

function test_stop_matcher()
{
    global $index, $merge_stops;

    foreach($index as $name => $stops)
    {
        $matches = magic_stop_matcher($index, $merge_stops, $name);

        if(count($matches) != 1)
        {
            //make sure $matches are in merge_stops.

            $found = false;

            foreach($merge_stops as $merge_stop)
            {
                if(count(array_intersect($merge_stop, $matches)) == count($matches))
                    $found = true;
            }

            assert($found);
        }
    }

    return true;
}

assert(test_stop_matcher());
