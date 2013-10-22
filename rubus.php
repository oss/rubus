<?php

function build_inverted_index($data)
{
    print_r($data);

    $index = array();
    foreach($data['stops'] as $stop => $names)
    {
        foreach($names as $name)
        {
            if(empty($index[$name]))
                $index[$name] = array($stop);
            else
                $index[$name][] = $stop;
        }
    }
    return $index;
}

function build_route_config($data)
{
    $rv = array();
    foreach($data['stopsByTitle'] as $stop => $stop_info)
    {
        $rv[$stop] = array();
        foreach($stop_info['tags'] as $tag)
        {
            $stop_id = $data['stops'][$tag]['stopId'];

            if(!in_array($stop_id, $rv[$stop]))
                $rv[$stop][] = $stop_id;
        }
    }

    return $rv;
}

function stops_matching_term($index, $term)
{
    $term = strtolower(trim($term));

    //match the term.
    if(empty($index[$term])) {
        $words = explode(' ', $term);
        //match a word.
        foreach($words as $word)
        {
            if(!empty($index[$word]))
            {
                return $index[$word];
            }
        }

        if(empty($stops))
        {
            //match the first three letters.
            foreach($words as $word)
            {
                $first_three_letters = substr($word, 0, 3);
                if(!empty($index[$first_three_letters]))
                {
                    return $index[$first_three_letters];
                }
            }
        }

        if(empty($stops))
        {
            return NULL;
        }

    } else {
        return $index[$term];
    }
}

$abbreviations = json_decode(file_get_contents('abbreviations.json'), true);
$index = build_inverted_index($abbreviations);

$merge_stops = $abbreviations['merge'];

$route_config_raw = json_decode(file_get_contents('routeconfig.json'), true);
$route_config = build_route_config($route_config_raw);

print_r($route_config);

if(count($argv) == 1)
{
    die("Need a search term\n");
}

$term = trim($argv[1]);

$stops = array();

$stops = stops_matching_term($index, $term);

$add_stops = array();
foreach($stops as $stop)
{
    // check to see if we need to add merge stops.
    foreach($merge_stops as $merge)
    {
        if(in_array($stop, $merge))
            $add_stops = array_merge($add_stops, $merge);
    }
}

$stops = array_unique(array_merge($stops, $add_stops));

print_r($stops);
