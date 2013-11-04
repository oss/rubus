<?php
require_once('rubus_functions.php');

ini_set('log_errors', 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('error_log', 'logs/error.log');

function file_error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        logger('error', "[$errno] $errstr\n");
        logger('error',  "Fatal error on line $errline in file $errfile");
        exit(1);
        break;

    case E_USER_WARNING:
    case E_WARNING:
        logger('warn', "[$errno] $errstr\n");
        break;

    case E_USER_NOTICE:
    case E_NOTICE:
        logger('notice', "[$errno] $errstr\n");
        break;

    default:
        logger('unknown', "[$errno] $errstr\n");
        break;
    }

    return true;
}

set_error_handler('file_error_handler');

function logger($type='INFO', $message)
{
    $type = strtoupper($type);
    $message = "[$type] - $message";

    error_log($message);
}

function help()
{
    $message = "Usage: 'RUBUS [stopname]'\n";
    $message .= "Stop names can be abbreviated titles\n";
    $message .= "More info: http://oss.rutgers.edu/rubus\n";

    return $message;
}

logger('info', $_SERVER['REQUEST_URI']);

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
    try {
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
        }
        else {
            $message = help();
        }

    } catch(Exception $e) {
        logger('warn', (string) $e);
        $message = 'RUBUS is temporarily unavailable. Please try again';
    }
}
?>
<Response>
    <Sms> <?php echo $message; ?> </Sms>
</Response>
