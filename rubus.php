<?php
require_once('rubus_functions.php');

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

    try {
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
    } catch (Exception $e) {
        $message = "RUBUS is temporarily unavailable. Please try again.\n";
    }

} else {
    $message = "Usage: 'RUBUS [stopname]'\n";
    $message .= "Stop names can be abbreviated titles\n";
    $message .= "More info: http://rubus.rutgers.edu\n";
}

echo $message;
echo strlen($message)."\n";
