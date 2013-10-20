<?php
require('secrets.php');
require('twilio.php');

/** Sends the specified message to the specified phone_id */
function send_message($to_phone_id, $message)
{
    //LOAD TWILIO CONSTANTS
    global $AccountSid, $AuthToken, $TwilioNumber;

    // darn this is old.
    $ApiVersion = '2010-04-01';

    //CHOP OFF TEXT OVER 160
    if(strlen($message) > 160)
        $message = substr($message, 0, 160);
    
    //FIND PHONE NUMBER
    $to_phone_number = $to_phone_id;

    //SEND MESSAGE
    $client = new TwilioRestClient($AccountSid, $AuthToken);
    
    $response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages",
        "POST", array(
            "To" => $to_phone_number,
            "From" => $TwilioNumber,
            "Body" => $message
        )
    );

    //TWILIO ERRORS
    if($response->IsError)
    {
        error_log("Error: ".$response->ErrorMessage."File: ".__FILE__. "\n");
    }
}
?>
