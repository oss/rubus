<?php

/** Takes in a json that looks like abbreviations.json and builds an inverted index */
function build_inverted_index($data)
{
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

/** Takes in a json that looks like routeconfig.json and builds route config data */
function build_route_config($data)
{
    $rv = array();
    foreach($data['stopsByTitle'] as $stop => $stop_info)
    {
        $rv[$stop] = array();
        foreach($stop_info['tags'] as $tag)
        {
            $routes = $data['stops'][$tag]['routes'];

            $rv[$stop][$tag] = $routes;
        }
    }

    return $rv;
}

/** Some of our abbreviations affect two stop names
 * resolve_stop_conflicts tries to guess which stop name you're looking for
 */
function resolve_stop_conflicts($stops, $term)
{
    if(count($stops) <= 1)
        return $stops;

    $stop = $stops[0];
    switch($stop)
    {
        case 'Rutgers Student Center':
        case 'College Hall':
        case 'Colony House':
            assert(count($stops) == 3);

            if(strpos($term, 'h') !== FALSE)
                return array('College Hall');
            if(strpos($term, 'y') !== FALSE)
                return array('Colony House');
            return array('Rutgers Student Center');

        case 'Livingston Plaza':
        case 'Livingston Student Center':
            assert(count($stops) == 2);

            if(strpos($term, 'p') !== FALSE)
                return array('Livingston Plaza');
            return array('Livingston Student Center');

        case 'Liberty Street':
        case 'Library of Science':
            assert(count($stops) == 2);
            if(strpos($term, 't'))
                return array('Library of Science');
            return array('Library of Science');

        case 'Busch Student Center':
        case 'Busch Suites':
            assert(count($stops) == 2);

            if(strpos($term, 'sui'))
                return array('Busch Suites');
            return array('Busch Student Center');
    }
    return $stops;
}

/** Takes in a term and matches abbreviations
 * Then merges stops that should be merged.
 * Then resolves any stop conflicts.
 * Returns resulting stops (array of strings)
 *
 * Example:
 * magic_stop_matcher(index, merge_stops, 'rsc')
 *  => array('Rutgers Student Center')
 * magic_stop_matcher(index, merge_stops, 'col')
 *  => array('Rutgers Student Center')
 * magic_stop_matcher(index, merge_stops, 'colh')
 *  => array('College Hall')
 * magic_stop_matcher(index, merge_stops, 'werb')
 *  => array('Werblin Main Entrance', 'Werblin Back Entrance')
 */
function magic_stop_matcher($index, $merge_stops, $term)
{
    $stops = stops_matching_term($index, $term);
    $stops = resolve_stop_conflicts($stops, $term);

    if(empty($stops))
        return $stops;

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

    //get the stops we need to get times for.
    return array_unique(array_merge($stops, $add_stops));
}

/** stops_matching_term($index, 'rsc') => array('Rutgers Student Center')
  * stops_matching_term($index, 'werb') => array('Werblin Back Entrance', 'Werblin Main Entrance')
  */
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

/** If $needle exists at the start of $haystack get rid of it. Lowercases haystack.
 * Example: strip_start('herpderpfoo', 'herp') => 'derpfoo'
 * */
function strip_start($haystack, $needle){
    $haystack = strtolower($haystack);
    $needle = strtolower($needle);
    $pos = strpos($haystack, $needle);

    if($pos !== FALSE)
    {
        $haystack = substr($haystack, $pos+1+strlen($needle));
    }
    return $haystack;
}


/** Returns a short version of the name of a direction.
 * short_direction_name('To Rutgers Student Center') => 'TO RSC'
 */
function short_direction_name($name)
{
    $name = trim($name);

    if(empty($name))
        return $name;

    $name = trim(strip_start($name, 'to'));

    $rv = "";
    $words = explode(' ', $name);
    foreach($words as $word)
        $rv .= $word[0];

    return strtoupper("TO $rv");
}

function get_predictions_from_nextbus($route_config, $stops)
{
    //we know which stops we want times for. Let's just get them now.
    $query = "";
    foreach($stops as $stop)
    {
        $tags = $route_config[$stop];
        foreach($tags as $tag => $routes)
        {
            foreach($routes as $route)
            {
                $query .= "&stops=".$route.'|null|'.$tag;
            }
        }
    }

    //hit nextbus.
    $url = "http://webservices.nextbus.com/service/publicXMLFeed?a=rutgers&command=predictionsForMultiStops".$query;
    $xml_str = file_get_contents($url);

    $prediction_data = new SimpleXMLElement($xml_str);

    //build up a list of results.
    $result = array();

    foreach($prediction_data->predictions as $prediction)
    {
        $route = strtoupper($prediction['routeTag']);
        $direction = short_direction_name($prediction->direction['title']);

        //add the direction to the route
        if(!empty($direction))
            $route .= " $direction";

        $result[$route] = array();

        if($prediction->direction->prediction)
            foreach($prediction->direction->prediction as $time)
                $result[$route][] = (string)$time['minutes'];
    }
    return $result;
}

$abbreviations = json_decode(file_get_contents('abbreviations.json'), true);
$index = build_inverted_index($abbreviations);

$merge_stops = $abbreviations['merge'];

$route_config_raw = json_decode(file_get_contents('routeconfig.json'), true);
$route_config = build_route_config($route_config_raw);

if(count($argv) == 1)
{
    die("Need a search term\n");
}

$term = trim(strtolower($argv[1]));

$stops = magic_stop_matcher($index, $merge_stops, $term);

print_r($stops);

if(!empty($stops)) {
    $nextbus_predictions = get_predictions_from_nextbus($route_config, $stops);

    // build the message.
    $message = "";
    $message .= $stops[0]."\n";
    foreach($nextbus_predictions as $stop => $times)
    {
        //only pick the first three times.
        $times = array_splice($times, 0, 3);
        if(!empty($times))
        {
            $message .= "$stop ".implode(' ', $times). "\n";
        }
    }

} else {
    $message = "Usage: 'RUBUS [stopname]'\n";
    $message .= "Stop names can be abbreviated titles\n";
    $message .= "More info: http://rubus.rutgers.edu\n";
}

echo $message;
echo strlen($message)."\n";
