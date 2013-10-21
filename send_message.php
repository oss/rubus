<?php

require('textio.php');

$message = "RUBUS (41411) has moved to this new phone number (848-999-3001). Continue to get bus times by texting RUBUS <stop>. Sorry for the downtime. Tell your friends.";

$handle = fopen("users", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $numba = trim($line);

        print $numba."\n";
        send_message($numba, $message);
    }
}
