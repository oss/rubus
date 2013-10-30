<?php
require_once('rubus_functions.php');

function help()
{
    $message = "Usage: 'RUBUS [stopname]'\n";
    $message .= "Stop names can be abbreviated titles\n";
    $message .= "More info: http://rubus.vverma.net/\n";

    return $message;
}

$abbreviations = json_decode(file_get_contents('abbreviations.json'), true);
$index = build_inverted_index($abbreviations);

$merge_stops = $abbreviations['merge'];

$route_config_raw = json_decode(file_get_contents('routeconfig.json'), true);
$route_config = build_route_config($route_config_raw);

if(empty($_REQUEST['Body']))
{
    $message = help();
}
else
{
    $term = strtolower(trim($_REQUEST['Body']));
    $stops = magic_stop_matcher($index, $merge_stops, $term);

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
        $message = help();
    }
}
?>
<Response>
    <Sms> <?php echo $message; ?> </Sms>
</Response>
